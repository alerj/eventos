<?php
namespace App\Data\Models;

use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use Illuminate\Support\Facades\File;
use App\Data\Scopes\CurrentClient as CurrentClientScope;

class Address extends BaseWithClient
{
    const HTTPS_WWW_GOOGLE_COM_MAPS = 'https://www.google.com/maps';

    protected $table = 'addresses';

    protected $orderBy = ['street' => 'asc'];

    protected $appends = [
        'name',
        'model',
        'full_address',
        'google_maps_link_url',
        'google_maps_image_url',
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'zipcode',
        'street',
        'number',
        'complement',
        'neighbourhood',
        'city',
        'state',
        'addressable_id',
        'addressable_type',
        'client_id',
        'latitude',
        'longitude',
    ];

    public function scopeFromSubEvent($query)
    {
        return $query->where('addressable_type', SubEvent::class);
    }

    public static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CurrentClientScope());

        static::saved(function (Address $model) {
            if ($model->isSubEventAddress()) {
                $model->saveGoogleMapsStaticImage();
            }
        });

        static::created(function (Address $model) {
            if ($model->isSubEventAddress()) {
                $model->saveGoogleMapsStaticImage();
            }
        });
    }

    public function isSubEventAddress()
    {
        return $this->addressable_type == SubEvent::class;
    }

    public function addressable()
    {
        return $this->morphTo();
    }

    public function getNameAttribute()
    {
        return $this->getFullAddressAttribute();
    }

    public function getFullAddressAttribute()
    {
        $fullAddress = $this->street;

        if ($this->number) {
            $fullAddress .= ', ' . $this->number;
        }

        if ($this->complement) {
            $fullAddress .= ', ' . $this->complement;
        }

        if ($this->neighbourhood) {
            $fullAddress .= ' - ' . $this->neighbourhood;
        }

        if ($this->city || $this->state) {
            $fullAddress .= '. ';
        }

        if ($this->city) {
            $fullAddress .= $this->city;
        }

        if ($this->state) {
            $fullAddress .= '/' . $this->state;
        }

        return $fullAddress;
    }

    public function getGoogleMapsLinkUrlAttribute()
    {
        return $this->latitude && $this->longitude
            ? self::HTTPS_WWW_GOOGLE_COM_MAPS .
                    '/search/?api=1&query=' .
                    $this->latitude .
                    ',' .
                    $this->longitude .
                    '&zoom=18'
            : '';
    }

    public function staticMapsFileName()
    {
        return $this->latitude . ',' . $this->longitude . '.png';
    }

    public function getGoogleMapsImageUrlAttribute()
    {
        return $this->latitude && $this->longitude
            ? config('filesystems.disks.maps.url_prefix') .
                    $this->staticMapsFileName()
            : '';
    }

    public function staticMapsPath()
    {
        $path = config('filesystems.disks.maps.root');

        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0777, true, true);
        }

        return $path;
    }

    public function saveGoogleMapsStaticImage()
    {
        $guzzle = new Guzzle();

        try {
            $response = $guzzle
                ->request('GET', 'https://www.google.com/maps/api/staticmap', [
                    'query' => [
                        'center' => $this->latitude . ',' . $this->longitude,
                        'zoom' => 16,
                        'size' => '600x300',
                        'maptype' => 'roadmap',
                        'markers' =>
                            'color:red|label:|' .
                            $this->latitude .
                            ',' .
                            $this->longitude,
                        'key' => config('services.google_maps.api_key'),
                    ],
                    'save_to' =>
                        $this->staticMapsPath() .
                        '/' .
                        $this->staticMapsFileName(),
                ])
                ->getBody()
                ->getContents();
        } catch (ClientException $exception) {
            report($exception);

            $response = $exception->getResponse();
        } catch (ConnectException $exception) {
            //timeout
            throw $exception;
        }

        return $response;
    }
}

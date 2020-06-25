<?php

namespace App\Data\Models;

use App\Data\Models\Traits\Selectable;
use function foo\func;
use Illuminate\Notifications\Notifiable;
use App\Data\Repositories\Clients as ClientsRepository;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Str;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class User extends Authenticatable  implements AuditableContract
{
    use Notifiable, Selectable, AuditableTrait;

    protected static $allowedClients;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'permissions',
        'profiles',
        'last_login_at',
    ];

    protected $orderBy = ['name' => 'asc'];

    protected $appends = ['permissions_array'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    private function filterProfilesForCurrentClient($permissions)
    {
        return collect(json_decode($permissions, true))
            ->filter(function ($value, $key) {
                list($client) = extract_client_and_permission($key);

                return $client === get_current_client_slug();
            })
            ->toJson();
    }

    public function getAllPermissions()
    {
        return collect(config('roles'));
    }

    private function filterPermissionsForCurrentClient($permissions)
    {
        if ($this->isSuperUser($this->makeProfilesList())) {
            return $this->getAllPermissions()->toArray();
        } else {
            return collect(json_decode($permissions, true))
                ->mapWithKeys(function ($value, $key) {
                    list($client, $permission) = extract_client_and_permission(
                        $key
                    );

                    return [
                        $key => [
                            'client' => $client,
                            'permission' => $permission,
                        ],
                    ];
                })
                ->filter(function ($permission) {
                    return $permission['client'] === get_current_client_slug();
                })
                ->pluck('permission')
                ->values()
                ->unique()
                ->toArray();
        }
    }

    public function getAllProfilesArrayAttribute()
    {
        return json_decode($this->attributes['profiles'], true);
    }

    public function getAllPermissionsArrayAttribute()
    {
        return json_decode($this->attributes['permissions'], true);
    }

    public function getProfilesAttribute()
    {
        return $this->filterProfilesForCurrentClient(
            $this->attributes['profiles']
        );
    }

    public function getProfilesArrayAttribute()
    {
        return json_decode($this->profiles, true);
    }

    public function getPermissionsArrayAttribute()
    {
        return $this->filterPermissionsForCurrentClient($this->permissions);
    }

    protected function makeProfilesList()
    {
        $allowed = collect(
            json_decode($this->attributes['profiles'], true)
        )->mapWithKeys(function ($value, $key) {
            list($client, $profile) = extract_client_and_permission($key);

            return [$client => $profile];
        });

        return $allowed;
    }

    /**
     * Add webhook url for slack.
     *
     * @return \Illuminate\Config\Repository|mixed
     */
    public function routeNotificationForSlack()
    {
        return config('services.slack.webhook_url');
    }

    //TODO trabalhar futuramente com uma escolha de o sistema favorito , para que este seja o sistema inicial da aplicação
    public function getClientIdAttribute()
    {
        return ($allowed = $this->allowed_clients)->isEmpty()
            ? null
            : $allowed->first()->id;
    }

    public function getAllowedClientsAttribute()
    {
        if (static::$allowedClients) {
            return static::$allowedClients;
        }

        $profilesAllowed = $this->makeProfilesList();

        static::$allowedClients = app(ClientsRepository::class)
            ->all()
            ->filter(function ($client) use ($profilesAllowed) {
                return $this->isSuperUser($profilesAllowed) ||
                    array_has($profilesAllowed, $client->slug);
            });

        return static::$allowedClients;
    }

    /**
     * @param $allowed
     * @return bool
     */
    public function isSuperUser($allowed): bool
    {
        return isset($allowed['all']) && $allowed['all'] === 'administrador';
    }

    public function getIsSecurityAttribute()
    {
        return isset($this->profiles_array['Seguranca']) &&
            !isset($this->profiles_array['Administrador']);
    }

    /**
     * @return array
     */
    public function getJoins()
    {
        return coollect($this->joins);
    }

    public function getIsAdministratorAttribute()
    {
        return isset($this->profiles_array['Administrador']);
    }
}

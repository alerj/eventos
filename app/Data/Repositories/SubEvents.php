<?php

namespace App\Data\Repositories;

use App\Data\Models\SubEvent;
use App\Events\SubEventUpdated;
use App\Events\InvitationsChanged;
use App\Data\Models\SubEvent as SubEventModel;
use App\Data\Repositories\Traits\AddressesTraits;
use App\Data\Repositories\Traits\SubEventsDownload;
use App\Data\Repositories\Addresses as AddressesRepository;

class SubEvents extends Repository
{
    use AddressesTraits, SubEventsDownload;

    /**
     * @var string
     */
    protected $model = SubEventModel::class;

    /**
     * @param $eventId
     * @param $subEventId
     */
    public function confirm($eventId, $subEventId)
    {
        $subEvent = $this->findById($subEventId);

        $subEvent->confirmed_at = now();

        $subEvent->save();

        event(new InvitationsChanged($subEvent));
        event(new SubEventUpdated($subEvent));
    }

    /**
     * @param $subEvent - objet
     * @param $address - array
     * @return mixed
     */
    private function createOrUpdateAddress($subEvent, $address)
    {
        if (!$subEvent->address) {
            return $this->createAddress($subEvent, $address);
        }

        return app(AddressesRepository::class)->updateAddress(
            $subEvent->address,
            $address
        );
    }

    /**
     * @param $eventId
     * @param $subEventId
     */
    public function finalize($eventId, $subEventId)
    {
        $subEvent = $this->findById($subEventId);

        $subEvent->ended_at = now();

        $subEvent->save();

        event(new InvitationsChanged($subEvent));
        event(new SubEventUpdated($subEvent));
    }

    /**
     * @return mixed
     */
    public function getAvailableAddresses()
    {
        return app(AddressesRepository::class)->getByAddressableType(
            SubEventModel::class
        );
    }

    /**
     * @param $eventId
     * @return mixed
     */
    public function filterByEventId($eventId)
    {
        $response = parent::filterByEventId($eventId);

        $response['available_addresses'] = $this->getAvailableAddresses();

        return $response;
    }

    public function storeFromArray($array)
    {
        $subEvent = parent::storeFromArray($array);

        if (isset($array['address'])) {
            $this->createAddress($subEvent, $array['address']);
        }

        return $subEvent;
    }

    public function update($id, $attributes)
    {
        $subEvent = parent::update($id, $attributes);

        $subEvent->address = $this->createOrUpdateAddress(
            $subEvent,
            $attributes['address']
        );

        return $subEvent;
    }

    /**
     *
     * Return all subEvents to happen in 7 days
     *
     * @return array
     */
    public function allAboutToHappen()
    {
        return $this->applyFilter($this->newQuery()->AboutToHappen());
    }

    public function transform($data)
    {
        $this->addTransformationPlugin(function ($subEvent) {
            $subEvent['date'] = substr((string) $subEvent['date'], 0, 10);

            $subEvent['confirmations_end_date'] = substr(
                (string) $subEvent['confirmations_end_date'],
                0,
                10
            );

            $subEvent['time'] = substr($subEvent['time'], 0, 5);

            return $subEvent;
        });

        return parent::transform($data);
    }

    public function makeInvitationImageUrl($link)
    {
        return SubEvent::makeUrl($link);
    }

    public function replicateCommonInfo($eventId, $subEventId)
    {
        $subEvent = $this->findById($subEventId);

        $subEvent->event->subEvents->each(function ($replicable) use (
            $subEvent
        ) {
            $replicable->invitation_text = $subEvent->invitation_text;
            $replicable->credentials_text = $subEvent->credentials_text;
            $replicable->thank_you_text = $subEvent->thank_you_text;
            $replicable->rejection_text = $subEvent->rejection_text;

            $replicable->save();
        });

        event(new SubEventUpdated($subEvent));
    }
}

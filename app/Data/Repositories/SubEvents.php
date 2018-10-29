<?php

namespace App\Data\Repositories;

use App\Data\Models\SubEvent as SubEventModel;
use App\Data\Models\SubEvent;
use App\Data\Repositories\Addresses as AddressesRepository;

class SubEvents extends Repository
{
    /**
     * @var string
     */
    protected $model = SubEventModel::class;

    private function createAddress($subEvent, $array)
    {
        return app(AddressesRepository::class)->storeFromArray(
            app(AddressesRepository::class)->fillAddressableFromModel(
                $array,
                $subEvent
            )
        );
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
     */
    public function filterByEventId($eventId)
    {
        $response = parent::filterByEventId($eventId);

        $response['available_addresses'] = $this->getAvailableAddresses();

        return $response;
    }

    public function storeFromArray($array)
    {
        $this->createAddress(
            ($subEvent = parent::storeFromArray($array)),
            $array
        );

        return $subEvent;
    }

    public function update($id, $attributes)
    {
        $subEvent = parent::update($id, $attributes);

        if (!$subEvent->address) {
            $this->createAddress($subEvent, $attributes['address']);
        } else {
            app(AddressesRepository::class)->updateAddress(
                $subEvent->address,
                $attributes
            );
        }

        return $subEvent;
    }
}

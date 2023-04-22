<?php

namespace App\Repositories;

use App\Interfaces\LocationCheckRepositoryInterface;
use Stevebauman\Location\Facades\Location;

class LocationCheckRepository implements LocationCheckRepositoryInterface
{
    /**
     * @return array
     */
    public function getUserLocation(): array
    {
        $position = Location::get();

        return [
            'lat' => $position->latitude,
            'lon' => $position->longitude,
            'countryCode' => $position->countryCode,
            'regionCode' => $position->regionCode,
            'cityName' => $position->cityName,
        ];
    }
}

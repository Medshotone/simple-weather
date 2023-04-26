<?php

namespace App\Listeners;

use App\Interfaces\LocationCheckRepositoryInterface;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Login;

class LocationCheck
{
    protected LocationCheckRepositoryInterface $locationCheckRepository;

    /**
     * @param LocationCheckRepositoryInterface $locationCheckRepository
     */
    public function __construct(LocationCheckRepositoryInterface $locationCheckRepository)
    {
        $this->locationCheckRepository = $locationCheckRepository;
    }

    public function handle(Login|Registered $event):void
    {
        $user = $event->user;

        $position = $this->locationCheckRepository->getUserLocation();

        $user->lat = $position['lat'];
        $user->lon = $position['lon'];
        $user->countryCode = $position['countryCode'];
        $user->regionCode = $position['regionCode'];
        $user->cityName = $position['cityName'];
        $user->save();
    }
}

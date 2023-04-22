<?php

namespace App\Listeners;

use App\Interfaces\LocationCheckRepositoryInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
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


    /**
     * @param Login $event
     * @return void
     */
    public function handle(Login $event):void
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

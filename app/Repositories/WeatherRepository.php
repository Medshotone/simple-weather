<?php

namespace App\Repositories;

use App\Interfaces\WeatherRepositoryInterface;
use App\Interfaces\WeatherServicesInterface;

class WeatherRepository implements WeatherRepositoryInterface
{
    protected WeatherServicesInterface $weatherService;

    /**
     * @param WeatherServicesInterface $weatherService
     */
    function __construct(WeatherServicesInterface $weatherService)
    {
        $this->weatherService = $weatherService;
    }

    /**
     * @param string $location
     * @return array
     */
    public function getWeatherByLocation(string $location): array
    {
        return $this->weatherService->getWeatherByLocation($location);
    }
}

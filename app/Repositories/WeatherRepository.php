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
     * @param string $lat
     * @param string $lon
     * @return array
     */
    public function getWeatherByLocation(string $lat, string $lon): array
    {
        return $this->weatherService->getWeatherByLocation($lat, $lon);
    }
}

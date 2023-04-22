<?php

namespace App\Interfaces;

interface WeatherRepositoryInterface
{
    /**
     * @param string $location
     * @return array
     */
    public function getWeatherByLocation(string $location): array;
}

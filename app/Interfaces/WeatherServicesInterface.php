<?php

namespace App\Interfaces;

interface WeatherServicesInterface
{
    /**
     * @param string $location
     * @return array
     */
    public function getWeatherByLocation(string $location): array;
}

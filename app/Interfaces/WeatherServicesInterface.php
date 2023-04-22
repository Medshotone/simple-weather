<?php

namespace App\Interfaces;

interface WeatherServicesInterface
{
    /**
     * @param string $lat
     * @param string $lon
     * @return array
     */
    public function getWeatherByLocation(string $lat, string $lon): array;
}

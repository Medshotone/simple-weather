<?php

namespace App\Interfaces;

interface WeatherRepositoryInterface
{
    /**
     * @param string $lat
     * @param string $lon
     * @return array
     */
    public function getWeatherByLocation(string $lat, string $lon): array;
}

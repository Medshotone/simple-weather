<?php

namespace App\Interfaces;

interface WeatherServicesInterface
{
    public function getWeatherByLocation(string $location): array;
}

<?php

namespace App\Interfaces;

interface WeatherRepositoryInterface
{
    public function getWeatherByLocation(string $location): array;
}

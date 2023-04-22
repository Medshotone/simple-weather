<?php

namespace App\Services;

use App\Interfaces\WeatherServicesInterface;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Http;

class OpenWeatherMapService implements WeatherServicesInterface
{
    protected string $api_key;
    protected string $api_url;
    protected int $cache_ttl;

    public function __construct()
    {
        $this->api_key = env('OPENWEATHERMAP_API_KEY');
        $this->api_url = env('OPENWEATHERMAP_URL');
        $this->cache_ttl = env('OPENWEATHERMAP_CACHE_TTL');
    }

    /**
     * @param string $location
     * @return array
     */
    public function getWeatherByLocation(string $location): array
    {
//        if ($weather_data = $this->getWeatherDataCache($location)) {
//            return $weather_data;
//        }

        $coordinates = json_decode($location, true);

        if (empty($coordinates['lon']) || empty($coordinates['lat'])) {
            return [];
        }

        $query = [
            'lat' => $coordinates['lat'],
            'lon' => $coordinates['lon'],
            'appid' => $this->api_key,
        ];

        $response = Http::get($this->api_url, $query);

        if (!$response->successful()) {
            return json_decode($response->getBody(), true);
        }

        return $this->setWeatherDataCache($location, $response->getBody())['main'] ?? [];
    }

    /**
     * @param string $location
     * @param string $weather_data
     * @return array
     */
    public function setWeatherDataCache(string $location, string $weather_data): array
    {
        Redis::set($location, $weather_data, $this->cache_ttl);

        return json_decode($weather_data, true);
    }

    /**
     * @param string $location
     * @return array
     */
    public function getWeatherDataCache(string $location): array
    {
        if (!Redis::exists($location)) {
            return [];
        }

        return json_decode(Redis::get($location), true);
    }
}

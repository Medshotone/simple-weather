<?php

namespace App\Services;

use App\Interfaces\WeatherServicesInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Redis;

class OpenWeatherMapService implements WeatherServicesInterface
{
    protected $guzzle;
    protected $api_key;
    protected $api_url;
    protected $cache_ttl;

    public function __construct()
    {
        $this->guzzle = new Client();
        $this->api_key = env('OPENWEATHERMAP_API_KEY');
        $this->api_url = env('OPENWEATHERMAP_URL');
        $this->cache_ttl = env('OPENWEATHERMAP_CACHE_TTL');
    }

    /**
     * @param $location
     * @return array
     * @throws GuzzleException
     */
    public function getWeatherByLocation($location): array
    {
        if ($weather_data = $this->getWeatherDataCache($location)) {
            return $weather_data;
        }

        $coordinates = json_decode($location, true);

        if (empty($coordinates['lon']) || empty($coordinates['lat'])) {
            return [];
        }

        $query = [
            'lon' => $coordinates['lon'],
            'lat' => $coordinates['lat'],
            'appid' => $this->api_key,
        ];

        $weather_data = json_decode($this->guzzle->get($this->api_url, [
            'query' => $query
        ])->getBody(), true);

        return $this->setWeatherDataCache($location, $weather_data);
    }

    /**
     * @param $location
     * @param array $weather_data
     * @return array
     */
    public function setWeatherDataCache($location, array $weather_data): array
    {
        Redis::set($location, json_encode($weather_data), $this->cache_ttl);

        return $weather_data;
    }

    /**
     * @param $location
     * @return array
     */
    public function getWeatherDataCache($location): array
    {
        if (!Redis::exists($location)) {
            return [];
        }

        return json_decode(Redis::get($location), true);
    }
}

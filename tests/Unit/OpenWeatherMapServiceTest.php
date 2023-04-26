<?php

namespace Tests\Unit;

use App\Services\OpenWeatherMapService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class OpenWeatherMapServiceTest extends TestCase
{
    use RefreshDatabase;

    protected OpenWeatherMapService $openWeatherMapService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->openWeatherMapService = new OpenWeatherMapService();
    }

    public function test_can_get_weather_by_location()
    {
        Http::fake([
            'api.openweathermap.org/*' => Http::response([
                'coord' =>
                    array (
                        'lon' => -122.084,
                        'lat' => 37.422,
                    ),
                'weather' =>
                    array (
                        0 =>
                            array (
                                'id' => 721,
                                'main' => 'Haze',
                                'description' => 'haze',
                                'icon' => '50d',
                            ),
                    ),
                'base' => 'stations',
                'main' =>
                    array (
                        'temp' => 283.58,
                        'feels_like' => 282.9,
                        'temp_min' => 280.2,
                        'temp_max' => 287.3,
                        'pressure' => 1014,
                        'humidity' => 85,
                    ),
                'visibility' => 9656,
                'wind' =>
                    array (
                        'speed' => 0,
                        'deg' => 0,
                    ),
                'clouds' =>
                    array (
                        'all' => 0,
                    ),
                'dt' => 1682517541,
                'sys' =>
                    array (
                        'type' => 1,
                        'id' => 5122,
                        'country' => 'US',
                        'sunrise' => 1682515151,
                        'sunset' => 1682563968,
                    ),
                'timezone' => -25200,
                'id' => 5375480,
                'name' => 'Mountain View',
                'cod' => 200,
            ]),
        ]);

        $weather = $this->openWeatherMapService->getWeatherByLocation('40.7128', '-74.0060');

        $this->assertEquals(283.58, $weather['temp']);
        $this->assertEquals(85, $weather['humidity']);
    }

    public function test_returns_error_if_api_fails()
    {
        Http::fake([
            'api.openweathermap.org/*' => Http::response([
                'cod' => 401,
                'message' => 'Invalid API key. Please see https://openweathermap.org/faq#error401 for more info.',
            ], 401),
        ]);

        $weather = $this->openWeatherMapService->getWeatherByLocation('40.7228', '-74.0060');

        $this->assertEquals(401, $weather['cod']);
        $this->assertEquals('Invalid API key. Please see https://openweathermap.org/faq#error401 for more info.', $weather['message']);
    }

    public function test_can_set_weather_data_cache()
    {
        $weather_data = json_encode([
            'main' => [
                'temp' => 20,
                'humidity' => 50,
            ],
        ]);

        Redis::shouldReceive('set')
            ->once()
            ->with('location_40.7128+-74.0060', $weather_data, env('OPENWEATHERMAP_CACHE_TTL'));

        $weather = $this->openWeatherMapService->setWeatherDataCache('location_40.7128+-74.0060', $weather_data);

        $this->assertEquals(20, $weather['main']['temp']);
        $this->assertEquals(50, $weather['main']['humidity']);
    }

    public function test_can_get_weather_data_cache()
    {
        Redis::shouldReceive('exists')
            ->once()
            ->with('location_40.7128+-74.0060')
            ->andReturn(true);

        Redis::shouldReceive('get')
            ->once()
            ->with('location_40.7128+-74.0060')
            ->andReturn(
                json_encode([
                    'main' => [
                        'temp' => 20,
                        'humidity' => 50,
                    ],
                ])
            );

        $weather = $this->openWeatherMapService->getWeatherDataCache('location_40.7128+-74.0060');

        $this->assertEquals(20, $weather['main']['temp']);
        $this->assertEquals(50, $weather['main']['humidity']);
    }

    public function test_returns_empty_array_if_no_weather_data_cache()
    {
        Redis::shouldReceive('exists')
            ->once()
            ->with('location_40.7128+-74.0060')
            ->andReturn(false);

        Redis::shouldNotReceive('get');

        $weather = $this->openWeatherMapService->getWeatherDataCache('location_40.7128+-74.0060');

        $this->assertEmpty($weather);
    }
}

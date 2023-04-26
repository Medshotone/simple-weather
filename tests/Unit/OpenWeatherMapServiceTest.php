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
                'main' => [
                    'temp' => 20,
                    'humidity' => 50,
                ],
            ]),
        ]);

        $weather = $this->openWeatherMapService->getWeatherByLocation('40.7128', '-74.0060');

        $this->assertEquals(20, $weather['main']['temp']);
        $this->assertEquals(50, $weather['main']['humidity']);
    }

    public function test_returns_error_if_api_fails()
    {
        Http::fake([
            'api.openweathermap.org/*' => Http::response([
                'cod' => 401,
                'message' => 'Invalid API key',
            ], 401),
        ]);

        $weather = $this->openWeatherMapService->getWeatherByLocation('40.7228', '-74.0060');

        $this->assertEquals(401, $weather['cod']);
        $this->assertEquals('Invalid API key', $weather['message']);
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

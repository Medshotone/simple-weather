<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\WeatherRepository;
use App\Interfaces\WeatherRepositoryInterface;
use App\Interfaces\WeatherServicesInterface;
use App\Services\OpenWeatherMapService;

class WeatherServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $openWeatherMapService = new OpenWeatherMapService();
        $weatherRepository = new WeatherRepository($openWeatherMapService);
        $this->app->instance(WeatherRepositoryInterface::class, $weatherRepository);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}

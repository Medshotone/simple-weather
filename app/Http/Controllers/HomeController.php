<?php

namespace App\Http\Controllers;

use App\Interfaces\WeatherRepositoryInterface;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    protected $weatherRepository;

    /**
     * @param WeatherRepositoryInterface $weatherRepository
     */
    public function __construct(WeatherRepositoryInterface $weatherRepository)
    {
        $this->weatherRepository = $weatherRepository;
    }

    public function index()
    {
        $data = [
            'weather_data' => $this->weatherRepository->getWeatherByLocation('{"lat": 51.5084,"lon": -0.1256}')
        ];

        return view('home', $data);
    }
}

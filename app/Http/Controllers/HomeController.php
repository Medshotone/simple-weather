<?php

namespace App\Http\Controllers;

use App\Interfaces\WeatherRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use Illuminate\View\View;

class HomeController extends Controller
{
    protected WeatherRepositoryInterface $weatherRepository;
    protected UserRepositoryInterface $userRepository;

    /**
     * @param WeatherRepositoryInterface $weatherRepository
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(WeatherRepositoryInterface $weatherRepository, UserRepositoryInterface $userRepository)
    {
        $this->weatherRepository = $weatherRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @return View
     */
    public function index(): View
    {
        $userData = $this->userRepository->getCurrentUser();

        return view('home', [
            'userData' => $userData,
            'weatherData' => $this->weatherRepository->getWeatherByLocation($userData['lat'], $userData['lon'])
        ]);
    }
}

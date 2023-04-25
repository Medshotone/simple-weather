<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\HomeController;
use Illuminate\Http\JsonResponse;

class ApiHomeController extends HomeController
{
    /**
     * @return JsonResponse
     */
    public function json(): JsonResponse
    {
        $userData = $this->userRepository->getCurrentUser();

        return response()->json([
            'userData' => $userData,
            'weatherData' => $this->weatherRepository->getWeatherByLocation($userData['lat'], $userData['lon'])
        ], 200);
    }
}

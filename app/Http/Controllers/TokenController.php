<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TokenController extends Controller
{
    public function index(): JsonResponse
    {
        $authUser = Auth::user();

        $success['token'] = $authUser->createToken('weather')->plainTextToken;
        $success['name'] = $authUser->name;

        return response()->json([
            'success' => true,
            'data' => $success,
            'message' => 'Token generated successfully.',
        ], 200);
    }
}

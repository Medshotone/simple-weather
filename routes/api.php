<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Auth\ApiAuthController;
use App\Http\Controllers\API\Auth\ApiOAuthLoginController;
use App\Http\Controllers\API\ApiHomeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', [ApiAuthController::class, 'login'])
    ->name('api.login');
Route::post('/register', [ApiAuthController::class, 'register'])
    ->name('api.register');

// Google login
Route::post('/login/google', [ApiOAuthLoginController::class, 'googleLogin'])
    ->name('api.login.google');


Route::middleware('auth:sanctum')
    ->get('/home', [ApiHomeController::class, 'json'])
    ->middleware(['auth'])
    ->name('api.home');

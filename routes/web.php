<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::redirect('/', '/home');

// Google login
Route::get('login/google', [App\Http\Controllers\Auth\LoginUserController::class, 'redirectToGoogle'])
    ->name('login.google');
Route::get('login/google/callback', [App\Http\Controllers\Auth\LoginUserController::class, 'handleGoogleCallback']);

Route::get('/home', [HomeController::class, 'index'])
    ->middleware(['auth'])
    ->name('home');

require __DIR__.'/auth.php';

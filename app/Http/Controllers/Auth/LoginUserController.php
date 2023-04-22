<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class LoginUserController extends Controller
{

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * @return RedirectResponse
     */
    public function redirectToGoogle(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * @return RedirectResponse
     */
    public function handleGoogleCallback(): RedirectResponse
    {
        $googleUser = Socialite::driver('google')->user();

        $user = User::where('email', '=', $googleUser->email)->first();
        if (!$user) {
            $user = User::create([
                'name' => (string)$googleUser->name,
                'email' => (string)$googleUser->email,
                'provider_id' => (int)$googleUser->id,
                'password' => Hash::make((string)$googleUser->email),
            ]);

            event(new Registered($user));
        }

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}

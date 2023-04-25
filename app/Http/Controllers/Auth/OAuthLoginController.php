<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Interfaces\UserRepositoryInterface;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Validator;

class OAuthLoginController extends Controller
{
    protected UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->middleware('guest')->except('logout');
        $this->userRepository = $userRepository;
    }

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

        $user = $this->userRepository->getUserByEmail((string)$googleUser->email);

        if (!$user) {
            $validator = Validator::make((array)$googleUser, [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255'],
                'id' => ['required', 'string', 'max:255'],
            ]);

            if ($validator->fails()) {
                return redirect(route('login'))
                    ->withErrors(['email' => trans('auth.Google failed')]);
            }

            $user = $this->userRepository->createUser([
                'name' => (string)$googleUser->name,
                'email' => (string)$googleUser->email,
                'provider_id' => (int)$googleUser->id,
                'password' => (string)$googleUser->email,
            ]);

            event(new Registered($user));
        }

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}

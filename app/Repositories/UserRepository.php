<?php

namespace App\Repositories;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
    /**
     * @return array
     */
    public function getCurrentUser(): array
    {
        return Auth::user()->toArray();
    }

    /**
     * @param array $user
     * @return Authenticatable
     */
    function createUser(array $user): Authenticatable
    {
        $user = User::create([
            'name' => $user['name'],
            'email' => $user['email'],
            'provider_id' => $user['id'] ?? null,
            'password' => Hash::make($user['password'] ?? $user['email']),
        ]);

        event(new Registered($user));

        return $user;
    }


    /**
     * @param string $email
     * @return Authenticatable|null
     */
    public function getUserByEmail(string $email): ?Authenticatable
    {
        return User::where('email', '=', $email)->first();
    }
}

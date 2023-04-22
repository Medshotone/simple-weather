<?php

namespace App\Repositories;

use App\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class UserRepository implements UserRepositoryInterface
{
    /**
     * @return array
     */
    public function getCurrentUser(): array
    {
        return Auth::user()->toArray();
    }
}

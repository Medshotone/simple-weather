<?php

namespace App\Interfaces;

use Illuminate\Contracts\Auth\Authenticatable;

interface UserRepositoryInterface
{
    /**
     * @return array
     */
    public function getCurrentUser(): array;

    /**
     * @param array $user
     * @return Authenticatable
     */
    public function createUser(array $user): Authenticatable;

    /**
     * @param string $email
     * @return Authenticatable|null
     */
    public function getUserByEmail(string $email): ?Authenticatable;
}

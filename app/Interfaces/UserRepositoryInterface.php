<?php

namespace App\Interfaces;

interface UserRepositoryInterface
{
    /**
     * @return array
     */
    public function getCurrentUser(): array;
}

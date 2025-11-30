<?php
namespace App\Repositories;

use App\Models\AuthUser;

class AuthRepository
{
    public function findByUsername($username)
    {
        return AuthUser::where('username', $username)->first();
    }
}
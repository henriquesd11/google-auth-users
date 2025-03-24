<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function createUserFromGoogle($googleUser)
    {
        return User::create([
            'name' => $googleUser->getName(),
            'email' => $googleUser->getEmail(),
            'google_id' => $googleUser->getId(),
            'google_token' => $googleUser->token,
        ]);
    }
}

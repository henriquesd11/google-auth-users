<?php

namespace App\Repositories;

use App\Models\PendingUsers;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Crypt;
use Laravel\Socialite\Two\User as SocialiteUser;

class UserRepository
{
    public function findUserByGoogleIdOrEmail(string $googleId, string $email): ?User
    {
        return User::where('google_id', $googleId)->orWhere('email', $email)->first();
    }

    public function findPendingUserByGoogleIdOrEmail(string $googleId, string $email): ?PendingUsers
    {
        return PendingUsers::where('google_id', $googleId)->orWhere('email', $email)->first();
    }

    public function updatePendingUserToken(PendingUsers $pendingUser, string $googleId, string $googleToken): PendingUsers
    {
        $pendingUser->update(
            [
                'google_token' => Crypt::encryptString($googleToken),
                'google_id' => Crypt::encryptString($googleId),
            ]
        );

        return $pendingUser;
    }

    public function createPendingUser(SocialiteUser $googleUser): PendingUsers
    {
        return PendingUsers::create([
            'email' => $googleUser->email,
            'google_id' => Crypt::encryptString($googleUser->id),
            'google_token' => Crypt::encryptString($googleUser->token),
        ]);
    }

    public function getUsersFiltered(?string $name, ?string $cpf): Collection
    {
        return User::when($name, fn ($query) => $query->where('name', 'LIKE', "%$name%"))
            ->when($cpf, fn ($query) => $query->where('cpf', $cpf))
            ->get();
    }

    public function create(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'cpf' => $data['cpf'],
            'birth_date' => $data['birth_date'],
            'google_id' => $data['google_id'],
            'google_token' => $data['google_token'],
        ]);
    }

    public function removePendingUser(PendingUsers $pendingUser): void
    {
        $pendingUser->delete();
    }
}

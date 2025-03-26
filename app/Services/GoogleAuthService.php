<?php

namespace App\Services;

use App\Enums\UserResponses;
use App\Models\PendingUsers;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Http\Response;
use Laravel\Socialite\Two\User as SocialiteUser;
use App\Models\User;

class GoogleAuthService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function handleGoogleCallback(SocialiteUser $socialiteUser): User | PendingUsers
    {
        $user = $this->ifUserExists($socialiteUser);

        if ($user) {
            return $user;
        }

        $pendingUser = $this->ifPendingUserExists($socialiteUser);

        if ($pendingUser) {
            return $pendingUser;
        }

        return $this->userRepository->createPendingUser($socialiteUser);
    }

    private function ifUserExists(SocialiteUser $socialiteUser): ?User
    {
        return $this->userRepository->findUserByGoogleIdOrEmail($socialiteUser->id, $socialiteUser->email);
    }

    private function ifPendingUserExists(SocialiteUser $socialiteUser): ?PendingUsers
    {
        $pendingUser = $this->userRepository
            ->findPendingUserByGoogleIdOrEmail($socialiteUser->id, $socialiteUser->email);

        if ($pendingUser) {
            return $this->userRepository
                ->updatePendingUserToken($pendingUser, $socialiteUser->id, $socialiteUser->token);
        }

        return null;
    }
}

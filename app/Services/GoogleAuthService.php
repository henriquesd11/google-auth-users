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

    /**
     * @throws Exception
     */
    public function handleGoogleCallback(SocialiteUser $socialiteUser): User | PendingUsers
    {
        $this->ifUserExists($socialiteUser);

        $pendingUser = $this->ifPendingUserExists($socialiteUser);

        if ($pendingUser) {
            return $pendingUser;
        }

        return $this->userRepository->createPendingUser($socialiteUser);
    }

    /**
     * @throws Exception
     */
    private function ifUserExists(SocialiteUser $socialiteUser): void
    {
        $user = $this->userRepository->findUserByGoogleIdOrEmail($socialiteUser->id, $socialiteUser->email);

        if ($user) {
            throw new Exception(UserResponses::ALREADY_EXIST->value, Response::HTTP_CONFLICT);
        }
    }

    private function ifPendingUserExists(SocialiteUser $socialiteUser): ?PendingUsers
    {
        $pendingUser = $this->userRepository->findPendingUserByGoogleIdOrEmail($socialiteUser->id, $socialiteUser->email);

        if ($pendingUser) {
            return $this->userRepository->updatePendingUserToken($pendingUser, $socialiteUser->token);
        }

        return null;
    }
}

<?php

namespace App\Services;

use App\Jobs\SendRegistrationEmail;
use App\Models\PendingUsers;
use App\Repositories\UserRepository;
use Laravel\Socialite\Two\User as SocialiteUser;
use App\Models\User;

/**
 * Class GoogleAuthService
 *
 * Serviço responsável por lidar com a autenticação do Google.
 *
 * @package App\Services
 */
class GoogleAuthService
{
    /**
     * Repositório de usuários.
     *
     * @var UserRepository
     */
    private UserRepository $userRepository;

    /**
     * Construtor do GoogleAuthService.
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Lida com o callback do Google.
     *
     * @param SocialiteUser $socialiteUser
     * @return User|PendingUsers
     */
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

        $user = $this->userRepository->createPendingUser($socialiteUser);
        SendRegistrationEmail::dispatch($socialiteUser->email);

        return $user;
    }

    /**
     * Verifica se o usuário já existe.
     *
     * @param SocialiteUser $socialiteUser
     * @return User|null
     */
    private function ifUserExists(SocialiteUser $socialiteUser): ?User
    {
        return $this->userRepository->findUserByGoogleIdOrEmail($socialiteUser->id, $socialiteUser->email);
    }

    /**
     * Verifica se o usuário pendente já existe.
     *
     * @param SocialiteUser $socialiteUser
     * @return PendingUsers|null
     */
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

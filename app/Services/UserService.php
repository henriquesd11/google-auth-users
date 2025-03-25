<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Collection;

class UserService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function listUsers(?string $name = null, ?string $cpf = null): Collection
    {
        return $this->userRepository->getUsersFiltered($name, $cpf);
    }

    public function createUser(array $data): User
    {
        $pendingUser = $this->userRepository->findPendingUserByGoogleIdOrEmail($data['google_id'], $data['email']);
        $this->userRepository->removePendingUser($pendingUser);

        $data['google_token'] = $pendingUser->google_token;
        return $this->userRepository->create($data);
    }
}

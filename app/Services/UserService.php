<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Class UserService
 *
 * Serviço responsável por gerenciar a lógica de negócios relacionada aos usuários.
 *
 * @package App\Services
 */
class UserService
{
    /**
     * Repositório de usuários.
     *
     * @var UserRepository
     */
    private UserRepository $userRepository;

    /**
     * Construtor do UserService.
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Lista os usuários com filtros opcionais.
     *
     * @param string|null $name
     * @param string|null $cpf
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function listUsers(?string $name = null, ?string $cpf = null, int $perPage = 10): LengthAwarePaginator
    {
        return $this->userRepository->getUsersFiltered($name, $cpf, $perPage);
    }

    /**
     * Cria um novo usuário a partir dos dados fornecidos.
     *
     * @param array $data
     * @return User
     */
    public function createUser(array $data): User
    {
        $pendingUser = $this->userRepository->findPendingUserByGoogleIdOrEmail($data['google_id'], $data['email']);
        $this->userRepository->removePendingUser($pendingUser);

        $data['google_token'] = $pendingUser->google_token;
        return $this->userRepository->create($data);
    }
}

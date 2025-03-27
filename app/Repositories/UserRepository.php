<?php

namespace App\Repositories;

use App\Models\PendingUsers;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Crypt;
use Laravel\Socialite\Two\User as SocialiteUser;

/**
 * Class UserRepository
 *
 * Repositório responsável por gerenciar usuários e usuários pendentes.
 *
 * @package App\Repositories
 */
class UserRepository
{
    /**
     * Encontra um usuário pelo Google ID ou email.
     *
     * @param string $googleId
     * @param string $email
     * @return User|null
     */
    public function findUserByGoogleIdOrEmail(string $googleId, string $email): ?User
    {
        return User::where('google_id', $googleId)->orWhere('email', $email)->first();
    }

    /**
     * Encontra um usuário pendente pelo Google ID ou email.
     *
     * @param string $googleId
     * @param string $email
     * @return PendingUsers|null
     */
    public function findPendingUserByGoogleIdOrEmail(string $googleId, string $email): ?PendingUsers
    {
        return PendingUsers::where('google_id', $googleId)->orWhere('email', $email)->first();
    }

    /**
     * Atualiza o token do usuário pendente.
     *
     * @param PendingUsers $pendingUser
     * @param string $googleId
     * @param string $googleToken
     * @return PendingUsers
     */
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

    /**
     * Cria um novo usuário pendente.
     *
     * @param SocialiteUser $googleUser
     * @return PendingUsers
     */
    public function createPendingUser(SocialiteUser $googleUser): PendingUsers
    {
        return PendingUsers::create([
            'email' => $googleUser->email,
            'google_id' => Crypt::encryptString($googleUser->id),
            'google_token' => Crypt::encryptString($googleUser->token),
        ]);
    }

    /**
     * Obtém uma lista paginada de usuários filtrados por nome ou CPF.
     *
     * @param string|null $name
     * @param string|null $cpf
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getUsersFiltered(?string $name, ?string $cpf, int $perPage = 10): LengthAwarePaginator
    {
        return User::when($name, fn ($query) => $query->where('name', 'LIKE', "%$name%"))
            ->when($cpf, fn ($query) => $query->where('cpf', $cpf))
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Cria um novo usuário.
     *
     * @param array $data
     * @return User
     */
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

    /**
     * Remove um usuário pendente.
     *
     * @param PendingUsers $pendingUser
     * @return void
     */
    public function removePendingUser(PendingUsers $pendingUser): void
    {
        $pendingUser->delete();
    }
}

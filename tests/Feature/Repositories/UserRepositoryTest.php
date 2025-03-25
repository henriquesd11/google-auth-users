<?php

namespace Tests\Feature\Repositories;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    protected UserRepository $userRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->userRepository = new UserRepository();
    }

    public function test_find_user_by_google_id_or_email()
    {
        // Cria um usuário no banco
        $this->userRepository->create(
            [
                'name' => 'Teste',
                'email' => 'henr@teste.com',
                'cpf' => '08008008001',
                'birth_date' => '1990-01-01',
                'google_id' => 'google123',
                'google_token' => 'token123',
            ]
        );

        // Testa a função
        $result = $this->userRepository->findUserByGoogleIdOrEmail('google123', 'henr@teste.com');

        $this->assertNotNull($result);
        $this->assertEquals('henr@teste.com', $result->email);
        $this->assertEquals('google123', $result->google_id);
    }
}



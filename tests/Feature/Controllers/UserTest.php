<?php

namespace Controllers;

use App\Enums\UserResponses;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Response;
use Mockery;
use Tests\TestCase;

class UserTest extends TestCase
{
    use DatabaseTransactions;

    private UserService $mockedUserService;

    protected function setUp(): void
    {
        parent::setUp();

        // Mockando UserService para evitar acesso ao banco de dados
        $this->mockedUserService = Mockery::mock(UserService::class);
        $this->app->instance(UserService::class, $this->mockedUserService);
    }

    public function test_it_should_create_a_user(): void
    {
        $userData = collect([
            'name' => 'Luiz Teste',
            'cpf' => '12345678901',
            'birth_date' => '1990-01-01',
            'email' => 'luiz@teste.com',
            'google_id' => 'google123',
        ]);

        $fakeUser = new User($userData->toArray());

        $this->mockedUserService
            ->shouldReceive('createUser')
            ->once()
            ->with($userData->toArray())
            ->andReturn($fakeUser);

        // Fazendo a requisição
        $response = $this->postJson('/api/users', $userData->toArray());

        // Verificando resposta
        $response->assertStatus(Response::HTTP_CREATED)
            ->assertContent(json_encode([
                'message' => UserResponses::CREATED,
                'data' => $userData->except(['google_id'])->toArray(),
            ]));
    }
}

<?php

namespace Controllers;

use App\Enums\UserResponses;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Mockery;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    private UserService $mockedUserService;

    protected function setUp(): void
    {
        parent::setUp();

        // Mockando UserService para evitar acesso ao banco de dados
        $this->mockedUserService = Mockery::mock(UserService::class);
        $this->app->instance(UserService::class, $this->mockedUserService);
    }

    public function test_it_should_list_users(): void
    {
        // Simulando retorno de usuários sem acessar o banco
        $fakeUsers = new Collection([
            ['name' => 'John Doe', 'cpf' => '12345678901'],
            ['name' => 'Jane Doe', 'cpf' => '98765432100'],
        ]);

        $this->mockedUserService
            ->shouldReceive('listUsers')
            ->once()
            ->with('John Doe', '12345678901')
            ->andReturn($fakeUsers);

        // Fazendo a requisição
        $response = $this->getJson('/api/users?name=John Doe&cpf=12345678901');

        // Verificando resposta
        $response->assertStatus(200)
            ->assertContent(json_encode(['data' => $fakeUsers]));
    }

    public function test_it_should_create_a_user(): void
    {
        $userData = collect([
            'nameas' => 'John Doe',
            'cpf' => '12345678901',
            'birth_date' => '1990-01-01',
            'email' => 'john@example.com',
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

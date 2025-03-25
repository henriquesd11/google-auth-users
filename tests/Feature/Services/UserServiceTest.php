<?php

namespace Services;

use App\Models\PendingUsers;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\UserService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Mockery;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    use DatabaseTransactions;

    protected UserService $userService;
    protected UserRepository $userRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->userRepository = Mockery::mock(UserRepository::class);
        $this->userService = new UserService($this->userRepository);
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_list_users_returns_filtered_collection()
    {
        // Cria usuários no banco
        User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'cpf' => '12345678901',
            'birth_date' => '1990-01-01',
            'google_id' => 'google123',
            'google_token' => 'token123',
        ]);
        User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'cpf' => '98765432109',
            'birth_date' => '1995-05-10',
            'google_id' => 'google456',
            'google_token' => 'token456',
        ]);

        // Mock do UserRepository
        $this->userRepository->shouldReceive('getUsersFiltered')
            ->with('John', null)
            ->once()
            ->andReturn(new Collection([User::where('name', 'LIKE', '%John%')->first()]));
        $this->userRepository->shouldReceive('getUsersFiltered')
            ->with(null, '98765432109')
            ->once()
            ->andReturn(new Collection([User::where('cpf', '98765432109')->first()]));
        $this->userRepository->shouldReceive('getUsersFiltered')
            ->with(null, null)
            ->once()
            ->andReturn(User::all());

        // Testa com filtro por nome
        $resultByName = $this->userService->listUsers('John');
        $this->assertInstanceOf(Collection::class, $resultByName);
        $this->assertCount(1, $resultByName);
        $this->assertEquals('John Doe', $resultByName->first()->name);

        // Testa com filtro por CPF
        $resultByCpf = $this->userService->listUsers(null, '98765432109');
        $this->assertInstanceOf(Collection::class, $resultByCpf);
        $this->assertCount(1, $resultByCpf);
        $this->assertEquals('Jane Smith', $resultByCpf->first()->name);

        // Testa sem filtros
        $resultAll = $this->userService->listUsers();
        $this->assertInstanceOf(Collection::class, $resultAll);
        $this->assertCount(2, $resultAll);
    }
}

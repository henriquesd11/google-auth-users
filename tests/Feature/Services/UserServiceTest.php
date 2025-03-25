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
        $currentTotal = User::count();
        $users = User::factory()->count(2)->create();

        // Mock do UserRepository
        $this->userRepository->shouldReceive('getUsersFiltered')
            ->with($users[0]->name, null)
            ->once()
            ->andReturn(new Collection([User::where('name', 'LIKE', "%{$users[0]->name}%")->first()]));
        $this->userRepository->shouldReceive('getUsersFiltered')
            ->with(null, $users[1]->cpf)
            ->once()
            ->andReturn(new Collection([User::where('cpf', $users[1]->cpf)->first()]));
        $this->userRepository->shouldReceive('getUsersFiltered')
            ->with(null, null)
            ->once()
            ->andReturn(User::all());

        // Testa com filtro por nome
        $resultByName = $this->userService->listUsers($users[0]->name);
        $this->assertInstanceOf(Collection::class, $resultByName);
        $this->assertCount(1, $resultByName);
        $this->assertEquals($users[0]->name, $resultByName->first()->name);

        // Testa com filtro por CPF
        $resultByCpf = $this->userService->listUsers(null, $users[1]->cpf);
        $this->assertInstanceOf(Collection::class, $resultByCpf);
        $this->assertCount(1, $resultByCpf);
        $this->assertEquals($users[1]->name, $resultByCpf->first()->name);

        // Testa sem filtros
        $resultAll = $this->userService->listUsers();
        $this->assertInstanceOf(Collection::class, $resultAll);
        $this->assertCount($currentTotal + 2, $resultAll);
    }
}

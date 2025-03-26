<?php

namespace Services;

use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\UserService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
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

    public function test_list_users_returns_paginated_response()
    {
        $users = User::factory()->count(5)->make();
        $paginatedUsers = new \Illuminate\Pagination\LengthAwarePaginator(
            $users,
            $users->count(),
            2,
            1,
            ['path' => url('/api/users')]
        );

        // Mock do UserRepository para a listagem paginada
        $this->userRepository->shouldReceive('getUsersFiltered')
            ->with('Test Name', null, 2)
            ->once()
            ->andReturn($paginatedUsers);

        $this->userRepository->shouldReceive('getUsersFiltered')
            ->with(null, '12345678901', 2)
            ->once()
            ->andReturn($paginatedUsers);

        $this->userRepository->shouldReceive('getUsersFiltered')
            ->with(null, null, 2)
            ->once()
            ->andReturn($paginatedUsers);

        // Testa com filtro por nome
        $resultByName = $this->userService->listUsers('Test Name', null, 2);
        $this->assertInstanceOf(LengthAwarePaginator::class, $resultByName);
        $this->assertCount(5, $resultByName->items());

        // Testa com filtro por CPF
        $resultByCpf = $this->userService->listUsers(null, '12345678901', 2);
        $this->assertInstanceOf(LengthAwarePaginator::class, $resultByCpf);
        $this->assertCount(5, $resultByCpf->items());

        // Testa sem filtros
        $resultAll = $this->userService->listUsers(null, null, 2);
        $this->assertInstanceOf(LengthAwarePaginator::class, $resultAll);
        $this->assertCount(5, $resultAll->items());
    }
}

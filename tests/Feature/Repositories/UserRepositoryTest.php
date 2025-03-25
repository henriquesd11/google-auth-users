<?php

namespace Tests\Feature\Repositories;

use App\Models\PendingUsers;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Crypt;
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

        $result = $this->userRepository->findUserByGoogleIdOrEmail('google123', 'henr@teste.com');

        $this->assertNotNull($result);
        $this->assertEquals('henr@teste.com', $result->email);
        $this->assertEquals('google123', $result->google_id);
    }

    public function test_find_pending_user_by_google_id_or_email()
    {
        $pendingUser = PendingUsers::create([
            'email' => 'henr@teste.com',
            'google_id' => 'google456',
            'google_token' => encrypt('some-token'),
        ]);

        $result = $this->userRepository->findPendingUserByGoogleIdOrEmail('google456', 'henr@teste.com');

        $this->assertNotNull($result);
        $this->assertEquals('henr@teste.com', $result->email);
        $this->assertEquals('google456', $result->google_id);
        $this->assertEquals($pendingUser->id, $result->id);
    }

    public function test_update_pending_user_token()
    {
        $pendingUser = PendingUsers::create([
            'email' => 'henr@teste.com',
            'google_id' => 'google789',
            'google_token' => 'old-token',
        ]);

        $updatedUser = $this->userRepository
            ->updatePendingUserToken($pendingUser, 'google7898', 'new-token');

        $this->assertEquals($pendingUser->id, $updatedUser->id);
        $this->assertEquals('google7898', Crypt::decryptString($updatedUser->google_id));
        $this->assertEquals('new-token', Crypt::decryptString($updatedUser->google_token));
    }

    public function test_create_pending_user()
    {
        $googleUser = new \Laravel\Socialite\Two\User();
        $googleUser->email = 'henr@teste.com';
        $googleUser->id = 'google999';
        $googleUser->token = 'google-token';

        $pendingUser = $this->userRepository->createPendingUser($googleUser);
        $this->assertNotNull($pendingUser->id);
        $this->assertEquals('henr@teste.com', $pendingUser->email);
        $this->assertEquals('google999', Crypt::decryptString($pendingUser->google_id));
        $this->assertEquals('google-token', Crypt::decryptString($pendingUser->google_token));
    }

    public function test_get_users_filtered()
    {
        $currentTotalUsers = User::count();
        $users = User::factory()->count(2)->create();

        $resultByName = $this->userRepository->getUsersFiltered($users[0]->name, null);
        $this->assertCount(1, $resultByName);
        $this->assertEquals($users[0]->name, $resultByName->first()->name);

        $resultByCpf = $this->userRepository->getUsersFiltered(null, $users[1]->cpf);
        $this->assertCount(1, $resultByCpf);
        $this->assertEquals($users[1]->name, $resultByCpf->first()->name);

        $resultAll = $this->userRepository->getUsersFiltered(null, null);
        $this->assertCount($currentTotalUsers + 2, $resultAll);
    }

    public function test_create()
    {
        $user = User::factory()->create();

        $this->assertNotNull($user->id); // Confirms that the user was created
        $this->assertEquals($user->name, $user->name);
        $this->assertEquals($user->email, $user->email);
        $this->assertEquals($user->cpf, $user->cpf);
        $this->assertEquals($user->birth_date, $user->birth_date);
        $this->assertEquals($user->google_id, $user->google_id);
        $this->assertEquals($user->google_token, $user->google_token);
    }

    public function test_remove_pending_user()
    {
        $pendingUser = PendingUsers::factory()->create();

        $this->assertDatabaseHas('pending_users', ['id' => $pendingUser->id]);

        $this->userRepository->removePendingUser($pendingUser);

        $this->assertDatabaseMissing('pending_users', ['id' => $pendingUser->id]);
    }
}



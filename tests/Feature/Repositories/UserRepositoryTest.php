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
        User::create([
            'name' => 'Gabi',
            'email' => 'henr@teste.com',
            'cpf' => '12345678901',
            'birth_date' => '1990-01-01',
            'google_id' => 'google123',
            'google_token' => 'token123',
        ]);
        User::create([
            'name' => 'jose',
            'email' => 'henr1@teste.com',
            'cpf' => '98765432109',
            'birth_date' => '1995-05-10',
            'google_id' => 'google456',
            'google_token' => 'token456',
        ]);

        $resultByName = $this->userRepository->getUsersFiltered('Gabi', null);
        $this->assertCount(1, $resultByName);
        $this->assertEquals('Gabi', $resultByName->first()->name);

        $resultByCpf = $this->userRepository->getUsersFiltered(null, '98765432109');
        $this->assertCount(1, $resultByCpf);
        $this->assertEquals('jose', $resultByCpf->first()->name);

        $resultAll = $this->userRepository->getUsersFiltered(null, null);
        $this->assertCount(2, $resultAll);
    }

    public function test_create()
    {
        $data = [
            'name' => 'Teste',
            'email' => 'henr@teste.com',
            'cpf' => '11122233344',
            'birth_date' => '1985-03-15',
            'google_id' => 'google789',
            'google_token' => 'token789',
        ];

        $user = $this->userRepository->create($data);

        $this->assertNotNull($user->id); // Confirma que foi criado
        $this->assertEquals('Teste', $user->name);
        $this->assertEquals('henr@teste.com', $user->email);
        $this->assertEquals('11122233344', $user->cpf);
        $this->assertEquals('1985-03-15', $user->birth_date);
        $this->assertEquals('google789', $user->google_id);
        $this->assertEquals('token789', $user->google_token);
    }

    public function test_remove_pending_user()
    {
        $pendingUser = PendingUsers::create([
            'email' => 'henr@teste.com',
            'google_id' => 'google111',
            'google_token' => Crypt::encryptString('delete-token'),
        ]);

        $this->assertDatabaseHas('pending_users', ['id' => $pendingUser->id]);

        $this->userRepository->removePendingUser($pendingUser);

        $this->assertDatabaseMissing('pending_users', ['id' => $pendingUser->id]);
    }
}



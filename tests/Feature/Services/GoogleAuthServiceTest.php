<?php

namespace Services;

use App\Enums\UserResponses;
use App\Models\PendingUsers;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\GoogleAuthService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Response;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class GoogleAuthServiceTest extends TestCase
{
    use DatabaseTransactions;

    protected GoogleAuthService $googleAuthService;
    protected UserRepository $userRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->userRepository = Mockery::mock(UserRepository::class);
        $this->googleAuthService = new GoogleAuthService($this->userRepository);
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_handle_google_callback_creates_pending_user_when_no_user_or_pending_user_exists()
    {
        $socialiteUser = new SocialiteUser();
        $socialiteUser->id = 'google123';
        $socialiteUser->email = 'newuser@example.com';
        $socialiteUser->token = 'new-token';
        $user = PendingUsers::factory()->count(1)->make();
        $this->userRepository->shouldReceive('findUserByGoogleIdOrEmail')
            ->with('google123', 'newuser@example.com')
            ->once()
            ->andReturn(null);
        $this->userRepository->shouldReceive('findPendingUserByGoogleIdOrEmail')
            ->with('google123', 'newuser@example.com')
            ->once()
            ->andReturn(null);
        $this->userRepository->shouldReceive('createPendingUser')
            ->with($socialiteUser)
            ->once()
            ->andReturn($user->first());

        $result = $this->googleAuthService->handleGoogleCallback($socialiteUser);

        $this->assertInstanceOf(PendingUsers::class, $result);
        $this->assertEquals($user->first()->email, $result->email);
    }

    public function test_handle_google_callback_throws_exception_when_user_exists()
    {
        $socialiteUser = new SocialiteUser();
        $socialiteUser->id = 'google456';
        $socialiteUser->email = 'teste@example.com';
        $socialiteUser->token = 'existing-token';

        $user = User::factory()->create();

        $this->userRepository->shouldReceive('findUserByGoogleIdOrEmail')
            ->with('google456', 'teste@example.com')
            ->once()
            ->andReturn($user);

        $result = $this->googleAuthService->handleGoogleCallback($socialiteUser);

        $this->assertInstanceOf(User::class, $result);
    }

    public function test_handle_google_callback_updates_pending_user_when_pending_user_exists()
    {
        $socialiteUser = new SocialiteUser();
        $socialiteUser->id = 'google789';
        $socialiteUser->email = 'teste@example.com';
        $socialiteUser->token = 'updated-token';

        $pendingUser = PendingUsers::create([
            'email' => 'teste@example.com',
            'google_id' => encrypt('google789'),
            'google_token' => encrypt('old-token'),
        ]);

        $this->userRepository->shouldReceive('findUserByGoogleIdOrEmail')
            ->with('google789', 'teste@example.com')
            ->once()
            ->andReturn(null);
        $this->userRepository->shouldReceive('findPendingUserByGoogleIdOrEmail')
            ->with('google789', 'teste@example.com')
            ->once()
            ->andReturn($pendingUser);
        $this->userRepository->shouldReceive('updatePendingUserToken')
            ->with($pendingUser, $socialiteUser->id, $socialiteUser->token)
            ->once()
            ->andReturnUsing(function ($pendingUser, $token) {
                $pendingUser->update([
                    'google_token' => encrypt($token),
                    'google_id' => encrypt($pendingUser->google_id),
                ]);
                return $pendingUser;
            });

        $result = $this->googleAuthService->handleGoogleCallback($socialiteUser);

        $this->assertInstanceOf(PendingUsers::class, $result);
        $this->assertEquals('teste@example.com', $result->email);
        $this->assertEquals($pendingUser->google_token, $result->google_token);
    }
}

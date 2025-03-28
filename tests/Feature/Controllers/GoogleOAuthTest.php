<?php

namespace Controllers;

use App\Models\PendingUsers;
use App\Services\GoogleAuthService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Response;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class GoogleOAuthTest extends TestCase
{
    use DatabaseTransactions;

    public function test_redirect_to_google(): void
    {
        // Simula o comportamento do Socialite
        $mockedSocialite = Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $mockedSocialite->shouldReceive('stateless')->andReturnSelf();
        $mockedSocialite->shouldReceive('redirect')->andReturnSelf();
        $mockedSocialite->shouldReceive('getTargetUrl')
            ->andReturn('https://accounts.google.com/o/oauth2/auth?mocked_url');

        // Substitui o Socialite pelo mock
        Socialite::shouldReceive('driver')->with('google')->andReturn($mockedSocialite);

        // Faz a requisição para a API
        $response = $this->getJson('/api/google/login');

        // Verifica se o JSON contém a URL de login esperada
        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'url' => 'https://accounts.google.com/o/oauth2/auth?mocked_url',
            ]);
    }

    public function test_google_callback_returns_pending_user(): void
    {
        // 1️⃣ Mock do usuário retornado pelo Google
        $mockedGoogleUser = Mockery::mock(SocialiteUser::class);
        $mockedGoogleUser->id = 'google123';
        $mockedGoogleUser->email = 'teste@teste.com';
        $mockedGoogleUser->token = 'mocked_token';

        // 2️⃣ Mock do Socialite para retornar o usuário falso
        Socialite::shouldReceive('driver->stateless->user')
            ->andReturn($mockedGoogleUser);

        // 3️⃣ Mock do GoogleAuthService para retornar um usuário pendente fake
        $mockedPendingUser = new PendingUsers([
            'email' => 'teste@teste.com',
            'google_id' => 'google123',
        ]);

        $mockedService = Mockery::mock(GoogleAuthService::class);
        $mockedService->shouldReceive('handleGoogleCallback')
            ->once()
            ->with($mockedGoogleUser)
            ->andReturn($mockedPendingUser);

        $this->app->instance(GoogleAuthService::class, $mockedService);

        // 4️⃣ Faz a requisição para a rota que chama `callback()`
        $response = $this->getJson('/api/google/callback');

        // 5️⃣ Valida se a resposta está correta
        $response->assertStatus(Response::HTTP_FOUND)
            ->assertRedirect(
                env('FRONTEND_URL') . '/register?' . http_build_query($mockedPendingUser->only(
                    [
                        'email', 'google_id'
                    ])
                )
            );
    }

}

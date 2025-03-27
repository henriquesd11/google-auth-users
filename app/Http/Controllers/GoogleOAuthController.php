<?php

namespace App\Http\Controllers;

use App\Services\GoogleAuthService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Socialite\Facades\Socialite;
use App\Enums\GoogleResponses;
use Illuminate\Http\JsonResponse;

/**
 * Class GoogleOAuthController
 *
 * Controlador responsável pela autenticação via Google OAuth.
 *
 * @package App\Http\Controllers
 */
class GoogleOAuthController extends Controller
{
    /**
     * Serviço de autenticação do Google.
     *
     * @var GoogleAuthService
     */
    private GoogleAuthService $googleAuthService;

    /**
     * Construtor do GoogleOAuthController.
     *
     * @param GoogleAuthService $googleAuthService
     */
    public function __construct(GoogleAuthService $googleAuthService)
    {
        $this->googleAuthService = $googleAuthService;
    }

    /**
     * Redireciona para o Google para autenticação.
     *
     * @return JsonResponse
     */
    public function redirectToGoogle(): JsonResponse
    {
        return response()->json([
            'url' => Socialite::driver('google')
                ->stateless()
                ->redirect()
                ->getTargetUrl()
        ]);
    }

    /**
     * Lida com o callback do Google.
     *
     * @param Request $request
     * @return JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function callback(Request $request): JsonResponse | \Illuminate\Http\RedirectResponse
    {
        try {
            if ($request->has('error')) {
                return redirect(
                    env('FRONTEND_URL')
                );
            }

            $googleUser = Socialite::driver('google')->stateless()->user();

            $user = $this->googleAuthService->handleGoogleCallback($googleUser);

            $query = http_build_query($user->only(['email', 'google_id']));
            $endpoint = '/register?' . $query;
            if ($user instanceof \App\Models\User) {
                $endpoint = '/users';
            }

            return redirect(
                env('FRONTEND_URL') . $endpoint
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'error' =>  GoogleResponses::ERROR_INTEGRATION,
                    'message' => $e->getMessage()
                ]
                ,$e->getCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

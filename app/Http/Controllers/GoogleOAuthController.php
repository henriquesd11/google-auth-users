<?php

namespace App\Http\Controllers;

use App\Services\GoogleAuthService;
use Illuminate\Http\Response;
use Laravel\Socialite\Facades\Socialite;
use App\Enums\GoogleResponses;
use Illuminate\Http\JsonResponse;

class GoogleOAuthController extends Controller
{
    private GoogleAuthService $googleAuthService;

    public function __construct(GoogleAuthService $googleAuthService)
    {
        $this->googleAuthService = $googleAuthService;
    }

    public function redirectToGoogle(): JsonResponse
    {
        return response()->json([
            'url' => Socialite::driver('google')
                ->stateless()
                ->redirect()
                ->getTargetUrl()
        ]);
    }

    public function callback(): JsonResponse | \Illuminate\Http\RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $user = $this->googleAuthService->handleGoogleCallback($googleUser);

            $redirectUrl = 'http://localhost:5173/register?' . http_build_query($user->only(['email', 'google_id']));

            return redirect($redirectUrl);
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

<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepository;
use Laravel\Socialite\Facades\Socialite;

class GoogleOAuthController extends Controller
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function redirectToGoogle()
    {
        return response()->json([
            'url' => Socialite::driver('google')->stateless()->redirect()->getTargetUrl()
        ]);
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

//            // Apenas armazenamos os dados no banco e retornamos uma resposta JSON
//            $user = $this->userRepository->createUserFromGoogle($googleUser);

            return response()->json([
                'message' => 'Usuário salvo com sucesso!',
                'user' => $googleUser->user
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'error' => 'Erro na integração com o Google',
                    'message' => $e->getMessage()
                ]
                ,500);
        }
    }
}

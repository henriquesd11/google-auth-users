<?php

use App\Enums\RootResponses;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleOAuthController;
use App\Http\Controllers\UserController;

/**
 * Define a rota raiz.
 *
 * @return \Illuminate\Http\JsonResponse
 */
Route::get('/', function () {
    return response()->json([
        'message' => RootResponses::WELCOME,
    ]);
});

/**
 * Agrupa rotas relacionadas ao Google OAuth.
 */
Route::group(['prefix' => 'google'], function () {
    /**
     * Redireciona para o Google para autenticação.
     *
     * @see \App\Http\Controllers\GoogleOAuthController::redirectToGoogle()
     */
    Route::get('/login', [GoogleOAuthController::class, 'redirectToGoogle']);

    /**
     * Lida com o callback do Google.
     *
     * @see \App\Http\Controllers\GoogleOAuthController::callback()
     */
    Route::get('/callback', [GoogleOAuthController::class, 'callback']);
});

/**
 * Rotas de recurso para UserController.
 *
 * @see \App\Http\Controllers\UserController
 */
Route::resource('users', UserController::class);

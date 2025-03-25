<?php

use App\Enums\RootResponses;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleOAuthController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return response()->json([
        'message' => RootResponses::WELCOME,
    ]);
});

Route::group(['prefix' => 'google'], function () {
    Route::get('/login', [GoogleOAuthController::class, 'redirectToGoogle']);
    Route::get('/callback', [GoogleOAuthController::class, 'callback']);
});

Route::resource('users', UserController::class);

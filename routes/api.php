<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleOAuthController;

Route::get('/', function () {
    return response()->json([
        'message' => 'Welcome to Laravel API Starter',
    ]);
});

Route::group(['prefix' => 'google'], function () {
    Route::get('/login', [GoogleOAuthController::class, 'redirectToGoogle']);
    Route::get('/callback', [GoogleOAuthController::class, 'handleGoogleCallback']);
});

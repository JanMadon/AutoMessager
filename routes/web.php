<?php

use App\Http\Controllers\GoogleOAuthController;
use App\Http\Controllers\LearningPhraseController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/oauth/google/callback', [GoogleOAuthController::class, 'callback']);

Route::get('/mail/show', [GoogleOAuthController::class, 'mailShow']);

Route::get('/api/learning-phrases', [LearningPhraseController::class, 'index']);

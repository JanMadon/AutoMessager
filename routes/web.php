<?php

use App\Http\Controllers\GoogleOAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/oauth/google/callback', [GoogleOAuthController::class, 'callback']);

Route::get('/mail/show', [GoogleOAuthController::class, 'mailShow']);

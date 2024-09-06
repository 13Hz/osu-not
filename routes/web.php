<?php

use App\Http\Controllers\OauthCallbackController;
use Illuminate\Support\Facades\Route;

Route::get('/oauth/callback', [OauthCallbackController::class, 'handle']);

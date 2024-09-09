<?php

use App\Http\Controllers\OauthCallbackController;
use App\Http\Controllers\TelegramWebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/oauth/callback', [OauthCallbackController::class, 'handle']);
Route::post('/webhook', [TelegramWebhookController::class, 'hook']);

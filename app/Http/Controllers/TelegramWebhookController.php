<?php

namespace App\Http\Controllers;

use Telegram\Bot\Laravel\Facades\Telegram;
use Illuminate\Http\Response;

class TelegramWebhookController extends Controller
{
    public function hook(): Response
    {
        Telegram::commandsHandler(true);

        return response('ok');
    }
}

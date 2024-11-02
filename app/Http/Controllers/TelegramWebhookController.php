<?php

namespace App\Http\Controllers;

use App\Factories\TelegramCallbackQueryResponseFactory;
use App\Kernel\Builders\KeyboardBuilder;
use Telegram\Bot\Laravel\Facades\Telegram;
use Illuminate\Http\Response;

class TelegramWebhookController
{
    public function __construct(protected KeyboardBuilder $keyboardBuilder)
    {
    }

    public function hook(): Response
    {
        Telegram::commandsHandler(true);
        $callback = Telegram::getWebhookUpdate()->callbackQuery;
        if ($callback) {
            TelegramCallbackQueryResponseFactory::exec($callback)?->run();
        }

        return response('ok');
    }
}

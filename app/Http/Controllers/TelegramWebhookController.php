<?php

namespace App\Http\Controllers;

use App\Factories\TelegramCallbackQueryResponseFactory;
use App\Kernel\Builders\KeyboardBuilder;
use Exception;
use Illuminate\Support\Facades\Log;
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
            try {
                TelegramCallbackQueryResponseFactory::exec($callback)?->run();
            } catch (Exception $exception) {
                Log::warning($exception->getMessage());
            }
        }

        return response('ok');
    }
}

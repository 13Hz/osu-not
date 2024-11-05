<?php

namespace App\Http\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Message;

class MessagesService
{
    public function sendMessage(int $chatId, string $text, string $parseMode = 'HTML', bool $disablePagePreview = true): ?Message
    {
        try {
            $message = Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => $parseMode,
                'disable_web_page_preview' => $disablePagePreview
            ]);
            if ($message && $message->messageId) {
                return $message;
            }
        } catch (Exception $ex) {
            Log::error('Ошибка отправки сообщения', ['text' => $text, 'exception' => $ex->getMessage()]);
        }

        return null;
    }
}

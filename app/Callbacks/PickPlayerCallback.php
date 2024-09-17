<?php

namespace App\Callbacks;

use App\Kernel\Builders\KeyboardBuilder;
use Telegram\Bot\Laravel\Facades\Telegram;

final class PickPlayerCallback extends CallbackResponse
{
    public function run(): void
    {
        $keyboardBuilder = new KeyboardBuilder();

        Telegram::editMessageText([
            'chat_id' => $this->chatId,
            'message_id' => $this->messageId,
            'text' => 'Выберите действие',
            'reply_markup' => $keyboardBuilder->buildPlayerActionsMenu($this->data['id']),
        ]);
    }
}

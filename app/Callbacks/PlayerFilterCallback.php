<?php

namespace App\Callbacks;

use App\Kernel\Builders\FiltersKeyboardBuilder;
use App\Models\ChatUser;
use Telegram\Bot\Laravel\Facades\Telegram;

final class PlayerFilterCallback extends CallbackResponse
{
    public function run(): void
    {
        $chatUser = ChatUser::query()
            ->where('chat_id', $this->chatId)
            ->where('user_id', $this->data['id'])->get()->first();
        if ($chatUser) {
            $keyboardBuilder = new FiltersKeyboardBuilder($chatUser);
            Telegram::editMessageText([
                'chat_id' => $this->chatId,
                'message_id' => $this->messageId,
                'text' => 'Выберите действие',
                'reply_markup' => $keyboardBuilder->buildPlayerFilterMenu(),
            ]);
        }
    }
}

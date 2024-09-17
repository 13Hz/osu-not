<?php

namespace App\Callbacks;

use App\Kernel\Builders\KeyboardBuilder;
use Telegram\Bot\Laravel\Facades\Telegram;

final class PlayersListCallback extends CallbackResponse
{
    public function run(): void
    {
        $keyboardBuilder = new KeyboardBuilder();
        $playersMenu = $keyboardBuilder->buildPlayersMenu($this->chatId);
        if ($playersMenu) {
            Telegram::editMessageText([
                'chat_id' => $this->chatId,
                'message_id' => $this->messageId,
                'text' => 'Список добавленных пользователей',
                'reply_markup' => $playersMenu
            ]);
        } else {
            Telegram::answerCallbackQuery([
                'callback_query_id' => $this->callbackQuery->id,
                'text' => 'Список пользователей пуст'
            ]);
            Telegram::deleteMessage([
                'chat_id' => $this->chatId,
                'message_id' => $this->messageId
            ]);
        }
    }
}

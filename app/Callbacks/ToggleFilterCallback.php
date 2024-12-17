<?php

namespace App\Callbacks;

use App\Kernel\Builders\FiltersKeyboardBuilder;
use App\Models\ChatUser;
use Telegram\Bot\Laravel\Facades\Telegram;

final class ToggleFilterCallback extends CallbackResponse
{
    public function run(): void
    {
        $chatUser = ChatUser::query()->find($this->data['chat_user_id']);
        if ($chatUser) {
            $chatUserFilter = $chatUser->filters()->find($this->data['filter_id']);
            if (!$chatUserFilter) {
                $chatUser->filters()->attach($this->data['filter_id'], ['active' => true]);
            } else {
                $chatUserFilter->pivot->update(['active' => !$chatUserFilter->pivot->active]);
            }

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

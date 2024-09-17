<?php

namespace App\Callbacks;

use App\Models\Chat;
use App\Models\User;

final class DeletePlayerCallback extends CallbackResponse
{
    public function run(): void
    {
        $user = User::find($this->data['id']);
        $chat = Chat::find($this->chatId);
        if ($user && $chat) {
            $chat->users()->detach($user);
            if ($user->chats->isEmpty()) {
                $user->delete();
            }
        }

        (new PlayersListCallback($this->callbackQuery))->run();
    }
}

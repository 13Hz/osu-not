<?php

namespace App\Callbacks;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Support\Facades\Log;

final class DeletePlayerCallback extends CallbackResponse
{
    public function run(): void
    {
        $user = User::find($this->data['id']);
        $chat = Chat::find($this->chatId);
        if ($user && $chat) {
            Log::info('Пользователь {userName} откреплен от чата', ['userName' => $user->name]);
            $chat->users()->detach($user);
            if ($user->chats->isEmpty()) {
                $user->delete();
            }
        }

        (new PlayersListCallback($this->callbackQuery))->run();
    }
}

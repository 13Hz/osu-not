<?php

namespace App\Callbacks;

use App\Models\Chat;

final class DeletePlayerCallback extends CallbackResponse
{
    public function run(): void
    {
        Chat::find($this->chatId)->users()->detach($this->data['id']);
        (new PlayersListCallback($this->callbackQuery))->run();
    }
}

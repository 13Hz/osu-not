<?php

namespace App\Http\Services;

use App\Models\Chat;
use Illuminate\Support\Collection;

class ChatsService
{
    public function getOrCreateChat(Collection $chatData): ?Chat
    {
        $name = $chatData->get('username') ?? $chatData->get('title');
        $chat = Chat::firstOrCreate(
            ['id' => $chatData->get('id')],
            ['name' => $name]
        );

        if ($chat && $chat->name != $name) {
            $chat->update(['name' => $name]);
        }

        return $chat;
    }
}

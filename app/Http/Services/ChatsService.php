<?php

namespace App\Http\Services;

use App\Models\Chat;

class ChatsService
{
    public function getOrCreateChat(int $id): ?Chat
    {
        return Chat::firstOrCreate(['id' => $id]);
    }
}

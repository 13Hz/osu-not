<?php

namespace App\Schedules;

use App\Http\Services\OsuUsersService;
use App\Kernel\DTO\GetUserScoresDTO;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class CheckUsersScoresSchedule
{
    public function __invoke(OsuUsersService $usersService)
    {
        //TODO: Вынести в очередь
        $users = User::all();
        foreach ($users as $user) {
            $lastScore = $usersService->getUserScores(new GetUserScoresDTO($user->id, 'recent', limit: 1))[0] ?? null;
            if ($lastScore && $lastScore->getHash() != $user->last_score_hash) {
                Log::info("player $user->name submitted new score");
                if ($lastScore->passed && $lastScore->pp > 0) {
                    $accuracy = round($lastScore->accuracy * 100, 2);
                    $mods = null;
                    if (!empty($lastScore->mods)) {
                        $mods = '+' . implode(array: $lastScore->mods);
                    }
                    foreach ($user->chats()->get() as $chat) {
                        //TODO: Добавить обложку карты + инфу по показателям AR CS OD и тд + ссылку на профиль, карту, скор
                        Telegram::sendMessage([
                            'chat_id' => $chat->id,
                            'text' => "$lastScore->rank | $user->name | {$lastScore->beatmapset->artist} - {$lastScore->beatmapset->title} [{$lastScore->beatmap->version}] | {$lastScore->pp}pp $accuracy% {$lastScore->beatmap->difficulty_rating}✩ $mods",
                        ]);
                    }
                }
                $user->update(['last_score_hash' => $lastScore->getHash()]);
            }
        }
    }
}

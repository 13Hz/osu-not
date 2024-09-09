<?php

namespace App\Schedules;

use App\Http\Services\OsuUsersService;
use App\Kernel\DTO\GetUserScoresDTO;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class CheckUsersScoresSchedule
{
    public function __invoke(OsuUsersService $usersService)
    {
        //TODO: Вынести в очереди
        $users = User::all();
        foreach ($users as $user) {
            $lastScore = $usersService->getUserScores(new GetUserScoresDTO($user->id, 'recent', limit: 1))[0] ?? null;
            if ($lastScore && $lastScore->getHash() != $user->last_score_hash) {
                //TODO: Шлем сообщение в тг
                Log::debug('YAAAY', $lastScore);
                $user->update(['last_score_hash' => $lastScore->getHash()]);
            }
        }
    }
}

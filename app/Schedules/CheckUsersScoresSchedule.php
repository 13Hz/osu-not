<?php

namespace App\Schedules;

use App\Http\Services\OsuUsersService;
use App\Http\Services\ScoresService;
use App\Kernel\Builders\MessageBuilder;
use App\Kernel\DTO\GetUserScoresDTO;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class CheckUsersScoresSchedule
{
    public function __invoke(OsuUsersService $usersService, ScoresService $scoresService)
    {
        //TODO: Вынести в очередь
        $users = User::all();
        foreach ($users as $user) {
            $lastScoreResponse = $usersService->getUserScores(new GetUserScoresDTO($user->id, 'recent', limit: 1))[0] ?? null;
            if ($lastScoreResponse) {
                if ($lastScoreResponse['user']['username'] != $user->name) {
                    $user->update(['name' => $lastScoreResponse['user']['username']]);
                }
                if ($lastScoreResponse['user']['avatar_url'] != $user->avatar_url) {
                    $user->update(['avatar_url' => $lastScoreResponse['user']['avatar_url']]);
                }
                $score = $scoresService->firstOrCreateFromResponse($lastScoreResponse);
                if ($score && $score->id != $user->last_score_id) {
                    Log::info("player $user->name submitted new score");
                    if ($score->passed && $score->pp > 0) {
                        $messageBuilder = new MessageBuilder();
                        $pp = round($score->pp, 1);
                        $accuracy = round($score->accuracy * 100, 2);
                        $mods = null;
                        if (!empty($score->mods)) {
                            $mods = '+' . implode('', $score->mods);
                        }
                        $message = $messageBuilder
                            ->addText($score->rank)
                            ->addLink($user->name, "https://osu.ppy.sh/users/$user->id")
                            ->addText("{$score->beatmapset['artist']} - {$score->beatmapset['title']} [{$score->beatmap['version']}]")
                            ->addText("{$pp}pp $accuracy% {$score->beatmap['difficulty_rating']}✩ $mods")
                            ->getText();
                        foreach ($user->chats()->get() as $chat) {
                            //TODO: Добавить обложку карты + инфу по показателям AR CS OD и тд + ссылку на профиль, карту, скор
                            try {
                                Telegram::sendMessage([
                                    'chat_id' => $chat->id,
                                    'text' => $message,
                                    'parse_mode' => 'HTML',
                                    'disable_web_page_preview' => true
                                ]);
                            } catch (\Exception $ex) {
                                Log::error('Ошибка отправки сообщения, текст: ' . $message . ' ' . $ex->getMessage());
                                break;
                            }
                        }
                    }
                    $user->lastScore()->associate($score)->save();
                }
            }
        }
    }
}

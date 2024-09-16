<?php

namespace App\Jobs;

use App\Http\Services\OsuUsersService;
use App\Http\Services\ScoresService;
use App\Kernel\Builders\MessageBuilder;
use App\Kernel\DTO\GetUserScoresDTO;
use App\Models\Message;
use App\Models\User;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class CheckPlayerLastScoreJob implements ShouldQueue
{
    use Queueable;
    use Batchable;

    private array $userIds;

    /**
     * Create a new job instance.
     */
    public function __construct(array $usersIds)
    {
        $this->userIds = $usersIds;
    }

    /**
     * Execute the job.
     */
    public function handle(OsuUsersService $osuUsersService, ScoresService $scoresService): void
    {
        if (empty($this->userIds)) {
            return;
        }

        $users = User::find($this->userIds);
        foreach ($users as $user) {
            $lastScoreResponse = $osuUsersService->getUserScores(new GetUserScoresDTO($user->id, 'recent', limit: 1))[0] ?? null;
            if ($lastScoreResponse) {
                if ($lastScoreResponse['user']['username'] != $user->name) {
                    $user->update(['name' => $lastScoreResponse['user']['username']]);
                }
                if ($lastScoreResponse['user']['avatar_url'] != $user->avatar_url) {
                    $user->update(['avatar_url' => $lastScoreResponse['user']['avatar_url']]);
                }
                $score = $scoresService->firstOrCreateFromResponse($lastScoreResponse);
                if ($score && $score->id != $user->last_score_id && $score->mode == 'osu') {
                    Log::info("player $user->name submitted new score");
                    if ($score->passed && $score->pp > 0) {
                        $messageBuilder = new MessageBuilder();
                        $pp = round($score->pp, 1);
                        $accuracy = round($score->accuracy * 100, 2);
                        $mods = null;
                        if (!empty($score->mods)) {
                            $mods = '+' . implode('', $score->mods);
                        }
                        $text = $messageBuilder
                            ->addText($score->rank)
                            ->addLink($user->name, "https://osu.ppy.sh/users/$user->id")
                            ->addLink("{$score->beatmapset['artist']} - {$score->beatmapset['title']} [{$score->beatmap['version']}]", $score->beatmap['url'])
                            ->addText("{$pp}pp $accuracy% {$score->beatmap['difficulty_rating']}★ $mods")
                            ->getText();
                        foreach ($user->chats()->get() as $chat) {
                            //TODO: Добавить обложку карты + инфу по показателям AR CS OD и тд + ссылку на профиль, карту, скор
                            try {
                                $message = Telegram::sendMessage([
                                    'chat_id' => $chat->id,
                                    'text' => $text,
                                    'parse_mode' => 'HTML',
                                    'disable_web_page_preview' => true
                                ]);
                                if ($message) {
                                    Message::create([
                                        'score_id' => $score->id,
                                        'id' => $message->messageId,
                                        'message' => $text,
                                        'chat_id' => $chat->id,
                                    ]);
                                }
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

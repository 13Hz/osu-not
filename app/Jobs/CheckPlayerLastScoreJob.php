<?php

namespace App\Jobs;

use App\Http\Services\MessagesService;
use App\Http\Services\OsuUsersService;
use App\Http\Services\ScoresService;
use App\Kernel\DTO\GetUserScoresDTO;
use App\Models\Message;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

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
    public function handle(OsuUsersService $osuUsersService, ScoresService $scoresService, MessagesService $messagesService): void
    {
        if (empty($this->userIds)) {
            return;
        }
        $now = Carbon::now();
        $users = User::find($this->userIds);
        foreach ($users as $user) {
            $lastCheckAt = Carbon::parse($user->last_score_check_at);
            $daysInactive = $lastCheckAt->diffInDays($now);
            $minutes = $this->calculatePollingIntervalMinutes($daysInactive);
            if ($lastCheckAt->addMinutes($minutes)->isFuture()) {
                continue;
            }

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
                    $user->update(['last_score_check_at' => $now]);
                    Log::info('Пользователь {userName} поставил новый результат', ['userName' => $user->name]);
                    if ($score->passed && $score->pp > 0) {
                        $text = $scoresService->getScoreStringInfo($score);
                        foreach ($user->chats()->get() as $chat) {
                            $message = $messagesService->sendMessage($chat->id, $text);
                            if ($message) {
                                $telegramMessage = Message::create([
                                    'score_id' => $score->id,
                                    'id' => $message->messageId,
                                    'message' => $text,
                                    'chat_id' => $chat->id,
                                ]);
                                if ($telegramMessage) {
                                    GenerateScorePreview::dispatch($score->id);
                                }
                            }
                        }
                    }
                    $user->lastScore()->associate($score)->save();
                }
            }
        }
    }

    private function calculatePollingIntervalMinutes(int $daysInactive): int
    {
        if ($daysInactive <= 2) {
            return 0;
        } elseif ($daysInactive <= 7) {
            return 1;
        } elseif ($daysInactive <= 30) {
            return 5;
        } else {
            return 60;
        }
    }
}

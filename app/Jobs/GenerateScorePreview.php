<?php

namespace App\Jobs;

use App\Kernel\Builders\ScorePreviewBuilder;
use App\Models\Message;
use App\Models\Score;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\InputMedia\InputMediaPhoto;

class GenerateScorePreview implements ShouldQueue
{
    use Queueable;

    private int $scoreId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $scoreId)
    {
        $this->scoreId = $scoreId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $score = Score::find($this->scoreId);
        if ($score && !$score->preview()->exists()) {
            $previewBuilder = new ScorePreviewBuilder($score);
            $preview = $previewBuilder->getPreview();
            if ($preview) {
                $messages = Message::query()->where('score_id', $score->id)->get();
                foreach ($messages as $message) {
                    Telegram::editMessageMedia([
                        'chat_id' => $message->chat_id,
                        'message_id' => $message->id,
                        'media' => json_encode(InputMediaPhoto::make([
                            'type' => 'photo',
                            'media' => $preview->url,
                            'caption' => $message->message,
                            'parse_mode' => 'HTML'
                        ]))
                    ]);
                }
            }
        }
    }
}

<?php

namespace App\Http\Services;

use App\Kernel\Builders\MessageBuilder;
use App\Models\Score;
use App\Models\User;
use Carbon\Carbon;

class ScoresService
{
    public function firstOrCreateFromResponse(array $scoreResponse): ?Score
    {
        if (empty($scoreResponse) || !$scoreResponse['id']) {
            return null;
        }

        $user = User::find($scoreResponse['user']['id'] ?? -1);
        if ($user) {
            return Score::firstOrCreate(
                ['id' => $scoreResponse['id']],
                [
                    'id' => $scoreResponse['id'],
                    'user_id' => $user['id'],
                    'accuracy' => $scoreResponse['accuracy'],
                    'max_combo' => $scoreResponse['max_combo'],
                    'mode' => $scoreResponse['mode'],
                    'mods' => $scoreResponse['mods'],
                    'passed' => $scoreResponse['passed'],
                    'perfect' => $scoreResponse['perfect'],
                    'pp' => $scoreResponse['pp'],
                    'rank' => $scoreResponse['rank'],
                    'score' => $scoreResponse['score'],
                    'statistics' => $scoreResponse['statistics'],
                    'beatmap' => $scoreResponse['beatmap'],
                    'beatmapset' => $scoreResponse['beatmapset'],
                    'beatmapset_id' => $scoreResponse['beatmap']['beatmapset_id'],
                    'beatmap_id' => $scoreResponse['beatmap']['id'],
                    'created_at' => Carbon::create($scoreResponse['created_at'])
                ]
            );
        }

        return null;
    }

    public function getScoreStringInfo(Score $score): string
    {
        $user = $score->user;
        $messageBuilder = new MessageBuilder();
        $pp = round($score->pp, 1);
        $accuracy = round($score->accuracy * 100, 2);
        $mods = null;
        if (!empty($score->mods)) {
            $mods = '+' . implode('', $score->mods);
        }

        return $messageBuilder
            ->addText($score->rank)
            ->addLink($user->name, "https://osu.ppy.sh/users/$user->id")
            ->addLink("{$score->beatmapset['artist']} - {$score->beatmapset['title']} [{$score->beatmap['version']}]", $score->beatmap['url'])
            ->addText("{$pp}pp $accuracy% {$score->beatmap['difficulty_rating']}★ $mods")
            ->getText();
    }
}

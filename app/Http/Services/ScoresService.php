<?php

namespace App\Http\Services;

use \App\Models\Score;
use App\Models\User;
use Carbon\Carbon;

class ScoresService
{
    public function firstOrCreateFromResponse(array $scoreResponse): ?Score
    {
        if (empty($scoreResponse)) {
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
                    'mods' => json_encode($scoreResponse['mods']),
                    'passed' => $scoreResponse['passed'],
                    'perfect' => $scoreResponse['perfect'],
                    'pp' => $scoreResponse['pp'],
                    'rank' => $scoreResponse['rank'],
                    'score' => $scoreResponse['score'],
                    'statistics' => json_encode($scoreResponse['statistics']),
                    'beatmap' => json_encode($scoreResponse['beatmap']),
                    'beatmapset' => json_encode($scoreResponse['beatmapset']),
                    'created_at' => Carbon::create($scoreResponse['created_at'])
                ]
            );
        }

        return null;
    }
}

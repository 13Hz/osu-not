<?php

namespace App\Kernel\Filters;

use App\Models\Score;

class TopScoreFilter implements FilterInterface
{
    public function apply(mixed $data): bool
    {
        $max = Score::query()
            ->whereNot('id', $data['id'])
            ->where('user_id', $data['user_id'])
            ->where('beatmapset_id', $data['beatmapset_id'])
            ->where('beatmap_id', $data['beatmap_id'])
            ->max('pp');

        return $data['pp'] > $max;
    }
}

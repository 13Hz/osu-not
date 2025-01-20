<?php

namespace App\Kernel\DTO;

class GetUserBeatmapScoreDTO
{
    public int $beatmapId;
    public int $userId;

    public function __construct(int $beatmapId, int $userId)
    {
        $this->beatmapId = $beatmapId;
        $this->userId = $userId;
    }
}

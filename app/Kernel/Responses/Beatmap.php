<?php

namespace App\Kernel\Responses;

class Beatmap extends ApiEntity
{
    public float $difficulty_rating;
    public int $id;
    public string $mode;
    public string $status;
    public int $total_length;
    public string $version;
    public int $accuracy;
    public int $ar;
    public int $bpm;
    public int $cs;
    public int $drain;
    public int $ranked;
}

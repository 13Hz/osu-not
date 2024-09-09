<?php

namespace App\Kernel\Responses;

class Score extends ApiEntity
{
    public float $accuracy;
    public ?int $best_id;
    public string $created_at;
    public int $id;
    public int $max_combo;
    public string $mode;
    public array $mods;
    public bool $passed;
    public bool $perfect;
    public ?float $pp;
    public string $rank;
    public int $score;
    public User $user;
    public Beatmap $beatmap;
    public BeatmapSet $beatmapset;

    public function getHash(): string
    {
        return hash('sha256', $this->created_at . $this->max_combo . $this->accuracy);
    }
}

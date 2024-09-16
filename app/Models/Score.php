<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Score extends Model
{
    use HasFactory;
    public $incrementing = false;
    public $timestamps = false;

    protected $casts = [
        'mods' => 'array',
        'statistics' => 'json',
        'beatmap' => 'json',
        'beatmapset' => 'json',
    ];

    protected $fillable = [
        'accuracy',
        'user_id',
        'created_at',
        'hash',
        'id',
        'max_combo',
        'mode',
        'mods',
        'passed',
        'perfect',
        'pp',
        'rank',
        'score',
        'statistics',
        'user',
        'beatmap',
        'beatmapset',
    ];

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'last_score_id', 'id');
    }
}

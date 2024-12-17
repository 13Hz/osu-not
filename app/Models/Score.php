<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'beatmapset_id',
        'beatmap_id'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function preview(): BelongsTo
    {
        return $this->belongsTo(File::class, 'preview_id', 'id');
    }
}

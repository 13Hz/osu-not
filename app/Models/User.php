<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $fillable = [
        'id',
        'name',
        'last_score_id',
        'avatar_url',
        'last_score_updated_at',
        'next_update_in'
    ];
    protected $appends = [];
    protected $casts = [
        'last_score_updated_at' => 'datetime',
        'next_update_in' => 'datetime'
    ];

    public function updateNextCheckDate(): void
    {
        $now = Carbon::now();
        $lastScoreUpdatedAt = Carbon::parse($this->last_score_updated_at);
        $inactiveDays = $now->diffInDays($lastScoreUpdatedAt, true);

        $minutesToAdd = match (true) {
            $inactiveDays <= 2 => 0,
            $inactiveDays <= 7 => 5,
            $inactiveDays <= 30 => 10,
            default => 30,
        };

        $this->update(['next_update_in' => $now->addMinutes($minutesToAdd)]);
    }

    public function chats(): BelongsToMany
    {
        return $this->belongsToMany(Chat::class);
    }

    public function lastScore(): BelongsTo
    {
        return $this->BelongsTo(Score::class);
    }
}

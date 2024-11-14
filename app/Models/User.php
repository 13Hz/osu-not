<?php

namespace App\Models;

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
        'last_score_check_at'
    ];
    protected $appends = [];
    protected $casts = [
        'last_score_check_at' => 'datetime'
    ];

    public function chats(): BelongsToMany
    {
        return $this->belongsToMany(Chat::class);
    }

    public function lastScore(): BelongsTo
    {
        return $this->BelongsTo(Score::class);
    }
}

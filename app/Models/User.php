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
        'is_vip',
        'last_score_id',
        'avatar_url',
        'last_score_updated_at',
        'next_update_in'
    ];
    protected $appends = [];
    protected $casts = [
        'is_vip' => 'bool',
        'last_score_updated_at' => 'datetime',
        'next_update_in' => 'datetime'
    ];

    public function chats(): BelongsToMany
    {
        return $this->belongsToMany(Chat::class)->withPivot('id');
    }

    public function lastScore(): BelongsTo
    {
        return $this->BelongsTo(Score::class);
    }
}

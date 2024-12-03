<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Filter extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'title',
        'model'
    ];

    public function chatUsers(): BelongsToMany
    {
        return $this->belongsToMany(ChatUser::class, 'chat_user_filter', 'filter_id', 'chat_user_id')->withPivot('active');
    }

    public function apply(mixed $data): bool
    {
        return app($this->model)->apply($data);
    }
}

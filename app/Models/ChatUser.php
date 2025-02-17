<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ChatUser extends Pivot
{
    protected $table = 'chat_user';

    public function filters(): BelongsToMany
    {
        return $this->belongsToMany(Filter::class, 'chat_user_filter', 'chat_user_id', 'filter_id')->withPivot('active');
    }
}

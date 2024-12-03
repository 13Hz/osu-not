<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ChatUserFilter extends Pivot
{
    protected $table = 'chat_user_filter';
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Chat extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $fillable = [
        'id',
        'name'
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('id');
    }

    protected static function booted(): void
    {
        static::deleting(function ($chat) {
            $chat->users()->detach();

            $usersToDelete = User::query()->doesntHave('chats')->get();

            foreach ($usersToDelete as $user) {
                $user->delete();
            }
        });
    }
}

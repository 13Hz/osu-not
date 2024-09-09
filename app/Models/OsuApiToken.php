<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OsuApiToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'access_token',
        'refresh_token',
        'expires_in'
    ];

    public function isActive(): bool
    {
        $expiresAt = (new Carbon($this->created_at))->addSeconds($this->expires_in);

        return $expiresAt < Carbon::now();
    }
}

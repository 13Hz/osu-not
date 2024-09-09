<?php

namespace App\Http\Services;

use App\Kernel\Api\OsuApi;
use App\Kernel\DTO\RefreshTokenDTO;
use App\Models\OsuApiToken;
use Illuminate\Support\Facades\Log;

class OsuTokenService
{
    public function __construct(
        protected OsuApi $api
    ) {}

    public function getToken(): ?string
    {
        $lastToken = OsuApiToken::latest('id')->first();
        if (!$lastToken) {
            Log::warning('Нет токенов авторизации');
            return null;
        }

        if (!$lastToken->isActive()) {
            return $lastToken->access_token;
        } else {
            $refreshedToken = $this->refreshToken($lastToken);
            if ($refreshedToken) {
                return $refreshedToken->access_token;
            }
        }

        Log::warning('Не удалось обновить токен');
        return null;
    }

    public function refreshToken(OsuApiToken $token): ?OsuApiToken
    {
        $result = $this->api->refreshToken(new RefreshTokenDTO(
            client_id: config('api.client_id'),
            client_secret: config('api.client_secret'),
            refresh_token: $token->refresh_token
        ));

        if ($result) {
            return OsuApiToken::create([
                'access_token' => $result->access_token,
                'refresh_token' => $result->refresh_token,
                'expires_in' => $result->expires_in,
            ]);
        }

        return null;
    }
}

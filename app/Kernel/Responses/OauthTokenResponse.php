<?php

namespace App\Kernel\Responses;

class OauthTokenResponse extends  ApiResponse
{
    public string $tokenType;
    public int $expiresIn;
    public string $accessToken;
    public string $refreshToken;
}

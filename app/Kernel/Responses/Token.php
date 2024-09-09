<?php

namespace App\Kernel\Responses;

class Token extends ApiEntity
{
    public string $token_type;
    public int $expires_in;
    public string $access_token;
    public string $refresh_token;
}

<?php

namespace App\Kernel\Responses;

class User extends ApiEntity
{
    public string $avatar_url;
    public string $country_code;
    public int $id;
    public string $username;
}

<?php

namespace App\Kernel\DTO;

class RefreshTokenDTO
{
    public string $client_id;
    public string $client_secret;
    public string $grant_type;
    public string $refresh_token;
    public ?string $scope;

    /**
     * @param string $client_id
     * @param string $client_secret
     * @param string $grant_type
     * @param string $refresh_token
     * @param string|null $scope
     */
    public function __construct(string $client_id, string $client_secret, string $refresh_token, string $grant_type = 'refresh_token', ?string $scope = null)
    {
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->grant_type = $grant_type;
        $this->refresh_token = $refresh_token;
        $this->scope = $scope;
    }
}

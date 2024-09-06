<?php

namespace App\Kernel\DTO;

class OauthTokenDTO
{
    public string $client_id;
    public string $client_secret;
    public string $code;
    public string $grant_type;
    public ?string $redirect_uri;

    /**
     * @param string $client_id
     * @param string $client_secret
     * @param string $code
     * @param string $grant_type
     * @param string|null $redirect_uri
     */
    public function __construct(string $client_id, string $client_secret, string $code, string $grant_type = 'authorization_code', ?string $redirect_uri = null)
    {
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->code = $code;
        $this->grant_type = $grant_type;
        $this->redirect_uri = $redirect_uri;
    }
}

<?php

namespace App\Kernel\Api;

use App\Kernel\DTO\OauthTokenDTO;
use App\Kernel\Responses\OauthTokenResponse;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;

class OsuApi
{
    private const BASE_URL = 'https://osu.ppy.sh';
    private const API_PATH = '/api/v2';
    private ?Client $httpClient = null;

    private function getClient(): Client
    {
        if (!$this->httpClient) {
            $this->httpClient = new Client();
        }

        return $this->httpClient;
    }

    private function post(string $path, mixed $data, bool $includeApiPath = true): ?ResponseInterface
    {
        try {
            $url = $includeApiPath ? self::BASE_URL . self::API_PATH . $path : self::BASE_URL . $path;
            $response = $this->getClient()->post($url, [
                'form_params' => $data
            ]);
            if ($response->getStatusCode() == 200) {
                return $response;
            }
            Log::warning("[$path] not success status");
        } catch (GuzzleException $e) {
            Log::warning("[$path] error: " . $e->getMessage());
        }

        return null;
    }

    public function getToken(OauthTokenDTO $oauthTokenDTO): ?OauthTokenResponse
    {
        $response = $this->post('/oauth/token', $oauthTokenDTO, false);
        if ($response) {
            return OauthTokenResponse::fromJson($response->getBody()->getContents());
        }

        return null;
    }
}

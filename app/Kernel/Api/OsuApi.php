<?php

namespace App\Kernel\Api;

use App\Kernel\DTO\GetUserDTO;
use App\Kernel\DTO\GetUserScoresDTO;
use App\Kernel\DTO\OauthTokenDTO;
use App\Kernel\DTO\RefreshTokenDTO;
use App\Kernel\Responses\GetUserResponse;
use App\Kernel\Responses\Score;
use App\Kernel\Responses\Token;
use App\Kernel\Responses\User;
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

    private function post(string $path, mixed $data, array $headers = [], bool $includeApiPath = true): ?ResponseInterface
    {
        try {
            $url = $includeApiPath ? self::BASE_URL . self::API_PATH . $path : self::BASE_URL . $path;
            $response = $this->getClient()->post($url, [
                'form_params' => $data,
                'headers' => $headers,
            ]);
            if ($response->getStatusCode() == 200) {
                return $response;
            }
            Log::warning("[$path] неуспешный статус");
        } catch (GuzzleException $e) {
            Log::warning("[$path] ошибка: " . $e->getMessage());
        }

        return null;
    }

    private function get(string $path, mixed $data = null, array $headers = [], bool $includeApiPath = true): ?ResponseInterface
    {
        try {
            $url = $includeApiPath ? self::BASE_URL . self::API_PATH . $path : self::BASE_URL . $path;
            $response = $this->getClient()->get($url, [
                'query' => (array) $data,
                'headers' => $headers,
            ]);
            if ($response->getStatusCode() == 200) {
                return $response;
            }
            Log::warning("[$path] неуспешный статус");
        } catch (GuzzleException $e) {
            Log::warning("[$path] ошибка: " . $e->getMessage());
        }

        return null;
    }

    public function getToken(OauthTokenDTO $oauthTokenDTO): ?Token
    {
        $response = $this->post('/oauth/token', $oauthTokenDTO, includeApiPath: false);
        if ($response) {
            return new Token($response->getBody()->getContents());
        }

        return null;
    }

    public function refreshToken(RefreshTokenDTO $refreshTokenDTO): ?Token
    {
        $response = $this->post('/oauth/token', $refreshTokenDTO, includeApiPath: false);
        if ($response) {
            return new Token($response->getBody()->getContents());
        }

        return null;
    }

    public function getUser(GetUserDTO $getUserDTO, string $token): ?User
    {
        $response = $this->get("/users/$getUserDTO->user/$getUserDTO->mode", headers: [
            'Authorization' => 'Bearer ' . $token,
        ]);
        if ($response) {
            return new User($response->getBody()->getContents());
        }

        return null;
    }

    /**
     * @param GetUserScoresDTO $getUserScoresDTO
     * @param string $token
     * @return Score[]|null
     */
    public function getUserScores(GetUserScoresDTO $getUserScoresDTO, string $token): ?array
    {
        $response = $this->get(
            path: "/users/$getUserScoresDTO->user/scores/$getUserScoresDTO->type",
            data: $getUserScoresDTO,
            headers: ['Authorization' => 'Bearer ' . $token]
        );
        if ($response) {
            $array = json_decode($response->getBody()->getContents(), true);
            $scores = [];
            foreach ($array as $score) {
                $scores[] = new Score($score);
            }
            return $scores;
        }

        return null;
    }
}

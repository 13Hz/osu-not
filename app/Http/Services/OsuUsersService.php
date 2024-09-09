<?php

namespace App\Http\Services;

use App\Kernel\Api\OsuApi;
use App\Kernel\DTO\GetUserDTO;
use App\Kernel\DTO\GetUserScoresDTO;
use App\Kernel\Responses\Score;
use App\Kernel\Responses\User;

class OsuUsersService
{
    public function __construct(
        protected OsuApi $api,
        protected OsuTokenService $tokenService,
    ) {}

    public function getUser(GetUserDTO $getUserDTO): ?User
    {
        $token = $this->tokenService->getToken();
        if ($token) {
            return $this->api->getUser($getUserDTO, $token);
        }

        return null;
    }

    /**
     * @param GetUserScoresDTO $getUserScoresDTO
     * @return Score[]|null
     */
    public function getUserScores(GetUserScoresDTO $getUserScoresDTO): ?array
    {
        $token = $this->tokenService->getToken();
        if ($token) {
            return $this->api->getUserScores($getUserScoresDTO, $token);
        }

        return null;
    }

    public function trackUser(string $name): void
    {
        $osuUser = $this->getUser(new GetUserDTO($name));
        if ($osuUser && !\App\Models\User::find($osuUser->id)) {
            $lastScore = $this->getUserScores(new GetUserScoresDTO(
                $osuUser->id,
                'recent',
                limit: 1
            ))[0] ?? null;
            if ($lastScore) {
                \App\Models\User::create([
                    'id' => $osuUser->id,
                    'name' => $osuUser->username,
                    'last_score_hash' => $lastScore->getHash(),
                ]);
            }
        }
    }
}

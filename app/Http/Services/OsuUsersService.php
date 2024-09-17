<?php

namespace App\Http\Services;

use App\Kernel\Api\OsuApi;
use App\Kernel\DTO\GetUserDTO;
use App\Kernel\DTO\GetUserScoresDTO;
use App\Kernel\Responses\User;

class OsuUsersService
{
    public function __construct(
        protected OsuApi $api,
        protected OsuTokenService $tokenService,
    ) {
    }

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
     * @return array|null
     */
    public function getUserScores(GetUserScoresDTO $getUserScoresDTO): ?array
    {
        $token = $this->tokenService->getToken();
        if ($token) {
            return $this->api->getUserScores($getUserScoresDTO, $token);
        }

        return null;
    }

    public function updateLastScore(\App\Models\User $user): bool
    {
        $lastScore = $this->getUserScores(new GetUserScoresDTO(
            $user->id,
            'recent',
            limit: 1
        ))[0] ?? null;
        if ($lastScore) {
            $scoresService = new ScoresService();
            $score = $scoresService->firstOrCreateFromResponse($lastScore);

            if ($score) {
                return $user->lastScore()->associate($score)->save();
            }
        }

        return false;
    }

    public function getOrCreateUser(string $name): ?\App\Models\User
    {
        $osuUser = $this->getUser(new GetUserDTO("@$name"));
        if ($osuUser) {
            $user = \App\Models\User::firstOrCreate(
                ['id' => $osuUser->id],
                [
                    'name' => $name,
                    'avatar_url' => $osuUser->avatar_url,
                ]
            );
            if ($user) {
                $this->updateLastScore($user);
            }

            return $user;
        }

        return null;
    }
}

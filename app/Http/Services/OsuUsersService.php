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

    public function updateLastScore(\App\Models\User $user): bool
    {
        $lastScores = $this->getUserScores(new GetUserScoresDTO(
            $user->id,
            'recent',
            limit: 1
        ));
        if (is_array($lastScores)) {
            $hash = !empty($lastScores[0]) ? $lastScores[0]->getHash() : null;

            return $user->update([
                'last_score_hash' => $hash
            ]);
        }

        return false;
    }

    public function getOrCreateUser(string $name): ?\App\Models\User
    {
        $osuUser = $this->getUser(new GetUserDTO("@$name"));
        if ($osuUser) {
            $user = \App\Models\User::firstOrCreate(
                ['id' => $osuUser->id],
                ['name' => $name]
            );
            if ($user) {
                $this->updateLastScore($user);
            }

            return $user;
        }

        return null;
    }
}

<?php

namespace App\Kernel\DTO;

class GetUserScoresDTO
{
    public int $user;
    public string $type;
    public int $legacy_only;
    public string $include_fails;
    public ?string $mode;
    public ?int $limit;
    public ?string $offset;

    /**
     * @param int $user Id of the user.
     * @param string $type Score type. Must be one of these: best, firsts, recent.
     * @param int $legacy_only Whether or not to exclude lazer scores. Defaults to 0.
     * @param string $include_fails Only for recent scores, include scores of failed plays. Set to 1 to include them. Defaults to 0.
     * @param string|null $mode Ruleset of the scores to be returned. Defaults to the specified user's mode.
     * @param int|null $limit Maximum number of results.
     * @param string|null $offset Result offset for pagination.
     */
    public function __construct(int $user, string $type, int $legacy_only = 0, string $include_fails = '0', ?string $mode = null, ?int $limit = null, ?string $offset = null)
    {
        $this->user = $user;
        $this->type = $type;
        $this->legacy_only = $legacy_only;
        $this->include_fails = $include_fails;
        $this->mode = $mode;
        $this->limit = $limit;
        $this->offset = $offset;
    }
}

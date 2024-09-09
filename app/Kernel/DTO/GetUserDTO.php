<?php

namespace App\Kernel\DTO;

class GetUserDTO
{
    public string $user;
    public ?string $mode;

    /**
     * @param string $user
     * @param string|null $mode
     */
    public function __construct(string $user, ?string $mode = null)
    {
        $this->user = $user;
        $this->mode = $mode;
    }
}

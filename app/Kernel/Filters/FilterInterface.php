<?php

namespace App\Kernel\Filters;

interface FilterInterface
{
    public function apply(mixed $data): bool;
}

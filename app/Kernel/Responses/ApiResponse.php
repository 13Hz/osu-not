<?php

namespace App\Kernel\Responses;

class ApiResponse
{
    public static function fromJson(string $json): static
    {
        $array = json_decode($json, true);
        $static = new static();
        foreach ($array as $key => $value) {
            $static->{$key} = $value;
        }

        return $static;
    }
}

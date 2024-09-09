<?php

namespace App\Kernel\Responses;

abstract class ApiEntity
{
    public function __construct(string|array $json)
    {
        $array = is_array($json) ? $json : json_decode($json, true);
        $reflection = new \ReflectionClass($this);
        foreach ($array as $key => $value) {
            if (!$reflection->hasProperty($key)) {
                continue;
            }
            $propertyType = $reflection->getProperty($key)->getType();
            if ($propertyType && !$propertyType->isBuiltin() && is_subclass_of($propertyType->getName(), ApiEntity::class)) {
                $this->{$key} = new ($propertyType->getName())($value);
            } else {
                $this->{$key} = $value;
            }
        }
    }
}

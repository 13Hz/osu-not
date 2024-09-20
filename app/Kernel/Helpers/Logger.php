<?php

namespace App\Kernel\Helpers;

use DataDog\DogStatsd;

class Logger
{
    private static DogStatsd $dogStatsd;

    private static function getDogStatsdInstance(): DogStatsd
    {
        if (self::$dogStatsd == null) {
            self::$dogStatsd = new DogStatsd([
                'host' => config('api.datadog.host'),
                'port' => config('api.datadog.port')
            ]);
        }

        return self::$dogStatsd;
    }

    public static function increment(string $metric, array|string $tags = null): void
    {
        self::getDogStatsdInstance()->increment($metric, tags: $tags, value: 10);
    }
}

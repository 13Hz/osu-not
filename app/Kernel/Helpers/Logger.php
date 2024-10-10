<?php

namespace App\Kernel\Helpers;

use DataDog\DogStatsd;

class Logger
{
    private static ?DogStatsd $dogStatsd = null;

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

    public static function set(string $metric, float $value, array|string $tags = null): void
    {
        self::getDogStatsdInstance()->set($metric, value: $value, tags: $tags);
    }

    public static function gauge(string $metric, float $value, array|string $tags = null): void
    {
        self::getDogStatsdInstance()->gauge($metric, value: $value, tags: $tags);
    }
}

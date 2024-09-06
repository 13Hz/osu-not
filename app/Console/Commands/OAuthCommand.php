<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class OAuthCommand extends Command
{
    protected $signature = 'api:oauth';

    protected $description = 'Команда для генерации ссылки на получения токена через oauth';

    public function handle(): int
    {
        $this->info('https://osu.ppy.sh/oauth/authorize?' . http_build_query([
            'client_id' => config('api.client_id'),
            'response_type' => 'code',
            'scope' => 'public',
            'redirect_uri' => config('api.callback_uri'),
        ]));

        return self::SUCCESS;
    }
}

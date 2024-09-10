<?php

namespace App\Console\Commands;

use App\Http\Services\OsuTokenService;
use Illuminate\Console\Command;

class OAuthCommand extends Command
{
    protected $signature = 'api:oauth';

    protected $description = 'Команда для генерации ссылки на получения токена через oauth';

    public function handle(): int
    {
        $this->info(OsuTokenService::getOauthLink());

        return self::SUCCESS;
    }
}

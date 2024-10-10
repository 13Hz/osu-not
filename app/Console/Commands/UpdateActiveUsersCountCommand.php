<?php

namespace App\Console\Commands;

use App\Kernel\Helpers\Logger;
use App\Models\User;
use Illuminate\Console\Command;

class UpdateActiveUsersCountCommand extends Command
{
    protected $signature = 'app:update-active-users-count';

    protected $description = 'Команда для запуска обновления количества активных игроков в системе';

    public function handle()
    {
        Logger::gauge('osu-not.api.users', User::all()->count());
    }
}

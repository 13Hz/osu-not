<?php

namespace App\Commands;

use App\Http\Services\ChatsService;
use App\Http\Services\OsuUsersService;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Commands\Command;

class AddCommand extends Command
{
    protected string $name = 'add';
    protected string $description = 'Добавление игрока для уведомлений в чат';
    protected string $pattern = '{username: .+}';

    public function __construct(
        protected OsuUsersService $osuUsersService,
        protected ChatsService $chatsService,
    ) {
    }

    public function handle()
    {
        if (config('api.players_limit_enabled')) {
            $usersCount = User::count();
            if ($usersCount >= config('api.players_limit')) {
                $this->replyWithMessage([
                    'text' => 'Бот уже обрабатывает максимальное количество пользователей, пожалуйста, свяжитесь с администратором'
                ]);
                return;
            }
        }

        $name = $this->argument('username');
        $chatId = $this->getUpdate()->getChat()->get('id') ?? null;
        if ($name && $chatId) {
            $chat = $this->chatsService->getOrCreateChat($chatId);
            $user = $this->osuUsersService->getOrCreateUser($name);
            if ($chat && $user) {
                if (!$chat->users->contains($user)) {
                    $chat->users()->attach($user);
                    $this->replyWithMessage([
                        'text' => "Пользователь $name добавлен"
                    ]);
                    Log::info('Пользователь {userName} добавлен в чат', ['userName' => $user->name]);
                } else {
                    $this->replyWithMessage([
                        'text' => "Пользователь $name уже присутствует в этом чате"
                    ]);
                }
            } else {
                $this->replyWithMessage([
                    'text' => 'Ошибка добавления пользователя'
                ]);
            }
        } else {
            $this->replyWithMessage([
                'text' => 'Введите имя игрока `/add peppy`',
                'parse_mode' => 'markdown'
            ]);
        }
    }
}

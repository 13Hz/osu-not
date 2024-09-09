<?php

namespace App\Commands;

use App\Http\Services\ChatsService;
use App\Http\Services\OsuUsersService;
use Telegram\Bot\Commands\Command;

class AddCommand extends Command
{
    protected string $name = 'add';
    protected string $description = 'Добавление игрока для уведомлений в чат';
    protected string $pattern = '{username: .+}';

    public function __construct(
        protected OsuUsersService $osuUsersService,
        protected ChatsService $chatsService,
    ) {}

    public function handle()
    {
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

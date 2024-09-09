<?php

namespace App\Commands;

use App\Http\Services\ChatsService;
use Telegram\Bot\Commands\Command;

class StartCommand extends Command
{
    protected string $name = 'start';
    protected string $description = 'Инициализация бота в чате';

    public function __construct(
        protected ChatsService $chatsService
    ) {}

    public function handle()
    {
        $chatId = $this->getUpdate()->getChat()->get('id') ?? null;
        if ($chatId) {
            if ($this->chatsService->getOrCreateChat($chatId)) {
                return $this->replyWithMessage([
                    'text' => 'Чат успешно добавлен!',
                ]);
            }
        }

        return $this->replyWithMessage([
            'text' => 'Произошла ошибка при добавлении чата',
        ]);
    }
}

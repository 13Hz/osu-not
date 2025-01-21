<?php

namespace App\Telegram\Commands;

use App\Http\Services\ChatsService;
use Telegram\Bot\Commands\Command;

class StartCommand extends Command
{
    protected string $name = 'start';
    protected string $description = 'Инициализация бота в чате';

    public function __construct(
        protected ChatsService $chatsService
    ) {
    }

    public function handle()
    {
        if ($this->chatsService->getOrCreateChat($this->getUpdate()->getChat())) {
            return $this->replyWithMessage([
                'text' => 'Чат успешно добавлен!',
            ]);
        }

        return $this->replyWithMessage([
            'text' => 'Произошла ошибка при добавлении чата',
        ]);
    }
}

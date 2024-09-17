<?php

namespace App\Commands;

use App\Kernel\Builders\KeyboardBuilder;
use Telegram\Bot\Commands\Command;

class PlayersCommand extends Command
{
    protected string $name = 'players';
    protected string $description = 'Получить список добавленных игроков';

    public function __construct(
        protected KeyboardBuilder $keyboardBuilder
    ) {
    }

    public function handle()
    {
        $keyboard = $this->keyboardBuilder->buildPlayersMenu($this->getUpdate()->getChat()->get('id') ?? -1);
        if ($keyboard) {
            $this->replyWithMessage([
                'text' => 'Список добавленных пользователей',
                'reply_markup' => $keyboard
            ]);
        } else {
            $this->replyWithMessage([
                'text' => 'Список пользователей пуст'
            ]);
        }
    }
}

<?php

namespace App\Kernel\Builders;

use App\Models\Chat;
use App\Models\User;
use Telegram\Bot\Keyboard\Keyboard;

class KeyboardBuilder
{
    private Keyboard $keyboard;

    public function __construct()
    {
        $this->keyboard = Keyboard::make()->inline();
    }

    private function addRow(array $buttons): self
    {
        $this->keyboard->row($buttons);

        return $this;
    }

    private function addBackButton(string $to): self
    {
        return $this->addRow([
            Keyboard::inlineButton([
                'text' => 'Назад',
                'callback_data' => json_encode([
                    'action' => 'back',
                    'to' => $to,
                ]),
            ])
        ]);
    }

    private function addPlayerButton(User $user): self
    {
        return $this->addRow([
            Keyboard::inlineButton([
                'text' => $user->name,
                'callback_data' => json_encode([
                    'action' => 'pick_player',
                    'id' => $user->id,
                ]),
            ])
        ]);
    }

    private function addDeletePlayerButton(int $playerId): self
    {
        return $this->addRow([
            Keyboard::inlineButton([
                'text' => 'Удалить',
                'callback_data' => json_encode([
                    'action' => 'delete_player',
                    'id' => $playerId,
                ]),
            ])
        ]);
    }

    public function buildPlayersMenu(int $chatId): ?Keyboard
    {
        $chat = Chat::find($chatId);
        if ($chat && $chat->users->count() > 0) {
            foreach ($chat->users as $user) {
                $this->addPlayerButton($user);
            }

            return $this->keyboard;
        }

        return null;
    }

    public function buildPlayerActionsMenu(int $playerId): Keyboard
    {
        $this->addDeletePlayerButton($playerId)
            ->addBackButton('list_players');

        return $this->keyboard;
    }
}

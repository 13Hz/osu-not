<?php

namespace App\Kernel\Builders;

use App\Models\Chat;
use App\Models\User;
use Telegram\Bot\Keyboard\Keyboard;

class KeyboardBuilder
{
    protected Keyboard $keyboard;

    public function __construct()
    {
        $this->keyboard = Keyboard::make()->inline();
    }

    protected function addRow(array $buttons): self
    {
        $this->keyboard->row($buttons);

        return $this;
    }

    protected function addBackButton(string $to, array $data = []): self
    {
        return $this->addRow([
            Keyboard::inlineButton([
                'text' => 'Назад',
                'callback_data' => json_encode(array_merge([
                    'action' => 'back',
                    'to' => $to,
                ], $data)),
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

    private function addPlayerFilterButton(int $playerId): self
    {
        return $this->addRow([
            Keyboard::inlineButton([
                'text' => 'Фильтр',
                'callback_data' => json_encode([
                    'action' => 'player_filter',
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
        $this->addPlayerFilterButton($playerId)
            ->addDeletePlayerButton($playerId)
            ->addBackButton('list_players');

        return $this->keyboard;
    }
}

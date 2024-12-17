<?php

namespace App\Kernel\Builders;

use App\Models\ChatUser;
use App\Models\Filter;
use Telegram\Bot\Keyboard\Keyboard;

class FiltersKeyboardBuilder extends KeyboardBuilder
{
    private ChatUser $chatUser;

    public function __construct(ChatUser $chatUser)
    {
        $this->chatUser = $chatUser;
        parent::__construct();
    }

    private function buildFilterTitle(string $title, bool $active = false): string
    {
        return ($active ? '[ВКЛ]' : '[ВЫКЛ]') . ' ' . $title;
    }

    public function buildPlayerFilterMenu(): Keyboard
    {
        $filters = Filter::all();
        foreach ($filters as $filter) {
            $chatUserFilter = $this->chatUser->filters()->find($filter->id);
            $this->addRow([
                Keyboard::inlineButton([
                    'text' => $this->buildFilterTitle($filter->title, $chatUserFilter?->pivot->active ?? false),
                    'callback_data' => json_encode([
                        'action' => 'toggle_filter',
                        'filter_id' => $filter->id,
                        'chat_user_id' => $this->chatUser->id
                    ]),
                ])
            ]);
        }
        $this->addBackButton('pick_player', ['id' => $this->chatUser->user_id]);

        return $this->keyboard;
    }
}

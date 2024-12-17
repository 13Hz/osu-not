<?php

namespace App\Factories;

use App\Callbacks\DeletePlayerCallback;
use App\Callbacks\PickPlayerCallback;
use App\Callbacks\PlayerFilterCallback;
use App\Callbacks\PlayersListCallback;
use App\Callbacks\ToggleFilterCallback;
use App\Interfaces\CallbackResponseRunnable;
use Telegram\Bot\Objects\CallbackQuery;

class TelegramCallbackQueryResponseFactory
{
    public static function exec(CallbackQuery $callbackQuery): ?CallbackResponseRunnable
    {
        if ($callbackQuery->data) {
            $data = json_decode($callbackQuery->data, true);
            if ($data && !empty($data['action'])) {
                switch ($data['action']) {
                    case 'back':
                        switch ($data['to']) {
                            case 'list_players':
                                return new PlayersListCallback($callbackQuery);
                            case 'pick_player':
                                return new PickPlayerCallback($callbackQuery);
                        }
                        break;
                    case 'pick_player':
                        return new PickPlayerCallback($callbackQuery);
                    case 'delete_player':
                        return new DeletePlayerCallback($callbackQuery);
                    case 'player_filter':
                        return new PlayerFilterCallback($callbackQuery);
                    case 'toggle_filter':
                        return new ToggleFilterCallback($callbackQuery);
                }
            }
        }

        return null;
    }
}

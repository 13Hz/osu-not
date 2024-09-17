<?php

namespace App\Factories;

use App\Callbacks\DeletePlayerCallback;
use App\Callbacks\PickPlayerCallback;
use App\Callbacks\PlayersListCallback;
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
                        if ($data['to'] == 'list_players') {
                            return new PlayersListCallback($callbackQuery);
                        }
                        break;
                    case 'pick_player':
                        return new PickPlayerCallback($callbackQuery);
                    case 'delete_player':
                        return new DeletePlayerCallback($callbackQuery);
                }
            }
        }

        return null;
    }
}

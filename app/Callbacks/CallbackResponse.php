<?php

namespace App\Callbacks;

use App\Interfaces\CallbackResponseRunnable;
use Telegram\Bot\Objects\CallbackQuery;
use Telegram\Bot\Objects\Message;

abstract class CallbackResponse implements CallbackResponseRunnable
{
    protected CallbackQuery $callbackQuery;
    protected Message $message;
    protected int $chatId;
    protected int $messageId;
    protected array $data;


    public function __construct(CallbackQuery $callbackQuery)
    {
        $this->callbackQuery = $callbackQuery;
        $this->message = $callbackQuery->message;
        $this->chatId = $this->message->getChat()->id;
        $this->messageId = (int)$this->message['message_id'];
        $this->data = json_decode($callbackQuery->data, true);
    }
}

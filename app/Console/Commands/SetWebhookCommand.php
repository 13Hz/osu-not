<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Telegram\Bot\Laravel\Facades\Telegram;

class SetWebhookCommand extends Command
{
    protected $signature = 'webhook:set';

    protected $description = 'Команда установки вебхука';

    public function handle(): int
    {
        try {
            $webhookUrl = $this->ask('По какому адресу находится Webhook?', url('/webhook'));
            $result = Telegram::setWebhook([
                'url' => $webhookUrl,
                'secret_token' => config('telegram.webhook.token')
            ]);

            if ($result) {
                $this->info('Вебхук успешно установлен');
                return self::SUCCESS;
            } else {
                $this->error('Ошибка установки вебхука');
            }
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }

        return self::FAILURE;
    }
}

<?php

namespace App\Console\Commands;

use App\Jobs\CheckPlayerLastScoreJob;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\LazyCollection;

class CheckPlayersLastScoreCommand extends Command
{
    protected $signature = 'app:check-players-last-score';

    protected $description = 'Команда для создания очереди для проверки пользователей на наличие новых результатов';

    private const CHUNK_SIZE = 1;

    public function handle()
    {
        if (Cache::has('batch_running')) {
            return self::SUCCESS;
        }

        Cache::put('batch_running', true, 3600);

        $batch = Bus::batch([]);

        User::all()->lazy()->pluck('id')->chunk(self::CHUNK_SIZE)->each(function (LazyCollection $collection) use ($batch) {
            $batch->add(new CheckPlayerLastScoreJob($collection->toArray()));
        });

        $batch->catch(function (\Throwable $throwable) {
            Log::warning('Ошибка выполнения задачи ' . $throwable->getMessage());
        })->finally(function () {
            Cache::forget('batch_running');
        })->dispatch();

        return self::SUCCESS;
    }
}

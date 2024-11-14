<?php

namespace App\Console\Commands;

use App\Jobs\CheckPlayerLastScoreJob;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Batch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\LazyCollection;
use Throwable;

class CheckPlayersLastScoreCommand extends Command
{
    protected $signature = 'app:check-players-last-score';

    protected $description = 'Команда для создания очереди для проверки пользователей на наличие новых результатов';

    private const BATCH_NAME = 'check-players-last-score';
    private const CHUNK_SIZE = 5;
    private ?string $lastBatchId;

    public function __construct()
    {
        parent::__construct();

        $this->lastBatchId = DB::table('job_batches')
            ->where('name', '=', self::BATCH_NAME)
            ->latest()
            ->value('id');
    }

    public function handle()
    {
        if ($this->lastBatchId) {
            $lastBatch = Bus::findBatch($this->lastBatchId);
            if ($lastBatch && !$lastBatch->finished()) {
                $this->info('Обновление уже запущено, жду завершения');

                return self::SUCCESS;
            }
        }

        $batch = Bus::batch([])->name(self::BATCH_NAME);

        User::query()
            ->where('next_update_in', '<=', Carbon::now())->lazy()
            ->pluck('id')
            ->chunk(self::CHUNK_SIZE)
            ->each(function (LazyCollection $collection) use ($batch) {
                $batch->add(new CheckPlayerLastScoreJob($collection->toArray()));
            });

        $batch->catch(function (Batch $batch, Throwable $throwable) {
            Log::warning('Ошибка выполнения задачи ' . $throwable->getMessage());
        })->dispatch();

        return self::SUCCESS;
    }
}

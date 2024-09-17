<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeleteFinishedBatchesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-finished-batches';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Удаление завершенных групп задач';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $rowsCount = DB::table('job_batches')->whereNotNull('finished_at')->delete();
        Log::info("Удалено $rowsCount завершенных групп задач");
    }
}

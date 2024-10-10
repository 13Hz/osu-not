<?php

use Illuminate\Support\Facades\Schedule;
use Spatie\ShortSchedule\Facades\ShortSchedule;

ShortSchedule::command('app:check-players-last-score')->everySeconds(config('api.players_check_delay'))->withoutOverlapping();
Schedule::command('app:delete-finished-batches')->daily();
Schedule::command('app:update-active-users-count')->everyFiveMinutes()->withoutOverlapping();

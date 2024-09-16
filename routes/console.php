<?php

use Spatie\ShortSchedule\Facades\ShortSchedule;

ShortSchedule::command('app:check-players-last-score')->everySeconds(config('api.players_check_delay'))->withoutOverlapping();

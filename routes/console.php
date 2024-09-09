<?php

use Illuminate\Support\Facades\Schedule;
use \App\Schedules\CheckUsersScoresSchedule;

Schedule::call(new CheckUsersScoresSchedule)->everyTenSeconds();

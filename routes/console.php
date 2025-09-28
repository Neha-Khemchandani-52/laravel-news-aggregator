<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Added scheduled command here

Schedule::command('news:fetch')->everyFifteenMinutes();
Schedule::command('queue:work --stop-when-empty')->everyMinute();

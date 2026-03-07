<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Development: Reset database hourly
Schedule::call(function () {
    Artisan::call('down', ['--render' => 'maintenance']);
    Artisan::call('migrate:fresh', ['--seed' => true, '--force' => true]);
    Artisan::call('up');
})->hourly();

// Send queue notifications every 5 minutes
Schedule::command('queue:notify')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->runInBackground();

Artisan::command('inspire', function (): void {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

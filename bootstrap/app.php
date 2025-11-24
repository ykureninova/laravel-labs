<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->withSchedule(function (Schedule $schedule) {

        $schedule->command('app:expire-old-tasks')
            ->daily()
            ->appendOutputTo(storage_path('logs/scheduler.log'));

        $schedule->command('app:generate-report --file')
            ->weeklyOn(1, '09:00')
            ->appendOutputTo(storage_path('logs/scheduler.log'));

    })
    ->withBroadcasting(
        __DIR__.'/../routes/channels.php',
        [
            'middleware' => ['auth:sanctum'],
        ],
    )
    ->create();

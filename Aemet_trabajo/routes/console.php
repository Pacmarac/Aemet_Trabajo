<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Console\Scheduling\Schedule;
use App\Http\Controllers\StatsController;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

app()->singleton(Schedule::class, function ($app) {
    $schedule = new Schedule;

    // Programar la ejecución de recolectaStat cada 10 horas
    $schedule->call(function () {
        $controller = new StatsController();
        $controller->recolectaStat();
    })->everyTenHours();

    return $schedule;
});

app()->singleton(Schedule::class, function ($app) {
    $schedule = new Schedule;

    $schedule->call(function () {
        $controller = new StatsController();
        $controller->almacenaStat();
    })->weekly();

    return $schedule;
});

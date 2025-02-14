<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Console\Scheduling\Schedule;
use App\Http\Controllers\StatsController;

/**
 * Comando Artisan 'inspire'
 *
 * Este comando muestra una cita inspiradora en la consola.
 *
 * @command inspire
 * @purpose Display an inspiring quote
 */
Artisan::command('inspire', function () {
    // Muestra una cita inspiradora obtenida del helper Inspiring.
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * Registro de tareas programadas (Scheduler) como singleton en el contenedor de la aplicación.
 *
 * Se configuran dos tareas:
 * 1. Ejecutar el método recolectaStat del controlador StatsController cada 10 horas.
 *    - Se intenta ejecutar la tarea hasta 5 veces en caso de fallo, esperando 5 segundos entre cada intento.
 * 2. Ejecutar el método almacenaStat del controlador StatsController cada sábado.
 *    - Se intenta ejecutar la tarea hasta 3 veces en caso de fallo, con una espera de 5 segundos entre cada intento.
 *
 * @param \Illuminate\Contracts\Foundation\Application $app Instancia de la aplicación.
 * @return \Illuminate\Console\Scheduling\Schedule La instancia configurada del scheduler.
 */
app()->singleton(Schedule::class, function ($app) {
    // Se crea una nueva instancia de Schedule
    $schedule = new Schedule;

    /**
     * Tarea programada: Ejecutar recolectaStat cada 10 horas.
     *
     * Se utiliza el helper 'retry' para reintentar la ejecución hasta 5 veces en caso de fallo,
     * esperando 5000 milisegundos (5 segundos) entre cada intento.
     */
    $schedule->call(function () {
        retry(5, function () {
            // Se crea una instancia del StatsController y se llama al método recolectaStat.
            $controller = new StatsController();
            $controller->recolectaStat();
        }, 5000);
    })->everyTenHours();

    /**
     * Tarea programada: Ejecutar almacenaStat cada sábado.
     *
     * Se utiliza el helper 'retry' para reintentar la ejecución hasta 3 veces en caso de fallo,
     * esperando 5000 milisegundos (5 segundos) entre cada intento.
     */
    $schedule->call(function () {
        retry(3, function () {
            // Se crea una instancia del StatsController y se llama al método almacenaStat.
            $controller = new StatsController();
            $controller->almacenaStat();
        }, 5000);
    })->saturdays();

    // Se retorna la instancia del scheduler configurado
    return $schedule;
});



<?php

use App\Jobs\DetectarRiesgoJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| SMER — Tareas programadas
|--------------------------------------------------------------------------
|
| DetectarRiesgoJob se ejecuta cada día a las 07:00 AM.
| En localhost se puede disparar manualmente con:
|   C:\PHP\php.exe artisan app:detectar-riesgo
|
| Para activar el scheduler en producción, agregar al cron del servidor:
|   * * * * * php /ruta/al/proyecto/artisan schedule:run >> /dev/null 2>&1
|
*/

Schedule::job(DetectarRiesgoJob::class)
    ->dailyAt('07:00')
    ->name('smer-detectar-riesgo')
    ->withoutOverlapping();

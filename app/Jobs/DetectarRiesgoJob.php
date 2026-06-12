<?php

namespace App\Jobs;

use App\Services\AlertaService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * DetectarRiesgoJob
 *
 * Job programado que escanea diariamente todos los estudiantes
 * en busca de deterioro progresivo entre entrevistas sucesivas.
 *
 * Se registra en el scheduler (Console/Kernel.php).
 * En localhost se ejecuta manualmente con:
 *   php artisan app:detectar-riesgo
 */
class DetectarRiesgoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 120;

    public function handle(AlertaService $alertaService): void
    {
        Log::info('[SMER] DetectarRiesgoJob: iniciando escaneo de estudiantes...');

        $total = $alertaService->escanearTodosLosEstudiantes();

        Log::info("[SMER] DetectarRiesgoJob: escaneo completado. Alertas generadas: {$total}.");
    }
}

<?php

namespace App\Console\Commands;

use App\Services\AlertaService;
use Illuminate\Console\Command;

/**
 * Comando para ejecutar manualmente el escaneo de alertas.
 * Uso: php artisan app:detectar-riesgo
 */
class DetectarRiesgoCommand extends Command
{
    protected $signature   = 'app:detectar-riesgo';
    protected $description = 'Escanea todos los estudiantes y genera alertas de riesgo y deterioro progresivo';

    public function handle(AlertaService $alertaService): int
    {
        $this->info('🔍 Iniciando escaneo de riesgo estudiantil...');

        $total = $alertaService->escanearTodosLosEstudiantes();

        $this->info("✅ Escaneo completado. Alertas generadas: {$total}.");

        return self::SUCCESS;
    }
}

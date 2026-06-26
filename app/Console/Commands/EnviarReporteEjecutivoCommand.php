<?php

namespace App\Console\Commands;

use App\Mail\ReporteEjecutivoMail;
use App\Models\Entrevista;
use App\Models\Institucion;
use App\Models\Periodo;
use App\Models\Usuario;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * EnviarReporteEjecutivoCommand
 *
 * Genera el reporte ejecutivo de riesgo estudiantil (PDF) y lo envía
 * por correo a los directivos configurados (rol coordinador y admin
 * por defecto, o a un correo explícito vía --to=).
 *
 * Uso manual:
 *   php artisan app:enviar-reporte-ejecutivo
 *   php artisan app:enviar-reporte-ejecutivo --to=director@tecsup.edu.pe
 *   php artisan app:enviar-reporte-ejecutivo --frecuencia=mensual
 *
 * Programado automáticamente desde routes/console.php (RF15 / CUS08).
 */
class EnviarReporteEjecutivoCommand extends Command
{
    protected $signature = 'app:enviar-reporte-ejecutivo
                            {--to= : Correo destino explícito (separados por coma). Si se omite, usa coordinadores y admins activos.}
                            {--frecuencia=semanal : semanal|mensual — solo afecta el texto del correo}
                            {--institucion=1 : ID de la institución}';

    protected $description = 'Genera el reporte ejecutivo de riesgo estudiantil en PDF y lo envía por correo a directivos';

    public function handle(): int
    {
        $institucionId = (int) $this->option('institucion');
        $institucion   = Institucion::find($institucionId);

        if (! $institucion) {
            $this->error("No se encontró la institución con ID {$institucionId}.");
            return self::FAILURE;
        }

        $frecuencia      = $this->option('frecuencia') === 'mensual' ? 'mensual' : 'semanal';
        $frecuenciaLabel = $frecuencia === 'mensual' ? 'Mensual' : 'Semanal';

        // Periodo activo actual (si existe)
        $periodoActivo = Periodo::where('institucion_id', $institucionId)
            ->where('activo', 1)
            ->first();
        $periodoLabel = $periodoActivo?->nombre ?? 'Periodo actual';

        // Rango de fechas según frecuencia
        $desde = $frecuencia === 'mensual' ? now()->subMonth() : now()->subWeek();

        $datos = Entrevista::with(['estudiante', 'consejero', 'periodo'])
            ->whereHas('estudiante', fn ($q) => $q->where('institucion_id', $institucionId))
            ->where('fecha_entrevista', '>=', $desde)
            ->orderBy('nivel_riesgo')
            ->orderByDesc('fecha_entrevista')
            ->get();

        if ($datos->isEmpty()) {
            $this->warn('No hay entrevistas en el periodo evaluado. El reporte se enviará igualmente, vacío.');
        }

        $resumen = [
            'alto'  => $datos->where('nivel_riesgo', 'ALTO')->count(),
            'medio' => $datos->where('nivel_riesgo', 'MEDIO')->count(),
            'bajo'  => $datos->where('nivel_riesgo', 'BAJO')->count(),
            'total' => $datos->count(),
        ];

        // Reutiliza la misma vista PDF que el reporte manual de Reportes.php
        $pdf = Pdf::loadView('reportes.pdf-riesgo', [
            'datos'   => $datos,
            'periodo' => $periodoLabel,
            'carrera' => 'Todas',
            'nivel'   => 'Todos',
        ])->setPaper('a4', 'landscape');

        $pdfContent  = $pdf->output();
        $fecha       = now()->format('Y-m-d');
        $pdfFilename = "SMER_Reporte_Ejecutivo_{$frecuencia}_{$fecha}.pdf";

        // Determinar destinatarios
        $destinatarios = $this->resolverDestinatarios($institucionId);

        if (empty($destinatarios)) {
            $this->error('No hay destinatarios configurados (ni --to, ni coordinadores/admins activos con correo).');
            return self::FAILURE;
        }

        foreach ($destinatarios as $correo) {
            Mail::to($correo)->send(new ReporteEjecutivoMail(
                institucionNombre: $institucion->nombre,
                periodoLabel: $periodoLabel,
                frecuenciaLabel: $frecuenciaLabel,
                resumen: $resumen,
                pdfContent: $pdfContent,
                pdfFilename: $pdfFilename,
            ));
        }

        Log::info('[SMER] Reporte ejecutivo enviado.', [
            'institucion'    => $institucion->nombre,
            'frecuencia'     => $frecuencia,
            'destinatarios'  => $destinatarios,
            'total_registros'=> $resumen['total'],
        ]);

        $this->info('✅ Reporte ejecutivo generado y enviado a: ' . implode(', ', $destinatarios));

        return self::SUCCESS;
    }

    /**
     * @return string[]
     */
    private function resolverDestinatarios(int $institucionId): array
    {
        if ($to = $this->option('to')) {
            return array_filter(array_map('trim', explode(',', $to)));
        }

        return Usuario::where('institucion_id', $institucionId)
            ->where('activo', 1)
            ->whereIn('rol', [Usuario::ROL_COORDINADOR, Usuario::ROL_ADMIN])
            ->whereNotNull('email')
            ->pluck('email')
            ->unique()
            ->values()
            ->toArray();
    }
}

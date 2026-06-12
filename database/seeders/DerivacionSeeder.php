<?php

namespace Database\Seeders;

use App\Models\Derivacion;
use Illuminate\Database\Seeder;

class DerivacionSeeder extends Seeder
{
    public function run(): void
    {
        $derivaciones = [
            [
                'entrevista_id' => 1, // Carlos Quispe — ALTO
                'consejero_id'  => 2,
                'bienestar_id'  => null,
                'motivo'        => 'Estudiante presenta bajo rendimiento académico y múltiples inasistencias. Requiere atención inmediata de Bienestar.',
                'prioridad'     => 'URGENTE',
                'estado'        => 'PENDIENTE',
            ],
            [
                'entrevista_id' => 2, // Miguel Condori — ALTO
                'consejero_id'  => 2,
                'bienestar_id'  => 4,
                'motivo'        => 'Situación económica crítica. Familia en riesgo de deserción por falta de recursos.',
                'prioridad'     => 'ALTA',
                'estado'        => 'EN_ATENCION',
                'resolucion'    => null,
            ],
            [
                'entrevista_id' => 3, // Roberto Puma — ALTO
                'consejero_id'  => 3,
                'bienestar_id'  => 4,
                'motivo'        => 'Dificultades emocionales y familiares reportadas. Se solicita orientación psicológica.',
                'prioridad'     => 'ALTA',
                'estado'        => 'RESUELTA',
                'resolucion'    => 'Se brindó acompañamiento psicológico por 3 sesiones. Estudiante muestra mejora. Se cierra con seguimiento mensual.',
                'fecha_cierre'  => '2025-06-15',
            ],
        ];

        foreach ($derivaciones as $data) {
            if (!Derivacion::where('entrevista_id', $data['entrevista_id'])->exists()) {
                Derivacion::create($data);
            }
        }

        $this->command->info('✅ 3 derivaciones de prueba creadas.');
    }
}

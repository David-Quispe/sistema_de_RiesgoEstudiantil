<?php

namespace Database\Seeders;

use App\Models\Entrevista;
use App\Models\IndicadorEntrevista;
use Illuminate\Database\Seeder;

class EntrevistaSeeder extends Seeder
{
    // Nombres de los 6 indicadores
    private array $indicadores = [
        ['nombre' => 'Rendimiento Académico',    'peso' => 0.25],
        ['nombre' => 'Bienestar Socioemocional', 'peso' => 0.20],
        ['nombre' => 'Asistencia',               'peso' => 0.20],
        ['nombre' => 'Participación',            'peso' => 0.15],
        ['nombre' => 'Situación Económica',      'peso' => 0.10],
        ['nombre' => 'Red de Apoyo Familiar',    'peso' => 0.10],
    ];

    public function run(): void
    {
        // Consejero 1 = id 2, Consejero 2 = id 3 (admin es id 1)
        // Periodo 2025-I = id 1, Periodo 2025-II = id 2

        $casos = [
            // Riesgo ALTO — puntaje ponderado bajo
            ['estudiante_id' => 1,  'consejero_id' => 2, 'periodo_id' => 1, 'fecha' => '2025-04-10',
             'puntajes' => [2, 1, 3, 2, 1, 2], 'obs' => 'Estudiante con múltiples ausencias y bajo rendimiento.'],

            ['estudiante_id' => 3,  'consejero_id' => 2, 'periodo_id' => 1, 'fecha' => '2025-04-15',
             'puntajes' => [1, 2, 2, 1, 2, 1], 'obs' => 'Situación económica crítica. Requiere derivación urgente.'],

            ['estudiante_id' => 9,  'consejero_id' => 3, 'periodo_id' => 1, 'fecha' => '2025-05-03',
             'puntajes' => [3, 2, 2, 3, 1, 2], 'obs' => 'Dificultades emocionales reportadas.'],

            // Riesgo MEDIO — puntaje ponderado moderado
            ['estudiante_id' => 2,  'consejero_id' => 2, 'periodo_id' => 1, 'fecha' => '2025-04-12',
             'puntajes' => [5, 4, 6, 4, 5, 4], 'obs' => 'Estudiante con leve bajón en asistencia.'],

            ['estudiante_id' => 6,  'consejero_id' => 3, 'periodo_id' => 1, 'fecha' => '2025-04-20',
             'puntajes' => [4, 5, 5, 4, 4, 5], 'obs' => 'Seguimiento preventivo recomendado.'],

            ['estudiante_id' => 11, 'consejero_id' => 3, 'periodo_id' => 1, 'fecha' => '2025-05-10',
             'puntajes' => [5, 4, 4, 5, 4, 4], 'obs' => 'Alerta temprana por tendencia descendente.'],

            // Riesgo BAJO — puntaje ponderado alto
            ['estudiante_id' => 4,  'consejero_id' => 2, 'periodo_id' => 1, 'fecha' => '2025-04-18',
             'puntajes' => [8, 9, 9, 8, 9, 8], 'obs' => 'Excelente desempeño integral.'],

            ['estudiante_id' => 5,  'consejero_id' => 2, 'periodo_id' => 1, 'fecha' => '2025-04-22',
             'puntajes' => [7, 8, 8, 9, 7, 8], 'obs' => 'Estudiante destacado, sin alertas.'],

            ['estudiante_id' => 7,  'consejero_id' => 3, 'periodo_id' => 1, 'fecha' => '2025-04-25',
             'puntajes' => [9, 8, 9, 8, 9, 9], 'obs' => 'Sin observaciones negativas.'],

            ['estudiante_id' => 12, 'consejero_id' => 3, 'periodo_id' => 1, 'fecha' => '2025-05-05',
             'puntajes' => [8, 7, 8, 7, 8, 8], 'obs' => 'Buen desempeño general.'],

            // Periodo 2025-II — para el gráfico de tendencia
            ['estudiante_id' => 1,  'consejero_id' => 2, 'periodo_id' => 2, 'fecha' => '2025-09-10',
             'puntajes' => [4, 3, 4, 3, 3, 4], 'obs' => 'Mejora leve respecto al periodo anterior.'],

            ['estudiante_id' => 3,  'consejero_id' => 2, 'periodo_id' => 2, 'fecha' => '2025-09-15',
             'puntajes' => [2, 2, 3, 2, 2, 2], 'obs' => 'Persiste situación de riesgo alto.'],

            ['estudiante_id' => 2,  'consejero_id' => 2, 'periodo_id' => 2, 'fecha' => '2025-09-12',
             'puntajes' => [7, 7, 8, 6, 7, 7], 'obs' => 'Recuperación notable en este periodo.'],

            ['estudiante_id' => 10, 'consejero_id' => 3, 'periodo_id' => 2, 'fecha' => '2025-09-20',
             'puntajes' => [6, 6, 6, 5, 6, 6], 'obs' => 'Nivel estable, seguimiento mensual.'],

            ['estudiante_id' => 13, 'consejero_id' => 3, 'periodo_id' => 2, 'fecha' => '2025-10-01',
             'puntajes' => [8, 9, 8, 9, 8, 9], 'obs' => 'Excelente desempeño en segundo periodo.'],
        ];

        foreach ($casos as $caso) {
            // Evitar duplicados
            if (Entrevista::where('estudiante_id', $caso['estudiante_id'])
                          ->where('periodo_id', $caso['periodo_id'])
                          ->where('fecha_entrevista', $caso['fecha'])
                          ->exists()) {
                continue;
            }

            $entrevista = Entrevista::create([
                'estudiante_id'    => $caso['estudiante_id'],
                'consejero_id'     => $caso['consejero_id'],
                'periodo_id'       => $caso['periodo_id'],
                'fecha_entrevista' => $caso['fecha'],
                'observaciones'    => $caso['obs'],
                'puntaje_total'    => 0,
                'nivel_riesgo'     => 'BAJO',
            ]);

            // Crear los 6 indicadores
            foreach ($this->indicadores as $i => $ind) {
                IndicadorEntrevista::create([
                    'entrevista_id' => $entrevista->id,
                    'nombre'        => $ind['nombre'],
                    'puntaje'       => $caso['puntajes'][$i],
                    'peso'          => $ind['peso'],
                    'observacion'   => null,
                ]);
            }

            // Calcular riesgo automáticamente
            $entrevista->calcularRiesgo();
        }

        $this->command->info('✅ 15 entrevistas de prueba creadas con cálculo de riesgo.');
    }
}

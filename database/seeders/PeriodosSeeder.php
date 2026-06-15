<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PeriodosSeeder extends Seeder
{
    public function run(): void
    {
        $periodos = [
            ['nombre' => '2026-I',  'fecha_inicio' => '2026-03-01', 'fecha_fin' => '2026-07-31'],
            ['nombre' => '2026-II', 'fecha_inicio' => '2026-08-01', 'fecha_fin' => '2026-12-15'],
            ['nombre' => '2027-I',  'fecha_inicio' => '2027-03-01', 'fecha_fin' => '2027-07-31'],
            ['nombre' => '2027-II', 'fecha_inicio' => '2027-08-01', 'fecha_fin' => '2027-12-15'],
        ];

        foreach ($periodos as $p) {
            $existe = DB::selectOne(
                "SELECT COUNT(*) AS total FROM PERIODOS WHERE NOMBRE = :nombre AND INSTITUCION_ID = 1",
                ['nombre' => $p['nombre']]
            );

            if (($existe->total ?? $existe->TOTAL) == 0) {
                DB::statement(
                    "INSERT INTO PERIODOS (ID, INSTITUCION_ID, NOMBRE, FECHA_INICIO, FECHA_FIN, ACTIVO)
                     VALUES (SEQ_PERIODOS.NEXTVAL, 1, :nombre,
                             TO_DATE(:inicio, 'YYYY-MM-DD'),
                             TO_DATE(:fin,    'YYYY-MM-DD'), 1)",
                    ['nombre' => $p['nombre'], 'inicio' => $p['fecha_inicio'], 'fin' => $p['fecha_fin']]
                );
                echo "  ✓ Periodo {$p['nombre']} creado\n";
            } else {
                echo "  — Periodo {$p['nombre']} ya existe\n";
            }
        }
    }
}

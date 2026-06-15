<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class LimpiarDatosCommand extends Command
{
    protected $signature   = 'smer:limpiar-datos';
    protected $description = 'Elimina todos los datos de prueba excepto usuarios';

    public function handle(): void
    {
        $this->info('Limpiando datos de prueba...');

        // Oracle no tiene SET FOREIGN_KEY_CHECKS, se borra en orden correcto
        $tablas = [
            'AUDITORIA',
            'ALERTAS',
            'DOCUMENTOS_ADJUNTOS',
            'INDICADORES_ENTREVISTA',
            'DERIVACIONES',
            'ENTREVISTAS',
            'ESTUDIANTES',
        ];

        foreach ($tablas as $tabla) {
            DB::statement("DELETE FROM {$tabla}");
            $this->line("  ✓ {$tabla} vaciada");
        }

        // Reiniciar secuencias (opcional pero prolijo)
        $secuencias = [
            'SEQ_AUDITORIA',
            'SEQ_ALERTAS',
            'SEQ_DOCUMENTOS',
            'SEQ_INDICADORES',
            'SEQ_DERIVACIONES',
            'SEQ_ENTREVISTAS',
            'SEQ_ESTUDIANTES',
        ];

        foreach ($secuencias as $seq) {
            try {
                // Obtener valor actual
                $row = DB::selectOne("SELECT {$seq}.NEXTVAL AS val FROM DUAL");
                $current = $row->val ?? $row->VAL;
                // Reiniciar al 1 restando el actual
                $decrement = $current; // el próximo será 1
                DB::statement("ALTER SEQUENCE {$seq} INCREMENT BY -" . $decrement);
                DB::selectOne("SELECT {$seq}.NEXTVAL AS val FROM DUAL");
                DB::statement("ALTER SEQUENCE {$seq} INCREMENT BY 1");
                $this->line("  ✓ {$seq} reiniciada a 1");
            } catch (\Exception $e) {
                $this->warn("  ⚠ {$seq} no se pudo reiniciar: " . $e->getMessage());
            }
        }

        $this->info('✅ Listo. Usuarios y configuración intactos.');
    }
}

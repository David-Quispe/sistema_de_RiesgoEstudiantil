<?php

namespace App\Console\Commands;

use App\Models\Estudiante;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

/**
 * EncriptarEmailsEstudiantesCommand
 *
 * RNF06 — Migra los correos de estudiantes que actualmente están
 * guardados en texto plano (datos sembrados antes de activar el
 * cast "encrypted" en el modelo Estudiante) hacia su forma cifrada.
 *
 * Es IDEMPOTENTE: si un valor ya está cifrado, lo detecta y lo
 * deja igual (no lo vuelve a cifrar dos veces).
 *
 * IMPORTANTE: ejecutar primero el script SQL
 *   database/oracle/04_alter_estudiantes_email_encriptado.sql
 * para ampliar la columna EMAIL antes de correr este comando.
 *
 * Uso:
 *   php artisan app:encriptar-emails-estudiantes
 *   php artisan app:encriptar-emails-estudiantes --dry-run
 */
class EncriptarEmailsEstudiantesCommand extends Command
{
    protected $signature   = 'app:encriptar-emails-estudiantes {--dry-run : Solo muestra qué se haría, sin guardar cambios}';
    protected $description = 'Re-encripta los emails de estudiantes que aún están en texto plano (RNF06)';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        // Leemos directo de la BD (bypass del cast) para inspeccionar el valor crudo
        $registros = DB::table('ESTUDIANTES')
            ->select('id', 'codigo', 'email')
            ->whereNotNull('email')
            ->get();

        if ($registros->isEmpty()) {
            $this->info('No hay estudiantes con email registrado. Nada que hacer.');
            return self::SUCCESS;
        }

        $aEncriptar = 0;
        $yaEncriptados = 0;

        foreach ($registros as $row) {
            if ($this->pareceCifrado($row->email)) {
                $yaEncriptados++;
                continue;
            }

            $aEncriptar++;

            if ($dryRun) {
                $this->line("  [DRY-RUN] {$row->codigo}: se encriptaría \"{$row->email}\"");
                continue;
            }

            // Usamos el modelo Eloquent para que el cast "encrypted" haga el trabajo
            $estudiante = Estudiante::find($row->id);
            if ($estudiante) {
                // Forzamos el set del atributo crudo y guardamos: el cast lo cifra al persistir
                $estudiante->email = $row->email;
                $estudiante->save();
            }
        }

        $this->info("✅ Proceso completado.");
        $this->line("   Ya cifrados: {$yaEncriptados}");
        $this->line(($dryRun ? '   Pendientes (dry-run, no se modificó nada): ' : '   Encriptados ahora: ') . $aEncriptar);

        return self::SUCCESS;
    }

    /**
     * Heurística simple: el payload cifrado de Laravel es un JSON
     * Base64 con las claves iv/value/mac. Un email en texto plano
     * jamás tendrá esa forma.
     */
    private function pareceCifrado(string $valor): bool
    {
        try {
            Crypt::decryptString($valor);
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }
}

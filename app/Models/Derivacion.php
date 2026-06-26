<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class Derivacion extends Model
{
    protected $table = 'DERIVACIONES';

    protected $fillable = [
        'entrevista_id', 'consejero_id', 'bienestar_id',
        'motivo', 'prioridad', 'estado', 'resolucion', 'fecha_cierre',
    ];

    protected $casts = [
        'fecha_cierre' => 'date',
    ];

    const ESTADO_PENDIENTE    = 'PENDIENTE';
    const ESTADO_EN_ATENCION  = 'EN_ATENCION';
    const ESTADO_RESUELTA     = 'RESUELTA';
    const ESTADO_CERRADA      = 'CERRADA';

    const PRIORIDAD_BAJA    = 'BAJA';
    const PRIORIDAD_NORMAL  = 'NORMAL';
    const PRIORIDAD_ALTA    = 'ALTA';
    const PRIORIDAD_URGENTE = 'URGENTE';

    protected static function booted(): void
    {
        // Regla de negocio (CUS06): no se puede cerrar una derivación
        // sin haber documentado su resolución. Esta validación se aplica
        // a nivel de modelo para que no pueda saltarse desde ningún punto
        // de entrada (Filament, tinker, comandos, futuras integraciones).
        static::saving(function (Derivacion $derivacion) {
            if ($derivacion->estado === self::ESTADO_CERRADA && blank($derivacion->resolucion)) {
                throw ValidationException::withMessages([
                    'resolucion' => 'Debes registrar la resolución antes de cerrar la derivación.',
                ]);
            }
        });
    }

    public function entrevista()
    {
        return $this->belongsTo(Entrevista::class, 'entrevista_id');
    }

    public function consejero()
    {
        return $this->belongsTo(Usuario::class, 'consejero_id');
    }

    public function atendiendoPor()
    {
        return $this->belongsTo(Usuario::class, 'bienestar_id');
    }

    public function estudiante()
    {
        return $this->hasOneThrough(
            Estudiante::class,
            Entrevista::class,
            'id',           // FK en ENTREVISTAS
            'id',           // FK en ESTUDIANTES
            'entrevista_id', // Local key en DERIVACIONES
            'estudiante_id'  // Local key en ENTREVISTAS
        );
    }
}

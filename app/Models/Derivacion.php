<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estudiante extends Model
{
    protected $table = 'ESTUDIANTES';

    protected $fillable = [
        'institucion_id', 'codigo', 'nombre', 'apellidos',
        'email', 'carrera', 'ciclo', 'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'ciclo'  => 'integer',
    ];

    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombre} {$this->apellidos}";
    }

    public function institucion()
    {
        return $this->belongsTo(Institucion::class, 'institucion_id');
    }

    public function entrevistas()
    {
        return $this->hasMany(Entrevista::class, 'estudiante_id');
    }

    public function alertas()
    {
        return $this->hasMany(Alerta::class, 'estudiante_id');
    }

    public function ultimaEntrevista()
    {
        return $this->hasOne(Entrevista::class, 'estudiante_id')
                    ->latestOfMany('fecha_entrevista');
    }

    public function getNivelRiesgoActualAttribute(): string
    {
        // Usa la relación ya cargada para evitar N+1 queries en la tabla
        $rel = $this->relationLoaded('ultimaEntrevista')
            ? $this->ultimaEntrevista
            : $this->ultimaEntrevista()->first();

        return $rel?->nivel_riesgo ?? 'SIN DATOS';
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carrera extends Model
{
    protected $table    = 'CARRERAS';
    protected $fillable = ['institucion_id', 'nombre', 'grupo', 'activo'];

    protected $casts = ['activo' => 'boolean'];

    public function institucion()
    {
        return $this->belongsTo(Institucion::class, 'institucion_id');
    }

    public function estudiantes()
    {
        return $this->hasMany(Estudiante::class, 'carrera', 'nombre');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Periodo extends Model
{
    protected $table = 'PERIODOS';

    protected $fillable = ['institucion_id', 'nombre', 'fecha_inicio', 'fecha_fin', 'activo'];

    protected $casts = [
        'activo'       => 'boolean',
        'fecha_inicio' => 'date',
        'fecha_fin'    => 'date',
    ];

    public function institucion()
    {
        return $this->belongsTo(Institucion::class, 'institucion_id');
    }

    public function entrevistas()
    {
        return $this->hasMany(Entrevista::class, 'periodo_id');
    }
}

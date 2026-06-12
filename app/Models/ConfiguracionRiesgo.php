<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionRiesgo extends Model
{
    protected $table = 'CONFIGURACION_RIESGO';

    protected $fillable = [
        'institucion_id', 'indicador', 'peso', 'umbral_medio', 'umbral_alto', 'activo',
    ];

    protected $casts = [
        'peso'         => 'float',
        'umbral_medio' => 'float',
        'umbral_alto'  => 'float',
        'activo'       => 'boolean',
    ];

    public function institucion()
    {
        return $this->belongsTo(Institucion::class, 'institucion_id');
    }
}

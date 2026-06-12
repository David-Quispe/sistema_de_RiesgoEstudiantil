<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndicadorEntrevista extends Model
{
    protected $table = 'INDICADORES_ENTREVISTA';

    protected $fillable = [
        'entrevista_id', 'nombre', 'puntaje', 'peso', 'observacion',
    ];

    protected $casts = [
        'puntaje' => 'float',
        'peso'    => 'float',
    ];

    public function entrevista()
    {
        return $this->belongsTo(Entrevista::class, 'entrevista_id');
    }

    public function getPuntajePonderadoAttribute(): float
    {
        return round($this->puntaje * $this->peso, 4);
    }
}

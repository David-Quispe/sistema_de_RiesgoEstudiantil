<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Institucion extends Model
{
    protected $table = 'INSTITUCIONES';

    protected $fillable = ['nombre', 'codigo', 'activo'];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'institucion_id');
    }

    public function estudiantes()
    {
        return $this->hasMany(Estudiante::class, 'institucion_id');
    }

    public function periodos()
    {
        return $this->hasMany(Periodo::class, 'institucion_id');
    }

    public function configuracionesRiesgo()
    {
        return $this->hasMany(ConfiguracionRiesgo::class, 'institucion_id');
    }
}

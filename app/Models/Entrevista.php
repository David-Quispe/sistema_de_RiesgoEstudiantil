<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Entrevista extends Model
{
    protected $table = 'ENTREVISTAS';

    protected $fillable = [
        'estudiante_id', 'consejero_id', 'periodo_id',
        'fecha_entrevista', 'observaciones', 'puntaje_total', 'nivel_riesgo',
    ];

    protected $casts = [
        'fecha_entrevista' => 'date',
        'puntaje_total'    => 'float',
    ];

    const RIESGO_BAJO  = 'BAJO';
    const RIESGO_MEDIO = 'MEDIO';
    const RIESGO_ALTO  = 'ALTO';

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'estudiante_id');
    }

    public function consejero()
    {
        return $this->belongsTo(Usuario::class, 'consejero_id');
    }

    public function periodo()
    {
        return $this->belongsTo(Periodo::class, 'periodo_id');
    }

    public function indicadores()
    {
        return $this->hasMany(IndicadorEntrevista::class, 'entrevista_id');
    }

    public function derivaciones()
    {
        return $this->hasMany(Derivacion::class, 'entrevista_id');
    }

    public function documentos()
    {
        return $this->hasMany(DocumentoAdjunto::class, 'entrevista_id');
    }

    /**
     * Calcula el puntaje ponderado y clasifica el nivel de riesgo.
     * Se llama desde el Resource de Filament después de guardar los indicadores.
     */
    public function calcularRiesgo(): void
    {
        $puntaje = $this->indicadores()
            ->selectRaw('SUM(puntaje * peso) as total')
            ->value('total') ?? 0;

        // Determinar nivel según umbrales de CONFIGURACION_RIESGO
        // Por defecto: ALTO < 3, MEDIO < 5, BAJO >= 5
        $config = ConfiguracionRiesgo::where('institucion_id', $this->estudiante->institucion_id)
            ->where('activo', 1)
            ->first();

        $umbralAlto  = $config?->umbral_alto  ?? 3.0;
        $umbralMedio = $config?->umbral_medio ?? 5.0;

        if ($puntaje < $umbralAlto) {
            $nivel = self::RIESGO_ALTO;
        } elseif ($puntaje < $umbralMedio) {
            $nivel = self::RIESGO_MEDIO;
        } else {
            $nivel = self::RIESGO_BAJO;
        }

        $this->update([
            'puntaje_total' => round($puntaje, 2),
            'nivel_riesgo'  => $nivel,
        ]);
    }
}

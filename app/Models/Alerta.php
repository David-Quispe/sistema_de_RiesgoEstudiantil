<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alerta extends Model
{
    protected $table = 'ALERTAS';

    // Oracle no tiene TIMESTAMP automático — se maneja con trigger en BD
    // pero Laravel necesita saber los nombres de las columnas
    const CREATED_AT = 'CREATED_AT';
    const UPDATED_AT = 'UPDATED_AT';

    protected $fillable = [
        'estudiante_id', 'usuario_id', 'tipo', 'canal',
        'mensaje', 'leida', 'fecha_lectura',
    ];

    protected $casts = [
        'leida'         => 'boolean',
        'fecha_lectura' => 'datetime',
    ];

    // ── Tipos de alerta ─────────────────────────────────────────────────
    const TIPO_RIESGO_ALTO          = 'RIESGO_ALTO';
    const TIPO_DETERIORO_PROGRESIVO = 'DETERIORO_PROGRESIVO';
    const TIPO_DERIVACION           = 'DERIVACION';
    const TIPO_SISTEMA              = 'SISTEMA';

    // ── Relaciones ──────────────────────────────────────────────────────
    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'estudiante_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    // ── Helpers ─────────────────────────────────────────────────────────
    public function marcarLeida(): void
    {
        $this->update([
            'leida'         => true,
            'fecha_lectura' => now(),
        ]);
    }

    // Scope: alertas no leídas del usuario actual
    public function scopeNoLeidas($query)
    {
        return $query->where('leida', 0);
    }

    // Scope: alertas recientes (últimos N días)
    public function scopeRecientes($query, int $dias = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($dias));
    }
}

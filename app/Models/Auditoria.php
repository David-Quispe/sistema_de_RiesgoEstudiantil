<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Auditoria extends Model
{
    protected $table = 'AUDITORIA';

    public $timestamps = false; // Solo tiene created_at, sin updated_at

    protected $fillable = [
        'usuario_id', 'accion', 'tabla_afectada',
        'registro_id', 'detalle', 'ip_address', 'user_agent',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    /**
     * Registra una acción de auditoría.
     */
    public static function registrar(
        int $usuarioId,
        string $accion,
        string $tabla,
        ?int $registroId = null,
        ?string $detalle = null
    ): void {
        static::create([
            'usuario_id'     => $usuarioId,
            'accion'         => $accion,
            'tabla_afectada' => $tabla,
            'registro_id'    => $registroId,
            'detalle'        => $detalle,
            'ip_address'     => request()->ip(),
            'user_agent'     => request()->userAgent(),
        ]);
    }
}

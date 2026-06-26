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

    /**
     * RNF06 — Encriptación de datos sensibles.
     * El email del estudiante se almacena cifrado en la base de datos
     * (AES-256-CBC vía APP_KEY) y se descifra automáticamente al
     * leerlo a través de Eloquent. Esto es transparente para el resto
     * del código (formularios, PDFs, correos) ya que el casting ocurre
     * en el propio modelo.
     *
     * Importante: al estar cifrado, el campo email YA NO puede usarse
     * en cláusulas WHERE/LIKE a nivel de base de datos (no es buscable).
     * No se incluye en searchable() de ningún Resource de Filament.
     */
    protected $casts = [
        'activo' => 'boolean',
        'ciclo'  => 'integer',
    ];

    /**
     * RNF06 — Getter tolerante a fallos para email encriptado.
     * Si el valor en BD ya está cifrado lo descifra; si está en texto
     * plano (registros legacy o email null) lo devuelve sin explotar.
     * Esto permite convivir con datos anteriores a la encriptación.
     */
    public function getEmailAttribute(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }
        try {
            return decrypt($value);
        } catch (\Throwable) {
            return $value; // texto plano legacy: lo devuelve sin modificar
        }
    }

    /**
     * RNF06 — Setter: siempre guarda el email cifrado.
     */
    public function setEmailAttribute(?string $value): void
    {
        $this->attributes['email'] = $value !== null ? encrypt($value) : null;
    }

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

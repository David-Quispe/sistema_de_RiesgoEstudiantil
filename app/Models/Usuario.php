<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;

class Usuario extends Authenticatable implements FilamentUser, HasName
{
    use Notifiable;

    protected $table = 'USUARIOS';

    protected $rememberTokenName = false;

    protected $fillable = [
        'institucion_id', 'nombre', 'apellidos',
        'email', 'password', 'rol', 'activo', 'ultimo_acceso',
    ];

    protected $hidden = ['password'];

    protected $casts = [
        'activo'        => 'boolean',
        'ultimo_acceso' => 'datetime',
        'password'      => 'hashed',
    ];

    const ROL_CONSEJERO    = 'consejero';
    const ROL_COORDINADOR  = 'coordinador';
    const ROL_BIENESTAR    = 'bienestar';
    const ROL_ADMIN        = 'admin';

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->activo;
    }

    // Requerido por Filament para mostrar el nombre en la barra superior
    public function getFilamentName(): string
    {
        return "{$this->nombre} {$this->apellidos}";
    }

    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombre} {$this->apellidos}";
    }

    public function esAdmin(): bool        { return $this->rol === self::ROL_ADMIN; }
    public function esConsejero(): bool    { return $this->rol === self::ROL_CONSEJERO; }
    public function esCoordinador(): bool  { return $this->rol === self::ROL_COORDINADOR; }
    public function esBienestar(): bool    { return $this->rol === self::ROL_BIENESTAR; }

    public function institucion()
    {
        return $this->belongsTo(Institucion::class, 'institucion_id');
    }

    public function entrevistasComoConsejero()
    {
        return $this->hasMany(Entrevista::class, 'consejero_id');
    }

    public function derivacionesCreadas()
    {
        return $this->hasMany(Derivacion::class, 'consejero_id');
    }

    public function derivacionesAtendidas()
    {
        return $this->hasMany(Derivacion::class, 'bienestar_id');
    }

    public function alertas()
    {
        return $this->hasMany(Alerta::class, 'usuario_id');
    }
}

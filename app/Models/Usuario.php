<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model implements AuthenticatableContract
{
    use Authenticatable;

    protected $table    = 'usuarios';
    protected $fillable = [
        'rol_id',
        'nombre',
        'email',
        'password',
        'estado',
        'ultimo_acceso',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'estado'        => 'boolean',
        'ultimo_acceso' => 'datetime',
    ];

    // ── RELACIONES ──

    // Pertenece a un rol
    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }

    // Tiene muchos movimientos
    public function movimientos()
    {
        return $this->hasMany(Movimiento::class, 'usuario_id');
    }

    // ── HELPERS ──

    // Verifica si el usuario tiene un rol específico
    public function esAdmin(): bool
    {
        return $this->rol->nombre === 'admin';
    }

    public function tieneRol(string $rol): bool
    {
        return $this->rol->nombre === $rol;
    }

    // Devuelve las iniciales del nombre para avatar
    public function getInicialesAttribute(): string
    {
        $partes = explode(' ', trim($this->nombre));
        $ini    = strtoupper(substr($partes[0], 0, 1));
        if (count($partes) > 1) {
            $ini .= strtoupper(substr($partes[1], 0, 1));
        }
        return $ini;
    }
}
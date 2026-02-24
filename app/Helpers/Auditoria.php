<?php
namespace App\Helpers;

use App\Models\RegistroAuditoria;

class Auditoria
{
    public static function registrar(string $modulo, string $accion, string $descripcion): void
    {
        RegistroAuditoria::create([
            'usuario_id'  => session('usuario_id'), // ← lee tu sesión personalizada
            'modulo'      => $modulo,
            'accion'      => $accion,
            'descripcion' => $descripcion,
            'ip'          => request()->ip(),
        ]);
    }
}
<?php

namespace Database\Seeders;

use App\Models\Rol;
use Illuminate\Database\Seeder;

class RolSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'nombre'      => 'admin',
                'descripcion' => 'Acceso y control total al sistema',
            ],
            [
                'nombre'      => 'almacenero',
                'descripcion' => 'Gestión completa de categorías, materiales, inventario y movimientos',
            ],
            [
                'nombre'      => 'contable',
                'descripcion' => 'Solo visualización de módulos y exportación de reportes',
            ],
            [
                'nombre'      => 'invitado',
                'descripcion' => 'Solo puede ver materiales e inventario',
            ],
        ];

        foreach ($roles as $rol) {
            Rol::firstOrCreate(['nombre' => $rol['nombre']], $rol);
        }
    }
}
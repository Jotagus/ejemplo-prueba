<?php

namespace Database\Seeders;

use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        $rolAdmin = Rol::where('nombre', 'admin')->first();

        Usuario::firstOrCreate(
            ['email' => 'admin@emdell.com'],
            [
                'rol_id'   => $rolAdmin->id,
                'nombre'   => 'Administrador',
                'password' => Hash::make('admin123'),  // ← cambia esto después
                'estado'   => true,
            ]
        );
    }
}
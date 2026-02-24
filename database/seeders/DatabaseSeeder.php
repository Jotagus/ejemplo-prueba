<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolSeeder::class,    // primero roles
            UsuarioSeeder::class, // luego el usuario admin
        ]);
    }
}
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Directiva: solo roles con permiso de escritura ven el contenido
        Blade::if('canEdit', function () {
            return in_array(session('usuario_rol'), ['admin', 'almacenero']);
        });

        // Directiva: solo admin
        Blade::if('isAdmin', function () {
            return session('usuario_rol') === 'admin';
        });
    }
}

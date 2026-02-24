<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\SubcategoriaController;
use App\Http\Controllers\UnidadMedidaController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\MovimientoController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\AuditoriaController;

// ══════════════════════════════════════════════════════
//  RUTAS PÚBLICAS
// ══════════════════════════════════════════════════════
Route::get('/', [LoginController::class, 'showLogin'])->name('login');
Route::get('/login', [LoginController::class, 'showLogin'])->name('login.get');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ══════════════════════════════════════════════════════
//  RUTAS PROTEGIDAS
// ══════════════════════════════════════════════════════
Route::middleware(['auth.check'])->group(function () {

    // ── DASHBOARD — todos los roles ──
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ──────────────────────────────────────────────────
    //  CATEGORÍAS
    //  admin, almacenero → CRUD completo
    //  contable          → solo index (ver)
    //  invitado          → sin acceso
    // ──────────────────────────────────────────────────
    Route::middleware('auth.rol:admin,almacenero,contable')->group(function () {
        Route::get('/categorias', [CategoriaController::class, 'index'])->name('categorias.index');
    });

    Route::middleware('auth.rol:admin,almacenero')->group(function () {
        Route::post('/categorias', [CategoriaController::class, 'store'])->name('categorias.store');
        Route::get('/categorias/{categoria}/editar', [CategoriaController::class, 'edit'])->name('categorias.edit');
        Route::put('/categorias/{categoria}', [CategoriaController::class, 'update'])->name('categorias.update');
        Route::delete('/categorias/{categoria}', [CategoriaController::class, 'destroy'])->name('categorias.destroy');
    });

    // Subcategorías — mismos permisos que categorías
    Route::middleware('auth.rol:admin,almacenero,contable')->group(function () {
        Route::get('/subcategorias', [SubcategoriaController::class, 'index'])->name('subcategorias.index');
    });

    Route::middleware('auth.rol:admin,almacenero')->group(function () {
        Route::get('/subcategorias/crear', [SubcategoriaController::class, 'create'])->name('subcategorias.create');
        Route::post('/subcategorias', [SubcategoriaController::class, 'store'])->name('subcategorias.store');
        Route::get('/subcategorias/{subcategoria}/editar', [SubcategoriaController::class, 'edit'])->name('subcategorias.edit');
        Route::put('/subcategorias/{subcategoria}', [SubcategoriaController::class, 'update'])->name('subcategorias.update');
        Route::delete('/subcategorias/{subcategoria}', [SubcategoriaController::class, 'destroy'])->name('subcategorias.destroy');
    });

    // ──────────────────────────────────────────────────
    //  UNIDADES DE MEDIDA — admin y almacenero
    // ──────────────────────────────────────────────────
    Route::middleware('auth.rol:admin,almacenero')->group(function () {
        Route::get('/unidades', [UnidadMedidaController::class, 'index'])->name('unidades.index');
        Route::get('/unidades/create', [UnidadMedidaController::class, 'create'])->name('unidades.create');
        Route::post('/unidades', [UnidadMedidaController::class, 'store'])->name('unidades.store');
        Route::put('/unidades/{unidad}', [UnidadMedidaController::class, 'update'])->name('unidades.update');
        Route::delete('/unidades/{unidad}', [UnidadMedidaController::class, 'destroy'])->name('unidades.destroy');
    });

    // ──────────────────────────────────────────────────
    //  MATERIALES
    //  admin, almacenero → CRUD completo
    //  contable, invitado → solo index (ver)
    // ──────────────────────────────────────────────────
    Route::middleware('auth.rol:admin,almacenero,contable,invitado')->group(function () {
        Route::get('/materiales', [MaterialController::class, 'index'])->name('materiales.index');
        Route::get('/materiales/{material}', [MaterialController::class, 'show'])->name('materiales.show');
    });

    Route::middleware('auth.rol:admin,almacenero')->group(function () {
        Route::get('/materiales/crear', [MaterialController::class, 'create'])->name('materiales.create');
        Route::post('/materiales', [MaterialController::class, 'store'])->name('materiales.store');
        Route::get('/materiales/{material}/editar', [MaterialController::class, 'edit'])->name('materiales.edit');
        Route::put('/materiales/{material}', [MaterialController::class, 'update'])->name('materiales.update');
        Route::delete('/materiales/{material}', [MaterialController::class, 'destroy'])->name('materiales.destroy');
    });

    // ──────────────────────────────────────────────────
    //  INVENTARIO
    //  admin, almacenero → CRUD completo
    //  contable, invitado → solo index (ver)
    // ──────────────────────────────────────────────────
    Route::middleware('auth.rol:admin,almacenero,contable,invitado')->group(function () {
        Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario.index');
    });

    Route::middleware('auth.rol:admin,almacenero')->group(function () {
        Route::put('/inventario/{material}', [InventarioController::class, 'update'])->name('inventario.update');
    });

    // ──────────────────────────────────────────────────
    //  KARDEX
    //  admin, almacenero → ver + exportar
    //  contable          → ver + exportar
    //  invitado          → sin acceso
    // ──────────────────────────────────────────────────
    Route::middleware('auth.rol:admin,almacenero,contable')->group(function () {
        Route::get('/materiales/{material}/kardex', [MovimientoController::class, 'kardex'])->name('materiales.kardex');
        Route::get('/materiales/{material}/kardex/pdf', [MovimientoController::class, 'exportarKardexPDF'])->name('materiales.kardex.pdf');
        Route::get('/materiales/{material}/kardex/excel', [MovimientoController::class, 'exportarKardexExcel'])->name('materiales.kardex.excel');
    });

    // ──────────────────────────────────────────────────
    //  MOVIMIENTOS
    //  admin, almacenero → CRUD completo
    //  contable          → solo index (ver)
    //  invitado          → sin acceso
    // ──────────────────────────────────────────────────
    Route::middleware('auth.rol:admin,almacenero,contable')->group(function () {
        Route::get('/movimientos', [MovimientoController::class, 'index'])->name('movimientos.index');
        Route::get('/movimientos/{movimiento}', [MovimientoController::class, 'show'])->name('movimientos.show');
    });

    Route::middleware('auth.rol:admin,almacenero')->group(function () {
        Route::get('/movimientos/crear', [MovimientoController::class, 'create'])->name('movimientos.create');
        Route::post('/movimientos', [MovimientoController::class, 'store'])->name('movimientos.store');
        Route::get('/movimientos/{movimiento}/editar', [MovimientoController::class, 'edit'])->name('movimientos.edit');
        Route::put('/movimientos/{movimiento}', [MovimientoController::class, 'update'])->name('movimientos.update');
        Route::delete('/movimientos/{movimiento}', [MovimientoController::class, 'destroy'])->name('movimientos.destroy');
    });

    // ──────────────────────────────────────────────────
    //  REPORTES
    //  admin, almacenero, contable → ver + exportar
    //  invitado → sin acceso
    // ──────────────────────────────────────────────────
    Route::middleware('auth.rol:admin,almacenero,contable')->prefix('reportes')->name('reportes.')->group(function () {
        Route::get('/', [ReporteController::class, 'index'])->name('index');
        Route::get('/inventario-general', [ReporteController::class, 'inventarioGeneral'])->name('inventario-general');
        Route::get('/kardex-material', [ReporteController::class, 'kardexMaterial'])->name('kardex-material');
        Route::get('/movimientos-periodo', [ReporteController::class, 'movimientosPeriodo'])->name('movimientos-periodo');
        Route::get('/stock-minimo', [ReporteController::class, 'stockMinimo'])->name('stock-minimo');
        Route::get('/valorizacion', [ReporteController::class, 'valorizacionInventario'])->name('valorizacion');
    });

    // ──────────────────────────────────────────────────
    //  USUARIOS — solo admin
    // ──────────────────────────────────────────────────
    Route::middleware('auth.rol:admin')->group(function () {
        Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
        Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
        Route::put('/usuarios/{usuario}', [UsuarioController::class, 'update'])->name('usuarios.update');
        Route::patch('/usuarios/{usuario}/toggle', [UsuarioController::class, 'toggleEstado'])->name('usuarios.toggle');
        Route::delete('/usuarios/{usuario}', [UsuarioController::class, 'destroy'])->name('usuarios.destroy');
    });

    // ──────────────────────────────────────────────────
    //  BACKUPS — solo admin
    // ──────────────────────────────────────────────────
    Route::prefix('backups')->name('backups.')->group(function () {
        Route::get('/', [BackupController::class, 'index'])->name('index');
        Route::post('/generate', [BackupController::class, 'generate'])->name('generate');
        Route::get('/download/{filename}', [BackupController::class, 'download'])->name('download');
        Route::delete('/delete/{filename}', [BackupController::class, 'delete'])->name('delete');
    });

    Route::get('/auditoria', [AuditoriaController::class, 'index'])->name('auditoria.index');
    Route::delete('/auditoria/{id}', [AuditoriaController::class, 'destroy'])->name('auditoria.destroy');
    Route::post('/auditoria/limpiar', [AuditoriaController::class, 'limpiar'])->name('auditoria.limpiar');

});
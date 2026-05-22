<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\DashboardController;



Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');

    Route::resource('clientes', ClienteController::class);
    Route::resource('productos', ProductoController::class);
    Route::resource('proveedores', ProveedorController::class);
    Route::get('/ventas/pos', [VentaController::class, 'pos'])->name('ventas.pos');
    Route::post('/ventas/{venta}/anular', [VentaController::class, 'anular'])->name('ventas.anular');
    Route::resource('ventas', VentaController::class)->only(['index', 'store', 'show']);
    Route::resource('usuarios', UsuarioController::class)->except(['show']);
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

require __DIR__.'/settings.php';

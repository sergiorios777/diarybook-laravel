<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TransactionController;

// --- RUTA DEL DASHBOARD ---
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// --- RUTAS DE GESTIÓN (CRUD) ---
Route::resource('categorias', CategoryController::class);
Route::resource('transactions', TransactionController::class)->only([
    'index', 'create', 'store'
]);

/*
// --- Rutas de Transacciones ---

// 1. Muestra el formulario para crear una nueva transacción
Route::get('/transacciones/crear', [TransactionController::class, 'create'])->name('transactions.create');

// 2. Guarda la nueva transacción enviada desde el formulario
Route::post('/transacciones', [TransactionController::class, 'store'])->name('transactions.store');

// 3. Muestra el listado de todas las transacciones
Route::get('/transacciones', [TransactionController::class, 'index'])->name('transactions.index');
*/

/*
Route::get('/', function () {
    return view('welcome');
});
*/

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TransactionController;

// --- RUTA DEL DASHBOARD ---
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// --- RUTAS DE GESTIÓN (CRUD) ---
Route::resource('categorias', CategoryController::class)->parameters(['categorias' => 'category']);
Route::resource('transactions', TransactionController::class)->only([
    'index', 'create', 'store'
]);
Route::resource('cuentas', AccountController::class);

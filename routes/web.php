<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ReportController;

// --- RUTA DEL DASHBOARD ---
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// --- NUEVA RUTA DE INFORMES ---
Route::get('/informes', [ReportController::class, 'index'])->name('reports.index');

// --- RUTAS DE GESTIÓN (CRUD) ---
Route::resource('categorias', CategoryController::class)->parameters(['categorias' => 'category']);
Route::resource('cuentas', AccountController::class)->parameters(['cuentas' => 'account']);
Route::resource('transactions', TransactionController::class)->only([
    'index', 'create', 'store'
]);

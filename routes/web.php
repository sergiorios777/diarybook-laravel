<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ReportController;

// --- RUTA DEL DASHBOARD ---
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// --- RUTAS DE INFORMES ---
// Renombramos la ruta de gastos para ser específica
Route::get('/informes/gastos', [ReportController::class, 'expenses'])->name('reports.expenses');
// Creamos la nueva ruta de ingresos
Route::get('/informes/ingresos', [ReportController::class, 'income'])->name('reports.income');

// --- RUTAS DE GESTIÓN (CRUD) ---
Route::resource('categorias', CategoryController::class)->parameters(['categorias' => 'category']);
Route::resource('cuentas', AccountController::class)->parameters(['cuentas' => 'account']);
Route::resource('transactions', TransactionController::class)->only([
    'index', 'create', 'store', 'edit', 'update', 'destroy'
]);

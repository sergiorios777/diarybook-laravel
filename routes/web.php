<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CashCountController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\WeeklyReportController;
use App\Http\Controllers\MonthlyReportController;
use App\Http\Controllers\PdfController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- RUTAS DE INFORMES ---
    // Renombramos la ruta de gastos para ser específica
    Route::get('/informes/gastos', [ReportController::class, 'expenses'])->name('reports.expenses');
    // Creamos la nueva ruta de ingresos
    Route::get('/informes/ingresos', [ReportController::class, 'income'])->name('reports.income');
    // Ruta para el informe semanal
    Route::get('/informes/semanal', [WeeklyReportController::class, 'index'])->name('reports.weekly');
    // Ruta para el informe mensual
    Route::get('/informes/mensual', [MonthlyReportController::class, 'index'])->name('reports.monthly');
    // Ruta para el informe detallado
    Route::get('/informes/detallado', [ReportController::class, 'detailed'])->name('reports.detailed');
    // Ruta para generar PDFs
    Route::post('/informes/pdf', [PdfController::class, 'generate'])->name('reports.pdf');

    // --- RUTAS DE GESTIÓN (CRUD) ---
    Route::resource('categorias', CategoryController::class)->parameters(['categorias' => 'category']);
    Route::resource('cuentas', AccountController::class)->parameters(['cuentas' => 'account']);

    // --- IMPORTACIÓN DE TRANSACCIONES ---
    Route::get('/transactions/import', [TransactionController::class, 'showImportForm'])->name('transactions.import.form');
    Route::post('/transactions/import', [TransactionController::class, 'processImport'])->name('transactions.import.process');
    Route::resource('transactions', TransactionController::class)->only([
        'index', 'create', 'store', 'edit', 'update', 'destroy'
    ]);

    // --- RUTA DE ARQUEO DE CAJA (CONTADOR) ---
    Route::get('/arqueo', [CashCountController::class, 'index'])->name('cash_counts.index');
    Route::post('/arqueo', [CashCountController::class, 'store'])->name('cash_counts.store');
    Route::get('/cuentas/{account}/saldo', [CashCountController::class, 'getBalance'])->name('cuentas.balance');
    // Historial
    Route::get('/arqueos/historial', [CashCountController::class, 'history'])->name('cash_counts.history');

    // Ver Detalle (usamos el ID del arqueo)
    Route::get('/arqueos/{cashCount}', [CashCountController::class, 'show'])->name('cash_counts.show');
    
});

require __DIR__.'/auth.php';

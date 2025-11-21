<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Muestra el informe de gastos por categoría.
     * (Este era tu método 'index' anterior, ahora renombrado)
     */
    public function expenses(Request $request): View
    {
        $date_from = $request->input('date_from', now()->startOfMonth()->toDateString());
        $date_to = $request->input('date_to', now()->endOfMonth()->toDateString());

        $reportData = Transaction::query()
            ->select(
                'category_id', 
                DB::raw('SUM(amount) as total_gastos') // <-- total_gastos
            )
            ->where('type', 'gasto') // <-- gasto
            ->whereBetween('date', [$date_from, $date_to])
            ->groupBy('category_id')
            ->orderBy('total_gastos', 'desc')
            ->with('category')
            ->get();

        $totalGeneral = $reportData->sum('total_gastos');

        // Apuntamos a la nueva vista 'reports.expenses'
        return view('reports.expenses', compact(
            'reportData', 
            'totalGeneral', 
            'date_from', 
            'date_to'
        ));
    }

    /**
     * ¡NUEVO MÉTODO!
     * Muestra el informe de ingresos por categoría.
     */
    public function income(Request $request): View
    {
        $date_from = $request->input('date_from', now()->startOfMonth()->toDateString());
        $date_to = $request->input('date_to', now()->endOfMonth()->toDateString());

        $reportData = Transaction::query()
            ->select(
                'category_id', 
                DB::raw('SUM(amount) as total_ingresos') // <-- total_ingresos
            )
            ->where('type', 'ingreso') // <-- ingreso
            ->whereBetween('date', [$date_from, $date_to])
            ->groupBy('category_id')
            ->orderBy('total_ingresos', 'desc')
            ->with('category')
            ->get();

        $totalGeneral = $reportData->sum('total_ingresos');

        // Apuntamos a la nueva vista 'reports.income'
        return view('reports.income', compact(
            'reportData', 
            'totalGeneral', 
            'date_from', 
            'date_to'
        ));
    }

    /**
     * Reporte Detallado con Filtros Múltiples
     */
    public function detailed(Request $request): View
    {
        // 1. Obtener datos para los selectores del filtro
        $accounts = \App\Models\Account::all();
        $categories = \App\Models\Category::orderBy('name')->get();

        // 2. Valores por defecto para los filtros
        $dateFrom = $request->input('date_from', now()->startOfMonth()->toDateString());
        $dateTo = $request->input('date_to', now()->endOfMonth()->toDateString());
        $accountId = $request->input('account_id');
        $categoryId = $request->input('category_id');

        // 3. Construcción de la consulta
        $query = Transaction::with(['account', 'category'])
            ->whereBetween('date', [$dateFrom, $dateTo]);

        // Filtro opcional por Cuenta
        if ($accountId) {
            $query->where('account_id', $accountId);
        }

        // Filtro opcional por Categoría
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        // Ordenar: Fecha descendente, luego Hora descendente
        $transactions = $query->orderBy('date', 'desc')
                              ->orderBy('time', 'desc')
                              ->get();

        // 4. Calcular totales de lo que se ha filtrado (¡Muy útil!)
        $totalIngresos = $transactions->where('type', 'ingreso')->sum('amount');
        $totalGastos = $transactions->where('type', 'gasto')->sum('amount');
        $saldoPeriodo = $totalIngresos - $totalGastos;

        return view('reports.detailed', compact(
            'transactions',
            'accounts',
            'categories',
            'dateFrom',
            'dateTo',
            'accountId',
            'categoryId',
            'totalIngresos',
            'totalGastos',
            'saldoPeriodo'
        ));
    }
}

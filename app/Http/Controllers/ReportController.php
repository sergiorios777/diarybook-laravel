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
}

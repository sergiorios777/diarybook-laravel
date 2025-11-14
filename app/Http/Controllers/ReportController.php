<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB; // <-- ¡MUY IMPORTANTE!

class ReportController extends Controller
{
    /**
     * Muestra el informe de gastos por categoría.
     */
    public function index(Request $request): View
    {
        // 1. Validar las fechas del filtro.
        // Si no se proporcionan, usamos el mes actual por defecto.
        $date_from = $request->input('date_from', now()->startOfMonth()->toDateString());
        $date_to = $request->input('date_to', now()->endOfMonth()->toDateString());

        // 2. Esta es la consulta principal del informe.
        $reportData = Transaction::query()
            // Importante: Usamos DB::raw() para poder usar la función SUM() de SQL.
            ->select(
                'category_id', 
                DB::raw('SUM(amount) as total_gastos')
            )
            ->where('type', 'gasto') // Solo transacciones de gasto
            ->whereBetween('date', [$date_from, $date_to]) // Filtramos por el rango de fechas
            ->groupBy('category_id') // Agrupamos por categoría
            ->orderBy('total_gastos', 'desc') // Mostramos los gastos más altos primero
            ->with('category') // Cargamos la información de la categoría (nombre)
            ->get();

        // 3. Calculamos el total de todos los gastos en ese período
        $totalGeneral = $reportData->sum('total_gastos');

        // 4. Pasamos todos los datos a la vista
        return view('reports.index', compact(
            'reportData', 
            'totalGeneral', 
            'date_from', 
            'date_to'
        ));
    }
}

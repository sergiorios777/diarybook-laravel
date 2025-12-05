<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Muestra el panel de control principal.
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        // 1. Aquí está la consulta eficiente.
        //    Usamos 'withSum' para precargar los totales que nuestro
        //    Accessor 'current_balance' necesita.
        //    Los alias 'total_ingresos' y 'total_gastos' DEBEN coincidir
        //    con los usados en el Accessor del Modelo Account.
        $accounts = Account::withSum([
                'transactions as total_ingresos' => fn($query) => $query->where('type', 'ingreso')
            ], 'amount')
            ->withSum([
                'transactions as total_gastos' => fn($query) => $query->where('type', 'gasto')
            ], 'amount')
            ->get(); // Obtenemos la colección de cuentas

        // 2. Ahora que cada cuenta tiene su 'current_balance' calculado
        //    (gracias al Accessor), podemos sumar todos los saldos
        //    para obtener un gran total.
        $total_general = $accounts->sum('current_balance');

        // === NUEVO: Datos para el gráfico (últimos 6 meses) ===
        $months = collect(range(5, 0))->map(fn($i) => now()->subMonths($i))->reverse();

        $chartData = $months->map(function ($date) {
            return [
                'label' => $date->format('M Y'),
                'ingresos' => \App\Models\Transaction::where('type', 'ingreso')
                    ->whereMonth('date', $date->month)
                    ->whereYear('date', $date->year)
                    ->sum('amount'),
                'gastos' => \App\Models\Transaction::where('type', 'gasto')
                    ->whereMonth('date', $date->month)
                    ->whereYear('date', $date->year)
                    ->sum('amount'),
            ];
        });

        $chartLabels = $chartData->pluck('label')->toArray();
        $chartIngresos = $chartData->pluck('ingresos')->toArray();
        $chartGastos = $chartData->pluck('gastos')->toArray();
        // === FIN NUEVO ===

        // 3. Pasamos los datos a la nueva vista del dashboard.
        return view('dashboard.index', compact(
            'accounts', 'total_general',
            'chartLabels',     // ← AÑADIR ESTA
            'chartIngresos',   // ← AÑADIR ESTA
            'chartGastos'      // ← AÑADIR ESTA
            ));
    }
}

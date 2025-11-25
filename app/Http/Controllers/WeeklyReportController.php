<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;

class WeeklyReportController extends Controller
{
    public function index(Request $request): View
    {
        // 1. Determinar la fecha base (por defecto hoy)
        $dateInput = $request->input('date', now()->toDateString());
        $date = Carbon::parse($dateInput);

        // 2. Calcular inicio y fin de la semana (Domingo a Sábado)
        // Ajusta startOfWeek según tu preferencia (Carbon::MONDAY o Carbon::SUNDAY)
        $startOfWeek = $date->copy()->startOfWeek(Carbon::SUNDAY);
        $endOfWeek   = $date->copy()->endOfWeek(Carbon::SATURDAY);

        // 3. CALCULAR EL SALDO INICIAL HISTÓRICO (Antes de esta semana)
        //    Saldo Inicial de Cuentas + (Ingresos Pasados - Gastos Pasados)
        $initialAccounts = Account::sum('initial_balance');
        
        $pastIncomes = Transaction::where('date', '<', $startOfWeek->format('Y-m-d'))
            ->where('type', 'ingreso')->sum('amount');
            
        $pastExpenses = Transaction::where('date', '<', $startOfWeek->format('Y-m-d'))
            ->where('type', 'gasto')->sum('amount');

        $balanceStartOfWeek = $initialAccounts + $pastIncomes - $pastExpenses;

        // 4. OBTENER TRANSACCIONES DE LA SEMANA
        $transactions = Transaction::with('category')
            ->whereBetween('date', [$startOfWeek->format('Y-m-d'), $endOfWeek->format('Y-m-d')])
            ->get();

        // 5. ESTRUCTURAR DATOS PARA LA TABLA
        // Generamos un array con los 7 días
        $days = [];
        $current = $startOfWeek->copy();
        while ($current <= $endOfWeek) {
            $days[$current->format('Y-m-d')] = [
                'date' => $current->format('Y-m-d'),
                'label' => $current->format('d/m/Y'),
                'day_name' => $current->locale('es')->dayName
            ];
            $current->addDay();
        }

        // Agrupamos transacciones por Tipo -> Categoría -> Fecha
        $matrix = [
            'ingreso' => [],
            'gasto' => []
        ];

        // Definimos las categorías que NO queremos ver en el reporte
        $excludedCategories = ['Transferencias internas recibidas [T.INT]', 
                               'Transferencias internas enviadas [T.INT]']; // Puedes agregar más aquí si necesitas

        // Precargamos nombres de categorías para que aparezcan aunque no tengan datos (opcional)
        // Aquí lo haremos dinámico: solo categorías con movimiento en la semana
        foreach ($transactions as $tx) {
            $catName = $tx->category ? $tx->category->name : 'Sin Categoría';

            // Excluir categorías definidas
            if (in_array($catName, $excludedCategories)) {
                continue;
            }

            $dateKey = $tx->date instanceof \Carbon\Carbon 
                ? $tx->date->format('Y-m-d') 
                : substr($tx->date, 0, 10); // Aseguramos formato fecha string
            
            // Aseguramos que la celda exista
            if (!isset($matrix[$tx->type][$catName][$dateKey])) {
                $matrix[$tx->type][$catName][$dateKey] = 0;
            }
            
            $matrix[$tx->type][$catName][$dateKey] += $tx->amount;
        }

        return view('reports.weekly', compact(
            'balanceStartOfWeek',
            'days',
            'matrix',
            'startOfWeek',
            'endOfWeek'
        ));
    }
}

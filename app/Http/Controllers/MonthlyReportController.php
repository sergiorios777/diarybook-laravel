<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;

class MonthlyReportController extends Controller
{
    public function index(Request $request): View
    {
        // 1. Configuración Inicial
        // (Eliminamos las líneas de Carbon::setWeekStartsAt que daban error)

        $monthInput = $request->input('month', now()->format('Y-m'));
        $date = Carbon::parse($monthInput . '-01'); 

        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth   = $date->copy()->endOfMonth();

        // 2. CALCULAR SALDO INICIAL HISTÓRICO
        $initialAccounts = Account::sum('initial_balance');
        
        $pastIncomes = Transaction::where('date', '<', $startOfMonth->format('Y-m-d'))
            ->where('type', 'ingreso')->sum('amount');
            
        $pastExpenses = Transaction::where('date', '<', $startOfMonth->format('Y-m-d'))
            ->where('type', 'gasto')->sum('amount');

        $balanceStartOfMonth = $initialAccounts + $pastIncomes - $pastExpenses;

        // 3. OBTENER TRANSACCIONES
        $transactions = Transaction::with('category')
            ->whereBetween('date', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
            ->get();

        // 4. GENERAR LAS COLUMNAS (SEMANAS)
        $weeks = [];
        
        // Empezamos buscando el inicio de la semana del primer día del mes.
        // ALERTA: Aquí le decimos explícitamente que la semana arranca en DOMINGO.
        $current = $startOfMonth->copy()->startOfWeek(Carbon::SUNDAY);
        
        while ($current <= $endOfMonth) {
            
            $wStart = $current->copy(); // Ya está en Domingo por la línea anterior/bucle
            
            // Definimos el fin como Sábado explícitamente
            $wEnd   = $current->copy()->endOfWeek(Carbon::SATURDAY); 

            // ID Único: La fecha del domingo (ej: 2025-11-16)
            $weekKey = $wStart->format('Y-m-d');

            // Etiqueta visual (Recortes estéticos)
            $visualStart = $wStart < $startOfMonth ? $startOfMonth : $wStart;
            $visualEnd   = $wEnd > $endOfMonth ? $endOfMonth : $wEnd;

            // Solo agregamos la semana si tiene al menos un día dentro del mes actual
            // (Esto evita columnas vacías si la semana empieza el mes siguiente)
            if ($visualStart <= $visualEnd) {
                $weeks[$weekKey] = [
                    'label' => 'Sem. ' . $wStart->weekOfYear,
                    'dates' => $visualStart->format('d') . '-' . $visualEnd->format('d/m'),
                ];
            }
            
            // Avanzamos 1 semana
            $current->addWeek();
        }

        // 5. LLENAR MATRIZ
        $matrix = ['ingreso' => [], 'gasto' => []];

        // Definimos las categorías que NO queremos ver en el reporte
        $excludedCategories = ['Transferencias internas recibidas [T.INT]', 
                               'Transferencias internas enviadas [T.INT]'];

        foreach ($transactions as $tx) {
            $catName = $tx->category ? $tx->category->name : 'Sin Categoría';
            
            // Excluir categorías definidas
            if (in_array($catName, $excludedCategories)) {
                continue;
            }
            
            // Obtenemos la fecha de la transacción
            $txDate = Carbon::parse($tx->date);
            
            // Calculamos a qué "Domingo de inicio" pertenece esta fecha.
            // Esto alinea la transacción con las columnas generadas arriba.
            $weekKey = $txDate->startOfWeek(Carbon::SUNDAY)->format('Y-m-d');

            if (isset($weeks[$weekKey])) {
                if (!isset($matrix[$tx->type][$catName][$weekKey])) {
                    $matrix[$tx->type][$catName][$weekKey] = 0;
                }
                $matrix[$tx->type][$catName][$weekKey] += $tx->amount;
            }
        }

        return view('reports.monthly', compact(
            'balanceStartOfMonth',
            'weeks',
            'matrix',
            'startOfMonth',
            'monthInput'
        ));
    }
}

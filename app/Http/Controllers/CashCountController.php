<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\CashCount;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class CashCountController extends Controller
{
    public function index(): View
    {
        // Pasamos las cuentas para poder comparar el saldo del sistema vs lo contado
        // Usamos el accessor 'current_balance' que ya creamos antes
        $accounts = Account::all();
        
        return view('cash_counts.index', compact('accounts'));
    }

    /**
     * Método API para obtener el saldo fresco de una cuenta sin recargar la página.
     */
    public function getBalance(Account $account)
    {
        return response()->json([
            'balance' => $account->current_balance
        ]);
    }

    // --- NUEVO MÉTODO PARA GUARDAR ---
    public function store(Request $request)
    {
        // 1. Validamos datos básicos
        $validated = $request->validate([
            'account_id'      => 'nullable|exists:accounts,id',
            'total_counted'   => 'required|numeric',
            'system_balance'  => 'required|numeric',
            'difference'      => 'required|numeric',
            // Validamos que 'details' sea un array (coins y bills)
            'details'         => 'required|array',
        ]);

        try {
            DB::beginTransaction();

            // 2. Creamos el registro
            $cashCount = CashCount::create([
                'user_id'        => auth()->id(), // El usuario logueado
                'account_id'     => $validated['account_id'],
                'total_counted'  => $validated['total_counted'],
                'system_balance' => $validated['system_balance'],
                'difference'     => $validated['difference'],
                'details'        => $validated['details'], // Laravel lo pasa a JSON solo
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Arqueo guardado correctamente #' . $cashCount->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false, 
                'message' => 'Error al guardar: ' . $e->getMessage()
            ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use Illuminate\View\View;

class CashCountController extends Controller
{
    public function index(): View
    {
        // Pasamos las cuentas para poder comparar el saldo del sistema vs lo contado
        // Usamos el accessor 'current_balance' que ya creamos antes
        $accounts = Account::all();
        
        return view('cash_counts.index', compact('accounts'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AccountController extends Controller
{
    /**
     * Muestra un listado de las cuentas con su saldo actual.
     */
    public function index(): View
    {
        $accounts = Account::withSum([
                'transactions as total_ingresos' => fn($query) => $query->where('type', 'ingreso')
            ], 'amount')
            ->withSum([
                'transactions as total_gastos' => fn($query) => $query->where('type', 'gasto')
            ], 'amount')
            ->orderBy('name')
            ->get();
        
        return view('accounts.index', compact('accounts'));
    }

    /**
     * Muestra el formulario para crear una nueva cuenta.
     */
    public function create(): View
    {
        return view('accounts.create');
    }

    /**
     * Almacena la nueva cuenta en la base de datos.
     */
    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:accounts',
            'initial_balance' => 'required|numeric|min:0'
        ]);

        Account::create($validatedData);

        return redirect()->route('cuentas.index')
                         ->with('success', '¡Cuenta creada con éxito!');
    }

    /**
     * MShow (No lo usaremos por ahora)
     */
    public function show(Account $account)
    {
        // Laravel inyecta $account automáticamente gracias a .parameters()
    }

    /**
     * Muestra el formulario para editar una cuenta.
     */
    public function edit(Account $account): View
    {
        // Laravel inyecta $account automáticamente
        return view('accounts.edit', compact('account'));
    }

    /**
     * Actualiza la cuenta en la base de datos.
     */
    public function update(Request $request, Account $account): RedirectResponse
    {
        // Laravel inyecta $account automáticamente
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:accounts,name,' . $account->id,
            'initial_balance' => 'required|numeric|min:0'
        ]);

        $account->update($validatedData);

        return redirect()->route('cuentas.index')
                         ->with('success', '¡Cuenta actualizada con éxito!');
    }

    /**
     * Elimina la cuenta de la base de datos.
     */
    public function destroy(Account $account): RedirectResponse
    {
        // Laravel inyecta $account automáticamente
        
        // Protección (onDelete('cascade') es peligroso)
        if ($account->transactions()->count() > 0) {
            return redirect()->route('cuentas.index')
                             ->with('error', '¡No se puede eliminar una cuenta que ya tiene transacciones registradas!');
        }

        $account->delete();

        return redirect()->route('cuentas.index')
                         ->with('success', '¡Cuenta eliminada con éxito!');
    }
}

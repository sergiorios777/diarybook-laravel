<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Services\CategoryAssignmentEngine;

class TransactionController extends Controller
{
    /**
     * Muestra un listado de todas las transacciones.
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        // 1. Obtenemos las transacciones.
        // 2. Usamos latest() para ordenarlas, de la más nueva a la más antigua.
        // 3. ¡MUY IMPORTANTE! Usamos with(['account', 'category'])
        //    Esto es "Eager Loading" (Carga Anticipada).
        //    Carga la cuenta y la categoría de cada transacción en la misma
        //    consulta, evitando el "problema N+1" y haciendo la app muy rápida.
        $transactions = Transaction::with(['account', 'category'])
                                 ->latest() // Ordena por created_at descendente
                                 ->get();

        // 4. Retornamos la nueva vista (que crearemos a continuación)
        //    y le pasamos la variable $transactions.
        return view('transactions.index', compact('transactions'));
    }
    
    /**
     * Muestra el formulario para crear una nueva transacción.
     *
     * @return \Illuminate\View\View
     */

    public function create(): View
    {
        // 1. Necesitamos enviar las cuentas y categorías a la vista,
        //    para que el usuario pueda seleccionarlas en los menús desplegables.

        // 2. Obtenemos solo las categorías de tipo 'ingreso' y 'gasto' por separado
        //    para poder mostrarlas en selectores distintos si fuera necesario,
        //    o pasarlas ambas. Para este formulario, pasaremos todas.
        $accounts = Account::all();
        $categories = Category::orderBy('name')->get(); // Obtenemos todas, ordenadas por nombre

        // 3. Retornamos la vista (que crearemos en el Paso 4)
        //    y le pasamos los datos usando 'compact'.
        return view('transactions.create', compact('accounts', 'categories'));
    }

    /**
     * Almacena una nueva transacción en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'account_id'   => 'required|exists:accounts,id',
            'category_id'  => 'nullable|exists:categories,id',
            'type'         => 'required|in:ingreso,gasto',
            'amount'       => 'required|numeric', // ← Quitamos min:0.01 para permitir negativos
            'description'  => 'required|string|max:255',
            'date'         => 'required|date',
            'time'         => 'nullable|date_format:H:i',
        ]);

        // Convertimos automáticamente montos negativos → gasto y monto positivo
        $rawAmount = $request->input('amount');

        if ($rawAmount < 0) {
            $validated['amount'] = abs($rawAmount); // Guardamos siempre positivo
            $validated['type']    = 'gasto';
        } else {
            $validated['amount'] = $rawAmount;
            $validated['type']    = 'ingreso';
        }

        // Auto-asignación de categoría si no viene
        if (empty($validated['category_id'])) {
            $engine = new \App\Services\CategoryAssignmentEngine();
            $suggested = $engine->suggestCategory($validated['description'], $validated['type']);
            $validated['category_id'] = $suggested?->id;
        }

        $transaction = Transaction::create($validated);

        // Aprendizaje si el usuario corrigió la categoría
        if ($request->filled('category_id') && $request->category_id != $transaction->category_id) {
            $transaction->update(['category_id' => $request->category_id]);
            (new \App\Services\CategoryAssignmentEngine())->learnFromTransaction($transaction);
        }

        return redirect()
            ->route('transactions.create')
            ->with('success', '¡Transacción registrada con éxito!');
    }
}

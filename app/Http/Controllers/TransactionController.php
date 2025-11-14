<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

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
        // 1. Validación (¡Crucial!)
        //    Nos aseguramos de que los datos recibidos son correctos.
        $validatedData = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'category_id' => 'required|exists:categories,id',
            'type' => 'required|in:ingreso,gasto',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
            'date' => 'required|date',
        ]);

        // 2. Si la validación pasa, creamos la transacción
        //    Usamos $validatedData para asegurarnos de que solo guardamos
        //    lo que hemos validado.
        Transaction::create($validatedData);

        // 3. Redirigimos al usuario de vuelta al formulario
        //    con un mensaje de éxito.
        return redirect()->route('transactions.create')
                         ->with('success', '¡Transacción registrada con éxito!');
    }
}

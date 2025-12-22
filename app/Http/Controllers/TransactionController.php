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
    public function index(Request $request): View
    {
        // 1. Configuración de los filtros
        // Por defecto: HOY.
        $dateFrom = $request->input('date_from', now()->toDateString());
        $dateTo   = $request->input('date_to', now()->toDateString());

        $accountId = $request->input('account_id');
        $categoryId = $request->input('category_id');

        // 1. Obtenemos las transacciones.
        // 2. Usamos latest() para ordenarlas, de la más nueva a la más antigua.
        // 3. ¡MUY IMPORTANTE! Usamos with(['account', 'category'])
        //    Esto es "Eager Loading" (Carga Anticipada).
        //    Carga la cuenta y la categoría de cada transacción en la misma
        //    consulta, evitando el "problema N+1" y haciendo la app muy rápida.
        // 1. Primero por Fecha descendente (lo más reciente arriba)
        // 2. Luego por Hora descendente (para movimientos del mismo día)
        $query = Transaction::with(['account', 'category'])
                                ->whereDate('date', '>=', $dateFrom)
                                ->whereDate('date', '<=', $dateTo);

        if ($accountId) {
            $query->where('account_id', $accountId);
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        // 3. Ordenamos primero por fecha y luego por hora
        $query->orderBy('date', 'desc')->orderBy('time', 'desc');

        // Calculamos los totales de lo filtrado
        // clonamos la consulta antes de paginar para no afectar los resultados
        $resumenQuery = clone $query;
        $totalIngresos = $resumenQuery->where('type', 'ingreso')->excludeInternalTransfers()->sum('amount');

        // reiniciar el clon para evitar conflictos
        $resumenQuery = clone $query;
        $totalGastos   = $resumenQuery->where('type', 'gasto')->excludeInternalTransfers()->sum('amount');
        
        $balanceFiltrado       = $totalIngresos - $totalGastos;

        // 4. Paginamos los resultados (añadimos los filtros a la URL de paginación)
        $transactions = $query->paginate(15)->appends($request->all());

        // 5. Cargar listas para los filtros
        $accounts = Account::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        
        // 6. Retornamos la nueva vista (que crearemos a continuación)
        //    y le pasamos las variables $transactions, $accounts y $categories; y todas las demás variables de filtro y totales.
        return view('transactions.index', compact(
            'transactions', 
            'accounts', 
            'categories', 
            'dateFrom', 
            'dateTo', 
            'accountId', 
            'categoryId', 
            'totalIngresos', 
            'totalGastos', 
            'balanceFiltrado'));
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
        $validated['amount'] = abs($rawAmount);

        /*
        // Forzamos el tipo según la categoría seleccionada
        $category = Category::find($request->category_id);
        $validated['type']    = $category ? $category->type : $request->type;
        */

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

    /**
     * Muestra el formulario para editar una transacción existente.
     */
    public function edit(Transaction $transaction): View
    {
        $accounts = Account::all();
        $categories = Category::orderBy('name')->get();
        
        // Pasamos las reglas al frontend para que el autocompletado funcione 
        // incluso si el usuario decide cambiar la descripción durante la edición.
        // Mapeamos igual que hiciste en tu vista create.
        $rules = \App\Models\CategorizationRule::all()->map(function($r) {
            return [
                'keyword' => $r->keyword ? strtolower($r->keyword) : null,
                'regex'   => $r->regex,
                'cat_id'  => $r->category_id,
                'type'    => $r->type
            ];
        })->filter()->values();

        return view('transactions.edit', compact('transaction', 'accounts', 'categories', 'rules'));
    }

    /**
     * Actualiza la transacción en la base de datos.
     */
    public function update(Request $request, Transaction $transaction): RedirectResponse
    {
        // 1. Validación (Idéntica al store) [cite: 200]
        $validated = $request->validate([
            'account_id'   => 'required|exists:accounts,id',
            'category_id'  => 'nullable|exists:categories,id',
            'type'         => 'required|in:ingreso,gasto',
            'amount'       => 'required|numeric',
            'description'  => 'required|string|max:255',
            'date'         => 'required|date',
            'time'         => 'nullable|date_format:H:i', // [cite: 207]
        ]);

        // 2. Lógica de monto negativo/positivo (Idéntica al store) [cite: 210]
        $rawAmount = $request->input('amount');
        $validated['amount'] = abs($rawAmount);

        /*
        // Forzamos el tipo según la categoría seleccionada
        $category = Category::find($request->category_id);
        $validated['type']    = $category ? $category->type : $request->type;
        */

        // 3. Detectar si la categoría cambió para "aprender"
        // Guardamos el ID anterior antes de actualizar
        $oldCategoryId = $transaction->category_id;
        
        // Si no seleccionó nada, intentamos sugerir de nuevo (opcional en edición, pero útil)
        if (empty($validated['category_id'])) {
            $engine = new \App\Services\CategoryAssignmentEngine();
            $suggested = $engine->suggestCategory($validated['description'], $validated['type']);
            $validated['category_id'] = $suggested?->id;
        }

        // 4. Actualizar registro
        $transaction->update($validated);

        // 5. Aprendizaje: Si el usuario cambió la categoría manualmente respecto a lo que había antes
        //    o respecto a lo que se sugirió, reforzamos esa regla.
        if ($request->filled('category_id') && $validated['category_id'] != $oldCategoryId) {
            // Invocamos al motor para que aprenda de este cambio [cite: 228]
            (new \App\Services\CategoryAssignmentEngine())->learnFromTransaction($transaction);
        }

        // === RECUPERAR FILTROS SIN COLISIÓN ===
        $filtros = [];

        // Mapeamos manualmente los "keep_" a sus nombres originales
        if ($request->filled('keep_date_from'))  $filtros['date_from']   = $request->input('keep_date_from');
        if ($request->filled('keep_date_to'))    $filtros['date_to']     = $request->input('keep_date_to');
        if ($request->filled('keep_account_id')) $filtros['account_id']  = $request->input('keep_account_id');
        if ($request->filled('keep_category_id')) $filtros['category_id'] = $request->input('keep_category_id');

        return redirect()
            ->route('transactions.index', $filtros) // Volvemos al listado y enviamos los valores de los filtros
            ->with('success', '¡Transacción actualizada correctamente!');
    }

    /**
     * Elimina una transacción de la base de datos.
     */
    public function destroy(Transaction $transaction): RedirectResponse
    {
        // Simplemente eliminamos el registro
        $transaction->delete();

        // Redirigimos al historial con un mensaje de éxito
        return redirect()->route('transactions.index')
                         ->with('success', '¡Transacción eliminada correctamente!');
    }

    /**
     * Muestra el formulario para importar transacciones desde CSV.
     */
    public function showImportForm(): View
    {
        return view('transactions.import');
    }

    /**
     * Procesa el archivo CSV cargado.
     */
    public function processImport(Request $request): RedirectResponse
    {
        // 1. Validar que se subió un archivo
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048', // Máx 2MB
        ]);

        $path = $request->file('file')->getRealPath();
        $data = array_map('str_getcsv', file($path));
        
        // Eliminamos la primera fila (Cabeceras)
        // SI tu archivo NO tiene cabeceras, comenta esta línea.
        array_shift($data);

        $count = 0;
        $ignoredCategory = 0;
        $ignoredVoid = 0;

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            foreach ($data as $row) {
                // Estructura esperada del CSV por columnas (índices del array):
                // 0: Fecha (YYYY-MM-DD), 1: Hora (HH:MM), 2: Descripción, 
                // 3: Monto, 4: Account_ID, 5: Category_ID

                // Validación básica de estructura de fila (mínimo 6 columnas)
                if (count($row) < 6) continue;

                // --- NUEVA LÓGICA: FILTRO DE ANULADOS ---
                // Verificamos si existe la columna 6 y si su contenido es "ANULADO"
                if (isset($row[6])) {
                    $estado = strtoupper(trim($row[6])); // Convertimos a mayúsculas para evitar errores
                    if ($estado === 'ANULADO') {
                        $ignoredVoid++;
                        continue; // Saltamos al siguiente registro sin guardar nada
                    }
                }
                // ----------------------------------------------------

                $categoryId = trim($row[5]);
                
                // REGLA DE NEGOCIO: Solo aceptamos Categoría ID 6 (Ventas)
                if ($categoryId != 6) {
                    $ignoredCategory++;
                    continue; // Saltamos esta fila
                }

                // --- CORRECCIÓN DE FECHA (NUEVO) ---
                $dateRaw = trim($row[0]);
                $finalDate = $dateRaw;

                // Si la fecha viene con barras (ej: 19/12/2025), la convertimos
                if (str_contains($dateRaw, '/')) {
                    try {
                        // Creamos un objeto fecha desde el formato Día/Mes/Año
                        $dateObj = \Carbon\Carbon::createFromFormat('d/m/Y', $dateRaw);
                        // Lo transformamos al formato que le gusta a MySQL: Año-Mes-Día
                        $finalDate = $dateObj->format('Y-m-d');
                    } catch (\Exception $e) {
                        // Si falla la conversión, dejamos el dato original para que salte el error
                        // o podrías poner 'continue' para saltar la fila errónea.
                    }
                }
                // -----------------------------------------------

                // Limpieza de datos
                $amount = abs((float) str_replace(',', '', $row[3])); // Convertir a positivo y limpiar comas
                $accountId = trim($row[4]);

                // Crear Transacción
                Transaction::create([
                    'date'        => $finalDate,
                    'time'        => trim($row[1]) ?: '00:00', // Hora por defecto si está vacía
                    'description' => trim($row[2]),
                    'amount'      => $amount,
                    'account_id'  => $accountId,
                    'category_id' => 6,        // Forzamos ID 6
                    'type'        => 'ingreso' // Forzamos 'ingreso' porque es Venta
                ]);

                $count++;
            }

            \Illuminate\Support\Facades\DB::commit();

            $message = "Se importaron $count ventas exitosamente.";
            if ($ignoredVoid > 0) {
                $message .= " Se omitieron $ignoredVoid filas marcadas como ANULADO.";
            }
            if ($ignoredCategory > 0) {
                $message .= " Se ignoraron $ignoredCategory filas por no ser Categoría 6.";
            }

            return redirect()->route('transactions.index')->with('success', $message);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->withErrors(['file' => 'Error al procesar el archivo: ' . $e->getMessage()]);
        }
    }

}

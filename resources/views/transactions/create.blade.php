<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Transacción</title>
    <style>
        /* Estilos base (Mismos de versiones anteriores) */
        body { font-family: Arial, sans-serif; margin: 0; background-color: #f4f7f6; }
        .navbar { background-color: #fff; padding: 15px 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .navbar a { text-decoration: none; color: #333; font-weight: bold; margin: 0 10px; }
        .form-container { max-width: 600px; margin: 0 auto; padding: 20px; background-color: #fff; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        h1 { text-align: center; color: #333; margin-top: 0; }
        .form-row { display: flex; gap: 15px; margin-bottom: 15px; }
        .form-group { margin-bottom: 15px; flex: 1; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #555; }
        input, select { width: 100%; padding: 10px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; font-size: 16px; }
        input:focus, select:focus { border-color: #007bff; outline: none; }
        button { width: 100%; background-color: #007bff; color: white; padding: 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; font-weight: bold; transition: background 0.3s; }
        button:hover { background-color: #0056b3; }
        .alert-success { background-color: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 15px; text-align: center;}
        /* Estilo para feedback visual */
        .auto-selected { border: 2px solid #28a745; background-color: #e8f5e9; }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="{{ route('dashboard') }}"><strong>Mi Dashboard</strong></a>
        <div><a href="{{ route('transactions.index') }}">Volver al Historial</a></div>
    </nav>

    <div class="form-container">
        <h1>Registrar Movimiento</h1>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('transactions.store') }}" method="POST">
            @csrf

            <div class="form-row">
                <div class="form-group">
                    <label for="date">Fecha:</label>
                    <input type="date" id="date" name="date" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="form-group">
                    <label for="time">Hora:</label>
                    <input type="time" id="time" name="time" value="{{ date('H:i') }}" required>
                </div>
            </div>

            <div class="form-group">
                <label for="description">Descripción:</label>
                <input type="text" id="description" name="description" placeholder="Escribe aquí..." required autofocus autocomplete="off">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="amount">Monto:</label>
                    <input type="number" id="amount" name="amount" step="0.01" placeholder="0.00" required style="font-size: 1.2em; font-weight: bold;">
                    <small style="color: #666; font-size: 0.8em;">Use negativo (-) para gastos</small>
                </div>
                <div class="form-group">
                    <label for="account_id">Cuenta:</label>
                    <select id="account_id" name="account_id" required>
                        <option value="">-- Seleccione --</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}">{{ $account->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group" style="flex: 2;">
                    <label for="category_id">Categoría (Auto-asignada):</label>
                    <select id="category_id" name="category_id" required>
                        <option value="">-- Seleccione Categoría --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" data-type="{{ $category->type }}">
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group" style="flex: 1;">
                    <label for="type">Tipo:</label>
                    <select id="type" name="type" required>
                        <option value="">-- Tipo --</option>
                        <option value="ingreso">Ingreso</option>
                        <option value="gasto">Gasto</option>
                    </select>
                </div>
            </div>

            <div class="form-group" style="margin-top: 20px;">
                <button type="submit">Guardar Transacción</button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- CONFIGURACIÓN: MAPEO DE IDs ---
            // IMPORTANTE: Cambia los números de la derecha por los IDs REALES de tus categorías en la base de datos.
            const categoryMap = {
                1: 1,  // Caso 1: Ventas IF (ID Real)
                2: 2,  // Caso 2: Otros Ingresos IF (ID Real)
                3: 3,  // Caso 3: Transferencia Ctas (ID Real)
                4: 4,  // Caso 4: Compras IF (ID Real)
                5: 5,  // Caso 5: Préstamo (Ingreso) (ID Real)
                6: 6,  // Caso 6: Pago Préstamo (Gasto) (ID Real)
                7: 7,  // Caso 7: Otros gastos IF (ID Real)
                8: 8,  // Caso 8: Otros gastos (NO IF) (ID Real)
                9: 9,  // Caso 9: Otros Ingresos Generales (ID Real)
                10: 10 // Caso 10: Otros/Neutro (ID Real)
            };

            const descInput = document.getElementById('description');
            const amountInput = document.getElementById('amount');
            const categorySelect = document.getElementById('category_id');
            const typeSelect = document.getElementById('type');

            function updateTypeFromCategory() {
                const selectedOption = categorySelect.options[categorySelect.selectedIndex];
                const type = selectedOption.getAttribute('data-type');
                if (type) typeSelect.value = type;
            }

            function applyRules() {
                const text = descInput.value.toLowerCase().trim();
                const amountVal = parseFloat(amountInput.value); // Puede ser NaN si está vacío

                // Si no hay texto, no hacemos nada
                if (text.length < 2) return;

                let assignedCatId = null;

                // --- REGLAS (CASOS 1 - 10) ---
                
                // CASO 3: Contiene "T.CTAS" (Prioridad alta porque aplica a cualquier monto)
                if (text.includes('t.ctas')) {
                    assignedCatId = categoryMap[3];
                }
                else if (amountVal > 0) {
                    // --- RAMA: MONTO POSITIVO (> 0) ---

                    // Caso 1: Empieza con "venta if"
                    if (text.startsWith('venta if')) {
                        assignedCatId = categoryMap[1];
                    }
                    // Caso 5: Contiene "ptmo" o "préstamo"
                    else if (text.includes('ptmo') || text.includes('préstamo')) {
                        assignedCatId = categoryMap[5];
                    }
                    // Caso 2: Contiene "if" (pero no es Venta IF, ya evaluado arriba)
                    else if (text.includes('if')) {
                        assignedCatId = categoryMap[2];
                    }
                    // Caso 9: Cualquier otro valor positivo
                    else {
                        assignedCatId = categoryMap[9];
                    }
                } 
                else if (amountVal < 0) {
                    // --- RAMA: MONTO NEGATIVO (< 0) ---

                    // Caso 4: Contiene "compra" ... y luego ... "if"
                    // Usamos Regex para asegurar el orden y que ambos existan
                    if (/compra.*if/.test(text)) {
                        assignedCatId = categoryMap[4];
                    }
                    // Caso 6: Contiene "ptmo" o "préstamo"
                    else if (text.includes('ptmo') || text.includes('préstamo')) {
                        assignedCatId = categoryMap[6];
                    }
                    // Caso 7: Contiene "if" (Excluye 4 y 6 ya evaluados arriba)
                    else if (text.includes('if')) {
                        assignedCatId = categoryMap[7];
                    }
                    // Caso 8: No contiene "if"
                    else {
                        assignedCatId = categoryMap[8];
                    }
                }
                else {
                     // --- RAMA: MONTO CERO O VACÍO ---
                     // (Opcional: Si quieres que asigne algo cuando el monto es 0)
                     if (text.includes('t.ctas')) assignedCatId = categoryMap[3]; // Reafirmamos caso 3
                     // Caso 10: Default para <= 0 o indefinido si no es T.CTAS
                     else assignedCatId = categoryMap[10]; 
                }

                // --- APLICAR CAMBIOS ---
                if (assignedCatId) {
                    categorySelect.value = assignedCatId;
                    updateTypeFromCategory();
                    
                    // Feedback visual temporal
                    categorySelect.classList.add('auto-selected');
                    setTimeout(() => categorySelect.classList.remove('auto-selected'), 500);
                }
            }

            // Escuchamos cambios en Descripción Y Monto
            descInput.addEventListener('input', applyRules);
            amountInput.addEventListener('input', applyRules);
            categorySelect.addEventListener('change', updateTypeFromCategory);
        });
    </script>

</body>
</html>
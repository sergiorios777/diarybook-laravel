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
        .auto-selected {
            border: 2px solid #28a745 !important;
            background-color: #f8fff9 !important;
        }
        #auto-label {
            font-size: 0.9em;
            transition: all 0.3s;
        }
    </style>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.14.9/cdn.min.js" defer></script>
    
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
                    <small style="color: #666; font-size: 0.85em;">
                        ✅ Puedes usar monto negativo para gastos
                    </small>
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
                    <label for="category_id">Categoría <span id="auto-label" style="color:#28a745;font-weight:bold;"></span>:</label>
                    <select id="category_id" name="category_id">
                        <option value="">-- Seleccione Categoría --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" 
                                    data-type="{{ $category->type }}">
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
    document.addEventListener('DOMContentLoaded', function () {
        const descriptionInput = document.getElementById('description');
        const categorySelect   = document.getElementById('category_id');
        const typeSelect       = document.getElementById('type');
        const amountInput      = document.getElementById('amount');
        const autoLabel        = document.getElementById('auto-label');

        // Reglas del motor inteligente
        const rules = {!! json_encode(
            \App\Models\CategorizationRule::all()->map(function($r) {
                return [
                    'keyword' => $r->keyword ? strtolower($r->keyword) : null,
                    'regex'   => $r->regex,
                    'cat_id'  => $r->category_id,
                    'type'    => $r->type
                ];
            })->filter()->values()
        ) !!};

        // Función principal de sugerencia
        function suggest() {
            if (!descriptionInput.value.trim()) {
                resetAuto();
                return;
            }

            const desc = descriptionInput.value.toLowerCase();
            const rawAmount = parseFloat(amountInput.value) || 0;
            const inferredType = rawAmount < 0 ? 'gasto' : 'ingreso';

            let matched = false;

            for (const rule of rules) {
                if (rule.type !== inferredType && typeSelect.value !== '') continue;

                const matchKeyword = rule.keyword && desc.includes(rule.keyword);
                const matchRegex   = rule.regex && new RegExp(rule.regex, 'i').test(desc);

                if (matchKeyword || matchRegex) {
                    const option = categorySelect.querySelector(`option[value="${rule.cat_id}"]`);
                    if (option) {
                        categorySelect.value = rule.cat_id;
                        autoLabel.textContent = ' (auto)';
                        categorySelect.classList.add('auto-selected');
                        syncTypeFromCategory();  // ← Aquí sincroniza el tipo
                        matched = true;
                        break;
                    }
                }
            }

            if (!matched) {
                resetAuto();
            }
        }

        // Nueva función: sincroniza Tipo según la categoría seleccionada
        function syncTypeFromCategory() {
            const selectedOption = categorySelect.options[categorySelect.selectedIndex];
            if (selectedOption && selectedOption.value !== '') {
                const categoryType = selectedOption.getAttribute('data-type');
                if (categoryType === 'ingreso' || categoryType === 'gasto') {
                    typeSelect.value = categoryType;
                }
            }
        }

        function resetAuto() {
            autoLabel.textContent = '';
            categorySelect.classList.remove('auto-selected');
        }

        // Eventos
        descriptionInput?.addEventListener('input', suggest);
        amountInput?.addEventListener('input', suggest);
        amountInput?.addEventListener('change', suggest);

        // Cuando el usuario cambia manualmente la categoría → actualiza tipo
        categorySelect?.addEventListener('change', function() {
            syncTypeFromCategory();
            if (categorySelect.value !== '') {
                autoLabel.textContent = ' (seleccionada)';
                categorySelect.classList.add('auto-selected');
            } else {
                resetAuto();
            }
        });

        // Si cambia manualmente el tipo, no lo sobreescribimos (respeta decisión del usuario)
        typeSelect?.addEventListener('change', function() {
            // No hacemos nada → el usuario manda
        });

        // Ejecutar al cargar por si hay datos precargados
        suggest();
    });
    </script>

</body>
</html>
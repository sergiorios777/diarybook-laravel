<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Transacción</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; }
        form { max-width: 500px; margin: 0 auto; padding: 20px; background-color: #fff; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        div { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, select { width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        button { background-color: #007bff; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; }
        .alert-success { background-color: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
    </style>
</head>
<body>

    <h1>Registrar Nueva Ocurrencia (Transacción)</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('transactions.store') }}" method="POST">
        @csrf

        <div>
            <label for="date">Fecha:</label>
            <input type="date" id="date" name="date" value="{{ date('Y-m-d') }}" required>
        </div>

        <div>
            <label for="account_id">Cuenta (Dónde/De dónde):</label>
            <select id="account_id" name="account_id" required>
                <option value="">-- Seleccione una cuenta --</option>
                @foreach($accounts as $account)
                    <option value="{{ $account->id }}">{{ $account->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="category_id">Categoría (En qué):</label>
            <select id="category_id" name="category_id" required>
                <option value="">-- Seleccione una categoría --</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">
                        {{ $category->name }} ({{ $category->type }})
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="type">Tipo de Movimiento:</label>
            <select id="type" name="type" required>
                <option value="">-- Seleccione el tipo --</option>
                <option value="ingreso">Ingreso</option>
                <option value="gasto">Gasto</option>
            </select>
            </div>

        <div>
            <label for="description">Descripción:</label>
            <input type="text" id="description" name="description" placeholder="Ej: Venta del día, Pago de luz" required>
        </div>

        <div>
            <label for="amount">Monto:</label>
            <input type="number" id="amount" name="amount" step="0.01" min="0.01" placeholder="Ej: 150.50" required>
        </div>

        <div>
            <button type="submit">Guardar Transacción</button>
        </div>
    </form>

</body>
</html>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Cuenta</title>
    <style>
        /* (Tus estilos de formulario) */
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; }
        form { max-width: 500px; margin: 0 auto; padding: 20px; background-color: #fff; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        div { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input { width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        button { background-color: #007bff; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>
    <h1>Editar Cuenta: {{ $account->name }}</h1>

    <form action="{{ route('cuentas.update', $account) }}" method="POST">
        @csrf
        @method('PUT')
        <div>
            <label for="name">Nombre de la Cuenta:</label>
            <input type="text" id="name" name="name" value="{{ old('name', $account->name) }}" required>
        </div>
        <div>
            <label for="initial_balance">Saldo Inicial:</label>
            <input type="number" id="initial_balance" name="initial_balance" step="0.01" min="0" value="{{ old('initial_balance', $account->initial_balance) }}" required>
            <small>Nota: Cambiar esto afectará el cálculo del saldo actual.</small>
        </div>
        <div>
            <button type="submit">Actualizar Cuenta</button>
        </div>
    </form>
</body>
</html>
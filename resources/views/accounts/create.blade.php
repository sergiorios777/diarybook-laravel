<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Cuenta</title>
    <style>
        /* (Tus estilos de formulario) */
        body { font-family: Arial, sans-serif; background-color: #f4f7f6; margin: 0; }
        .navbar { background-color: #fff; padding: 15px 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; }
        .navbar a { text-decoration: none; color: #333; font-weight: bold; margin: 0 10px; }
        .container { max-width: 600px; margin: 20px auto; padding: 20px; }
        form { max-width: 500px; margin: 0 auto; padding: 20px; background-color: #fff; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        div { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input { width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        button { background-color: #007bff; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="{{ route('dashboard') }}"><strong>Mi Dashboard</strong></a>
        <div>
            <a href="{{ route('cuentas.index') }}">Volver</a>
        </div>
    </nav>

    <div class="container">
        <h1>Crear Nueva Cuenta (Banco, Caja, etc.)</h1>

        <form action="{{ route('cuentas.store') }}" method="POST">
            @csrf
            <div>
                <label for="name">Nombre de la Cuenta:</label>
                <input type="text" id="name" name="name" placeholder="Ej: Caja Principal, Banco XYZ" required>
            </div>
            <div>
                <label for="initial_balance">Saldo Inicial:</label>
                <input type="number" id="initial_balance" name="initial_balance" step="0.01" min="0" value="0.00" required>
            </div>
            <div>
                <button type="submit">Guardar Cuenta</button>
            </div>
        </form>
    </div>

</body>
</html>
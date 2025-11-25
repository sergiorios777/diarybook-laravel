<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Categoría</title>
    <style>
        /* (Mismos estilos del formulario de transacciones) */
        body { font-family: Arial, sans-serif; background-color: #f4f6f9; margin: 0; }
        .navbar { background-color: #fff; padding: 15px 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; }
        .navbar a { text-decoration: none; color: #333; font-weight: bold; margin: 0 10px; }
        .content { max-width: 800px; margin: 20px auto; padding: 20px; background-color: #fff; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        form { max-width: 500px; margin: 0 auto; padding: 20px; background-color: #fff; border: 1px solid #ccc; border-radius: 8px; }
        div { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, select { width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        button { background-color: #007bff; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; }
        .titulo-pagina {
            max-width: 500px;
            margin: 20px auto;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="{{ route('dashboard') }}"><strong>Mi Dashboard</strong></a>
        <div>
            <a href="{{ route('categorias.index') }}">Volver</a>
        </div>
    </nav>

    <div class="content">
        <h1 class="titulo-pagina">Crear Nueva Categoría</h1>

        <form action="{{ route('categorias.store') }}" method="POST">
            @csrf <div>
                <label for="name">Nombre de la Categoría:</label>
                <input type="text" id="name" name="name" placeholder="Ej: Ventas, Servicios Públicos" required>
            </div>

            <div>
                <label for="type">Tipo:</label>
                <select id="type" name="type" required>
                    <option value="">-- Seleccione un tipo --</option>
                    <option value="ingreso">Ingreso</option>
                    <option value="gasto">Gasto</option>
                </select>
            </div>

            <div>
                <label for="parent_id">Categoría Padre (Opcional):</label>
                <select id="parent_id" name="parent_id">
                    <option value="">-- Sin Categoría Padre --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <button type="submit">Guardar Categoría</button>
            </div>
        </form>
    </div>

</body>
</html>
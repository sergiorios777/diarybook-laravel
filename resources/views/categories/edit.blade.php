<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Categoría</title>
    <style>
        /* (Mismos estilos del formulario de creación) */
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; }
        form { max-width: 500px; margin: 0 auto; padding: 20px; background-color: #fff; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        div { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, select { width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        button { background-color: #007bff; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>

    <h1>Editar Categoría: {{ $category->name }}</h1>

    <form action="{{ route('categorias.update', $category) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div>
            <label for="name">Nombre de la Categoría:</label>
            <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}" required>
        </div>

        <div>
            <label for="type">Tipo:</label>
            <select id="type" name="type" required>
                <option value="ingreso" {{ $category->type == 'ingreso' ? 'selected' : '' }}>Ingreso</option>
                <option value="gasto" {{ $category->type == 'gasto' ? 'selected' : '' }}>Gasto</option>
            </select>
        </div>

        <div>
            <label for="parent_id">Categoría Padre (Opcional):</label>
            <select id="parent_id" name="parent_id">
                <option value="">-- Sin Categoría Padre --</option>
                
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ $category->parent_id == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <button type="submit">Actualizar Categoría</button>
        </div>
    </form>

</body>
</html>
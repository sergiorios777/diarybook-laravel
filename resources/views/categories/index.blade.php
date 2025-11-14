<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Categorías</title>
    <style>
        /* (Usaremos los mismos estilos del dashboard/transacciones para consistencia) */
        body { font-family: Arial, sans-serif; margin: 0; background-color: #f4f7f6; }
        .navbar { background-color: #fff; padding: 15px 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; }
        .navbar a { text-decoration: none; color: #333; font-weight: bold; margin: 0 10px; }
        .navbar a.btn { background-color: #007bff; color: white; padding: 8px 12px; border-radius: 4px; }
        .container { max-width: 800px; margin: 20px auto; padding: 20px; background-color: #fff; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .btn { background-color: #007bff; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; }
        .alert-success { background-color: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        ul { list-style-type: none; padding-left: 0; }
        li { background-color: #f9f9f9; padding: 10px 15px; border: 1px solid #ddd; margin-bottom: 5px; border-radius: 4px; display: flex; justify-content: space-between; align-items: center; }
        li ul { margin-top: 10px; padding-left: 20px; }
        li ul li { background-color: #fff; }
        .badge { font-size: 0.8em; padding: 3px 8px; border-radius: 10px; color: white; }
        .badge-ingreso { background-color: #28a745; }
        .badge-gasto { background-color: #dc3545; }

        .btn-edit {
            font-size: 0.9em;
            color: #007bff;
            text-decoration: none;
            margin-right: 10px;
        }
        .btn-delete {
            font-size: 0.9em;
            color: #dc3545;
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
            margin: 0;
        }
    </style>
</head>
<body>
    
    <nav class="navbar">
        <a href="{{ route('dashboard') }}"><strong>Mi Dashboard</strong></a>
        <div>
            <a href="{{ route('reports.index') }}">Informes</a>
            <a href="{{ route('transactions.index') }}">Historial</a>
            <a href="{{ route('categorias.index') }}">Categorías</a>
            <a href="{{ route('cuentas.index') }}">Cuentas</a>
            <a href="{{ route('transactions.create') }}" class="btn">+ Nueva Transacción</a>
        </div>
    </nav>

    <div class="container">
        <h1>Gestión de Categorías</h1>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <a href="{{ route('categorias.create') }}" class="btn" style="margin-bottom: 20px;">
            + Crear Nueva Categoría
        </a>

        <ul>
            @forelse($categories as $category)
                <li>
                    <div>
                        <strong>{{ $category->name }}</strong>
                        <span class="badge {{ $category->type == 'ingreso' ? 'badge-ingreso' : 'badge-gasto' }}">
                            {{ $category->type }}
                        </span>
                    </div>
                    <div>
                        <a href="{{ route('categorias.edit', $category->id) }}" class="btn-edit">
                            Editar
                        </a>
                        <form action="{{ route('categorias.destroy', $category->id) }}" method="POST" style="display: inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-delete" onclick="return confirm('¿Estás seguro de que quieres eliminar esta categoría?')">
                                Eliminar
                            </button>
                        </form>
                    </div>
                </li>
                
                @if($category->children->isNotEmpty())
                    <ul>
                        @foreach($category->children as $child)
                        <li>
                            <div>
                                ↳ {{ $child->name }}
                                <span class="badge {{ $child->type == 'ingreso' ? 'badge-ingreso' : 'badge-gasto' }}">
                                    {{ $child->type }}
                                </span>
                            </div>
                            <div>
                                <a href="{{ route('categorias.edit', $child->id) }}" class="btn-edit">
                                    Editar
                                </a>
                                <form action="{{ route('categorias.destroy', $child->id) }}" method="POST" style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete" onclick="return confirm('¿Estás seguro de que quieres eliminar esta categoría?')">
                                        Eliminar
                                    </button>
                                </form>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                @endif
            @empty
                <li>No hay categorías creadas.</li>
            @endforelse
        </ul>
    </div>

</body>
</html>
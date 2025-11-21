<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Transacciones</title>
    <style>
        /* --- ESTILOS GENERALES --- */
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            margin: 0; 
            background-color: #f0f2f5; 
            color: #333; 
        }

        /* --- NAVBAR MEJORADO --- */
        .navbar { 
            background-color: #fff; 
            padding: 0 20px; 
            height: 60px; /* Altura fija para mejor alineación */
            box-shadow: 0 2px 4px rgba(0,0,0,0.08); 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
        }
        .navbar a { 
            text-decoration: none; 
            color: #555; 
            font-weight: 600; 
            margin: 0 10px; 
            font-size: 0.95rem;
            transition: color 0.2s;
        }
        .navbar a:hover { color: #007bff; }
        .navbar-links { display: flex; align-items: center; }
        .navbar a.btn-nav { 
            background-color: #007bff; 
            color: white; 
            padding: 8px 15px; 
            border-radius: 20px; 
            font-size: 0.9rem;
        }
        .navbar a.btn-nav:hover { background-color: #0056b3; color: white; }

        /* --- CONTENEDOR --- */
        .container { 
            max-width: 1100px; 
            margin: 30px auto; 
            padding: 25px; 
            background-color: #fff; 
            border-radius: 12px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.05); 
        }
        
        .header-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
        }
        h1 { margin: 0; font-size: 1.5rem; color: #2c3e50; }

        /* --- TABLA PROFESIONAL --- */
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 10px; 
        }
        
        /* Encabezados */
        thead th { 
            background-color: #343a40; 
            color: white; 
            padding: 12px 15px; 
            text-align: left; 
            font-size: 0.85rem; 
            text-transform: uppercase; 
            letter-spacing: 0.5px;
            font-weight: 600;
        }
        thead th:first-child { border-top-left-radius: 6px; }
        thead th:last-child { border-top-right-radius: 6px; }

        /* Celdas */
        tbody td { 
            padding: 10px 15px; 
            border-bottom: 1px solid #f1f1f1; 
            color: #444;
            vertical-align: middle;
            /* Aquí reducimos el tamaño de fuente general */
            font-size: 14px; 
        }

        /* Efecto Hover en filas */
        tbody tr:hover { background-color: #f8f9fa; }

        /* --- COLUMNAS ESPECÍFICAS --- */
        /* Hora: Un poco más pequeña y gris */
        .time-col { 
            color: #6c757d; 
            font-size: 13px; 
            font-family: 'Consolas', monospace; /* Alineación perfecta de números */
        }
        
        /* Montos: Fuente monoespaciada para alinear cifras */
        .amount-col {
            font-family: 'Consolas', monospace;
            font-size: 14px;
            font-weight: 700;
            text-align: right;  /* Centrado horizontal */
        }
        .monto-ingreso { color: #28a745; }
        .monto-gasto { color: #dc3545; }

        /* --- BOTONES DE ACCIÓN (La solución a las dos filas) --- */
        .actions-cell {
            display: flex;           /* Flexbox es la clave */
            gap: 8px;                /* Espacio entre botones */
            align-items: center;     /* Centrado vertical */
            white-space: nowrap;     /* Evita que salten de línea */
        }

        /* Estilos de botones pequeños */
        .btn-action {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            text-decoration: none;
            border: 1px solid transparent;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-edit {
            background-color: #e7f1ff;
            color: #0d6efd;
            border-color: #cff4fc;
        }
        .btn-edit:hover { background-color: #cfe2ff; }

        .btn-delete {
            background-color: #ffebe9;
            color: #dc3545;
            border-color: #fecaca;
        }
        .btn-delete:hover { background-color: #f8d7da; }

        /* Botón Principal */
        .btn-primary { 
            background-color: #007bff; 
            color: white; 
            padding: 10px 20px; 
            border: none; 
            border-radius: 6px; 
            cursor: pointer; 
            text-decoration: none; 
            display: inline-block; 
            font-size: 0.9rem;
            box-shadow: 0 2px 4px rgba(0,123,255,0.2);
        }
        .btn-primary:hover { background-color: #0056b3; }

        /* --- PAGINACIÓN PERSONALIZADA --- */
        .pagination-wrapper {
            margin-top: 20px;
            display: flex;
            justify-content: center;
        }
        /* Ocultamos los textos largos que genera Laravel por defecto */
        .pagination-wrapper nav div:first-child { display: none; } 
        .pagination-wrapper nav div:last-child { 
            display: flex; 
            gap: 5px; 
            box-shadow: none !important;
        }
        
        /* Estilo de los botones de página */
        .pagination-wrapper a, 
        .pagination-wrapper span {
            padding: 8px 12px;
            border: 1px solid #ddd;
            color: #007bff;
            text-decoration: none;
            border-radius: 4px;
            font-size: 13px;
            font-weight: 700;
            background: white;
        }
        
        /* Botón activo */
        .pagination-wrapper span[aria-current="page"] {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }
        
        /* Hover */
        .pagination-wrapper a:hover {
            background-color: #e9ecef;
        }
        
        /* Flechas SVG pequeñas */
        .pagination-wrapper svg { width: 16px; height: 16px; vertical-align: middle; }

    </style>
</head>
<body>
    
    <nav class="navbar">
        <a href="{{ route('dashboard') }}" style="font-size: 1.1rem;"><strong>Mi Dashboard</strong></a>
        <div class="navbar-links">
            <a href="{{ route('reports.expenses') }}">Gastos</a>
            <a href="{{ route('reports.income') }}">Ingresos</a>
            <a href="{{ route('reports.detailed') }}" style="color:#007bff;">Detallado</a>
            <a href="{{ route('transactions.index') }}" style="color: #007bff;">Historial</a>
            <a href="{{ route('categorias.index') }}">Categorías</a>
            <a href="{{ route('cuentas.index') }}">Cuentas</a>
            <a href="{{ route('transactions.create') }}" class="btn-nav">+ Nuevo</a>
        </div>
    </nav>

    <div class="container">
        
        <div class="header-flex">
            <h1>Historial de Movimientos</h1>
            <a href="{{ route('transactions.create') }}" class="btn-primary">
                + Registrar Transacción
            </a>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 25px;">Fecha</th>
                    <th style="width: 20px;">Hora</th>
                    <th>Descripción</th>
                    <th style="width: 90px;">Cuenta</th>
                    <th style="width: 120px;">Categoría</th>
                    <th style="width: 90px;">Monto</th>
                    <th style="width: 140px;">Acciones</th> </tr>
            </thead>
            <tbody>
                @if($transactions->isEmpty())
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 30px; color: #888;">
                            No hay transacciones registradas todavía.
                        </td>
                    </tr>
                @else
                    @foreach($transactions as $transaction)
                        <tr>
                            <td style="font-weight: 500;font-size: 13px;">
                                {{ \Carbon\Carbon::parse($transaction->date)->format('d/m/Y') }}
                            </td>
                            
                            <td class="time-col">
                                {{ $transaction->time ? \Carbon\Carbon::parse($transaction->time)->format('H:i') : '--:--' }}
                            </td>

                            <td>{{ $transaction->description }}</td>
                            
                            <td>
                                <span style="background: #eef2f7; padding: 2px 6px; border-radius: 4px; font-size: 12px; color: #555;">
                                    {{ $transaction->account->name }}
                                </span>
                            </td>
                            
                            <td style="font-weight: 500; font-size: 12px;">
                                {{ $transaction->category ? $transaction->category->name : '-' }}
                            </td>

                            <td class="amount-col {{ $transaction->type == 'ingreso' ? 'monto-ingreso' : 'monto-gasto' }}">
                                {{ $transaction->type == 'ingreso' ? '+' : '-' }}
                                ${{ number_format($transaction->amount, 2) }}
                            </td>
                            
                            <td>
                                <div class="actions-cell">
                                    <a href="{{ route('transactions.edit', $transaction) }}" class="btn-action btn-edit">
                                        Editar
                                    </a>

                                    <form action="{{ route('transactions.destroy', $transaction) }}" method="POST" style="margin:0;">
                                        @csrf
                                        @method('DELETE') 
                                        <button type="submit" 
                                                class="btn-action btn-delete"
                                                onclick="return confirm('¿Estás seguro de eliminar esta transacción?')">
                                                Borrar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>

        <div class="pagination-wrapper">
            {{ $transactions->links() }}
        </div>

    </div>

</body>
</html>
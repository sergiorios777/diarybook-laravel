<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe de Ingresos</title>
    <style>
        /* (Mismos estilos que reports.expenses) */
        body { font-family: Arial, sans-serif; margin: 0; background-color: #f4f7f6; }
        .navbar { background-color: #fff; padding: 15px 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; }
        .navbar a { text-decoration: none; color: #333; font-weight: bold; margin: 0 10px; }
        .navbar a.btn { background-color: #007bff; color: white; padding: 8px 12px; border-radius: 4px; }
        .container { max-width: 800px; margin: 20px auto; padding: 20px; }
        .filter-form { background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin-bottom: 20px; display: flex; gap: 15px; align-items: flex-end; }
        .filter-form div { flex: 1; }
        .filter-form label { display: block; margin-bottom: 5px; font-weight: bold; }
        .filter-form input { width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        .filter-form button { background-color: #28a745; color: white; padding: 8px 15px; border: none; border-radius: 4px; cursor: pointer; height: 36px; }

        /* ¡NUEVA CLASE! Le damos color verde a la tarjeta de ingresos */
        .card-total-ingreso { background-color: #28a745; color: white; padding: 20px; border-radius: 8px; text-align: center; margin-bottom: 20px; }
        .card-total-ingreso h2 { margin: 0 0 10px 0; font-size: 1rem; text-transform: uppercase; }
        .card-total-ingreso .amount { font-size: 2rem; font-weight: bold; }

        .report-table { background-color: #fff; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); overflow: hidden; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 15px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background-color: #f9f9f9; }
        td:last-child { font-weight: bold; text-align: right; }

        /* ESTILOS DE IMPRESIÓN A4 VERTICAL */
        @media print {
            @page { size: A4 portrait; margin: 1.5cm; }
            body { background: white; -webkit-print-color-adjust: exact; font-size: 12pt; }
            
            /* Ocultar elementos de navegación y formulario */
            .navbar, .filter-form, .no-print { display: none !important; }
            
            /* Ajustar contenedor al ancho del papel */
            .container { max-width: 100%; margin: 0; padding: 0; box-shadow: none; }
            
            /* Mejorar tabla para papel */
            .report-table { box-shadow: none; border: 1px solid #ccc; }
            th { background-color: #eee !important; color: black !important; border: 1px solid #aaa; }
            td { border: 1px solid #aaa; }
            
            /* Tarjeta de Total simplificada para impresión */
            .card-total { 
                border: 1px solid #000; 
                background-color: white !important; 
                color: black !important; 
                box-shadow: none;
            }
            
            /* Mostrar el encabezado de impresión */
            .print-only-header { display: block !important; }
        }

        /* Ocultar encabezado de impresión en pantalla */
        .print-only-header { display: none; text-align: center; margin-bottom: 20px; }
        .print-only-header h2 { margin: 0; text-transform: uppercase; }
        .print-only-header p { margin: 5px 0; font-size: 14px; color: #555; }

    </style>
</head>
<body>
    
    <nav class="navbar">
        <a href="{{ route('dashboard') }}"><strong>Mi Dashboard</strong></a>
        <div>
            <a href="{{ route('reports.expenses') }}">Informe Gastos</a>
            <a href="{{ route('reports.income') }}">Informe Ingresos</a>
            <a href="{{ route('transactions.index') }}">Transacciones</a>
            <a href="{{ route('categorias.index') }}">Categorías</a>
            <a href="{{ route('cuentas.index') }}">Cuentas</a>
            <a href="{{ route('transactions.create') }}" class="btn">+ Nueva Transacción</a>
        </div>
    </nav>

    <div class="container">
        <div class="print-only-header">
            <h2>Reporte de Ingresos</h2>
            <p>Del {{ \Carbon\Carbon::parse($date_from)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($date_to)->format('d/m/Y') }}</p>
        </div>

        <h1>Informe de Ingresos por Categoría</h1>

        <div class="filter-form" style="align-items: flex-end;">
            <form method="GET" action="{{ route('reports.income') }}" style="display: flex; gap: 15px; flex: 1;">
                <div style="flex: 1;">
                    <label for="date_from">Desde:</label>
                    <input type="date" id="date_from" name="date_from" value="{{ $date_from }}">
                </div>
                <div style="flex: 1;">
                    <label for="date_to">Hasta:</label>
                    <input type="date" id="date_to" name="date_to" value="{{ $date_to }}">
                </div>
                <button type="submit" style="height: 38px;">Filtrar</button>
                
                <button type="button" onclick="window.print()" class="no-print" style="background-color: #343a40; height: 38px;">
                    🖨️ Imprimir A4
                </button>
            </form>
        </div>

        <div class="card-total-ingreso">
            <h2>Total de Ingresos en este período</h2>
            <div class="amount">${{ number_format($totalGeneral, 2) }}</div>
        </div>

        <div class="report-table">
            <table>
                <thead>
                    <tr>
                        <th>Categoría</th>
                        <th style="text-align: right;">Total Ingresado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportData as $data)
                        <tr>
                            <td>
                                {{ $data->category ? $data->category->name : 'Sin Categoría' }}
                            </td>
                            <td>
                                ${{ number_format($data->total_ingresos, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2">No se encontraron ingresos en este período.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe de Gastos</title>
    <style>
        /* (Usaremos los mismos estilos del dashboard/categorías) */
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

        .card-total { background-color: #dc3545; color: white; padding: 20px; border-radius: 8px; text-align: center; margin-bottom: 20px; }
        .card-total h2 { margin: 0 0 10px 0; font-size: 1rem; text-transform: uppercase; }
        .card-total .amount { font-size: 2rem; font-weight: bold; }

        .report-table { background-color: #fff; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); overflow: hidden; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 15px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background-color: #f9f9f9; }
        td:last-child { font-weight: bold; text-align: right; }
    </style>
</head>
<body>
    
    <nav class="navbar">
        <a href="{{ route('dashboard') }}"><strong>Mi Dashboard</strong></a>
        <div>
            <a href="{{ route('reports.expenses') }}">Informe Gastos</a>
            <a href="{{ route('reports.income') }}">Informe Ingresos</a>
            
            <a href="{{ route('transactions.index') }}">Historial</a>
            <a href="{{ route('categorias.index') }}">Categorías</a>
            <a href="{{ route('cuentas.index') }}">Cuentas</a>
            <a href="{{ route('transactions.create') }}" class="btn">+ Nueva Transacción</a>
        </div>
    </nav>

    <div class="container">
        <h1>Informe de Gastos por Categoría</h1>

        <form method="GET" action="{{ route('reports.expenses') }}" class="filter-form">
            <div>
                <label for="date_from">Desde:</label>
                <input type="date" id="date_from" name="date_from" value="{{ $date_from }}">
            </div>
            <div>
                <label for="date_to">Hasta:</label>
                <input type="date" id="date_to" name="date_to" value="{{ $date_to }}">
            </div>
            <button type="submit">Filtrar</button>
        </form>

        <div class="card-total">
            <h2>Total de Gastos en este período</h2>
            <div class="amount">${{ number_format($totalGeneral, 2) }}</div>
        </div>

        <div class="report-table">
            <table>
                <thead>
                    <tr>
                        <th>Categoría</th>
                        <th style="text-align: right;">Total Gastado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportData as $data)
                        <tr>
                            <td>
                                {{ $data->category ? $data->category->name : 'Sin Categoría' }}
                            </td>
                            <td>
                                ${{ number_format($data->total_gastos, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2">No se encontraron gastos en este período.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
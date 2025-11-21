<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Detallado</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Estilos base */
        body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #f4f7f6; margin: 0; color: #333; }
        .navbar { background-color: #fff; padding: 15px 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; }
        .navbar a { text-decoration: none; color: #333; font-weight: bold; margin: 0 10px; }
        
        .container { max-width: 1100px; margin: 20px auto; padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }

        /* Panel de Filtros */
        .filter-panel { background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #e9ecef; }
        .filter-row { display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end; }
        .filter-group { flex: 1; min-width: 150px; }
        .filter-group label { display: block; font-size: 0.85rem; font-weight: bold; margin-bottom: 5px; color: #555; }
        .filter-group input, .filter-group select { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
        
        /* Botones */
        .btn { padding: 9px 15px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; color: white; font-size: 0.9rem; }
        .btn-filter { background-color: #007bff; }
        .btn-print { background-color: #343a40; display: flex; align-items: center; gap: 5px; }
        
        /* Resumen de Totales */
        .summary-cards { display: flex; gap: 20px; margin-bottom: 20px; }
        .card { flex: 1; padding: 15px; border-radius: 6px; color: white; text-align: center; }
        .bg-ingreso { background-color: #28a745; }
        .bg-gasto { background-color: #dc3545; }
        .bg-neto { background-color: #6c757d; }
        .card h3 { margin: 0; font-size: 0.9rem; text-transform: uppercase; opacity: 0.9; }
        .card .amount { font-size: 1.5rem; font-weight: bold; margin-top: 5px; }

        /* Tabla */
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        th, td { padding: 10px 8px; border-bottom: 1px solid #eee; text-align: left; }
        th { background-color: #343a40; color: white; text-transform: uppercase; font-size: 0.8rem; }
        tr:hover { background-color: #f8f9fa; }
        
        .text-ingreso { color: #28a745; font-weight: bold; }
        .text-gasto { color: #dc3545; font-weight: bold; }
        .text-muted { color: #999; font-size: 0.85em; }

        /* ESTILOS DE IMPRESIÓN A4 */
        @media print {
            @page { size: A4 portrait; margin: 1.5cm; }
            body { background: white; -webkit-print-color-adjust: exact; }
            .navbar, .filter-panel, .no-print { display: none !important; }
            .container { box-shadow: none; margin: 0; padding: 0; max-width: 100%; }
            
            /* Encabezado de impresión */
            .print-header { display: block !important; text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
            .print-header h2 { margin: 0; }
            
            /* Ajustes de tabla para papel */
            th { background-color: #eee !important; color: black !important; border-bottom: 1px solid #000; }
            td { border-bottom: 1px solid #ccc; }
            
            /* Ajustes de tarjetas de resumen para papel */
            .summary-cards { gap: 10px; }
            .card { background-color: white !important; color: black !important; border: 1px solid #000; padding: 10px; }
            .card h3 { font-size: 10px; }
            .card .amount { font-size: 14px; }
        }
        .print-header { display: none; }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="{{ route('dashboard') }}"><strong>Mi Dashboard</strong></a>
        <div>
            <a href="{{ route('reports.detailed') }}" style="color:#007bff;">Detallado</a>
            <a href="{{ route('reports.weekly') }}">Semanal</a>
            <a href="{{ route('reports.monthly') }}">Mensual</a>
            <a href="{{ route('transactions.index') }}">Volver</a>
        </div>
    </nav>

    <div class="container">

        <div class="print-header">
            <h2>REPORTE DETALLADO DE MOVIMIENTOS</h2>
            <p>Del {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}</p>
            @if($accountId) <p>Filtro Cuenta: {{ $accounts->find($accountId)->name }}</p> @endif
            @if($categoryId) <p>Filtro Categoría: {{ $categories->find($categoryId)->name }}</p> @endif
        </div>

        <div class="filter-panel">
            <form method="GET" action="{{ route('reports.detailed') }}">
                <div class="filter-row">
                    <div class="filter-group">
                        <label>Desde:</label>
                        <input type="date" name="date_from" value="{{ $dateFrom }}">
                    </div>
                    <div class="filter-group">
                        <label>Hasta:</label>
                        <input type="date" name="date_to" value="{{ $dateTo }}">
                    </div>
                    <div class="filter-group">
                        <label>Cuenta:</label>
                        <select name="account_id">
                            <option value="">Todas las cuentas</option>
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}" {{ $accountId == $acc->id ? 'selected' : '' }}>
                                    {{ $acc->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Categoría:</label>
                        <select name="category_id">
                            <option value="">Todas las categorías</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div style="display: flex; gap: 10px; align-items: flex-end;">
                        <button type="submit" class="btn btn-filter">Filtrar Resultados</button>
                        <button type="button" onclick="window.print()" class="btn btn-print">🖨️ Imprimir</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="summary-cards">
            <div class="card bg-ingreso">
                <h3>Total Ingresos</h3>
                <div class="amount">+ {{ number_format($totalIngresos, 2) }}</div>
            </div>
            <div class="card bg-gasto">
                <h3>Total Gastos</h3>
                <div class="amount">- {{ number_format($totalGastos, 2) }}</div>
            </div>
            <div class="card bg-neto">
                <h3>Saldo del Periodo</h3>
                <div class="amount">{{ number_format($saldoPeriodo, 2) }}</div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Fecha / Hora</th>
                    <th>Descripción</th>
                    <th>Categoría</th>
                    <th>Cuenta</th>
                    <th style="text-align: right;">Monto</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $tx)
                    <tr>
                        <td>
                            {{ \Carbon\Carbon::parse($tx->date)->format('d/m/Y') }}
                            <br>
                            <span class="text-muted">
                                {{ $tx->time ? \Carbon\Carbon::parse($tx->time)->format('H:i') : '' }}
                            </span>
                        </td>
                        <td>{{ $tx->description }}</td>
                        <td>{{ $tx->category ? $tx->category->name : '-' }}</td>
                        <td><small>{{ $tx->account->name }}</small></td>
                        <td style="text-align: right;" class="{{ $tx->type == 'ingreso' ? 'text-ingreso' : 'text-gasto' }}">
                            {{ $tx->type == 'ingreso' ? '+' : '-' }}
                            {{ number_format($tx->amount, 2) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 20px;">
                            No se encontraron transacciones con estos filtros.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </div>

</body>
</html>
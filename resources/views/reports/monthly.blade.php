<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Mensual</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Reutilizamos los estilos del reporte semanal */
        body { font-family: Arial, sans-serif; background-color: #f4f7f6; margin: 0; }
        .navbar { background-color: #fff; padding: 15px 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; }
        .navbar a { text-decoration: none; color: #333; font-weight: bold; margin: 0 10px; }
        .container { max-width: 1200px; margin: 20px auto; padding: 20px; background: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .filter-bar { margin-bottom: 20px; display: flex; gap: 10px; align-items: center; background: #f8f9fa; padding: 15px; border-radius: 8px; }
        
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th, td { border: 1px solid #ddd; padding: 8px 5px; text-align: right; }
        thead th { background-color: #2c3e50; color: white; text-align: center; font-weight: bold; }
        td:first-child { text-align: left; font-weight: bold; width: 200px; background-color: #f9f9f9; }
        
        .row-total { background-color: #e9ecef; font-weight: bold; }
        .section-total { background-color: #dfe6e9; font-weight: bold; color: #2d3436; }
        
        .text-ingreso { color: #27ae60; }
        .text-gasto { color: #c0392b; }
        .text-neutral { color: #7f8c8d; }

        @media print {
            @page { size: A4 landscape; margin: 1cm; }
            body { background: white; -webkit-print-color-adjust: exact; }
            .navbar, .filter-bar, .no-print { display: none; }
            .container { box-shadow: none; margin: 0; padding: 0; max-width: 100%; }
            th { background-color: #2c3e50 !important; color: white !important; }
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="{{ route('dashboard') }}"><strong>Mi Dashboard</strong></a>
        <div>
            <a href="{{ route('reports.expenses') }}">Gastos</a>
            <a href="{{ route('reports.income') }}">Ingresos</a>
            <a href="{{ route('reports.detailed') }}">Detallado</a>
            <a href="{{ route('reports.weekly') }}">Semanal</a>
            <a href="{{ route('reports.monthly') }}" style="color: #007bff;">Mensual</a>
            <a href="{{ route('transactions.index') }}">Volver</a>
        </div>
    </nav>

    <div class="container">
        
        <div class="filter-bar">
            <form action="{{ route('reports.monthly') }}" method="GET" style="display: flex; gap: 10px; align-items: center;">
                <label>Seleccionar Mes:</label>
                <input type="month" name="month" value="{{ $monthInput }}" style="padding: 5px;">
                <button type="submit" style="padding: 6px 12px; cursor: pointer;">Ver Reporte</button>
            </form>
            <button onclick="window.print()" class="no-print" style="margin-left: auto; padding: 6px 12px; cursor: pointer;">🖨️ Imprimir</button>
        </div>

        <div style="text-align: center; margin-bottom: 20px;">
            <h2 style="margin:0; text-transform: uppercase;">FLUJO DE CAJA MENSUAL: {{ $startOfMonth->locale('es')->monthName }} {{ $startOfMonth->year }}</h2>
        </div>

        <table>
            <thead>
                <tr>
                    <th>CONCEPTO</th>
                    @foreach($weeks as $weekNum => $info)
                        <th>{{ $info['label'] }}<br><small>{{ $info['dates'] }}</small></th>
                    @endforeach
                    <th>TOTAL MES</th>
                </tr>
            </thead>
            <tbody>
                
                <tr class="section-total">
                    <td>Saldo Inicial (Mes)</td>
                    <td>{{ number_format($balanceStartOfMonth, 2) }}</td>
                    @for($i=0; $i < count($weeks)-1; $i++) <td></td> @endfor
                    <td>{{ number_format($balanceStartOfMonth, 2) }}</td>
                </tr>

                <tr><td colspan="{{ count($weeks) + 2 }}" style="background:#eafaf1; text-align:center; font-size:10px; padding:2px;">INGRESOS</td></tr>
                
                @php $monthTotalIngresos = 0; $weeklyIngresosSum = array_fill_keys(array_keys($weeks), 0); @endphp
                
                @foreach($matrix['ingreso'] as $catName => $weekData)
                    <tr>
                        <td>{{ $catName }}</td>
                        @php $rowTotal = 0; @endphp
                        
                        @foreach($weeks as $weekNum => $info)
                            @php 
                                $amount = $weekData[$weekNum] ?? 0; 
                                $rowTotal += $amount;
                                $weeklyIngresosSum[$weekNum] += $amount;
                            @endphp
                            <td class="{{ $amount > 0 ? 'text-ingreso' : 'text-neutral' }}">
                                {{ $amount != 0 ? number_format($amount, 2) : '0' }}
                            </td>
                        @endforeach
                        
                        <td class="row-total">{{ number_format($rowTotal, 2) }}</td>
                        @php $monthTotalIngresos += $rowTotal; @endphp
                    </tr>
                @endforeach

                <tr class="section-total" style="border-top: 2px solid #aaa;">
                    <td>Total Ingresos</td>
                    @foreach($weeks as $weekNum => $info)
                        @php 
                            $val = $weeklyIngresosSum[$weekNum];
                            if ($loop->first) $val += $balanceStartOfMonth;
                        @endphp
                        <td>{{ number_format($val, 2) }}</td>
                    @endforeach
                    <td>{{ number_format($monthTotalIngresos + $balanceStartOfMonth, 2) }}</td>
                </tr>

                <tr><td colspan="{{ count($weeks) + 2 }}" style="background:#fdedec; text-align:center; font-size:10px; padding:2px;">GASTOS</td></tr>

                @php $monthTotalGastos = 0; $weeklyGastosSum = array_fill_keys(array_keys($weeks), 0); @endphp

                @foreach($matrix['gasto'] as $catName => $weekData)
                    <tr>
                        <td>{{ $catName }}</td>
                        @php $rowTotal = 0; @endphp
                        
                        @foreach($weeks as $weekNum => $info)
                            @php 
                                $amount = $weekData[$weekNum] ?? 0; 
                                $displayAmount = $amount * -1; // Visual negativo
                                $rowTotal += $displayAmount;
                                $weeklyGastosSum[$weekNum] += $displayAmount;
                            @endphp
                            <td class="{{ $displayAmount < 0 ? 'text-gasto' : 'text-neutral' }}">
                                {{ $displayAmount != 0 ? number_format($displayAmount, 2) : '0' }}
                            </td>
                        @endforeach
                        
                        <td class="row-total text-gasto">{{ number_format($rowTotal, 2) }}</td>
                        @php $monthTotalGastos += $rowTotal; @endphp
                    </tr>
                @endforeach

                <tr class="section-total" style="border-top: 2px solid #aaa;">
                    <td>Total Gastos</td>
                    @foreach($weeks as $weekNum => $info)
                        <td class="text-gasto">{{ number_format($weeklyGastosSum[$weekNum], 2) }}</td>
                    @endforeach
                    <td class="text-gasto">{{ number_format($monthTotalGastos, 2) }}</td>
                </tr>

                <tr><td colspan="{{ count($weeks) + 2 }}" style="height: 15px;"></td></tr>

                <tr style="font-weight: bold;">
                    <td>Saldo Neto Semana</td>
                    @foreach($weeks as $weekNum => $info)
                        @php
                            $neto = $weeklyIngresosSum[$weekNum] + $weeklyGastosSum[$weekNum];
                            if ($loop->first) $neto += $balanceStartOfMonth;
                        @endphp
                        <td>{{ number_format($neto, 2) }}</td>
                    @endforeach
                    <td>{{ number_format(($monthTotalIngresos + $balanceStartOfMonth) + $monthTotalGastos, 2) }}</td>
                </tr>

                <tr class="section-total" style="background-color: #2c3e50; color: white;">
                    <td>ACUMULADO</td>
                    @php $runningBalance = $balanceStartOfMonth; @endphp

                    @foreach($weeks as $weekNum => $info)
                        @php
                            $netoSemana = $weeklyIngresosSum[$weekNum] + $weeklyGastosSum[$weekNum];
                            $runningBalance += $netoSemana;
                        @endphp
                        <td>{{ number_format($runningBalance, 2) }}</td>
                    @endforeach
                    <td></td>
                </tr>

            </tbody>
        </table>

    </div>
</body>
</html>
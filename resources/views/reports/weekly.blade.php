@extends('layouts.app1')
@section('title', 'Reporte Semanal')

@push('styles')
<style>
    @media print {
        /* 1. Configuración de la hoja física */
        @page { 
            size: A4 landscape; 
            margin: 10mm; 
        }
        
        /* 2. Regla Nuclear: RESET TOTAL DE COLORES */
        /* Forzamos a que TODO sea fondo blanco y texto negro. Sin excepciones. */
        * {
            background-color: white !important;
            color: black !important;
            border-color: #000 !important;
            text-shadow: none !important;
            filter: none !important;
            -webkit-print-color-adjust: exact; 
        }

        /* 3. RESET DE LAYOUT (Vital para que se vea la tabla) */
        /* Anulamos el h-screen y overflow-hidden del layout principal */
        body, html, #app, main, div {
            height: auto !important;
            min-height: 0 !important;
            overflow: visible !important;
            width: auto !important;
            position: static !important;
            display: block !important; /* Rompemos el Flexbox para que fluya hacia abajo */
        }

        /* 4. Ocultar elementos de interfaz */
        /* , nav, header, aside, footer, .shadow-sm, .shadow-lg, button, form */
        .no-print { 
            display: none !important; 
        }

        /* 5. Estilos Específicos para la Tabla */
        table {
            width: 100% !important;
            border-collapse: collapse !important;
            margin-top: 20px !important;
            font-size: 10pt !important; /* Letra legible en papel */
        }

        th, td {
            border: 1px solid #000 !important;
            padding: 4px 8px !important;
            text-align: right !important; /* Números a la derecha */
        }

        /* Alinear textos a la izquierda solo en la primera columna */
        th:first-child, td:first-child {
            text-align: left !important;
        }

        /* Encabezados de tabla */
        th {
            font-weight: bold !important;
            text-transform: uppercase !important;
            border-bottom: 2px solid #000 !important;
        }

        /* Filas de totales (para destacarlas un poco con negrita, sin fondo) */
        .font-bold {
            font-weight: 900 !important;
        }

        /* Asegurar que el contenedor de la tabla no tenga scroll */
        .overflow-x-auto {
            overflow: visible !important;
            display: block !important;
        }
        
        /* Evitar cortes feos de página dentro de las filas */
        tr {
            page-break-inside: avoid;
        }
        
        /* Título del reporte grande */
        h1, h2 {
            text-align: center !important;
            font-size: 16pt !important;
            margin-bottom: 10px !important;
            color: black !important;
            display: block !important; /* Asegurar que se muestre */
        }
        p {
            text-align: center !important;
            color: #444 !important; /* Un gris muy oscuro para subtítulos */
        }
    }
</style>
@endpush

@section('content')
<div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
    
    <div class="no-print bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
        <div class="flex flex-col sm:flex-row gap-4 items-center justify-between">
            
            <form action="{{ route('reports.weekly') }}" method="GET" class="flex items-center gap-3 w-full sm:w-auto">
                <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Semana del:</label>
                <input type="date" name="date" value="{{ $startOfWeek->format('Y-m-d') }}"
                       class="px-3 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-md focus:ring-1 focus:ring-gray-500 bg-white dark:bg-gray-700 dark:text-gray-200">
                <button type="submit"
                        class="px-4 py-1.5 text-sm bg-gray-800 hover:bg-gray-900 text-white rounded-md transition shadow-sm">
                    Consultar
                </button>
            </form>

            <div class="flex items-center gap-3">
                <form method="POST" action="{{ route('reports.pdf') }}" class="inline">
                    @csrf
                    <input type="hidden" name="view" value="reports.pdf.weekly_pdf"> 
                    <input type="hidden" name="data" value="{{ base64_encode(serialize(compact('balanceStartOfWeek','days','matrix','startOfWeek','endOfWeek'))) }}">
                    
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-1.5 text-sm font-medium text-red-700 bg-red-50 hover:bg-red-100 border border-red-200 rounded-md transition dark:bg-red-900/20 dark:text-red-300 dark:border-red-800">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 2H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                        PDF
                    </button>
                </form>

                <button onclick="window.print()"
                        class="inline-flex items-center gap-2 px-4 py-1.5 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 border border-gray-300 rounded-md transition dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Imprimir
                </button>
            </div>
        </div>
    </div>

    <div class="mb-6 text-center border-b border-gray-200 dark:border-gray-700 pb-4">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white uppercase tracking-tight">Reporte Semanal de Flujo de Caja</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            Semana Nº {{ $startOfWeek->weekOfYear }} &bull; 
            <span class="font-medium text-gray-700 dark:text-gray-300">
                {{ $startOfWeek->format('d/m/Y') }} — {{ $endOfWeek->format('d/m/Y') }}
            </span>
        </p>
    </div>

    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm whitespace-nowrap">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-750 border-b border-gray-200 dark:border-gray-700">
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 w-64 bg-gray-50 dark:bg-gray-800 sticky left-0 z-10 border-r border-gray-200 dark:border-gray-700">
                            CONCEPTO
                        </th>
                        @foreach($days as $date => $info)
                            <th class="px-2 py-3 text-right min-w-[100px]">
                                <div class="text-xs text-gray-500 dark:text-gray-400 uppercase font-bold">{{ substr($info['day_name'], 0, 3) }}</div>
                                <div class="text-gray-800 dark:text-gray-200 font-medium">{{ \Carbon\Carbon::parse($info['date'])->format('d/m') }}</div>
                            </th>
                        @endforeach
                        <th class="px-4 py-3 text-right font-bold text-gray-800 dark:text-white bg-gray-50 dark:bg-gray-800 border-l border-gray-200 dark:border-gray-700">
                            TOTAL
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">

                    <tr class="bg-gray-50/50 dark:bg-gray-800/50">
                        <td class="px-4 py-2 font-bold text-gray-700 dark:text-gray-200 sticky left-0 bg-gray-50/50 dark:bg-gray-800/50 border-r border-gray-200 dark:border-gray-700">
                            Saldo Inicial
                        </td>
                        <td class="px-2 py-2 text-right font-mono text-gray-600 dark:text-gray-400">
                            {{ number_format($balanceStartOfWeek, 2) }}
                        </td>
                        @for($i = 1; $i < 7; $i++) <td class="px-2 py-2"></td> @endfor
                        <td class="px-4 py-2 text-right font-mono font-bold text-gray-800 dark:text-gray-200 border-l border-gray-200 dark:border-gray-700">
                            {{ number_format($balanceStartOfWeek, 2) }}
                        </td>
                    </tr>

                    <tr class="bg-gray-50 dark:bg-gray-700 border-y border-gray-200 dark:border-gray-600">
                        <td colspan="9" class="px-4 py-1.5 text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Ingresos
                        </td>
                    </tr>

                    @php
                        $weeklyTotalIngresos = 0;
                        $dailyIngresosSum = array_fill_keys(array_keys($days), 0);
                    @endphp

                    @foreach($matrix['ingreso'] as $catName => $dates)
                        @php $rowTotal = 0; @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-4 py-2 text-gray-600 dark:text-gray-300 font-medium truncate max-w-xs sticky left-0 bg-white dark:bg-gray-800 border-r border-gray-100 dark:border-gray-700">
                                {{ $catName }}
                            </td>
                            @foreach($days as $dayDate => $info)
                                @php
                                    $amount = $dates[$dayDate] ?? 0;
                                    $rowTotal += $amount;
                                    $dailyIngresosSum[$dayDate] += $amount;
                                @endphp
                                <td class="px-2 py-2 text-right font-mono text-sm {{ $amount > 0 ? 'text-gray-900 dark:text-gray-100' : 'text-gray-300 dark:text-gray-600' }}">
                                    {{ $amount > 0 ? number_format($amount, 2) : '-' }}
                                </td>
                            @endforeach
                            <td class="px-4 py-2 text-right font-mono font-medium text-gray-800 dark:text-gray-200 border-l border-gray-100 dark:border-gray-700 bg-gray-50/30">
                                {{ number_format($rowTotal, 2) }}
                            </td>
                            @php $weeklyTotalIngresos += $rowTotal; @endphp
                        </tr>
                    @endforeach

                    <tr class="bg-gray-50 dark:bg-gray-700/30 border-t-2 border-gray-300 dark:border-gray-600 font-bold">
                        <td class="px-4 py-2 text-gray-700 dark:text-gray-200 sticky left-0 bg-gray-50 dark:bg-gray-700/30 border-r border-gray-200 dark:border-gray-700">
                            Total Ingresos
                        </td>
                        @foreach($days as $dayDate => $info)
                            @php
                                $val = $dailyIngresosSum[$dayDate]; // Solo lo del día
                            @endphp
                            <td class="px-2 py-2 text-right font-mono text-green-600 dark:text-green-400">
                                {{ number_format($val, 2) }}
                            </td>
                        @endforeach
                        <td class="px-4 py-2 text-right font-mono text-green-700 dark:text-green-400 border-l border-gray-200 dark:border-gray-700">
                            {{ number_format($weeklyTotalIngresos, 2) }}
                        </td>
                    </tr>

                    <tr class="bg-gray-50 dark:bg-gray-700 border-y border-gray-200 dark:border-gray-600 mt-4">
                        <td colspan="9" class="px-4 py-1.5 text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Gastos
                        </td>
                    </tr>

                    @php
                        $weeklyTotalGastos = 0;
                        $dailyGastosSum = array_fill_keys(array_keys($days), 0);
                    @endphp

                    @foreach($matrix['gasto'] as $catName => $dates)
                        @php $rowTotal = 0; @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-4 py-2 text-gray-600 dark:text-gray-300 font-medium truncate max-w-xs sticky left-0 bg-white dark:bg-gray-800 border-r border-gray-100 dark:border-gray-700">
                                {{ $catName }}
                            </td>
                            @foreach($days as $dayDate => $info)
                                @php
                                    $amount = ($dates[$dayDate] ?? 0) * -1;
                                    $rowTotal += $amount;
                                    $dailyGastosSum[$dayDate] += $amount;
                                @endphp
                                <td class="px-2 py-2 text-right font-mono text-sm {{ $amount < 0 ? 'text-gray-900 dark:text-gray-100' : 'text-gray-300 dark:text-gray-600' }}">
                                    {{ $amount < 0 ? number_format($amount, 2) : '-' }}
                                </td>
                            @endforeach
                            <td class="px-4 py-2 text-right font-mono font-medium text-gray-800 dark:text-gray-200 border-l border-gray-100 dark:border-gray-700 bg-gray-50/30">
                                {{ number_format($rowTotal, 2) }}
                            </td>
                            @php $weeklyTotalGastos += $rowTotal; @endphp
                        </tr>
                    @endforeach

                    <tr class="bg-gray-50 dark:bg-gray-700/30 border-t-2 border-gray-300 dark:border-gray-600 font-bold">
                        <td class="px-4 py-2 text-gray-700 dark:text-gray-200 sticky left-0 bg-gray-50 dark:bg-gray-700/30 border-r border-gray-200 dark:border-gray-700">
                            Total Gastos
                        </td>
                        @foreach($dailyGastosSum as $sum)
                            <td class="px-2 py-2 text-right font-mono text-red-600 dark:text-red-400">
                                {{ number_format($sum, 2) }}
                            </td>
                        @endforeach
                        <td class="px-4 py-2 text-right font-mono text-red-700 dark:text-red-400 border-l border-gray-200 dark:border-gray-700">
                            {{ number_format($weeklyTotalGastos, 2) }}
                        </td>
                    </tr>

                    <tr class="border-t border-gray-200 dark:border-gray-700">
                        <td class="px-4 py-2 font-bold text-gray-500 dark:text-gray-400 sticky left-0 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700">
                            Flujo Neto
                        </td>
                        @foreach($days as $dayDate => $info)
                            @php
                                $neto = $dailyIngresosSum[$dayDate] + $dailyGastosSum[$dayDate];
                            @endphp
                            <td class="px-2 py-2 text-right font-mono text-sm font-semibold {{ $neto >= 0 ? 'text-blue-600' : 'text-orange-500' }}">
                                {{ number_format($neto, 2) }}
                            </td>
                        @endforeach
                        <td class="px-4 py-2 text-right font-mono font-bold text-gray-800 dark:text-gray-200 border-l border-gray-200 dark:border-gray-700">
                            {{ number_format($weeklyTotalIngresos + $weeklyTotalGastos, 2) }}
                        </td>
                    </tr>

                    <tr class="bg-gray-100 dark:bg-gray-900 border-t-2 border-gray-400 dark:border-gray-500">
                        <td class="px-4 py-3 font-black text-gray-900 dark:text-white uppercase sticky left-0 bg-gray-100 dark:bg-gray-900 border-r border-gray-300 dark:border-gray-600">
                            Saldo Final
                        </td>
                        @php $running = $balanceStartOfWeek; @endphp
                        @foreach($days as $dayDate => $info)
                            @php
                                $running += $dailyIngresosSum[$dayDate] + $dailyGastosSum[$dayDate];
                            @endphp
                            <td class="px-2 py-3 text-right font-mono font-bold text-base text-gray-900 dark:text-white">
                                {{ number_format($running, 2) }}
                            </td>
                        @endforeach
                        <td class="px-4 py-3 text-right font-mono font-black text-base text-gray-900 dark:text-white border-l border-gray-300 dark:border-gray-600 bg-gray-200 dark:bg-gray-800">
                            {{ number_format($running, 2) }}
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>
        
        <div class="bg-gray-50 dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 text-xs text-gray-500 dark:text-gray-400 flex justify-between">
            <span>Mostrando movimientos efectivos.</span>
            <span>Generado el {{ now()->format('d/m/Y H:i') }}</span>
        </div>
    </div>
</div>
@endsection
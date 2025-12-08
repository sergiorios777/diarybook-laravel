@extends('layouts.app1')
@section('title', 'Reporte de Ingresos')

@push('styles')
<style>
    @media print {
        @page { size: A4 portrait; margin: 1.5cm; }
        body { -webkit-print-color-adjust: exact; color-adjust: exact; }

        /* Ocultar todo lo que no va impreso */
        .no-print,
        nav, header, footer,
        .filter-form { display: none !important; }

        /* Mostrar solo lo esencial */
        .print-header { display: block !important; }
        .container { max-width: 100% !important; margin: 0 !important; padding: 0 !important; }
        .report-table { box-shadow: none !important; border: 2px solid #333 !important; }
        th { background-color: #333 !important; color: white !important; }
        .card-total { background: white !important; color: black !important; border: 3px double #333 !important; }
    }
</style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto">

    <!-- ENCABEZADO PARA IMPRESIÓN -->
    <div class="print-header hidden text-center mb-12 border-b-4 border-green-600 pb-6">
        <h1 class="text-4xl font-bold text-gray-800">REPORTE DE INGRESOS</h1>
        <p class="text-xl mt-3">
            Del {{ \Carbon\Carbon::parse($date_from)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($date_to)->format('d/m/Y') }}
        </p>
    </div>

    <!-- FILTROS -->
    <div class="no-print bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 mb-8">
        <form method="GET" action="{{ route('reports.income') }}" class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Desde</label>
                <input type="date" name="date_from" value="{{ $date_from }}"
                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 transition">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Hasta</label>
                <input type="date" name="date_to" value="{{ $date_to }}"
                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 transition">
            </div>
            <div class="flex gap-3">
                <button type="submit"
                        class="px-8 py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl shadow-lg transition transform hover:-translate-y-0.5">
                    Filtrar
                </button>
                <button type="button" onclick="window.print()"
                        class="px-8 py-3 bg-gray-800 hover:bg-gray-900 text-white font-bold rounded-xl shadow-lg transition flex items-center gap-2">
                    Imprimir
                </button>
            </div>
        </form>
    </div>

    <!-- TOTAL INGRESOS -->
    <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl p-10 text-white shadow-2xl text-center mb-10">
        <p class="text-green-100 text-lg uppercase tracking-wider font-semibold">Total de Ingresos en el Período</p>
        <p class="text-6xl font-bold mt-4">S/ {{ number_format($totalGeneral, 2) }}</p>
        <div class="mt-6 opacity-30">
            <svg class="w-24 h-24 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
            </svg>
        </div>
    </div>

    <!-- TABLA DE INGRESOS POR CATEGORÍA -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
        <div class="px-8 py-6 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Ingresos por Categoría</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-8 py-5 text-left font-bold text-gray-700 dark:text-gray-300">Categoría</th>
                        <th class="px-8 py-5 text-right font-bold text-gray-700 dark:text-gray-300">Total Ingresado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($reportData as $data)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                            <td class="px-8 py-4 font-medium text-gray-800 dark:text-gray-200">
                                {{ $data->category?->name ?? 'Sin Categoría' }}
                            </td>
                            <td class="px-8 py-4 text-right font-mono text-xl font-bold text-green-600 dark:text-green-400">
                                S/ {{ number_format($data->total_ingresos, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center py-16 text-gray-500 dark:text-gray-400">
                                <div class="text-6xl mb-4">Empty</div>
                                <p class="text-xl">No se encontraron ingresos en este período</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="bg-green-50 dark:bg-green-900/30 font-bold text-xl">
                        <td class="px-8 py-5 text-left">TOTAL GENERAL</td>
                        <td class="px-8 py-5 text-right text-green-700 dark:text-green-400">
                            S/ {{ number_format($totalGeneral, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
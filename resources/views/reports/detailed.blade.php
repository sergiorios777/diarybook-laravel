@extends('layouts.app1')
@section('title', 'Reporte Detallado')

@section('content')
<div class="max-w-7xl mx-auto">

    <!-- ENCABEZADO PARA IMPRESIÓN -->
    <div class="hidden print:block text-center mb-12 border-b-4 border-gray-800 pb-6">
        <h1 class="text-3xl font-bold">REPORTE DETALLADO DE MOVIMIENTOS</h1>
        <p class="text-lg mt-2">
            Del {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}
        </p>
        @if($accountId)
            <p>Cuenta: {{ $accounts->find($accountId)->name }}</p>
        @endif
        @if($categoryId)
            <p>Categoría: {{ $categories->find($categoryId)->name }}</p>
        @endif
    </div>

    <!-- FILTROS -->
    <div class="print:hidden bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 mb-8">
        <form method="GET" action="{{ route('reports.detailed') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Desde</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}"
                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Hasta</label>
                <input type="date" name="date_to" value="{{ $dateTo }}"
                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Cuenta</label>
                <select name="account_id" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl">
                    <option value="">Todas las cuentas</option>
                    @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}" {{ $accountId == $acc->id ? 'selected' : '' }}>{{ $acc->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Categoría</label>
                <select name="category_id" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl">
                    <option value="">Todas las categorías</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-3">
                <button type="submit"
                        class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-md transition">
                    Filtrar
                </button>
                <button type="button" onclick="window.print()"
                        class="px-6 py-3 bg-gray-800 hover:bg-gray-900 text-white font-bold rounded-xl shadow-md transition flex items-center gap-2">
                    Imprimir
                </button>
            </div>
        </form>
    </div>

    <!-- RESUMEN EN TARJETAS -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 print:grid-cols-3 print:gap-4">
        <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl p-8 text-white shadow-xl">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm uppercase tracking-wider">Total Ingresos</p>
                    <p class="text-4xl font-bold mt-2">S/ {{ number_format($totalIngresos, 2) }}</p>
                </div>
                <div class="text-6xl opacity-30">
                    <svg class="w-20 h-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-red-500 to-rose-600 rounded-2xl p-8 text-white shadow-xl">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-sm uppercase tracking-wider">Total Gastos</p>
                    <p class="text-4xl font-bold mt-2">S/ {{ number_format($totalGastos, 2) }}</p>
                </div>
                <div class="text-6xl opacity-30">
                    <svg class="w-20 h-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-blue-600 to-purple-700 rounded-2xl p-8 text-white shadow-xl">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm uppercase tracking-wider">Saldo del Período</p>
                    <p class="text-4xl font-bold mt-2">S/ {{ number_format($saldoPeriodo, 2) }}</p>
                </div>
                <div class="text-6xl opacity-30">
                    <svg class="w-20 h-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- BOTÓN NUEVA TRANSACCIÓN -->
    <div class="text-right mb-6 print:hidden">
        <a href="{{ route('transactions.create') }}"
           class="inline-flex items-center gap-3 px-6 py-4 bg-gradient-to-r from-blue-600 to-purple-700 hover:from-blue-700 hover:to-purple-800 text-white font-bold rounded-xl shadow-lg transition transform hover:-translate-y-1">
            Nueva Transacción
        </a>
    </div>

    <!-- TRANSACCIONES: Tabla siempre visible al imprimir, tarjetas solo en móvil -->
    <div class="overflow-x-auto print:overflow-visible">
        <!-- TABLA: Visible en escritorio + SIEMPRE al imprimir -->
        <div class="hidden lg:block print:block print:w-full">
            <table class="w-full min-w-full text-sm">
                <thead class="bg-gray-900 text-white text-xs uppercase tracking-wider print:bg-gray-200 print:text-black">
                    <tr>
                        <th class="px-6 py-4 text-left">Fecha / Hora</th>
                        <th class="px-6 py-4 text-left">Descripción</th>
                        <th class="px-6 py-4 text-left">Categoría</th>
                        <th class="px-6 py-4 text-left">Cuenta</th>
                        <th class="px-6 py-4 text-right">Monto</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($transactions as $tx)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition print:border-b print:border-gray-300">
                            <td class="px-6 py-4">
                                <div class="font-medium">{{ \Carbon\Carbon::parse($tx->date)->format('d/m/Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $tx->time ? \Carbon\Carbon::parse($tx->time)->format('H:i') : '-' }}</div>
                            </td>
                            <td class="px-6 py-4 font-medium">{{ $tx->description }}</td>
                            <td class="px-6 py-4">{{ $tx->category?->name ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 text-xs rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    {{ $tx->account->name }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-lg">
                                <span class="{{ $tx->type == 'ingreso' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $tx->type == 'ingreso' ? '+' : '-' }}S/ {{ number_format($tx->amount, 2) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-16 text-gray-500">
                                No se encontraron transacciones
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Tarjetas solo en móvil (nunca se imprimen) -->
        <div class="lg:hidden print:hidden">
            @forelse($transactions as $tx)
                <div class="p-6 border-b dark:border-gray-700">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="font-bold text-lg">{{ $tx->description }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                {{ \Carbon\Carbon::parse($tx->date)->format('d/m/Y') }}
                                @if($tx->time) • {{ \Carbon\Carbon::parse($tx->time)->format('H:i') }} @endif
                            </div>
                            <div class="flex gap-2 mt-2">
                                <span class="px-3 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                    {{ $tx->account->name }}
                                </span>
                                @if($tx->category)
                                    <span class="px-3 py-1 text-xs rounded-full bg-gray-100 text-gray-700">
                                        {{ $tx->category->name }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="text-2xl font-bold {{ $tx->type == 'ingreso' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $tx->type == 'ingreso' ? '+' : '-' }}S/ {{ number_format($tx->amount, 2) }}
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center text-gray-500">No hay transacciones</div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    @media print {
        @page { size: A4 portrait; margin: 1cm; }
        /* 1. REGLA DE ORO: Resetear altura y overflow del cuerpo y HTML */
        html, body {
            height: auto !important;
            overflow: visible !important;
            overflow-y: visible !important;
        }

        /* 2. Anular cualquier contenedor con scroll interno del layout */
        /* Esto afecta a todos los divs, asegurando que nada se recorte ni tenga scroll */
        div {
            overflow: visible !important;
            height: auto !important;
        }

        /* 3. Ocultar específicamente la interfaz de la barra de scroll (por si acaso) */
        ::-webkit-scrollbar {
            display: none !important;
            width: 0px !important;
            background: transparent !important;
        }
        /* 4. Ajustes de color para impresión */
        body { -webkit-print-color-adjust: exact; print-color-adjust: exact; background: white !important; font-size: 10pt !important;}
        .text-white { color: black !important; }
        .bg-gray-900 { background-color: #eee !important; color: black !important; border: 1px solid #000; }
        h1 { font-size: 18pt !important; } /* Título principal */
        p, td, th { font-size: 10pt !important; } /* Contenido general */
        .max-w-7xl {
            max-width: 100% !important;
        }
        
        /* Ajuste específico para tus tarjetas de resumen si quieres que se vean bien en papel */
        .bg-gradient-to-br {
            background: none !important;
            border: 1px solid #ccc;
            color: black !important;
        }
        
        /* Aseguramos que los SVGs no sean gigantes o invisibles */
        svg { color: #666 !important; }
    }
</style>
@endpush
@extends('layouts.app1')

@section('title', 'Historial de Transacciones')

@section('content')
    <div class="max-w-7xl mx-auto">
        
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-gray-100">
                Historial de Movimientos
            </h1>
            <a href="{{ route('transactions.create') }}"
               class="inline-flex justify-center items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-xl shadow-md transition-all transform hover:-translate-y-0.5">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Registrar Transacción
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-5 mb-6 border border-gray-100 dark:border-gray-700">
            <form method="GET" action="{{ route('transactions.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Desde</label>
                    <input type="date" name="date_from" value="{{ $dateFrom }}"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 bg-gray-50 dark:bg-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Hasta</label>
                    <input type="date" name="date_to" value="{{ $dateTo }}"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 bg-gray-50 dark:bg-gray-700 dark:text-white">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Cuenta</label>
                    <select name="account_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 dark:text-white">
                        <option value="">Todas las cuentas</option>
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}" {{ $accountId == $acc->id ? 'selected' : '' }}>{{ $acc->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Categoría</label>
                    <select name="category_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 dark:text-white">
                        <option value="">Todas las categorías</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-gray-800 hover:bg-gray-900 text-white font-bold py-2 px-4 rounded-lg transition">
                        Filtrar
                    </button>
                    <a href="{{ route('transactions.index') }}" class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-white" title="Resetear a Hoy">
                        ↺
                    </a>
                </div>
            </form>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-xl border border-green-100 dark:border-green-800 flex justify-between items-center">
                <span class="text-green-800 dark:text-green-300 font-semibold">Ingresos</span>
                <span class="text-xl font-bold text-green-600 dark:text-green-400">+{{ number_format($totalIngresos, 2) }}</span>
            </div>
            <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-xl border border-red-100 dark:border-red-800 flex justify-between items-center">
                <span class="text-red-800 dark:text-red-300 font-semibold">Gastos</span>
                <span class="text-xl font-bold text-red-600 dark:text-red-400">-{{ number_format($totalGastos, 2) }}</span>
            </div>
            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-xl border border-blue-100 dark:border-blue-800 flex justify-between items-center">
                <span class="text-blue-800 dark:text-blue-300 font-semibold">Balance</span>
                <span class="text-xl font-bold {{ $balanceFiltrado >= 0 ? 'text-blue-600 dark:text-blue-400' : 'text-red-600' }}">
                    {{ number_format($balanceFiltrado, 2) }}
                </span>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden border border-gray-100 dark:border-gray-700">
            
            <div class="hidden lg:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Fecha/Hora</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Descripción</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Cuenta</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Categoría</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Monto</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($transactions as $transaction)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900 dark:text-white">
                                        {{ \Carbon\Carbon::parse($transaction->date)->format('d/m/Y') }}
                                    </div>
                                    <div class="text-xs text-gray-500 font-mono">
                                        {{ $transaction->time ? \Carbon\Carbon::parse($transaction->time)->format('H:i') : '--:--' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                    {{ $transaction->description }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-md bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600">
                                        {{ $transaction->account->name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                    {{ $transaction->category?->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-sm font-bold font-mono {{ $transaction->type == 'ingreso' ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $transaction->type == 'ingreso' ? '+' : '-' }} S/ {{ number_format(abs($transaction->amount), 2) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center gap-3">
                                        <a href="{{ route('transactions.edit', $transaction) }}" class="text-blue-600 hover:text-blue-900" title="Editar">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>
                                        <form action="{{ route('transactions.destroy', $transaction) }}" method="POST" class="inline" onsubmit="return confirm('¿Confirma eliminar?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" title="Eliminar">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                    No hay movimientos registrados en este rango de fechas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="lg:hidden divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($transactions as $transaction)
                    <div class="p-4">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <span class="font-bold text-gray-900 dark:text-white block">
                                    {{ \Carbon\Carbon::parse($transaction->date)->format('d/m/Y') }}
                                </span>
                                <span class="text-xs text-gray-500">
                                    {{ $transaction->time ? \Carbon\Carbon::parse($transaction->time)->format('H:i') : '' }}
                                </span>
                            </div>
                            <span class="font-bold font-mono {{ $transaction->type == 'ingreso' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $transaction->type == 'ingreso' ? '+' : '-' }} S/ {{ number_format(abs($transaction->amount), 2) }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-800 dark:text-gray-200 mb-2">{{ $transaction->description }}</p>
                        <div class="flex justify-between items-center text-xs">
                            <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded">{{ $transaction->account->name }}</span>
                            <div class="flex gap-4">
                                <a href="{{ route('transactions.edit', $transaction) }}" class="text-blue-600">Editar</a>
                                </div>
                        </div>
                    </div>
                @empty
                     <div class="p-6 text-center text-gray-500">Sin movimientos</div>
                @endforelse
            </div>

            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
@endsection
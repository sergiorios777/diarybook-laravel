@extends('layouts.app1')
@section('title', 'Gestión de Cuentas')

@section('content')
<div class="max-w-5xl mx-auto">

    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">Gestión de Cuentas</h1>
        <a href="{{ route('cuentas.create') }}"
           class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg transition transform hover:-translate-y-0.5 flex items-center gap-2">
            + Nueva Cuenta
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 text-green-800 dark:text-green-200 rounded-xl">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 p-4 bg-red-100 dark:bg-red-900/30 border border-red-300 dark:border-red-700 text-red-800 dark:text-red-200 rounded-xl">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-900 text-white">
                    <tr>
                        <th class="px-8 py-5 text-left">Nombre</th>
                        <th class="px-8 py-5 text-right">Saldo Inicial</th>
                        <th class="px-8 py-5 text-right">Saldo Actual</th>
                        <th class="px-8 py-5 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($accounts as $account)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                            <td class="px-8 py-6 font-medium text-gray-800 dark:text-gray-200">
                                {{ $account->name }}
                            </td>
                            <td class="px-8 py-6 text-right font-mono text-gray-600 dark:text-gray-400">
                                S/ {{ number_format($account->initial_balance, 2) }}
                            </td>
                            <td class="px-8 py-6 text-right font-mono text-xl font-bold
                                {{ $account->current_balance >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                S/ {{ number_format($account->current_balance, 2) }}
                            </td>
                            <td class="px-8 py-6 text-center">
                                <div class="flex justify-center gap-4">
                                    <a href="{{ route('cuentas.edit', $account) }}"
                                       class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-200 font-medium">
                                        Editar
                                    </a>
                                    <form action="{{ route('cuentas.destroy', $account) }}" method="POST"
                                          onsubmit="return confirm('¿Estás seguro? Solo puedes eliminar cuentas sin transacciones.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-200 font-medium">
                                            Eliminar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-16 text-gray-500 dark:text-gray-400">
                                <div class="text-6xl mb-4">Bank</div>
                                <p class="text-xl">No hay cuentas creadas aún</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
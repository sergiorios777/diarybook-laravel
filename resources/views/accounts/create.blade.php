@extends('layouts.app1')
@section('title', 'Crear Cuenta')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">Crear Nueva Cuenta</h1>
            <a href="{{ route('cuentas.index') }}"
               class="text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200">
                ← Volver
            </a>
        </div>

        <form action="{{ route('cuentas.store') }}" method="POST" class="space-y-8">
            @csrf
            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">Nombre de la Cuenta</label>
                <input type="text" name="name" required placeholder="Ej: Caja Principal, Banco BCP"
                       class="w-full px-5 py-4 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-4 focus:ring-blue-500 focus:border-blue-500 transition text-lg">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">Saldo Inicial</label>
                <input type="number" name="initial_balance" step="0.01" min="0" value="0.00" required
                       class="w-full px-5 py-4 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-4 focus:ring-blue-500 focus:border-blue-500 transition text-lg font-mono">
            </div>

            <div class="pt-6">
                <button type="submit"
                        class="w-full py-5 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold text-xl rounded-xl shadow-xl transition transform hover:-translate-y-1">
                    Guardar Cuenta
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
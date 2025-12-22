@extends('layouts.app1')
@section('title', 'Importar Ventas')

@section('content')
<div class="max-w-3xl mx-auto py-10">
    
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">Importar Ventas Masivas</h1>
        <p class="text-gray-500 mt-2">Sube tu archivo CSV (Excel) para cargar las ventas pendientes.</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
        <div class="p-8">
            
            <div class="bg-blue-50 dark:bg-blue-900/30 border-l-4 border-blue-500 p-4 mb-6 rounded-r">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700 dark:text-blue-200">
                            <strong>Importante:</strong> El archivo debe ser formato <code>.CSV</code>.
                            <br>Asegúrate de que la columna <strong>Category ID</strong> sea <strong>6</strong>.
                        </p>
                    </div>
                </div>
            </div>

            <div class="mb-8">
                <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-3">
                    Estructura requerida (7 Columnas):
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left text-gray-500 dark:text-gray-400 border rounded-lg">
                        <thead class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 uppercase">
                            <tr>
                                <th class="px-4 py-2">Fecha</th>
                                <th class="px-4 py-2">Hora</th>
                                <th class="px-4 py-2">Descripción</th>
                                <th class="px-4 py-2">Monto</th>
                                <th class="px-4 py-2">ID Cta</th>
                                <th class="px-4 py-2">ID Cat</th>
                                <th class="px-4 py-2 text-blue-600">Estado</th> </tr>
                        </thead>
                        <tbody>
                            <tr class="border-t dark:border-gray-600 bg-green-50 dark:bg-green-900/10">
                                <td class="px-4 py-2">2023-12-01</td>
                                <td class="px-4 py-2">14:30</td>
                                <td class="px-4 py-2">Venta Tienda #001</td>
                                <td class="px-4 py-2">150.00</td>
                                <td class="px-4 py-2">1</td>
                                <td class="px-4 py-2">6</td>
                                <td class="px-4 py-2 font-bold text-green-700">OK</td>
                            </tr>
                            <tr class="border-t dark:border-gray-600 bg-red-50 dark:bg-red-900/10 opacity-75">
                                <td class="px-4 py-2">2023-12-01</td>
                                <td class="px-4 py-2">14:35</td>
                                <td class="px-4 py-2">Error de digitación</td>
                                <td class="px-4 py-2">150.00</td>
                                <td class="px-4 py-2">1</td>
                                <td class="px-4 py-2">6</td>
                                <td class="px-4 py-2 font-bold text-red-600">ANULADO</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p class="text-xs text-gray-500 mt-2">
                    * Nota: Si la columna "Estado" dice <strong>ANULADO</strong>, esa fila no se importará. Cualquier otro texto (o dejarlo vacío) se importará como venta válida.
                </p>
            </div>

            <form action="{{ route('transactions.import.process') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Seleccionar Archivo (.csv)
                    </label>
                    <input type="file" name="file" accept=".csv, .txt" required
                        class="block w-full text-sm text-gray-500 dark:text-gray-400
                        file:mr-4 file:py-3 file:px-6
                        file:rounded-full file:border-0
                        file:text-sm file:font-semibold
                        file:bg-blue-50 file:text-blue-700
                        hover:file:bg-blue-100 dark:file:bg-blue-900 dark:file:text-blue-200
                        transition cursor-pointer border border-gray-300 dark:border-gray-600 rounded-xl p-1"
                    />
                    @error('file')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end gap-4">
                    <a href="{{ route('transactions.index') }}" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 font-medium">
                        Cancelar
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg hover:shadow-xl transition transform hover:-translate-y-0.5">
                        Subir y Procesar 🚀
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
{{-- create y edit son casi idénticos --}}
@extends('layouts.app1')
@section('title', (isset($category) ? 'Editar' : 'Crear') . ' Categoría')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">
                {{ isset($category) ? 'Editar Categoría' : 'Crear Nueva Categoría' }}
            </h1>
            <a href="{{ route('categorias.index') }}"
               class="text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200">
                ← Volver
            </a>
        </div>

        <form action="{{ isset($category) ? route('categorias.update', $category) : route('categorias.store') }}" method="POST" class="space-y-8">
            @csrf
            @if(isset($category)) @method('PUT') @endif

            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">Nombre</label>
                <input type="text" name="name" value="{{ old('name', $category->name ?? '') }}" required
                       class="w-full px-5 py-4 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-4 focus:ring-purple-500 transition text-lg">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">Tipo</label>
                <select name="type" required
                        class="w-full px-5 py-4 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-4 focus:ring-purple-500 transition text-lg">
                    <option value="ingreso" {{ old('type', $category->type ?? '') == 'ingreso' ? 'selected' : '' }}>Ingreso</option>
                    <option value="gasto" {{ old('type', $category->type ?? '') == 'gasto' ? 'selected' : '' }}>Gasto</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">Categoría Padre (Opcional)</label>
                <select name="parent_id"
                        class="w-full px-5 py-4 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-4 focus:ring-purple-500 transition text-lg">
                    <option value="">-- Sin padre --</option>
                    @foreach($categories as $cat)
                        @if(!isset($category) || $cat->id != $category->id)
                            <option value="{{ $cat->id }}" {{ old('parent_id', $category->parent_id ?? '') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>

            <div class="pt-6">
                <button type="submit"
                        class="w-full py-5 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white font-bold text-xl rounded-xl shadow-xl transition transform hover:-translate-y-1">
                    {{ isset($category) ? 'Actualizar' : 'Crear' }} Categoría
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
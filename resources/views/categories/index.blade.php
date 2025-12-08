@extends('layouts.app1')
@section('title', 'Gestión de Categorías')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">Gestión de Categorías</h1>
        <a href="{{ route('categorias.create') }}"
           class="px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-xl shadow-lg transition flex items-center gap-2">
            + Nueva Categoría
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 text-green-800 dark:text-green-200 rounded-xl">
            {{ session('success') }}
        </div>
    @endif

    <div class="space-y-4">
        @forelse($categories as $category)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 hover:shadow-xl transition">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="text-2xl text-purple-600 dark:text-purple-400">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ $category->name }}</h3>
                            <span class="inline-block px-3 py-1 text-xs font-bold rounded-full mt-1
                                {{ $category->type == 'ingreso' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' }}">
                                {{ ucfirst($category->type) }}
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <a href="{{ route('categorias.edit', $category) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                            Editar
                        </a>
                        <form action="{{ route('categorias.destroy', $category) }}" method="POST"
                              onsubmit="return confirm('¿Eliminar esta categoría y sus subcategorías?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 font-medium">
                                Eliminar
                            </button>
                        </form>
                    </div>
                </div>

                @if($category->children->isNotEmpty())
                    <div class="mt-6 ml-10 pl-6 border-l-4 border-gray-300 dark:border-gray-600 space-y-3">
                        @foreach($category->children as $child)
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <span class="text-gray-500 dark:text-gray-400">
                                        <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </span>
                                    <span class="font-medium">{{ $child->name }}</span>
                                    <span class="text-xs px-2 py-1 rounded-full
                                        {{ $child->type == 'ingreso' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ ucfirst($child->type) }}
                                    </span>
                                </div>
                                <div class="flex gap-4 text-sm">
                                    <a href="{{ route('categorias.edit', $child) }}" class="text-blue-600 hover:text-blue-800">Editar</a>
                                    <form action="{{ route('categorias.destroy', $child) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('¿Eliminar?')">Eliminar</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @empty
            <div class="text-center py-16 text-gray-500 dark:text-gray-400">
                <div class="text-6xl mb-4">Empty</div>
                <p class="text-xl">No hay categorías creadas</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
{{-- resources/views/layouts/partials/sidebar.blade.php --}}
<aside class="fixed inset-y-0 left-0 w-72 bg-gray-900 text-gray-200 dark:bg-gray-800 transform transition-transform duration-300 ease-in-out z-50 flex flex-col
               lg:translate-x-0 lg:static lg:inset-auto"
       :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

    <!-- Header fijo -->
    <div class="h-16 flex items-center justify-between px-6 border-b border-gray-700">
        <h1 class="text-xl font-bold text-white flex items-center gap-2">
            Book Mi Libro Diario
        </h1>
        <button class="lg:hidden text-gray-400 hover:text-white" @click="sidebarOpen = false">
            Close
        </button>
    </div>

    <!-- Usuario -->
    <div class="px-6 py-5 border-b border-gray-700">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <div>
                <p class="text-sm text-gray-400">Bienvenido</p>
                <p class="font-semibold text-white">{{ Auth::user()->name }}</p>
            </div>
        </div>
    </div>

    <!-- SCROLL INDEPENDIENTE DEL MENÚ -->
    <div class="flex-1 overflow-y-auto py-6 px-4"> <!-- ← Este div hace magia -->
        <nav class="space-y-8">

            <!-- Principal -->
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-2 mb-3">Principal</p>
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }} group flex items-center gap-3 px-4 py-3 rounded-lg font-medium transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard
                </a>
            </div>

            <!-- Operaciones -->
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-2 mb-3">Operaciones</p>
                <a href="{{ route('transactions.create') }}" class="{{ request()->routeIs('transactions.create') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }} group flex items-center gap-3 px-4 py-3 rounded-lg font-medium transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Nueva Transacción
                </a>
                <a href="{{ route('transactions.index') }}" class="{{ request()->routeIs('transactions.*') && !request()->routeIs('transactions.create') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }} group flex items-center gap-3 px-4 py-3 rounded-lg font-medium transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Historial / Libro
                </a>
                <a href="{{ route('cash_counts.index') }}" class="{{ request()->routeIs('cash_counts.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }} group flex items-center gap-3 px-4 py-3 rounded-lg font-medium transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2  0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Arqueo de Caja
                </a>
            </div>

            <!-- Reportes -->
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-2 mb-3">Reportes</p>
                <a href="{{ route('reports.weekly') }}" class="{{ request()->routeIs('reports.weekly') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }} group flex items-center gap-3 px-4 py-3 rounded-lg font-medium transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Semanal
                </a>
                <a href="{{ route('reports.monthly') }}" class="{{ request()->routeIs('reports.monthly') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }} group flex items-center gap-3 px-4 py-3 rounded-lg font-medium transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Mensual
                </a>
                <a href="{{ route('reports.detailed') }}" class="{{ request()->routeIs('reports.detailed') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }} group flex items-center gap-3 px-4 py-3 rounded-lg font-medium transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 01-14 0z"/></svg>
                    Detallado
                </a>
                <a href="{{ route('reports.income') }}" class="{{ request()->routeIs('reports.income') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }} group flex items-center gap-3 px-4 py-3 rounded-lg font-medium transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    Ingresos
                </a>
                <a href="{{ route('reports.expenses') }}" class="{{ request()->routeIs('reports.expenses') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }} group flex items-center gap-3 px-4 py-3 rounded-lg font-medium transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>
                    Gastos
                </a>
            </div>

            <!-- Configuración -->
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-2 mb-3">Configuración</p>
                <a href="{{ route('cuentas.index') }}" class="{{ request()->routeIs('cuentas.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }} group flex items-center gap-3 px-4 py-3 rounded-lg font-medium transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    Cuentas
                </a>
                <a href="{{ route('categorias.index') }}" class="{{ request()->routeIs('categorias.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }} group flex items-center gap-3 px-4 py-3 rounded-lg font-medium transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                    Categorías
                </a>
            </div>

        </nav>
    </div>

    <!-- Botón Salir (siempre visible, abajo) -->
    <div class="border-t border-gray-700 p-4">
        <form method="POST" action="{{ route('logout') }}" class="w-full">
            @csrf
            <button type="submit"
                    class="w-full flex items-center justify-center gap-3 px-4 py-3 bg-red-600/20 hover:bg-red-600 text-red-400 hover:text-white rounded-lg font-medium transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Cerrar Sesión
            </button>
        </form>
    </div>
</aside>
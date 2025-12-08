<!DOCTYPE html>
<html lang="es" x-data="{ sidebarOpen: false, darkMode: false }" :class="darkMode ? 'dark' : ''">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel')</title>

    {{-- Breeze + Tailwind --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('styles')
</head>

<body class="bg-gray-100 dark:bg-gray-900 flex min-h-screen">
    @include('layouts.partials.sidebar')

    <div class="flex-1 flex flex-col min-h-screen"> <!-- ← min-h-screen aquí -->
        <!-- Header fijo -->
        <header class="h-16 bg-white dark:bg-gray-800 shadow flex items-center px-4 justify-between z-40 print:hidden">
            <button class="lg:hidden text-gray-700 dark:text-gray-300 text-2xl" @click="sidebarOpen = true">
                Menu
            </button>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                @yield('title')
            </h2>
            <button @click="darkMode = !darkMode"
                    class="px-3 py-1 rounded text-sm bg-gray-200 dark:bg-gray-700 dark:text-gray-200">
                <span x-show="!darkMode">Moon Modo oscuro</span>
                <span x-show="darkMode">Sun Modo claro</span>
            </button>
        </header>

        <!-- Contenido con scroll independiente -->
        <main class="flex-1 overflow-y-auto">
            <div class="p-6">
                @yield('content')
            </div>
        </main>
    </div>
    @stack('scripts')
</body>
</html>
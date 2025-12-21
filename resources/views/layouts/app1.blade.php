<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel')</title>

    <script>
        if (localStorage.getItem('darkMode') === 'true' || 
           (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    {{-- Breeze + Tailwind --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        /* Ocultar barra de scroll para Chrome, Safari y Opera */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        /* Ocultar barra de scroll para IE, Edge y Firefox */
        .no-scrollbar {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
        }
    </style>

    @stack('styles')
</head>

<body class="font-sans antialiased bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 h-screen overflow-hidden flex"
      x-data="{ 
          sidebarOpen: false, 
          darkMode: localStorage.getItem('darkMode') === 'true'
      }"
      x-init="$watch('darkMode', val => {
          localStorage.setItem('darkMode', val);
          if (val) {
              document.documentElement.classList.add('dark');
          } else {
              document.documentElement.classList.remove('dark');
          }
      })">

    @include('layouts.partials.sidebar')

    <div class="flex-1 flex flex-col h-screen relative"> 
        
        <header class="h-16 bg-white dark:bg-gray-800 shadow flex items-center px-4 justify-between z-40 print:hidden transition-colors duration-300 flex-shrink-0">
            
            <button @click="sidebarOpen = true" 
                    class="lg:hidden p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-700 focus:outline-none transition-colors">
                <span class="sr-only">Abrir menú</span>
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>

            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 tracking-tight">
                @yield('title')
            </h2>

            <button @click="darkMode = !darkMode" 
                    class="p-2 rounded-full text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700 focus:outline-none transition-colors"
                    title="Cambiar tema">
                <span x-show="!darkMode" class="flex items-center gap-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                    </svg>
                </span>
                <span x-show="darkMode" style="display: none;" class="flex items-center gap-2">
                    <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </span>
            </button>
        </header>

        <main class="flex-1 overflow-y-auto bg-gray-100 dark:bg-gray-900 transition-colors duration-300">
            <div class="p-6">
                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')
</body>
</html>
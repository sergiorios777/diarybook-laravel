<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Contable - @yield('title', 'Panel')</title>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.14.9/cdn.min.js" defer></script>

    <style>
        /* --- ESTILOS GLOBALES (RESET) --- */
        * { box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            margin: 0; 
            background-color: #f4f6f9; 
            color: #333;
            display: flex; /* CLAVE: Flexbox para Sidebar + Contenido */
            min-height: 100vh;
        }

        /* --- SIDEBAR (MENÚ LATERAL) --- */
        .sidebar {
            width: 260px;
            background-color: #343a40; /* Gris oscuro profesional */
            color: #c2c7d0;
            flex-shrink: 0; /* No permitimos que se encoja */
            display: flex;
            flex-direction: column;
            transition: all 0.3s;
        }

        .sidebar-header {
            padding: 20px;
            background-color: #3f474e;
            text-align: center;
            border-bottom: 1px solid #4b545c;
        }
        .sidebar-header h3 { margin: 0; color: white; font-size: 1.2rem; }

        .sidebar-menu {
            padding: 10px 0;
            flex-grow: 1;
            overflow-y: auto; /* Scroll si hay muchos ítems */
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: #c2c7d0;
            text-decoration: none;
            transition: background 0.2s, color 0.2s;
            border-left: 3px solid transparent;
        }

        .sidebar-menu a:hover {
            background-color: #494e53;
            color: white;
        }

        /* Estado Activo (Resalta la página actual) */
        .sidebar-menu a.active {
            background-color: #007bff;
            color: white;
            border-left-color: white;
        }

        .sidebar-menu i { margin-right: 10px; width: 20px; text-align: center; }
        .menu-label { text-transform: uppercase; font-size: 0.75rem; padding: 15px 20px 5px; color: #6c757d; font-weight: bold; }

        /* --- ÁREA DE CONTENIDO PRINCIPAL --- */
        .main-content {
            flex-grow: 1;
            padding: 30px;
            overflow-y: auto; /* El contenido hace scroll independientemente */
            width: 100%; /* Asegura que ocupe el resto */
        }

        /* --- UTILIDADES COMUNES --- */
        .btn { padding: 8px 15px; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; border: none; }
        .btn-primary { background-color: #007bff; color: white; }
        .btn-primary:hover { background-color: #0056b3; }
        
        /* Estilos para alertas */
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }

        /* --- Usuario y formulario logout --- */
        .user-info {
            font-weight: bold;
            color: #ffffffff;
            padding: 5px 20px;
        }

        .nav-link-logout {
            color: #6c757d; /* Gris oscuro */
            text-decoration: none;
            font-weight: 500;
            font-size: 16px;
            padding: 5px 10px;
            border: 1px solid transparent;
            transition: all 0.2s;
        }

        .nav-link-logout:hover {
            color: #dc3545; /* Se pone rojo al pasar el mouse */
            text-decoration: underline;
        }

        /* --- IMPRESIÓN (Ocultar sidebar) --- */
        @media print {
            .sidebar { display: none; }
            body { display: block; background: white; }
            .main-content { padding: 0; width: 100%; margin: 0; }
        }
    </style>

    @stack('styles')
</head>
<body>

    <aside class="sidebar">
        <div class="sidebar-header">
            <h3>📘 Mi Libro Diario</h3>
        </div>

        <nav class="sidebar-menu">
            <div class="user-info">Hola, {{ Auth::user()->name }}</div>
    
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" class="nav-link-logout">
                    Salir
                </a>
            </form>
            
            <div class="menu-label">Principal</div>
            
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i>📊</i> Dashboard
            </a>

            <div class="menu-label">Operaciones</div>

            <a href="{{ route('transactions.create') }}" class="{{ request()->routeIs('transactions.create') ? 'active' : '' }}">
                <i>➕</i> Nueva Transacción
            </a>
            <a href="{{ route('transactions.index') }}" class="{{ request()->routeIs('transactions.index*') ? 'active' : '' }}">
                <i>📝</i> Historial / Libro
            </a>
            <a href="{{ route('cash_counts.index') }}" class="{{ request()->routeIs('cash_counts.index') ? 'active' : '' }}">
                <i>💰</i> Arqueo de Caja
            </a>

            <div class="menu-label">Reportes</div>

            <a href="{{ route('reports.weekly') }}" class="{{ request()->routeIs('reports.weekly') ? 'active' : '' }}">
                <i>📅</i> Semanal
            </a>
            <a href="{{ route('reports.monthly') }}" class="{{ request()->routeIs('reports.monthly') ? 'active' : '' }}">
                <i>🗓️</i> Mensual
            </a>
            <a href="{{ route('reports.detailed') }}" class="{{ request()->routeIs('reports.detailed') ? 'active' : '' }}">
                <i>🔍</i> Detallado
            </a>
            <a href="{{ route('reports.income') }}" class="{{ request()->routeIs('reports.income') ? 'active' : '' }}">
                <i>📈</i> Ingresos
            </a>
            <a href="{{ route('reports.expenses') }}" class="{{ request()->routeIs('reports.expenses') ? 'active' : '' }}">
                <i>📉</i> Gastos
            </a>

            <div class="menu-label">Configuración</div>

            <a href="{{ route('cuentas.index') }}" class="{{ request()->routeIs('cuentas.*') ? 'active' : '' }}">
                <i>💳</i> Cuentas
            </a>
            <a href="{{ route('categorias.index') }}" class="{{ request()->routeIs('categorias.*') ? 'active' : '' }}">
                <i>📂</i> Categorías
            </a>
        </nav>
    </aside>

    <main class="main-content">
        @yield('content')
    </main>

    @stack('scripts')

</body>
</html>
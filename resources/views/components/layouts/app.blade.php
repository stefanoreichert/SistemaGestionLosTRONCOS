<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Los Troncos Resto Bar' }}</title>
    @vite(['resources/css/app.css', 'resources/css/layout.css'])

</head>
<body>
    <div class="app">
        <aside class="sidebar" id="sidebar">
            <div class="brand">
                <a class="brand-name" href="{{ route('dashboard') }}" aria-label="Los Troncos Resto Bar">
                    <span class="brand-name-main">Los Troncos</span>
                    <span class="brand-name-subtitle">Resto Bar</span>
                </a>
                <button class="collapse-btn" type="button" id="collapseSidebar" aria-label="Contraer menu">‹</button>
            </div>
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <span class="nav-icon">P</span><span class="nav-text">Panel principal</span>
            </a>
            <div class="nav-section">Gestion</div>
            <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">
                <span class="nav-icon">P</span><span class="nav-text">Productos</span>
            </a>
            <a class="nav-link {{ request()->routeIs('tables.*') ? 'active' : '' }}" href="{{ route('tables.index') }}">
                <span class="nav-icon">M</span><span class="nav-text">Mesas</span>
            </a>
            <a class="nav-link {{ request()->routeIs('tickets.*') ? 'active' : '' }}" href="{{ route('tickets.index') }}">
                <span class="nav-icon">T</span><span class="nav-text">Tickets</span>
            </a>
            <a class="nav-link {{ request()->routeIs('reports.daily') ? 'active' : '' }}" href="{{ route('reports.daily') }}">
                <span class="nav-icon">D</span><span class="nav-text">Resumen Diario</span>
            </a>
            <a class="nav-link {{ request()->routeIs('reports.monthly') ? 'active' : '' }}" href="{{ route('reports.monthly') }}">
                <span class="nav-icon">R</span><span class="nav-text">Resumen Mensual</span>
            </a>
        </aside>

        <main class="main">
            <header class="topbar">
                <div style="display:flex;align-items:center;gap:12px;">
                    <button class="btn mobile-toggle" type="button" id="openSidebar">Menu</button>
                    <h1>{{ $title ?? 'Panel principal' }}</h1>
                </div>
                <div class="user">Administrador</div>
            </header>

            <section class="content">
                @if (session('status'))
                    <div class="status">{{ session('status') }}</div>
                @endif

                {{ $slot }}
            </section>
        </main>
    </div>

    <script>
        const sidebar = document.getElementById('sidebar');
        document.getElementById('collapseSidebar')?.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
        });
        document.getElementById('openSidebar')?.addEventListener('click', () => {
            sidebar.classList.toggle('open');
        });
    </script>
    q
</body>
</html>

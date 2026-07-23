<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Los Troncos Resto Bar' }}</title>
    <style>
        :root {
            --bg: #f5f6f8;
            --panel: #ffffff;
            --line: #e4e7ec;
            --text: #101828;
            --muted: #667085;
            --dark: #111827;
            --dark-soft: #1f2937;
            --green: #16a34a;
            --green-bg: #dcfce7;
            --red: #dc2626;
            --red-bg: #fee2e2;
            --blue: #2563eb;
        }

        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, Helvetica, sans-serif; color: var(--text); background: var(--bg); }
        a { color: inherit; text-decoration: none; }
        button, input, textarea, select { font: inherit; }
        .app { min-height: 100vh; display: flex; }
        .sidebar { width: 260px; background: var(--dark); color: #fff; padding: 20px 14px; position: sticky; top: 0; height: 100vh; transition: width .2s ease; }
        .sidebar.collapsed { width: 82px; }
        .brand { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 4px 8px 24px; }
        .brand-mark { display: flex; align-items: center; min-width: 0; }
        .brand-logo { width: 180px; height: auto; display: block; }
        .brand-title { font-weight: 700; font-size: 17px; white-space: nowrap; }
        .collapse-btn { width: 34px; height: 34px; border: 1px solid #344054; border-radius: 6px; color: #fff; background: transparent; cursor: pointer; }
        .nav-section { color: #cbd5e1; font-size: 12px; margin: 18px 8px 8px; text-transform: uppercase; }
        .nav-link { display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 8px; color: #f8fafc; margin-bottom: 6px; }
        .nav-link.active, .nav-link:hover { background: var(--dark-soft); }
        .nav-icon { width: 22px; text-align: center; flex: 0 0 22px; }
        .sidebar.collapsed .brand-title, .sidebar.collapsed .nav-text, .sidebar.collapsed .nav-section { display: none; }
        .sidebar.collapsed .brand-logo { width: 42px; max-width: 42px; }
        .main { flex: 1; min-width: 0; }
        .topbar { height: 72px; background: var(--panel); border-bottom: 1px solid var(--line); display: flex; align-items: center; justify-content: space-between; padding: 0 28px; }
        .topbar h1 { font-size: 24px; margin: 0; }
        .user { color: var(--muted); font-size: 14px; }
        .content { padding: 24px 28px 40px; }
        .grid { display: grid; gap: 18px; }
        .metrics { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        .two-columns { grid-template-columns: minmax(0, 1.1fr) minmax(320px, .9fr); margin-top: 22px; }
        .tables-overview { grid-template-columns: minmax(560px, 1.65fr) minmax(300px, .65fr); align-items: start; }
        .table-workspace { grid-template-columns: minmax(420px, 1.35fr) minmax(380px, .65fr); align-items: start; }
        .card { background: var(--panel); border: 1px solid var(--line); border-radius: 8px; box-shadow: 0 1px 2px rgba(16, 24, 40, .04); }
        .card-header { padding: 18px 20px; border-bottom: 1px solid var(--line); display: flex; align-items: center; justify-content: space-between; gap: 12px; }
        .card-body { padding: 20px; }
        .metric { padding: 20px; display: flex; align-items: center; gap: 18px; }
        .metric-link { transition: border-color .15s ease, box-shadow .15s ease, transform .15s ease; }
        .metric-link:hover { border-color: #cbd5e1; box-shadow: 0 8px 18px rgba(16, 24, 40, .08); transform: translateY(-1px); }
        .summary-list { display: grid; gap: 12px; }
        .summary-list > div { display: flex; align-items: center; justify-content: space-between; gap: 14px; padding-bottom: 12px; border-bottom: 1px solid var(--line); }
        .summary-list > div:last-child { padding-bottom: 0; border-bottom: 0; }
        .summary-list span { color: var(--muted); }
        .summary-list strong { font-size: 18px; text-align: right; }
        .metric-icon { width: 58px; height: 58px; border-radius: 8px; display: grid; place-items: center; font-size: 24px; background: #e0e7ff; color: #1e3a8a; }
        .metric-icon.green { background: var(--green-bg); color: #166534; }
        .metric-icon.red { background: var(--red-bg); color: #991b1b; }
        .metric-value { font-size: 26px; font-weight: 700; margin: 4px 0; }
        .muted { color: var(--muted); font-size: 14px; }
        .btn { display: inline-flex; align-items: center; justify-content: center; min-height: 38px; padding: 8px 14px; border: 1px solid var(--line); border-radius: 6px; background: #fff; color: var(--text); cursor: pointer; }
        .btn.primary { background: var(--blue); border-color: var(--blue); color: #fff; }
        .btn.danger { background: #fff; border-color: #fecaca; color: var(--red); }
        .btn.full { width: 100%; }
        .status { margin-bottom: 16px; padding: 12px 14px; border-radius: 6px; background: var(--green-bg); color: #166534; border: 1px solid #bbf7d0; }
        .table-grid { display: grid; grid-template-columns: repeat(10, minmax(52px, 1fr)); gap: 12px; }
        .table-cell { min-height: 58px; display: grid; place-items: center; border-radius: 7px; font-size: 18px; font-weight: 700; border: 1px solid #86efac; background: var(--green-bg); color: #052e16; }
        .table-cell.occupied { border-color: #fca5a5; background: var(--red); color: #fff; }
        .legend { display: flex; gap: 16px; align-items: center; margin-bottom: 18px; color: var(--muted); font-size: 14px; }
        .dot { width: 14px; height: 14px; border-radius: 4px; display: inline-block; margin-right: 6px; vertical-align: middle; }
        .dot.free { background: var(--green-bg); border: 1px solid #86efac; }
        .dot.occupied { background: var(--red); }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 12px 14px; border-bottom: 1px solid var(--line); font-size: 14px; vertical-align: top; }
        th { color: #344054; font-weight: 700; background: #fafafa; }
        .badge { display: inline-flex; padding: 4px 10px; border-radius: 6px; font-size: 12px; border: 1px solid var(--line); }
        .badge.green { background: var(--green-bg); border-color: #bbf7d0; color: #166534; }
        .badge.red { background: var(--red-bg); border-color: #fecaca; color: #991b1b; }
        .actions { display: flex; gap: 8px; align-items: center; }
        .form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
        .field.full { grid-column: 1 / -1; }
        label { display: block; font-weight: 700; margin-bottom: 7px; font-size: 14px; }
        input, textarea, select { width: 100%; border: 1px solid #d0d5dd; border-radius: 6px; padding: 10px 12px; background: #fff; color: var(--text); }
        .quantity-input { width: 82px; min-height: 38px; padding: 8px 10px; }
        textarea { min-height: 110px; resize: vertical; }
        .error { color: var(--red); font-size: 13px; margin-top: 6px; }
        .product-list { display: grid; gap: 10px; }
        .product-row { display: grid; grid-template-columns: 1fr auto; gap: 12px; align-items: center; padding: 12px; border: 1px solid var(--line); border-radius: 8px; }
        .product-search { display: grid; grid-template-columns: 1fr auto; gap: 10px; margin-bottom: 18px; }
        .product-search-field { position: relative; }
        .search-suggestions { display: none; position: absolute; z-index: 10; top: calc(100% + 6px); left: 0; right: 0; max-height: 280px; overflow: auto; background: #fff; border: 1px solid var(--line); border-radius: 8px; box-shadow: 0 12px 24px rgba(16, 24, 40, .12); }
        .search-suggestions.visible { display: block; }
        .search-suggestion { width: 100%; display: grid; grid-template-columns: 1fr auto; gap: 10px; align-items: center; padding: 11px 12px; border: 0; border-bottom: 1px solid var(--line); background: #fff; color: var(--text); text-align: left; cursor: pointer; }
        .search-suggestion:last-child { border-bottom: 0; }
        .search-suggestion:hover, .search-suggestion.active { background: #f8fafc; }
        .search-suggestion small { display: block; color: var(--muted); margin-top: 3px; }
        .search-suggestion-price { color: var(--text); font-weight: 700; white-space: nowrap; }
        .mobile-toggle { display: none; }

        @media (max-width: 980px) {
            .app { display: block; }
            .sidebar { position: fixed; left: 0; top: 0; z-index: 20; transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .mobile-toggle { display: inline-flex; }
            .metrics, .two-columns, .tables-overview, .table-workspace, .form-grid, .product-search { grid-template-columns: 1fr; }
            .table-grid { grid-template-columns: repeat(5, minmax(42px, 1fr)); }
            .topbar { padding: 0 18px; }
            .content { padding: 18px; }
        }
    </style>
</head>
<body>
    <div class="app">
        <aside class="sidebar" id="sidebar">
            <div class="brand">
                <span class="brand-mark">
                    <img class="brand-logo" src="{{ asset('images/los-troncos-logo.svg') }}" alt="Los Troncos Resto Bar">
                </span>
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
</body>
</html>

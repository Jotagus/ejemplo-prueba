<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistema de Inventario')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&family=Space+Mono:wght@400;700&display=swap"
        rel="stylesheet">
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">

    <style>
        :root {
            --emdell-red: #E63946;
            --emdell-orange: #FF6B35;
            --emdell-yellow: #FFC857;
            --dark-bg: #1A1A2E;
            --darker-bg: #16161F;
            --text-dark: #2D2D2D;
            --text-muted: #9CA3AF;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.05);
            --sidebar-width: 280px;
            --topbar-height: 70px;
            --body-bg: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            --card-bg: #ffffff;
            --topbar-bg: #ffffff;
            --border-color: #eeeeee;
        }

        [data-bs-theme="dark"] {
            --body-bg: #121212;
            --text-dark: #e9ecef;
            --topbar-bg: #1a1a2e;
            --card-bg: #1e1e2d;
            --border-color: #2d2d3d;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: var(--body-bg);
            color: var(--text-dark);
            overflow-x: hidden;
            transition: background 0.3s ease, color 0.3s ease;
        }

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(180deg, var(--dark-bg) 0%, var(--darker-bg) 100%);
            z-index: 1000;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease;
        }

        .sidebar-logo {
            padding: 24px;
            background: linear-gradient(135deg, var(--emdell-red), var(--emdell-orange));
            position: relative;
            overflow: hidden;
        }

        .logo-title {
            font-size: 1.3rem;
            font-weight: 800;
            color: white;
            letter-spacing: -0.5px;
        }

        .logo-subtitle {
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.9);
            text-transform: uppercase;
        }

        .sidebar-nav {
            flex: 1;
            padding: 20px 0;
            overflow-y: auto;
        }

        .nav-section-title {
            padding: 0 24px;
            font-size: 0.7rem;
            color: var(--text-muted);
            text-transform: uppercase;
            font-family: 'Space Mono', monospace;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 24px;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: 0.3s;
            margin: 4px 12px;
            border-radius: 10px;
        }

        .nav-link:hover,
        .nav-link.active {
            color: white;
            background: rgba(255, 255, 255, 0.1);
        }

        .nav-link.active {
            border: 1px solid rgba(255, 200, 87, 0.2);
            background: rgba(230, 57, 70, 0.2);
        }

        .sidebar-user {
            padding: 16px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar-user-avatar {
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, var(--emdell-red), var(--emdell-orange));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.85rem;
            color: #fff;
            flex-shrink: 0;
        }

        .sidebar-user-name {
            font-size: 0.85rem;
            font-weight: 700;
            color: #fff;
            line-height: 1.2;
        }

        .sidebar-user-rol {
            font-size: 0.7rem;
            color: rgba(255, 255, 255, 0.45);
            text-transform: capitalize;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }

        .top-bar {
            background: var(--topbar-bg);
            height: var(--topbar-height);
            padding: 0 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
            border-bottom: 1px solid var(--border-color);
            transition: background 0.3s ease;
        }

        .page-title-section h1 {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
            background: linear-gradient(135deg, var(--emdell-red), var(--emdell-orange));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .theme-toggle {
            cursor: pointer;
            padding: 8px;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: grid;
            place-items: center;
            background: #f8f9fa;
            border: none;
            color: var(--text-dark);
            transition: 0.3s;
        }

        [data-bs-theme="dark"] .theme-toggle {
            background: #2d2d3d;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 15px;
            background: #f8f9fa;
            border-radius: 12px;
            cursor: pointer;
            border: none;
            transition: background 0.2s;
        }

        .user-profile:hover {
            background: #eee;
        }

        [data-bs-theme="dark"] .user-profile {
            background: #2d2d3d;
        }

        [data-bs-theme="dark"] .user-profile:hover {
            background: #3a3a4d;
        }

        .user-avatar {
            width: 35px;
            height: 35px;
            background: var(--emdell-red);
            color: white;
            border-radius: 50%;
            display: grid;
            place-items: center;
            font-weight: 800;
            font-size: 0.8rem;
        }

        .topbar-dropdown {
            min-width: 220px;
            border-radius: 14px !important;
            border: 1px solid var(--border-color) !important;
            background: var(--card-bg) !important;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12) !important;
            padding: 0.5rem !important;
            margin-top: 0.5rem !important;
        }

        .topbar-dropdown .dropdown-item {
            border-radius: 8px;
            padding: 0.5rem 0.85rem;
            font-size: 0.875rem;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 0.6rem;
            transition: background 0.15s;
        }

        .topbar-dropdown .dropdown-item:hover {
            background: rgba(255, 107, 53, 0.08);
            color: #FF6B35;
        }

        .topbar-dropdown .dropdown-item.text-danger:hover {
            background: rgba(239, 68, 68, 0.08);
            color: #EF4444 !important;
        }

        .topbar-dropdown .dropdown-divider {
            border-color: var(--border-color);
            margin: 0.35rem 0;
        }

        .topbar-user-info {
            padding: 0.6rem 0.85rem 0.5rem;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 0.35rem;
        }

        .content-area {
            padding: 30px;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert {
            border: none;
            border-left: 4px solid;
            border-radius: 12px;
            box-shadow: var(--shadow-sm);
        }

        .mobile-toggle {
            display: none;
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 55px;
            height: 55px;
            background: var(--emdell-red);
            color: white;
            border-radius: 50%;
            place-items: center;
            z-index: 1001;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .mobile-toggle {
                display: grid;
            }

            .top-bar {
                padding: 0 20px;
            }
        }
    </style>
    @yield('styles')
</head>

<body>

    <aside class="sidebar" id="sidebar">
        <div class="sidebar-logo">
            <div class="d-flex align-items-center" style="gap:14px;">
                <img src="{{ asset('imagenes/logo_emdell.png') }}" width="50" alt="Logo">
                <div>
                    <div class="logo-title">INVENTARIO</div>
                    <div class="logo-subtitle">BIENES DE CONSUMO</div>
                </div>
            </div>
        </div>

        <nav class="sidebar-nav">

            <div class="nav-section mb-4">
                <div class="nav-section-title mb-2">Principal</div>
                <a href="{{ route('dashboard') }}"
                    class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-fill"></i> Inicio
                </a>
            </div>

            {{-- ── GESTIÓN ── --}}
            <div class="nav-section mb-4">
                <div class="nav-section-title mb-2">Gestión</div>

                {{-- Categorías: admin, almacenero, contable --}}
                @if(in_array(session('usuario_rol'), ['admin', 'almacenero', 'contable']))
                    <a href="{{ route('categorias.index') }}"
                        class="nav-link {{ request()->routeIs('categorias.*') ? 'active' : '' }}">
                        <i class="bi bi-folder2-open"></i> Categorías
                    </a>
                @endif

                {{-- Materiales: todos los roles --}}
                <a href="{{ route('materiales.index') }}"
                    class="nav-link {{ request()->routeIs('materiales.*') ? 'active' : '' }}">
                    <i class="bi bi-box-seam-fill"></i> Materiales
                </a>

                {{-- Inventario: todos los roles --}}
                <a href="{{ route('inventario.index') }}"
                    class="nav-link {{ request()->routeIs('inventario.*') ? 'active' : '' }}">
                    <i class="bi bi-clipboard2-data-fill"></i> Inventario
                </a>

                {{-- Movimientos: admin, almacenero, contable --}}
                @if(in_array(session('usuario_rol'), ['admin', 'almacenero', 'contable']))
                    <a href="{{ route('movimientos.index') }}"
                        class="nav-link {{ request()->routeIs('movimientos.*') ? 'active' : '' }}">
                        <i class="bi bi-arrow-left-right"></i> Movimientos
                    </a>
                @endif

                {{-- Reportes: admin, almacenero, contable --}}
                @if(in_array(session('usuario_rol'), ['admin', 'almacenero', 'contable']))
                    <a href="{{ route('reportes.index') }}"
                        class="nav-link {{ request()->routeIs('reportes.*') ? 'active' : '' }}">
                        <i class="bi bi-bar-chart-line"></i> Reportes
                    </a>
                @endif

            </div>

            {{-- ── ADMINISTRACIÓN: solo admin ── --}}
            @isAdmin
            <div class="nav-section mb-4">
                <div class="nav-section-title mb-2">Administración</div>
                <a href="{{ route('usuarios.index') }}"
                    class="nav-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
                    <i class="bi bi-people-fill"></i> Usuarios
                </a>
                <a href="{{ route('backups.index') }}"
                    class="nav-link {{ request()->routeIs('backups.*') ? 'active' : '' }}">
                    <i class="bi bi-database-fill-down"></i> Backup
                </a>
                <a href="{{ route('auditoria.index') }}"
                    class="nav-link {{ request()->routeIs('auditoria.*') ? 'active' : '' }}">
                    <i class="bi bi-activity"></i> Control de Actividades
                </a>
            </div>
            @endisAdmin

        </nav>
    </aside>

    <div class="main-content">
        <header class="top-bar">
            <div class="page-title-section">
                <h1>@yield('page-title', 'Dashboard')</h1>
            </div>

            <div class="d-flex align-items-center gap-3">
                <button class="theme-toggle" id="themeToggle" title="Cambiar tema">
                    <i class="bi bi-moon-stars-fill"></i>
                </button>
                <div class="dropdown">
                    <button class="user-profile dropdown-toggle-no-arrow" data-bs-toggle="dropdown"
                        aria-expanded="false" style="border:none;">
                        <div class="user-avatar">
                            {{ session('usuario_ini', 'U') }}
                        </div>
                        <div class="d-none d-sm-block text-start">
                            <div class="fw-bold small" style="color:var(--text-dark); line-height:1.2;">
                                {{ session('usuario_nombre', 'Usuario') }}
                            </div>
                            <div style="font-size:0.7rem; color:var(--text-muted); text-transform:capitalize;">
                                {{ session('usuario_rol', '') }}
                            </div>
                        </div>
                        <i class="bi bi-chevron-down small" style="color:var(--text-muted);"></i>
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end topbar-dropdown">

                        <li>
                            <div class="topbar-user-info">
                                <div class="fw-bold small" style="color:var(--text-dark);">
                                    {{ session('usuario_nombre', 'Usuario') }}
                                </div>
                                <div style="font-size:0.75rem; color:var(--text-muted);">
                                    {{ session('usuario_email', '') }}
                                </div>
                            </div>
                        </li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right"></i> Cerrar sesión
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <main class="content-area">
            @foreach(['success' => 'check-circle-fill', 'error' => 'exclamation-triangle', 'warning' => 'exclamation-circle'] as $msg => $icon)
                @if(session($msg))
                    <div class="alert alert-{{ $msg == 'error' ? 'danger' : $msg }} alert-dismissible fade show">
                        <i class="bi bi-{{ $icon }}"></i> {{ session($msg) }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
            @endforeach

            @yield('content')
        </main>
    </div>

    <div class="mobile-toggle" id="mobileToggle"><i class="bi bi-list"></i></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const sidebar = document.getElementById('sidebar');
        const mobileToggle = document.getElementById('mobileToggle');
        const toggleIcon = mobileToggle.querySelector('i');

        mobileToggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
            toggleIcon.classList.toggle('bi-list');
            toggleIcon.classList.toggle('bi-x-lg');
        });

        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 992 && !sidebar.contains(e.target) && !mobileToggle.contains(e.target)) {
                sidebar.classList.remove('active');
                toggleIcon.className = 'bi bi-list';
            }
        });

        const themeToggle = document.getElementById('themeToggle');
        const themeIcon = themeToggle.querySelector('i');
        const htmlEl = document.documentElement;

        if (localStorage.getItem('theme') === 'dark') {
            htmlEl.setAttribute('data-bs-theme', 'dark');
            themeIcon.className = 'bi bi-sun-fill';
        }

        themeToggle.addEventListener('click', () => {
            if (htmlEl.getAttribute('data-bs-theme') === 'dark') {
                htmlEl.removeAttribute('data-bs-theme');
                themeIcon.className = 'bi bi-moon-stars-fill';
                localStorage.setItem('theme', 'light');
            } else {
                htmlEl.setAttribute('data-bs-theme', 'dark');
                themeIcon.className = 'bi bi-sun-fill';
                localStorage.setItem('theme', 'dark');
            }
        });
    </script>
    @yield('scripts')
</body>

</html>
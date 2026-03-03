<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#dc3545">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192x192.png') }}">
    <title>SISTEMA POS - @yield('titulo', 'Inicio')</title>

    <!-- Google Fonts - Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <!-- CSS Personalizado -->
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">

    @livewireStyles
    @yield('estilos')
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header d-flex align-items-center justify-content-between">
            <span><i class="fas fa-shield-halved me-2"></i> POS ELITE</span>
            <button class="sidebar-toggle d-lg-none" onclick="toggleSidebar()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <nav class="mt-4 flex-grow-1 overflow-y-auto">
            @can('ver-tablero')
                <a href="{{ route('admin.tablero') }}"
                    class="nav-link {{ request()->routeIs('admin.tablero') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i> Tablero
                </a>
            @endcan

            @can('ver-usuarios')
                <div class="px-4 py-3 text-uppercase text-white-50 small fw-bold"
                    style="font-size: 0.7rem; letter-spacing: 0.1em;">Gestión Principal</div>
                <a href="{{ route('admin.usuarios.inicio') }}"
                    class="nav-link {{ request()->routeIs('admin.usuarios.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i> Usuarios
                </a>
                <a href="{{ route('admin.clientes.inicio') }}"
                    class="nav-link {{ request()->routeIs('admin.clientes.*') ? 'active' : '' }}">
                    <i class="fas fa-user-tie"></i> Clientes
                </a>
            @endcan

            @can('ver-sucursales')
                <a href="{{ route('admin.sucursales.inicio') }}"
                    class="nav-link {{ request()->routeIs('admin.sucursales.*') ? 'active' : '' }}">
                    <i class="fas fa-building"></i> Sucursales
                </a>
            @endcan

            <div class="px-4 py-3 text-uppercase text-white-50 small fw-bold"
                style="font-size: 0.7rem; letter-spacing: 0.1em;">Inventario & Stock</div>
            <a href="{{ route('admin.productos.inicio') }}"
                class="nav-link {{ request()->routeIs('admin.productos.inicio') ? 'active' : '' }}">
                <i class="fas fa-box"></i> Productos
            </a>
            @can('ver-reportes-globales')
                <a href="{{ route('admin.productos.inventario.global') }}"
                    class="nav-link {{ request()->routeIs('admin.productos.inventario.global') ? 'active' : '' }}">
                    <i class="fas fa-warehouse"></i> Inv. Global
                </a>
            @endcan

            <div class="px-4 py-3 text-uppercase text-white-50 small fw-bold"
                style="font-size: 0.7rem; letter-spacing: 0.1em;">Operaciones</div>
            @can('gestionar-cajas')
                <a href="{{ route('admin.cash-register.index') }}"
                    class="nav-link {{ request()->routeIs('admin.cash-register.*') ? 'active' : '' }}">
                    <i class="fas fa-cash-register"></i> Caja (Apertura/Cierre)
                </a>
                <a href="{{ route('admin.cajas.index') }}"
                    class="nav-link {{ request()->routeIs('admin.cajas.*') ? 'active' : '' }}">
                    <i class="fas fa-tools"></i> Gestionar Cajas
                </a>
            @endcan

            @can('crear-ventas')
                <a href="{{ route('admin.pos') }}" class="nav-link {{ request()->routeIs('admin.pos') ? 'active' : '' }}">
                    <i class="fas fa-shopping-basket"></i> POS (Ventas)
                </a>
            @endcan

            @can('ver-ventas')
                <a href="{{ route('admin.sales.index') }}"
                    class="nav-link {{ request()->routeIs('admin.sales.*') ? 'active' : '' }}">
                    <i class="fas fa-shopping-cart"></i> Ventas
                </a>
            @endcan

            @can('ver-cotizaciones')
                <a href="{{ route('admin.quotations.index') }}"
                    class="nav-link {{ request()->routeIs('admin.quotations.*') ? 'active' : '' }}">
                    <i class="fas fa-file-invoice-dollar"></i> Cotizaciones
                </a>
            @endcan

            @can('ver-reportes-globales')
                <a href="{{ route('admin.reportes.dashboard') }}"
                    class="nav-link {{ request()->routeIs('admin.reportes.*') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar"></i> Reportes
                </a>
                <a href="{{ route('admin.reportes.defectuosos') }}"
                    class="nav-link {{ request()->routeIs('admin.reportes.defectuosos') ? 'active' : '' }}">
                    <i class="fas fa-exclamation-circle"></i> Defectuosos
                </a>
            @endcan

            @can('ver-traslados')
                <a href="{{ route('admin.traslados.inicio') }}"
                    class="nav-link {{ request()->routeIs('admin.traslados.*') ? 'active' : '' }}">
                    <i class="fas fa-exchange-alt"></i> Traslados
                </a>
            @endcan
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="main-content">
        <!-- Navbar Superior -->
        <header class="admin-navbar glass-card border-0 mb-4">
            <div class="d-flex align-items-center">
                <button class="sidebar-toggle me-3" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <h4 class="m-0 fw-bold">@yield('encabezado', 'Bienvenido')</h4>
                @if(session('active_sucursal_nombre'))
                    <a href="{{ route('admin.sucursales.seleccionar') }}"
                        class="ms-4 badge rounded-pill text-decoration-none hover-scale"
                        style="background: rgba(220, 53, 69, 0.2); color: #dc3545; border: 1px solid rgba(220, 53, 69, 0.3); transition: all 0.3s ease;">
                        <i class="fas fa-map-marker-alt me-1"></i> {{ session('active_sucursal_nombre') }}
                        <i class="fas fa-sync-alt ms-1 small opacity-50"></i>
                    </a>
                @endif
            </div>

            <div class="d-flex align-items-center">
                <div class="me-3">
                    @livewire('admin.notification-bell')
                </div>
                <div class="me-4 d-none d-md-block text-end">
                    <div class="fw-bold small text-white">{{ Auth::user()->name }}</div>
                    <div class="text-white-50 small" style="font-size: 0.75rem;">
                        {{ Auth::user()->getRoleNames()->first() ?? 'Sin Rol' }}
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                        <i class="fas fa-sign-out-alt me-1"></i> Salir
                    </button>
                </form>
            </div>
        </header>

        <!-- Contenido Dinámico -->
        <main>
            @if(session('exito'))
                <div class="alert alert-success border-0 glass-card text-white mb-4"
                    style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2) !important;">
                    <i class="fas fa-check-circle me-2 text-success"></i> {{ session('exito') }}
                </div>
            @endif

            <div class="fade-in">
                {{ $slot ?? '' }}
                @yield('contenido')
            </div>
        </main>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content'); if (window.innerWidth > 992) {
                sidebar.classList.toggle('active');
                mainContent.classList.toggle('sidebar-collapsed');
            } else {
                sidebar.classList.toggle('show');
            }
        }
    </script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register("{{ asset('sw.js') }}")
                    .then(reg => console.log('Service Worker registrado', reg))
                    .catch(err => console.log('Error registrando SW', err));
            });
        }
    </script>

    @livewireScripts
    @yield('scripts')
</body>

</html>
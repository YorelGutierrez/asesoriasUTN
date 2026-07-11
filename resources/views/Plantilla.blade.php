<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <!-- Metadatos -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titulo', 'Asesorias-UTN')</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Sweet Alerts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('estilos/sweetalert.css') }}">

    <!-- Estilos del proyecto -->
    <link rel="stylesheet" href="{{ asset('estilos/menu-fotter.css') }}">
    <link rel="stylesheet" href="{{ asset('estilos/UserCard.css') }}">
    <link rel="stylesheet" href="{{ asset('estilos/menu-lateral.css') }}">

    @yield('estilos')
</head>

<body>

    <div class="main-content" id="main-content">

        <!-- ===== Menú Superior ===== -->
        <nav>
            <ul>
                <!-- Botón hamburguesa -->
                <li class="menu-icon">
                    <a href="javascript:void(0)" onclick="toggleMenu()">
                        <i class="bi bi-list"></i>
                    </a>
                </li>

                <!-- Breadcrumbs: lado izquierdo del nav -->
                <li class="breadcrumb-wrapper">
                    @include('partials.breadcrumbs')
                </li>

                <!-- Perfil: lado derecho del nav -->
                <div class="nav-left">
                    <li class="ExtrasMenu"><i class="bi bi-calendar-event-fill"></i></li>
                    <li class="ExtrasMenu"><i class="bi bi-bell-fill"></i></li>
                    <li class="perfil">
                        <a style="color: white;">
                            {{ Auth::user()->nombres . ' ' . Auth::user()->apellido_paterno . ' ' . Auth::user()->apellido_materno }}
                        </a>
                        <img src="{{ Auth::user()->foto_url }}" alt="Perfil" class="perfil-img">
                    </li>
                </div>
            </ul>
        </nav>

        <!-- Contenido de cada vista -->
        <section>
            @yield('contenido')
        </section>

        <!-- Footer -->
        <footer class="pie-pagina">
            <div class="info-universidad">
                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSKLc3LGB1aSLdgvmI7TwAd0-rJLeTNqExKUw&s" alt="Logo UTN">
                <span>Universidad Tecnológica de Nayarit</span>
            </div>
            <div class="derechos-autor">
                © 2026 Equipo Web Asesorias. Todos los derechos reservados.
            </div>
        </footer>

    </div>

    <!-- ===== Dropdown de Perfil ===== -->
    <div class="perfil-dropdown">
        <div class="perfil-header"></div>
        <img src="{{ Auth::user()->foto_url }}" alt="Perfil" class="perfil-foto">
        <b>{{ Auth::user()->nombres }}</b>

        @if(in_array(auth()->user()->rol, ['admin', 'docente']))
            <a href="{{ route('historial') }}">
                <i class="bi bi-clock-history"></i> {{ __('Historial') }}
            </a>
        @endif

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="cerrar-btn">
                <i class="bi bi-box-arrow-right"></i> {{ __('Cerrar sesión') }}
            </button>
        </form>
    </div>

    <!-- ===== Menú Lateral ===== -->
    <aside id="menu-lateral" class="menu-lateral">
        <div class="menu-header">
            <a href="javascript:void(0)" class="close-btn" onclick="toggleMenu()">
                <i class="bi bi-list"></i>
            </a>
            <span class="menu-title"></span>
            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSpPReQfrDeeJiv0BuOf6r-eEUnb5Eos8DJTQ&s"
                alt="Logo" class="menu-logo">
        </div>

        <a href="{{ route('reset.navigation') }}"
            class="{{ request()->routeIs('admin.dashboard', 'docente.dashboard', 'alumno.dashboard') ? 'activo' : '' }}">
            <i class="bi bi-house-fill"></i> {{ __('Inicio') }}
        </a>

        {{-- Menú exclusivo del alumno --}}
        @if(auth()->user()->rol === 'alumno')
            <a href="{{ route('solicitud') }}" class="{{ request()->routeIs('solicitud') ? 'activo' : '' }}">
                <i class="bi bi-calendar-plus-fill" style="font-size: 18px;"></i> {{ __('Solicitud') }}
            </a>
            <a href="{{ route('agenda') }}" class="{{ request()->routeIs('agenda') ? 'activo' : '' }}">
                <i class="bi bi-calendar-plus-fill" style="font-size: 18px;"></i> {{ __('Agendar') }}
            </a>
        @endif

        {{-- Menú compartido: admin y docente --}}
        @if(in_array(auth()->user()->rol, ['admin', 'docente']))
            <a href="{{ route('grupos') }}" class="{{ request()->routeIs('grupos') ? 'activo' : '' }}">
                <i class="bi bi-people-fill"></i> {{ __('Grupos') }}
            </a>
            <a href="{{ route('alumnos') }}" class="{{ request()->routeIs('alumnos') ? 'activo' : '' }}">
                <i class="bi bi-person-vcard-fill" style="font-size: 18px;"></i> {{ __('Alumnos') }}
            </a>
            <a href="{{ route('agenda') }}" class="{{ request()->routeIs('agenda') ? 'activo' : '' }}">
                <i class="bi bi-calendar-plus-fill" style="font-size: 18px;"></i> {{ __('Agendar') }}
            </a>
        @endif

        {{-- Menú exclusivo del docente --}}
        @if(auth()->user()->rol === 'docente')
            <a href="{{ route('registro') }}" class="{{ request()->routeIs('registro') ? 'activo' : '' }}">
                <i class="bi bi-calendar-plus-fill" style="font-size: 18px;"></i> {{ __('Registro de asesorias') }}
            </a>
        @endif

        {{-- Menú exclusivo del admin --}}
        @if(auth()->user()->rol === 'admin')
            <a href="{{ route('roles_permisos') }}" class="{{ request()->routeIs('roles_permisos') ? 'activo' : '' }}">
                <i class="bi bi-clipboard2-check-fill" style="font-size: 18px;"></i> {{ __('Roles y permisos') }}
            </a>
            <a href="{{ route('gestion') }}" class="{{ request()->routeIs('gestion') ? 'activo' : '' }}">
                <i class="bi bi-person-workspace" style="font-size: 18px;"></i> {{ __('Gestión admin') }}
            </a>

            <div class="menu-seccion">
                <a href="#" class="menu-principal">
                    <span><i class="bi bi-person-fill-add" style="font-size: 18px;"></i> {{ __('Registros') }}</span>
                    <i class="bi bi-chevron-compact-down"></i>
                </a>
                <div class="subseccion">
                    <a href="{{ route('registro_alumnos') }}" class="{{ request()->routeIs('registro_alumnos') ? 'activo' : '' }}">
                        {{ __('Registro Alumnos') }}
                    </a>
                    <a href="{{ route('registro_docente') }}" class="{{ request()->routeIs('registro_docente') ? 'activo' : '' }}">
                        {{ __('Registro Docentes') }}
                    </a>
                </div>
            </div>
        @endif
    </aside>

    <div class="overlay"></div>

    <script src="{{ asset('js/menu-lateral.js') }}"></script>
    <script src="{{ asset('js/perfil-dropdown.js') }}"></script>

    @yield('scripts')

</body>

</html>
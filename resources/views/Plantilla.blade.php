<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <!-- Metadatos -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-rol" content="{{ auth()->user()->rol ?? '' }}">
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
                    <!-- Icono Calendario -->
                    <li class="ExtrasMenu" id="icono-cal" style="cursor:pointer; position:relative;">
                        <i class="bi bi-calendar-event-fill"></i>
                    </li>

                    <!-- Icono Notificaciones con badge -->
                    <li class="ExtrasMenu" id="icono-notif" style="cursor:pointer; position:relative;">
                        <i class="bi bi-bell-fill"></i>
                        <span class="notif-badge" id="notif-badge" style="display:none; position:absolute; top:-4px; right:-4px; background:#ef4444; color:white; border-radius:50%; width:16px; height:16px; font-size:10px; font-weight:700; text-align:center; line-height:16px;">0</span>
                    </li>
                    <li class="perfil">
                        <a style="color: white;">
                            {{ Auth::user()->nombres . ' ' . Auth::user()->apellido_paterno . ' ' . Auth::user()->apellido_materno }}
                        </a>
                        <img src="{{ Auth::user()->foto_url }}" alt="Perfil" class="perfil-img">
                    </li>
                </div>
            </ul>
        </nav>

        <!-- ===== Dropdown Notificaciones ===== -->
        <div class="notif-dropdown" id="notif-dropdown" style="display:none; position:fixed; top:60px; right:260px; background:white; border-radius:12px; box-shadow:0 4px 16px rgba(0,0,0,.15); width:320px; z-index:999; overflow:hidden;">
            <div class="notif-header" style="background:linear-gradient(to left, #00937f, #2c9f49); padding:12px 16px; color:white; display:flex; justify-content:space-between; align-items:center;">
                <span style="font-weight:700; font-size:14px;"><i class="bi bi-bell-fill me-1"></i> Notificaciones</span>
                <button id="btn-todas-leidas" style="background:rgba(255,255,255,.2); border:none; color:white; font-size:11px; padding:3px 8px; border-radius:6px; cursor:pointer;">Marcar todas como leídas</button>
            </div>
            <div class="notif-lista" id="notif-lista" style="max-height:340px; overflow-y:auto;">
                <div class="notif-vacia" style="text-align:center; padding:30px; color:#9ca3af; font-size:13px;">Cargando...</div>
            </div>
        </div>

        <!-- ===== Dropdown Calendario ===== -->
        <div class="cal-dropdown" id="cal-dropdown" style="display:none; position:fixed; top:60px; right:310px; background:white; border-radius:12px; box-shadow:0 4px 16px rgba(0,0,0,.15); width:260px; z-index:999; overflow:hidden; padding:14px;">
            <div class="cal-header-mini" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                <button id="cal-prev" style="background:none; border:none; cursor:pointer; font-size:14px;">◀</button>
                <span id="cal-mini-titulo" style="font-weight:700; font-size:13px; color:#2d3748;"></span>
                <button id="cal-next" style="background:none; border:none; cursor:pointer; font-size:14px;">▶</button>
            </div>
            <div class="cal-mini-weekdays" style="display:grid; grid-template-columns:repeat(7,1fr); text-align:center; font-size:10px; font-weight:700; color:#9ca3af; margin-bottom:4px;">
                <div>L</div>
                <div>M</div>
                <div>M</div>
                <div>J</div>
                <div>V</div>
                <div>S</div>
                <div>D</div>
            </div>
            <div class="cal-mini-days" id="cal-mini-days" style="display:grid; grid-template-columns:repeat(7,1fr); gap:2px;"></div>
            <p style="font-size:11px; color:#9ca3af; text-align:center; margin-top:8px; margin-bottom:0;">
                <span style="display:inline-block; width:10px; height:10px; border-radius:50%; background:linear-gradient(135deg,#2c9f49,#00937f); margin-right:4px;"></span>
                Días con asesoría
            </p>
        </div>

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
        <a href="{{ route('agenda') }}" class="{{ request()->routeIs('agenda') ? 'activo' : '' }}">
            <i class="bi bi-calendar-plus-fill" style="font-size: 18px;"></i> {{ __('Solicitar') }}
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
            <i class="bi bi-pass-fill" style="font-size: 18px;"></i> {{ __('Asesorias') }}
        </a>
        @endif

        {{-- Menú exclusivo del admin --}}
        @if(auth()->user()->rol === 'admin')
        <a href="{{ route('admin.asignaciones') }}" class="{{ request()->routeIs('admin.asignaciones') ? 'activo' : '' }}">
            <i class="bi bi-file-text-fill"></i> {{ __('Asignaciones') }}
        </a>

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

    {{-- Burbuja flotante: grupo activo en sesión --}}
    @if(in_array(auth()->user()->rol, ['admin', 'docente']) && session('grupo_activo_id'))
    <div class="grupo-activo-bubble" title="Grupo activo en sesión">
        <i class="bi bi-people-fill"></i>
        <div class="grupo-activo-info">
            <span class="grupo-activo-label">Grupo activo</span>
            <span class="grupo-activo-nombre">{{ session('grupo_activo_nombre') }}</span>
        </div>
        <form method="POST" action="{{ route('grupos.limpiar') }}" class="grupo-activo-cambiar">
            @csrf
            <button type="submit" title="Cambiar grupo">
                <i class="bi bi-arrow-left-right"></i>
            </button>
        </form>
    </div>
    @endif

    <script src="{{ asset('js/menu-lateral.js') }}"></script>
    <script src="{{ asset('js/perfil-dropdown.js') }}"></script>
    <script src="{{ asset('js/notificaciones.js') }}"></script>

    @yield('scripts')

</body>

</html>
<!DOCTYPE html>
<html lang="es">

<head>
    <!-- Sweet alerts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('estilos/sweetalert.css') }}">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Metadatos -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titulo', 'Asesorias-UTN')</title>

    <link rel="stylesheet" href="{{ asset('estilos/menu-fotter.css') }}">
    <link rel="stylesheet" href="{{ asset('estilos/UserCard.css') }}">
    <link rel="stylesheet" href="{{ asset('estilos/menu-lateral.css') }}">

    <style>
        .breadcrumb-container {
            padding: 18px 24px 0 24px;
        }

        .custom-breadcrumb {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            list-style: none;
            margin: 0;
            padding: 12px 20px;
            background: linear-gradient(135deg, #16a34a, #15803d);
            color: white;
            border-radius: 14px;
            box-shadow: 0 6px 18px rgba(22, 163, 74, 0.25);
            font-size: 14px;
            font-weight: 600;
        }

        .custom-breadcrumb li {
            display: flex;
            align-items: center;
        }

        .custom-breadcrumb li a {
            color: white;
            text-decoration: none;
            opacity: .9;
            transition: .25s;
        }

        .custom-breadcrumb li a:hover {
            opacity: 1;
        }

        .custom-breadcrumb .active {
            color: #dcfce7;
        }

        .custom-breadcrumb .separator {
            color: rgba(255, 255, 255, .75);
            font-size: 11px;
        }

        .goog-te-banner-frame.skiptranslate,
        iframe.skiptranslate,
        .goog-te-gadget,
        .goog-logo-link {
            display: none !important;
        }

        body {
            top: 0 !important;
            position: static !important;
        }

        html {
            margin-top: 0 !important;
        }

        .lang-btn {
            width: 100%;
            border: none;
            padding: 8px;
            border-radius: 8px;
            margin-top: 5px;
            cursor: pointer;
            background: #f3f4f6;
        }

        .lang-btn.active {
            background: #16a34a;
            color: white;
        }
    </style>
</head>

<body>

    @php
    $routeName = Route::currentRouteName();

    $breadcrumbMap = [
    'grupos' => [
    ['label' => __('Grupos')]
    ],

    'alumnos' => [
    ['label' => __('Grupos'), 'route' => 'grupos'],
    ['label' => __('Alumnos')]
    ],

    'alumnos.edit' => [
    ['label' => __('Grupos'), 'route' => 'grupos'],
    ['label' => __('Alumnos'), 'route' => 'alumnos'],
    ['label' => __('Editar')]
    ],

    'agenda' => [
    ['label' => __('Grupos'), 'route' => 'grupos'],
    ['label' => __('Alumnos'), 'route' => 'alumnos'],
    ['label' => __('Agenda')]
    ],

    'registro' => [
    ['label' => __('Grupos'), 'route' => 'grupos'],
    ['label' => __('Alumnos'), 'route' => 'alumnos'],
    ['label' => __('Agenda'), 'route' => 'agenda'],
    ['label' => __('Registro de asesorías')]
    ],

    'registro_alumnos' => [
    ['label' => __('Registros')],
    ['label' => __('Registro Alumnos')]
    ],

    'registro_docente' => [
    ['label' => __('Registros')],
    ['label' => __('Registro Docentes')]
    ],

    'docentes.edit' => [
    ['label' => __('Registros')],
    ['label' => __('Registro Docentes'), 'route' => 'registro_docente'],
    ['label' => __('Editar')]
    ],

    'historial' => [
    ['label' => __('Historial')]
    ],

    'roles_permisos' => [
    ['label' => __('Roles y permisos')]
    ],

    'gestion' => [
    ['label' => __('Gestión admin.')]
    ],

    'solicitud' => [
    ['label' => __('Solicitud')]
    ],
    ];

    $breadcrumbs = $breadcrumbMap[$routeName] ?? [];
    @endphp

    <div class="main-content" id="main-content">

        <!-- NAV -->
        <nav>
            <ul>
                <li class="menu-icon">
                    <a href="javascript:void(0)" onclick="toggleMenu()">
                        <i class="bi bi-list"></i>
                    </a>
                </li>

                <div class="nav-left">
                    <li class="ExtrasMenu"><i class="bi bi-calendar-event-fill"></i></li>
                    <li class="ExtrasMenu"><i class="bi bi-bell-fill"></i></li>

                    <li class="perfil">
                        <a style="color:white;">
                            {{ Auth::user()->nombres . ' ' . Auth::user()->apellido_paterno }}
                        </a>
                        <img src="{{ Auth::user()->foto_url }}" class="perfil-img">
                    </li>
                </div>
            </ul>
        </nav>

        <!-- BREADCRUMBS -->
        <div class="breadcrumb-container">
            <ol class="custom-breadcrumb">

                <li>
                    <a href="@if(auth()->user()->rol === 'admin') {{ route('admin.dashboard') }}
                    @elseif(auth()->user()->rol === 'docente') {{ route('docente.dashboard') }}
                    @else {{ route('alumno.dashboard') }} @endif">
                        <i class="bi bi-house-door-fill"></i> Inicio
                    </a>
                </li>

                @foreach($breadcrumbs as $crumb)
                <li class="separator">
                    <i class="bi bi-chevron-right"></i>
                </li>

                <li class="{{ isset($crumb['route']) ? '' : 'active' }}">
                    @if(isset($crumb['route']))
                    <a href="{{ route($crumb['route']) }}">
                        {{ $crumb['label'] }}
                    </a>
                    @else
                    {{ $crumb['label'] }}
                    @endif
                </li>
                @endforeach

            </ol>
        </div>

        <section>
            @yield('contenido')
        </section>

        <footer class="pie-pagina">
            <div class="info-universidad">
                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSKLc3LGB1aSLdgvmI7TwAd0-rJLeTNqExKUw&s">
                <span>Universidad Tecnologica de Nayarit</span>
            </div>

            <div class="derechos-autor">
                © 2026 Equipo Web Asesorias. Todos los derechos reservados.
            </div>
        </footer>

    </div>

    <!-- DROPDOWN PERFIL -->
    <div class="perfil-dropdown">
        <div class="perfil-header"></div>
        <img src="{{ Auth::user()->foto_url }}" alt="Perfil" class="perfil-foto">
        <b>{{ Auth::user()->nombres }}</b>

        <button onclick="translatePage('es')" class="lang-btn active" id="btn-es">🇪🇸 Español</button>
        <button onclick="translatePage('en')" class="lang-btn" id="btn-en">🇺🇸 English</button>
        <div id="google_translate_element" style="display:none;"></div>

        @if(in_array(auth()->user()->rol, ['admin', 'docente']))
        <a href="{{ route('historial') }}"><i class="bi bi-clock-history"></i> Historial</a>
        @endif
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="cerrar-btn"><i class="bi bi-box-arrow-right"></i> Cerrar sesión</button>
        </form>
    </div>

    <!-- Menú lateral -->
    <aside id="menu-lateral" class="menu-lateral">
        <div class="menu-header">
            <a href="javascript:void(0)" class="close-btn" onclick="toggleMenu()">
                <i class="bi bi-list"></i>
            </a>
            <span class="menu-title"></span>
            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSpPReQfrDeeJiv0BuOf6r-eEUnb5Eos8DJTQ&s"
                alt="Logo" class="menu-logo">
        </div>

        <a href="@if(auth()->user()->rol === 'admin') {{ route('admin.dashboard') }} 
            @elseif(auth()->user()->rol === 'docente') {{ route('docente.dashboard') }} 
            @else {{ route('alumno.dashboard') }} @endif"
            class="{{ request()->routeIs('admin.dashboard') || request()->routeIs('docente.dashboard') || request()->routeIs('alumno.dashboard') ? 'activo' : '' }}">
            <i class="bi bi-house-fill"></i> {{ __('Inicio') }}
        </a>
        <!-- Alumnos -->
        @if(auth()->user()->rol === 'alumno')
        <a href="{{ route('solicitud') }}" class="{{ request()->routeIs('solicitud') ? 'activo' : '' }}">
            <i class="bi bi-calendar-plus-fill" style="font-size: 18px;"></i> Solicitud
        </a>
        <a href="{{ route('agenda') }}" class="{{ request()->routeIs('agenda') ? 'activo' : '' }}">
            <i class="bi bi-calendar-plus-fill" style="font-size: 18px;"></i> Agendar
        </a>
        @endif
        <!-- Grupos y Alumnos: solo admin y docente -->
        @if(in_array(auth()->user()->rol, ['admin', 'docente']))
        <a href="{{ route('grupos') }}" class="{{ request()->routeIs('grupos') ? 'activo' : '' }}">
            <i class="bi bi-people-fill"></i> Grupos
        </a>
        <a href="{{ route('alumnos') }}" class="{{ request()->routeIs('alumnos') ? 'activo' : '' }}">
            <i class="bi bi-person-vcard-fill" style="font-size: 18px;"></i> Alumnos
        </a>
        <a href="{{ route('agenda') }}" class="{{ request()->routeIs('agenda') ? 'activo' : '' }}">
            <i class="bi bi-calendar-plus-fill" style="font-size: 18px;"></i> Agendar
        </a>
        @endif
        <!-- Solo Docente -->
        @if(auth()->user()->rol === 'docente')
        <a href="{{ route('registro') }}" class="{{ request()->routeIs('registro') ? 'activo' : '' }}">
            <i class="bi bi-calendar-plus-fill" style="font-size: 18px;"></i> Registro de asesorias
        </a>
        @endif()
        <!-- Roles y permisos: solo admin -->
        @if(auth()->user()->rol === 'admin')
        <a href="{{ route('roles_permisos') }}" class="{{ request()->routeIs('roles_permisos') ? 'activo' : '' }}">
            <i class="bi bi-clipboard2-check-fill" style="font-size: 18px;"></i> Roles y permisos
        </a>
        <a href="{{ route('gestion') }}" class="{{ request()->routeIs('gestion') ? 'activo' : '' }}">
            <i class="bi bi-person-workspace" style="font-size: 18px;"></i> Gestión admin.
        </a>
        @endif

        <!-- Registros: solo admin -->
        @if(auth()->user()->rol === 'admin')
        <div class="menu-seccion">
            <a href="#" class="menu-principal">
                <span><i class="bi bi-person-fill-add" style="font-size: 18px;"></i> Registros</span>
                <i class="bi bi-chevron-compact-down"></i>
            </a>
            <div class="subseccion">
                <a href="{{ route('registro_alumnos') }}" class="{{ request()->routeIs('registro_alumnos') ? 'activo' : '' }}">
                    Registro Alumnos
                </a>
                <a href="{{ route('registro_docente') }}" class="{{ request()->routeIs('registro_docente') ? 'activo' : '' }}">
                    Registro Docentes
                </a>
            </div>
        </div>
        @endif
    </aside>

    <div class="overlay"></div>

    <script src="{{ asset('js/menu-lateral.js') }}"></script>
    <script src="{{ asset('js/perfil-dropdown.js') }}"></script>

    <script>
        function googleTranslateElementInit() {
            new google.translate.TranslateElement({
                pageLanguage: 'es',
                autoDisplay: false
            }, 'google_translate_element');
        }

        function translatePage(lang) {
            const interval = setInterval(() => {
                const select = document.querySelector('.goog-te-combo');

                if (select) {
                    select.value = lang;
                    select.dispatchEvent(new Event('change'));

                    document.getElementById('btn-es').classList.remove('active');
                    document.getElementById('btn-en').classList.remove('active');
                    document.getElementById('btn-' + lang).classList.add('active');

                    clearInterval(interval);
                }
            }, 500);
        }
    </script>

    <script src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

</body>

</html>
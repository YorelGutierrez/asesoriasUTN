<!DOCTYPE html>
<html lang="es">

<head>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Metadatos -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="description del sitio.">
    <meta name="keywords" content="palabras claves para busqueda del sitio">
    <meta name="author" content="Corona Alain, Bernal Brandon, Gonzales Rubi, Gutierrez Yorel, Rivera Vanessa, Samaniego Andy">
    <!-- Titulos de la pagina -->
    <title>@yield('titulo', 'Asesorias-UTN')</title>
    <!-- Link del icono en el navegador -->
    <link rel="icon" type="image/x-icon" href="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSKLc3LGB1aSLdgvmI7TwAd0-rJLeTNqExKUw&s">
    <!-- Links aparte -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">
    <link rel="stylesheet" href="{{ asset('estilos/menu-fotter.css') }}">
    <link rel="stylesheet" href="{{ asset('estilos/UserCard.css') }}">
    <link rel="stylesheet" href="{{ asset('estilos/menu-lateral.css') }}">
</head>

<body>

    <div class="main-content" id="main-content">
        <!-- Menú Superior -->
        <nav>
            <ul>
                <!-- Icono del menú dentro del nav -->
                <li class="menu-icon">
                    <a href="javascript:void(0)" onclick="toggleMenu()"><i class="bi bi-list"></i> <span class="menu-title"></span></a>
                </li>

                <!-- Opciones posicionadas de lado izquierdo -->
                <div class="nav-left">

                    <!-- Opciones extras -->
                    <li class="ExtrasMenu"><i class="bi bi-calendar-event-fill"></i></li>
                    <li class="ExtrasMenu"><i class="bi bi-bell-fill"></i></li><!-- Notificaciones -->

                    <!--Apartados del perfil-->
                    <li class="perfil">
                        <a style="color: white;"></i> {{ Auth::user()->nombres . ' ' . Auth::user()->apellido_paterno . ' ' . Auth::user()->apellido_materno }}</a>
                        <img src="{{ Auth::user()->foto_url }}" alt="Perfil" class="perfil-img">
                    </li>
                </div>
            </ul>
        </nav>

        <!-- Contenido -->
        <section>
            @yield('contenido')
        </section>


        <!-- Pie de página -->
        <footer class="pie-pagina">
            <!-- Información de la universidad -->
            <div class="info-universidad">
                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSKLc3LGB1aSLdgvmI7TwAd0-rJLeTNqExKUw&s" alt="Logo de empresa">
                <span>Universidad Tecnologica de Nayarit</span>
            </div>
            <!-- Derechos de autor -->
            <div class="derechos-autor">
                &copy; © 2026 Equipo Web Asesorias. Todos los derechos reservados.
            </div>
            <!-- Información de contacto -->
            <div class="contacto">
                Dirección: C4G3+5V, Carretera México 200, Km 9 63786, Col, 24 de Febrero, 63786 Xalisco, Nay. | Teléfono: 311 211 9800 | Email: info@utnay.edu.mx
            </div>
        </footer>
    </div>


    <!-- Elementos emerguentes -->

    <!-- Dropdown de diseño -->
    @php
    $currentLocale = session('locale', 'es'); // por defecto español
    $switchToLang = $currentLocale === 'es' ? 'en' : 'es';
    @endphp
    <div class="perfil-dropdown">
        <div class="perfil-header"></div>
        <img src="{{ Auth::user()->foto_url }}" alt="Perfil" class="perfil-foto">
        <b>{{ Auth::user()->nombres }}</b>
        <a href="{{ route('lang.switch', $switchToLang) }}">
            <i class="bi bi-translate"></i> {{ __('Cambiar idioma') }}
        </a>
        <a href="#">Opcion 1</a>
        <a href="{{ route('admin.dashboard') }}">Opciones Admin</a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="cerrar-btn"><i class="bi bi-box-arrow-right"></i> Cerrar sesión</button>
        </form>
    </div>

    <!-- Menú lateral -->
    <aside id="menu-lateral" class="menu-lateral">
        <!-- Encabezado del menú -->
        <div class="menu-header">
            <!-- Ícono hamburguesa ahora aquí -->
            <a href="javascript:void(0)" class="close-btn" onclick="toggleMenu()">
                <i class="bi bi-list"></i>
            </a>
            <span class="menu-title"></span>
        </div>
        <!-- Links del menú -->
        <a href="{{ route('dashboard') }}"><i class="bi bi-house-fill" style="font-size: 18px;"></i> Inicio</a>
        <a href="{{ route('grupos') }}"><i class="bi bi-collection-fill" style="font-size: 18px;"></i> Grupos</a>
        <a href="{{ route('alumnos') }}"><i class="bi bi-person-vcard-fill" style="font-size: 18px;"></i> Alumnos</a>
        <a href="{{ route('agenda') }}"><i class="bi bi-calendar-plus-fill" style="font-size: 18px;"></i> Agendar</a>
        <a href="{{ route('roles_permisos') }}"><i class="bi bi-clipboard2-check-fill" style="font-size: 18px;"></i> Roles y permisos</a>
        <!-- Sección con subsección -->
        <div class="menu-seccion">
            <a href="#" class="menu-principal">Registro<i class="bi bi-chevron-compact-down"></i></a>
            <div class="subseccion">
                <a href="{{ route('registro_alumnos') }}"><i class="bi bi-person-fill-add" style="font-size: 18px;"></i> Registro Alumnos</a>
                <a href="{{ route('registro_docente') }}"><i class="bi bi-person-fill-add" style="font-size: 18px;"></i> Registro Docentes</a>
            </div>
        </div>
    </aside>
    <!-- Overlay -->
    <div class="overlay"></div>

    <!-- Scripts -->
    <script src="{{ asset('js/menu-lateral.js') }}"></script>
    <script src="{{ asset('js/perfil-dropdown.js') }}"></script>
</body>

</html>
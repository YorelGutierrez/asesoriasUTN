@php
    $routeName = Route::currentRouteName();
    $navigationHistory = session()->get('navigation_history', []);
    $breadcrumbs = [];

    // Mapeo de nombres de rutas a etiquetas amigables
    $routeLabels = [
        'admin.dashboard'   => 'Inicio',
        'docente.dashboard' => 'Inicio',
        'alumno.dashboard'  => 'Inicio',
        'grupos'            => 'Grupos',
        'alumnos'           => 'Alumnos',
        'agenda'            => 'Agendar',
        'registro'          => 'Registro de asesorías',
        'historial'         => 'Historial',
        'roles_permisos'    => 'Roles y permisos',
        'gestion'           => 'Gestión admin.',
        'registro_alumnos'  => 'Registro Alumnos',
        'registro_docente'  => 'Registro Docentes',
        'solicitud'         => 'Solicitud',
    ];

    // Construir breadcrumbs desde el historial de navegación
    if (!empty($navigationHistory)) {
        foreach ($navigationHistory as $index => $navRoute) {
            $label = $routeLabels[$navRoute] ?? ucfirst(str_replace('_', ' ', $navRoute));

            // El último elemento es el actual (sin enlace)
            if ($index === count($navigationHistory) - 1) {
                $breadcrumbs[] = ['label' => $label];
            } else {
                $breadcrumbs[] = ['label' => $label, 'route' => $navRoute];
            }
        }
    } else {
        // Sin historial, mostrar solo la página actual
        $currentLabel = $routeLabels[$routeName] ?? ucfirst(str_replace('_', ' ', $routeName));
        $breadcrumbs  = [['label' => $currentLabel]];
    }

    // Casos especiales para submenús de registros
    if ($routeName === 'registro_alumnos' || $routeName === 'registro_docente') {
        $hasRegistros = false;
        foreach ($breadcrumbs as $crumb) {
            if ($crumb['label'] === 'Registros') {
                $hasRegistros = true;
                break;
            }
        }
        if (!$hasRegistros) {
            $lastItem     = array_pop($breadcrumbs);
            $breadcrumbs[] = ['label' => 'Registros'];
            $breadcrumbs[] = $lastItem;
        }
    }
@endphp

<ol class="custom-breadcrumb">
    <li>
        <a href="{{ route('reset.navigation') }}">
            <i class="bi bi-house-door-fill"></i> {{ __('Inicio') }}
        </a>
    </li>

    @foreach($breadcrumbs as $crumb)
        @if($crumb['label'] !== 'Inicio')
            <li class="separator">
                <i class="bi bi-chevron-right"></i>
            </li>
            <li class="{{ isset($crumb['route']) ? '' : 'active' }}">
                @if(isset($crumb['route']))
                    <a href="{{ route($crumb['route']) }}">{{ $crumb['label'] }}</a>
                @else
                    {{ $crumb['label'] }}
                @endif
            </li>
        @endif
    @endforeach
</ol>
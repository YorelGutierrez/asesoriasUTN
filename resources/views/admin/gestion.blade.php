@extends('Plantilla')

@section('contenido')
<link rel="stylesheet" href="{{ asset('estilos/botones.css') }}">
<link rel="stylesheet" href="{{ asset('estilos/titulos.css') }}">
<link rel="stylesheet" href="{{ asset('estilos/gestion.css') }}">

<div class="titulo">
    <h1>Gestión administrativa</h1>
</div>

<!-- filtrados comunes -->
<div class="card shadow-sm mb-4 border-0">
    <div class="card-body p-4">
        <h5 class="fw-semibold mb-3">Filtros de búsqueda</h5>
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Buscar por nombre / matrícula / empleado</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="search-input" placeholder="Escribe para buscar...">
                    <button class="btn btn-outline-secondary" type="button">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Carrera</label>
                <select class="form-select" id="carrera-filter">
                    <option value="">Todas las carreras</option>
                    <option>Ingeniería en Desarrollo de Software</option>
                    <option>Ingeniería en Redes</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Estado</label>
                <select class="form-select" id="estado-filter">
                    <option value="">Todos</option>
                    <option>Activo</option>
                    <option>Inactivo</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn-principal w-100" id="filtrar-btn">Filtrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Pestañas de navegación -->
<ul class="nav nav-tabs mb-4" id="gestionTab" role="tablist">
    <li class="nav-item">
        <button class="nav-link active" id="grupos-tab" data-bs-toggle="tab" data-bs-target="#grupos" type="button" role="tab" aria-selected="true">
            <i class="bi bi-people-fill"></i> Grupos
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" id="alumnos-tab" data-bs-toggle="tab" data-bs-target="#alumnos" type="button" role="tab" aria-selected="false">
            <i class="bi bi-person-vcard-fill"></i> Alumnos
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" id="docentes-tab" data-bs-toggle="tab" data-bs-target="#docentes" type="button" role="tab" aria-selected="false">
            <i class="bi bi-person-badge-fill"></i> Docentes
        </button>
    </li>
</ul>

<!-- TABLAS -->
<div class="tab-content" id="gestionTabContent">
    <!-- GRUPOS -->
    <div class="tab-pane show active" id="grupos" role="tabpanel">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-semibold mb-0">Listado de grupos</h5>
                    <div class="col-md-4">
                        <button class="btn-principal" data-bs-toggle="modal" data-bs-target="#modalGrupo">
                            <i class="bi bi-plus-circle"></i> Nuevo grupo
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Nombre del grupo</th>
                                <th>Carrera</th>
                                <th>Cuatrimestre</th>
                                <th>Alumnos inscritos</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($grupos as $grupo)
                            <tr>
                                <td>{{ $grupo->id }}</td>
                                <td>{{ $grupo->nombre }}</td>
                                <td>{{ $grupo->carrera ? $grupo->carrera->nombre : 'N/A' }}</td>
                                <td>{{ $grupo->cuatrimestre }}</td>
                                <td>{{ $grupo->alumnos_count ?? 0 }}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-sm btn-outline-warning editar-grupo" data-id="{{ $grupo->id }}" data-nombre="{{ $grupo->nombre }}" data-carrera_id="{{ $grupo->carrera_id }}" data-cuatrimestre="{{ $grupo->cuatrimestre }}">
                                             Editar
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger eliminar-grupo" data-id="{{ $grupo->id }}" data-nombre="{{ $grupo->nombre }}">
                                             Eliminar
                                        </button>
                                    </div>
                                \n
                                </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">No hay grupos registrados</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $grupos->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- ALUMNOS -->
    <div class="tab-pane" id="alumnos" role="tabpanel">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-semibold mb-0">Listado de alumnos</h5>
                    <div class="col-md-4">
                        <a href="{{ route('registro_alumnos') }}" class="btn-principal">
                            <i class="bi bi-plus-circle"></i> Nuevo alumno
                        </a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Matrícula</th>
                                <th>Nombre completo</th>
                                <th>Grupo</th>
                                <th>Carrera</th>
                                <th>Correo</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($alumnos as $alumno)
                            <tr>
                                <td>{{ $alumno->matricula }}</td>
                                <td>{{ $alumno->user->nombres }} {{ $alumno->user->apellido_paterno }} {{ $alumno->user->apellido_materno }}</td>
                                <td>{{ $alumno->grupo ? $alumno->grupo->nombre : 'N/A' }}</td>
                                <td>{{ $alumno->carrera ? $alumno->carrera->nombre : 'N/A' }}</td>
                                <td>{{ $alumno->user->email }}</td>
                                <td>
                                    @if($alumno->user->estado ?? true)
                                        <span class="badge bg-success">Activo</span>
                                    @else
                                        <span class="badge bg-danger">Inactivo</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('alumnos.edit', $alumno->id) }}" class="btn btn-sm btn-outline-warning">
                                            Editar
                                        </a>
                                        <button class="btn btn-sm btn-outline-danger eliminar-alumno" data-id="{{ $alumno->id }}" data-nombre="{{ $alumno->user->nombres }}">
                                            Eliminar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">No hay alumnos registrados</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $alumnos->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- DOCENTES -->
    <div class="tab-pane" id="docentes" role="tabpanel">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-semibold mb-0">Listado de docentes</h5>
                    <div class="col-md-4">
                        <a href="{{ route('registro_docente') }}" class="btn-principal">
                            <i class="bi bi-plus-circle"></i> Nuevo docente
                        </a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Núm. empleado</th>
                                <th>Nombre completo</th>
                                <th>Carrera</th>
                                <th>Correo</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($docentes as $docente)
                            <tr>
                                <td>{{ $docente->numero_empleado }}</td>
                                <td>{{ $docente->user->nombres }} {{ $docente->user->apellido_paterno }} {{ $docente->user->apellido_materno }}</td>
                                <td>{{ $docente->carrera ? $docente->carrera->nombre : 'N/A' }}</td>
                                <td>{{ $docente->user->email }}</td>
                                <td>
                                    @if($docente->user->estado ?? true)
                                        <span class="badge bg-success">Activo</span>
                                    @else
                                        <span class="badge bg-danger">Inactivo</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('docentes.edit', $docente->id) }}" class="btn btn-sm btn-outline-warning">
                                             Editar
                                        </a>
                                        <button class="btn btn-sm btn-outline-danger eliminar-docente" data-id="{{ $docente->id }}" data-nombre="{{ $docente->user->nombres }}">
                                             Eliminar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">No hay docentes registrados</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $docentes->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Formulario oculto para eliminar -->
<form id="form-eliminar" action="" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
    // Eliminar Alumno
    document.querySelectorAll('.eliminar-alumno').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const nombre = this.dataset.nombre;
            
            Swal.fire({
                title: '¿Eliminar alumno?',
                text: `¿Estás seguro de eliminar a ${nombre}? Esta acción no se puede deshacer.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('form-eliminar');
                    form.action = `/alumnos/${id}`;
                    form.submit();
                }
            });
        });
    });

    // Eliminar Docente
    document.querySelectorAll('.eliminar-docente').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const nombre = this.dataset.nombre;
            
            Swal.fire({
                title: '¿Eliminar docente?',
                text: `¿Estás seguro de eliminar a ${nombre}? Esta acción no se puede deshacer.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('form-eliminar');
                    form.action = `/docentes/${id}`;
                    form.submit();
                }
            });
        });
    });

    // Eliminar Grupo
    document.querySelectorAll('.eliminar-grupo').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const nombre = this.dataset.nombre;
            
            Swal.fire({
                title: '¿Eliminar grupo?',
                text: `¿Estás seguro de eliminar el grupo ${nombre}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('form-eliminar');
                    form.action = `/grupos/${id}`;
                    form.submit();
                }
            });
        });
    });
</script>

<script>
    // Alerta de éxito
    @if(session('success'))
    Swal.fire({
        icon: 'success',
        title: '¡Éxito!',
        text: '{{ session('success') }}',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Aceptar'
    });
    @endif

    // Alerta de error
    @if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: '{{ session('error') }}',
        confirmButtonColor: '#d33',
        confirmButtonText: 'Aceptar'
    });
    @endif
</script>

<script src="{{ asset('js/gestion-opciones.js') }}"></script>
@endsection
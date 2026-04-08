@extends('Plantilla')

@section('contenido')
<link rel="stylesheet" href="{{ asset('estilos/botones.css') }}">
<link rel="stylesheet" href="{{ asset('estilos/titulos.css') }}">

<div class="titulo">
    <h1>Roles y permisos</h1>
</div>

<!-- CONTENEDOR SUPERIOR -->
<div class="row g-4 mb-4">

    <!-- FILTROS -->
    <div class="col-md-8">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-4">Gestión de usuarios</h5>

                <div class="row g-4">

                    <!-- Rol -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Rol</label>
                        <select class="form-select">
                            <option selected>Todos los roles</option>
                            <option>Administrador</option>
                            <option>Tutor</option>
                            <option>Docente</option>
                            <option>Alumno</option>
                        </select>
                    </div>

                    <!-- Carrera -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Carrera</label>
                        <select class="form-select">
                            <option selected>Todas las carreras</option>
                            <option>Ingeniería en Desarrollo de Software</option>
                        </select>
                    </div>

                    <!-- Búsqueda -->
                    <div class="col-12">
                        <label class="form-label fw-semibold">Buscar usuario</label>
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Nombre o correo...">
                            <button class="btn btn-outline-secondary">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Botón -->
                    <div class="col-12">
                        <button class="btn-principal w-100">Filtrar usuarios</button>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- ROLES Y PERMISOS -->
    <div class="col-md-4">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-3">Roles del sistema</h5>

                <ul class="list-group list-group-flush">

                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fw-semibold">Administrador</div>
                            <span class="badge bg-success">Crear</span>
                            <span class="badge bg-primary">Consultar</span>
                            <span class="badge bg-warning text-dark">Editar</span>
                            <span class="badge bg-danger">Eliminar</span>
                        </div>
                        <span class="badge rounded-pill text-bg-success"><i class="bi bi-person-fill"></i> 3</span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fw-semibold">Docente</div>
                            <span class="badge bg-success">Crear</span>
                            <span class="badge bg-primary">Consultar</span>
                            <span class="badge bg-warning text-dark">Editar</span>
                            <span class="badge bg-danger">Eliminar</span>
                        </div>
                        <span class="badge rounded-pill text-bg-success"><i class="bi bi-person-fill"></i> 8</span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fw-semibold">Tutor</div>
                            <span class="badge bg-success">Crear</span>
                            <span class="badge bg-primary">Consultar</span>
                            <span class="badge bg-warning text-dark">Editar</span>
                        </div>
                        <span class="badge rounded-pill text-bg-success"><i class="bi bi-person-fill"></i> 5</span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fw-semibold">Alumno</div>
                            <span class="badge bg-success">Crear</span>
                            <span class="badge bg-primary">Consultar</span>
                            <span class="badge bg-warning text-dark">Editar</span>
                        </div>
                        <span class="badge rounded-pill text-bg-success"><i class="bi bi-person-fill"></i> 120</span>
                    </li>

                </ul>

            </div>
        </div>
    </div>


    <!-- TABLA DE USUARIOS -->
    <div class="col-md-12">
        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-body p-4">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-semibold mb-0">Usuarios registrados</h5>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Nombre</th>
                                <th>Correo</th>
                                <th>Carrera</th>
                                <th>Matrícula/Clave</th>
                                <th>Rol</th>
                                <th>Estado</th>
                                <th>Gestión</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($usuarios as $usuario)
                            <tr>
                                <td>{{ $usuario->nombres }} {{ $usuario->apellido_paterno }}</td>
                                <td>{{ $usuario->email }}</td>
                                <td>
                                    @if($usuario->rol == 'alumno' && $usuario->alumno && $usuario->alumno->carrera)
                                        {{ $usuario->alumno->carrera->nombre }}
                                    @elseif($usuario->rol == 'docente' && $usuario->docente && $usuario->docente->carrera)
                                        {{ $usuario->docente->carrera->nombre }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($usuario->rol == 'alumno' && $usuario->alumno)
                                        {{ $usuario->alumno->matricula }}
                                    @elseif($usuario->rol == 'docente' && $usuario->docente)
                                        {{ $usuario->docente->numero_empleado }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($usuario->rol == 'admin')
                                        <span class="badge bg-danger">Administrador</span>
                                    @elseif($usuario->rol == 'docente')
                                        <span class="badge bg-primary">Docente</span>
                                    @elseif($usuario->rol == 'alumno')
                                        <span class="badge bg-success">Alumno</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $usuario->rol }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($usuario->estado)
                                        <span class="badge bg-success">Activo</span>
                                    @else
                                        <span class="badge bg-danger">Bloqueado</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        {{-- EDITAR --}}
                                        @if($usuario->rol == 'alumno')
                                            <a href="{{ route('alumnos.edit', $usuario->alumno->id ?? $usuario->id) }}" class="btn btn-outline-warning btn-sm">Editar</a>
                                        @elseif($usuario->rol == 'docente')
                                            <a href="{{ route('docentes.edit', $usuario->docente->id ?? $usuario->id) }}" class="btn btn-outline-warning btn-sm">Editar</a>
                                        @else
                                            <a href="#" class="btn btn-outline-warning btn-sm disabled">Editar</a>
                                        @endif
                                        
                                        {{-- ELIMINAR (solo para alumnos y docentes) --}}
                                        @if($usuario->rol == 'alumno')
                                            <form action="{{ route('alumnos.destroy', $usuario->alumno->id ?? $usuario->id) }}" method="POST" class="d-inline eliminar-alumno">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm">Eliminar</button>
                                            </form>
                                        @elseif($usuario->rol == 'docente')
                                            <form action="{{ route('docentes.destroy', $usuario->docente->id ?? $usuario->id) }}" method="POST" class="d-inline eliminar-docente">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm">Eliminar</button>
                                            </form>
                                        @endif
                                        
                                        {{-- BLOQUEAR / DESBLOQUEAR --}}
                                        @if($usuario->estado)
                                            <form action="{{ route('usuarios.toggleBlock', $usuario->id) }}" method="POST" class="d-inline toggle-block">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-success btn-sm">Bloquear</button>
                                            </form>
                                        @else
                                            <form action="{{ route('usuarios.toggleBlock', $usuario->id) }}" method="POST" class="d-inline toggle-block">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-primary btn-sm">Desbloquear</button>
                                            </form>
                                        @endif
                                    </div>  
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">No hay usuarios registrados</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Alerta para eliminar alumno
    document.querySelectorAll('.eliminar-alumno').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: '¿Eliminar alumno?',
                text: 'Esta acción no se puede deshacer',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // Alerta para eliminar docente
    document.querySelectorAll('.eliminar-docente').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: '¿Eliminar docente?',
                text: 'Esta acción no se puede deshacer',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // Alerta para bloquear/desbloquear
    document.querySelectorAll('.toggle-block').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const action = this.querySelector('button').innerText;
            const title = action === 'Bloquear' ? '¿Bloquear cuenta?' : '¿Desbloquear cuenta?';
            const text = action === 'Bloquear' ? 'El usuario no podrá iniciar sesión' : 'El usuario podrá iniciar sesión nuevamente';
            
            Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: action === 'Bloquear' ? '#d33' : '#28a745',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, ' + action.toLowerCase(),
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: '¡Éxito!',
        text: '{{ session('success') }}',
        confirmButtonColor: '#3085d6'
    });
</script>
@endif

@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: '{{ session('error') }}',
        confirmButtonColor: '#d33'
    });
</script>
@endif

@endsection
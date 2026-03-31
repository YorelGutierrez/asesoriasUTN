@extends('Plantilla')

@section('contenido')
<link rel="stylesheet" href="{{ asset('estilos/botones.css') }}">
<link rel="stylesheet" href="{{ asset('estilos/titulos.css') }}">

<div class="titulo">
    <h1>Bienvenido administrador!</h1>
</div>


<div class="row mb-4 align-items-stretch">
    <div class="col-md-4">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-3">Total de usuarios</h5>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fw-semibold">Administradores</div>
                        </div>
                        <span class="badge rounded-pill text-bg-success"><i class="bi bi-person-fill"></i> 3</span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fw-semibold">Docentes</div>
                        </div>
                        <span class="badge rounded-pill text-bg-success"><i class="bi bi-person-fill"></i> 8</span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fw-semibold">Tutores</div>
                        </div>
                        <span class="badge rounded-pill text-bg-success"><i class="bi bi-person-fill"></i> 5</span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fw-semibold">Alumnos</div>
                        </div>
                        <span class="badge rounded-pill text-bg-success"><i class="bi bi-person-fill"></i> 120</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- RESPALDOS -->
    <div class="col-md-8">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-4">Respaldos del sistema</A></h5>
                <p><strong>Último respaldo:</strong> 28/03/2026 - 03:00 AM</p>
                <p><strong>Estado:</strong> <span class="text-success">Correcto</span></p>
                <div class="text-end">
                    <div class="d-inline-flex gap-2 mt-3 col-12">
                        <button class="btn-principal">Generar respaldo</button>
                        <button class="btn-secundario">Programar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4 align-items-stretch">
    <div class="col-md-6">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-body p-4 d-flex flex-column">

                <h5 class="fw-semibold mb-4">Bitácora de actividad</h5>

                <div class="flex-grow-1 pe-2" style="max-height: 350px; overflow-y: auto;">

                    <div class="d-flex align-items-start mb-3 border-bottom pb-2">
                        <img src="https://ui-avatars.com/api/?name=Juan+Tovar&background=e9ecef&color=343a40" alt="Juan Tovar" class="rounded-circle me-3 mt-1" width="36" height="36">

                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-semibold" style="font-size: 0.9rem;">Juan Tovar</span>
                                <small class="text-black-50" style="font-size: 0.75rem;">Hace 2 hrs</small>
                            </div>
                            <p class="mb-0 text-muted lh-sm" style="font-size: 0.85rem;">
                                Realizó una asesoría al grupo <span class="fw-medium text-dark">IDGS-84</span>.
                            </p>
                        </div>

                        <button class="btn btn-sm text-danger p-0 ms-2 mt-1 border-0" title="Eliminar registro">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>

                    <div class="d-flex align-items-start mb-3 border-bottom pb-2">
                        <img src="https://ui-avatars.com/api/?name=Maria+Gomez&background=e9ecef&color=343a40" alt="María Gómez" class="rounded-circle me-3 mt-1" width="36" height="36">

                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-semibold" style="font-size: 0.9rem;">María Gómez</span>
                                <small class="text-black-50" style="font-size: 0.75rem;">Ayer</small>
                            </div>
                            <p class="mb-0 text-muted lh-sm" style="font-size: 0.85rem;">
                                Actualizó las calificaciones de la materia <span class="fw-medium text-dark">Bases de Datos</span>.
                            </p>
                        </div>

                        <button class="btn btn-sm text-danger p-0 ms-2 mt-1 border-0" title="Eliminar registro">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>

                    <div class="d-flex align-items-start mb-3 border-bottom pb-2">
                        <img src="https://ui-avatars.com/api/?name=Carlos+Ruiz&background=e9ecef&color=343a40" alt="Carlos Ruiz" class="rounded-circle me-3 mt-1" width="36" height="36">

                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-semibold" style="font-size: 0.9rem;">Carlos Ruiz</span>
                                <small class="text-black-50" style="font-size: 0.75rem;">28 Mar</small>
                            </div>
                            <p class="mb-0 text-muted lh-sm" style="font-size: 0.85rem;">
                                Registró a un nuevo alumno en el sistema.
                            </p>
                        </div>

                        <button class="btn btn-sm text-danger p-0 ms-2 mt-1 border-0" title="Eliminar registro">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ACCIONES DE GESTIÓN -->
    <div class="col-md-6">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-4">Gestión del sistema | Acciones rapidas</h5>
                <div class="d-grid gap-2">
                    <a href="{{ route('gestion', ['tab' => 'alumnos']) }}" class="btn-principal">Gestionar alumnos</a>
                    <a href="{{ route('gestion', ['tab' => 'grupos']) }}" class="btn-principal">Gestionar grupos</a>
                    <a href="{{ route('gestion', ['tab' => 'docentes']) }}" class="btn-principal">Gestionar docentes</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">

        <h5 class="fw-semibold mb-4">Asignación académica | Asiganaciones actuales</h5>

        <div class="table-responsive">
            <table class="table table-hover align-middle">

                <thead class="table-light">
                    <tr>
                        <th>Docente</th>
                        <th>Clave</th>
                        <th>Carreras</th>
                        <th>Materias</th>
                        <th>Grupos</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td>Juan Manuel Tovar Sanchez</td>
                        <td>19cJICP</td>
                        <td>Ingenieria en desarrollo de software</td>
                        <td>Programación</td>
                        <td>IDGS-84</td>
                        <td>
                            <button class="btn btn-outline-warning btn-sm">Editar</button>
                            <button class="btn btn-outline-danger btn-sm">Eliminar</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- BOTÓN -->
        <div class="mt-3 d-flex justify-content-end">
            <button class="btn-principal">+ Nueva asignación</button>
        </div>

    </div>
</div>

@endsection()
@extends('Plantilla')

@section('contenido')
<link rel="stylesheet" href="{{ asset('estilos/botones.css') }}">
<link rel="stylesheet" href="{{ asset('estilos/titulos.css') }}">
<link rel="stylesheet" href="{{ asset('estilos/gestion.css') }}">

<div class="titulo">
    <h1>Gestión administrativa</h1>
</div>

<!-- filtrados cumunes -->
<div class="card shadow-sm mb-4 border-0">
    <div class="card-body p-4">
        <h5 class="fw-semibold mb-3">Filtros de búsqueda</h5>
        <div class="row g-3 align-items-end"> <!-- Agregado align-items-end -->
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
                        <button class="btn-principal"><i class="bi bi-plus-circle"></i> Nuevo grupo</button>
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
                        <tbody id="tabla-grupos">
                            <tr>
                                <td>1</td>
                                <td>IDGS-84</td>
                                <td>Ingeniería en Desarrollo de Software</td>
                                <td>8vo</td>
                                <td>25</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-warning editar-grupo" data-id="1">Editar</button>
                                    <button class="btn btn-sm btn-outline-danger eliminar-grupo" data-id="1">Eliminar</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
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
                        <button class="btn-principal"><i class="bi bi-plus-circle"></i> Nuevo alumno</button>
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
                        <tbody id="tabla-alumnos">
                            <tr>
                                <td>TIC-310036</td>
                                <td>Yorel Gutiérrez</td>
                                <td>IDGS-84</td>
                                <td>Ingeniería en Desarrollo de Software</td>
                                <td>yorel@email.com</td>
                                <td>Activo</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-warning editar-alumno" data-id="1">Editar</button>
                                    <button class="btn btn-sm btn-outline-danger eliminar-alumno" data-id="1">Eliminar</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
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
                        <button class="btn-principal"><i class="bi bi-plus-circle"></i> Nuevo docente</button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Núm. empleado</th>
                                <th>Nombre completo</th>
                                <th>Carrera(s)</th>
                                <th>Correo</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tabla-docentes">
                            <tr>
                                <td>19cJICP</td>
                                <td>Juan Manuel Tovar Sánchez</td>
                                <td>Ing. Software, Redes</td>
                                <td>juan.tovar@utnay.edu.mx</td>
                                <td>Activo</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-warning editar-docente" data-id="1">Editar</button>
                                    <button class="btn btn-sm btn-outline-danger eliminar-docente" data-id="1">Eliminar</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/gestion-opciones.js') }}"></script>
@endsection
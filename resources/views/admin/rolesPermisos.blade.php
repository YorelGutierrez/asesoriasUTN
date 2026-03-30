@extends('Plantilla')

@section('contenido')
<link rel="stylesheet" href="{{ asset('estilos/botones.css') }}">
<link rel="stylesheet" href="{{ asset('estilos/titulos.css') }}">

<div class="titulo">
    <h1>
        Roles y permisos
    </h1>
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
                                <th>Matricula/Clave</th>
                                <th>Rol</th>
                                <th>Estado</th>
                                <th>Gestión</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td>Yorel Gutiérrez</td>
                                <td>yorel@email.com</td>
                                <td>Ing. Software</td>
                                <td>TIC-310036</td>
                                <td><span>Administrador</span></td>
                                <td><span>Activo</span></td>
                                <td>
                                    <a href="#" class="btn btn-outline-warning btn-sm">Editar</a>
                                    <a href="#" class="btn btn-outline-danger btn-sm">Eliminar</a>
                                    <br>
                                    <a href="#" class="btn btn-outline-primary btn-sm">Desbloquear cuenta</a>
                                    <a href="#" class="btn btn-outline-success btn-sm">Bloquear cuenta</a>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection()
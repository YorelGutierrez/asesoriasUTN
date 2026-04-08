@extends('Plantilla')

@section('contenido')
<link rel="stylesheet" href="{{ asset('estilos/botones.css') }}">
<link rel="stylesheet" href="{{ asset('estilos/titulos.css') }}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="titulo">
    <h1>Bienvenido {{ Auth::user()->nombres . ' ' . Auth::user()->apellido_paterno . ' ' . Auth::user()->apellido_materno }}!</h1>
</div>

<!-- Tarjetas de resumen -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4">
                <h5 class="fw-semibold mb-0">PRÓXIMA ASESORÍA</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    <i class="bi bi-calendar-event-fill fs-1 text-success"></i>
                    <div>
                        <p class="mb-0 fw-bold">Mañana</p>
                        <span class="text-muted">17:00 h</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4">
                <h5 class="fw-semibold mb-0">ASESORÍAS AGENDADAS</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-end">
                    <h1 class="display-4 fw-bold text-success">3</h1>
                    <span class="text-muted">pendientes</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4">
                <h5 class="fw-semibold mb-0">ASESORÍAS COMPLETADAS</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-end">
                    <h1 class="display-4 fw-bold text-success">7</h1>
                    <span class="text-muted">este cuatrimestre</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Buscador rápido / Solicitar asesoría rápida -->
<div class="mt-5 mb-3">
    <div class="titulo">
        <h3>Solicitar asesoría rápida</h3>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-semibold mb-1">Matemáticas</h5>
                        <p class="text-muted mb-0">Prof. Denzel Crocker</p>
                    </div>
                    <i class="bi bi-person-badge fs-1 text-secondary"></i>
                </div>
                <div class="mt-3 text-end">
                    <button class="btn-principal">Solicitar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-semibold mb-1">Programación Web</h5>
                        <p class="text-muted mb-0">Prof. Juan Tovar</p>
                    </div>
                    <i class="bi bi-person-badge fs-1 text-secondary"></i>
                </div>
                <div class="mt-3 text-end">
                    <button class="btn-principal">Solicitar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-semibold mb-1">Bases de Datos</h5>
                        <p class="text-muted mb-0">Prof. María Gómez</p>
                    </div>
                    <i class="bi bi-person-badge fs-1 text-secondary"></i>
                </div>
                <div class="mt-3 text-end">
                    <button class="btn-principal">Solicitar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de asesorías recientes -->
<div class="mt-5"> <!-- Separación superior -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-semibold mb-0">Asesorías realizadas</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Tipo</th>
                            <th>Grupo / Alumno</th>
                            <th>Materia</th>
                            <th>Tema tratado</th>
                            <th>Fecha</th>
                            <th>Acuerdos / Resultados</th>
                            <th>Ver asesoria</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Individual</td>
                            <td>Gutiérrez Zepeda Yorel</td>
                            <td>Programación Web</td>
                            <td>Flexbox y Grid</td>
                            <td>2025-03-15</td>
                            <td>Revisión de layout responsive</td>
                            <td><a href="#" class="btn-secundario">Ver</a></td>
                        </tr>
                        <tr>
                            <td>Grupal</td>
                            <td>IDGS-84</td>
                            <td>Bases de Datos</td>
                            <td>Modelo relacional</td>
                            <td>2025-03-10</td>
                            <td>Avance del 80% en proyecto</td>
                            <td><a href="#" class="btn-secundario">Ver</a></td>
                        </tr>
                        <!-- más filas dinámicas -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: '¡Bienvenido!',
        text: '{{ session('success') }}',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Aceptar'
    });
</script>
@endif
@endsection()
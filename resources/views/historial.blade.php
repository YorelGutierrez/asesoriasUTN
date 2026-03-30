@extends('Plantilla')

@section('contenido')
<link rel="stylesheet" href="{{ asset('estilos/botones.css') }}">
<link rel="stylesheet" href="{{ asset('estilos/titulos.css') }}">

<div class="titulo">
    <h1>Historial General de asesorias</h1>
</div>

<!-- Filtros principales -->
<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-body p-4">
        <h5 class="fw-semibold mb-3">Filtrado de búsqueda</h5>
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-semibold">Cuatrimestre</label>
                <select class="form-select">
                    <option selected>Selecciona un cuatrimestre</option>
                    <option>1er Cuatrimestre</option>
                    <option>2do Cuatrimestre</option>
                    <option>3er Cuatrimestre</option>
                    <option>4to Cuatrimestre</option>
                    <option>5to Cuatrimestre</option>
                    <option>6to Cuatrimestre</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Materia</label>
                <select class="form-select">
                    <option selected>Selecciona materia</option>
                    <option>Matemáticas</option>
                    <option>Programación Web</option>
                    <option>Bases de Datos</option>
                    <option>Redes</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Buscar alumno</label>
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Nombre o matrícula...">
                    <button class="btn btn-outline-secondary" type="button">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">Fecha</label>
                <input type="date" class="form-control">
            </div>
        </div>
    </div>
</div>

<!-- Tabla de asesorías recientes (opcional para mostrar el historial) -->
<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-semibold mb-0">Asesorías realizadas</h5>
            <div class="col-md-4">
                <button class="btn-principal">Generar reporte cuatrimestral</button>
            </div>
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

<!-- Botón de regreso (opcional) -->
<a href="{{ route('dashboard')}}" class="btn-principal"><- Regresar al escritorio</a>
@endsection()
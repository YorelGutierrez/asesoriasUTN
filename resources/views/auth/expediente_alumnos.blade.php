@extends('Plantilla')

@section('contenido')
<link rel="stylesheet" href="{{ asset('estilos/botones.css') }}">
<link rel="stylesheet" href="{{ asset('estilos/titulos.css') }}">
<link rel="stylesheet" href="{{ asset('estilos/expediente.css') }}">

<div class="titulo">
    <h1>Expediente del alumno</h1>
</div>

<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-body p-4">
        <h2 class="h4 fw-bold mb-3">Expediente del alumno: <span style="color: #2c9f49;">Gutiérrez Zepeda Yorel Isai</span></h2>

        <div class="row g-4">
            <!-- Información académica -->
            <div class="col-md-6">
                <div class="bg-light p-3 rounded-3 h-100">
                    <h5 class="fw-semibold mb-3">Información académica</h5>
                    <p class="mb-1"><strong>Matrícula:</strong> TIC-310036</p>
                    <p class="mb-1"><strong>Grupo:</strong> IDGS-84</p>
                    <p class="mb-1"><strong>Carrera:</strong> Ingeniería en desarrollo de software</p>
                    <p class="mb-0"><strong>Status académico:</strong> bueno</p>
                </div>
            </div>

            <!-- Últimas asesorías (resumen rápido) -->
            <div class="col-md-6">
                <div class="bg-light p-3 rounded-3 h-100">
                    <h5 class="fw-semibold mb-3">Últimas asesorías</h5>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">• <strong>10/03/2026</strong> – Estructura de base de datos (acuerdo: ejercicios prácticos)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Materias reprobadas y temas que no domina -->
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-body">
                <h5 class="fw-semibold mb-3">Materias reprobadas</h5>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Matematicas para ingenieria II
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Estructura de base de datos
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Inglés I
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-body">
                <h5 class="fw-semibold mb-3">Temas que no domina</h5>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">• Consultas SQL complejas</li>
                    <li class="list-group-item">• Diseño responsive con CSS</li>
                    <li class="list-group-item">• Derivada de la place</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de asesorías -->
<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-body p-4">
        <h5 class="fw-semibold mb-3">Historial de asesorías</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Tipo</th>
                        <th>Grupo / Nombre</th>
                        <th>Tema tratado</th>
                        <th>Modalidad</th>
                        <th>Acuerdos / Resultados</th>
                        <th>Ver asesoria</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Individual</td>
                        <td>Gutiérrez Zepeda Yorel</td>
                        <td>Derivadas e integrales</td>
                        <td>Presencial</td>
                        <td>Resolver 10 ejercicios para próxima clase</td>
                        <td><a href="#" class="btn-secundario">Ver</a></td>
                    </tr>
                    <tr>
                        <td>Grupo</td>
                        <td>IDGS-84</td>
                        <td>Modelo relacional</td>
                        <td>Virtual</td>
                        <td>Avance del 80% en proyecto</td>
                        <td><a href="#" class="btn-secundario">Ver</a></td>
                    </tr>
                    <tr>
                        <td>Individual</td>
                        <td>Gutiérrez Zepeda Yorel</td>
                        <td>Flexbox y Grid</td>
                        <td>Presencial</td>
                        <td>Revisión de layout responsive</td>
                        <td><a href="#" class="btn-secundario">Ver</a></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Botón de regreso (opcional) -->
<a href="{{ route('alumnos') }}" class="btn-principal"><- Regresar al listado</a>

@endsection()
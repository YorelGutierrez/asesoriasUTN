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
        <h2 class="h4 fw-bold mb-3 titulo-borde-verde">Expediente del alumno:
            <span style="color: #2c9f49;">
                {{ $alumno->user->apellido_paterno }} {{ $alumno->user->apellido_materno }} {{ $alumno->user->nombres }}
            </span>
        </h2>

        <div class="row g-4">
            <!-- Información académica -->
            <div class="col-md-6">
                <div class="bg-light p-3 rounded-3 h-100">
                    <h5 class="fw-semibold mb-3">Información académica</h5>
                    <p class="mb-1"><strong>Matrícula:</strong> {{ $alumno->matricula }}</p>
                    <p class="mb-1"><strong>Grupo:</strong> {{ $alumno->grupo->nombre ?? 'Sin grupo' }}</p>
                    <p class="mb-1"><strong>Carrera:</strong> {{ $alumno->carrera->nombre ?? 'Sin carrera' }}</p>
                    <p class="mb-0"><strong>Status académico:</strong>
                        {{ $alumno->status_academico ?? 'No registrado' }}
                    </p>
                </div>
            </div>

            <!-- Últimas asesorías (resumen rápido) -->
            <div class="col-md-6">
                <div class="bg-light p-3 rounded-3 h-100">
                    <h5 class="fw-semibold mb-3">Últimas asesorías</h5>
                    @if($ultimasSesiones->isEmpty())
                    <p class="text-muted mb-0">Sin asesorías registradas aún.</p>
                    @else
                    <ul class="list-unstyled mb-0">
                        @foreach($ultimasSesiones as $sesion)
                        <li class="mb-2">
                            • <strong>{{ \Carbon\Carbon::parse($sesion->fecha_inicio)->format('d/m/Y') }}</strong>
                            – {{ $sesion->tema }}
                            @if($sesion->acuerdos->first())
                            (acuerdo: {{ Str::limit($sesion->acuerdos->first()->acuerdo, 40) }})
                            @endif
                        </li>
                        @endforeach
                    </ul>
                    @endif
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
                <h5 class="fw-semibold mb-3 titulo-borde-verde">Materias reprobadas</h5>
                @if($materiasReprobadas->isEmpty())
                <p class="text-muted">Sin materias reprobadas registradas.</p>
                @else
                <ul class="list-group list-group-flush">
                    @foreach($materiasReprobadas as $historial)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        {{ $historial->materia->nombre }}
                    </li>
                    @endforeach
                </ul>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-body">
                <h5 class="fw-semibold mb-3 titulo-borde-verde">Temas que no domina</h5>
                @if($temasNoDominados->isEmpty())
                <p class="text-muted">Sin temas registrados.</p>
                @else
                <ul class="list-group list-group-flush">
                    @foreach($temasNoDominados as $historial)
                    @foreach(explode(',', $historial->temas_no_dominados) as $tema)
                    <li class="list-group-item">• {{ trim($tema) }}</li>
                    @endforeach
                    @endforeach
                </ul>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Tabla de asesorías -->
<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-body p-4">
        <h5 class="fw-semibold mb-3 titulo-borde-verde">Historial de asesorías</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Tipo</th>
                        <th>Grupo / Nombre</th>
                        <th>Motivo</th>
                        <th>Tema tratado</th>
                        <th>Modalidad</th>
                        <th>Acuerdos / Resultados</th>
                        <th>Ver asesoria</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sesiones as $sesion)
                    @php
                    $esGrupo = $sesion->alumnos->count() > 1;
                    $reporte = optional($sesion->reporte)->first();
                    @endphp
                    <tr>
                        <td>{{ $esGrupo ? 'Grupo' : 'Individual' }}</td>
                        <td>
                            @if($esGrupo)
                            {{ $alumno->grupo->nombre ?? 'Grupo' }}
                            @else
                            {{ $alumno->user->apellido_paterno }} {{ $alumno->user->nombres }}
                            @endif
                        </td>
                        <td>{{ $sesion->motivo ?? '—' }}</td>
                        <td>{{ $sesion->tema }}</td>
                        <td>{{ ucfirst($sesion->modalidad) }}</td>
                        <td>{{ $sesion->acuerdos->first()->acuerdo ?? '—' }}</td>
                        <td>
                            @if($reporte)
                            <a href="{{ route('reporte.ver', $reporte->id) }}" target="_blank" class="btn-secundario"><i class="bi bi-eye me-1"></i>Ver</a>
                            @else
                            <span class="text-muted">Sin PDF</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-3">
                            Sin asesorías registradas para este alumno.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Botón de regreso -->
<a href="{{ route('alumnos') }}" class="btn-principal"><i class="bi bi-arrow-left me-1"></i>Regresar al listado</a>

        @endsection
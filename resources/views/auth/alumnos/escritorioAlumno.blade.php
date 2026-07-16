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
                <h5 class="fw-semibold mb-0 titulo-borde-verde">PRÓXIMA ASESORÍA</h5>
            </div>
            <div class="card-body">
                @if($proximaAsesoria)
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-calendar-event-fill fs-1 text-success"></i>
                        <div>
                            <p class="mb-0 fw-bold">{{ $proximaAsesoria->tema }}</p>
                            <span class="text-muted">
                                {{ \Carbon\Carbon::parse($proximaAsesoria->fecha_inicio)->format('d/m/Y H:i') }} h
                            </span>
                        </div>
                    </div>
                @else
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-calendar-event-fill fs-1 text-secondary"></i>
                        <div>
                            <p class="mb-0 fw-bold text-muted">Sin asesorías próximas</p>
                            <span class="text-muted">Agenda una desde <a href="{{ route('agenda') }}">Solicitar</a></span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4">
                <h5 class="fw-semibold mb-0 titulo-borde-verde">ASESORÍAS AGENDADAS</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-end">
                    <h1 class="display-4 fw-bold text-success">{{ $agendadas }}</h1>
                    <span class="text-muted">pendientes</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4">
                <h5 class="fw-semibold mb-0 titulo-borde-verde">ASESORÍAS COMPLETADAS</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-end">
                    <h1 class="display-4 fw-bold text-success">{{ $completadas }}</h1>
                    <span class="text-muted">realizadas</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Solicitar asesoría rápida -->
<div class="mt-5 mb-3">
    <div class="titulo">
        <h3>Solicitar asesoría rápida</h3>
    </div>
</div>

<div class="row g-4">
    @forelse($docentesAleatorios as $item)
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="fw-semibold mb-1">
                                {{ $item['docente']->nombres }} {{ $item['docente']->apellido_paterno }}
                            </h5>
                            <p class="text-muted mb-0">
                                @if(count($item['materias']) > 0)
                                    {{ $item['materias'][0] }}
                                    @if(count($item['materias']) > 1)
                                        <span class="badge bg-success ms-1">+{{ count($item['materias']) - 1 }} más</span>
                                    @endif
                                @else
                                    Sin materia asignada
                                @endif
                            </p>
                        </div>
                        <i class="bi bi-person-badge fs-1 text-secondary"></i>
                    </div>
                    <div class="mt-3 text-end">
                        <a href="{{ route('agenda') }}" class="btn-principal">Solicitar</a>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                @if(!$alumno || !$alumno->grupo)
                    No tienes un grupo asignado. Contacta al administrador.
                @else
                    No hay docentes disponibles en tu grupo para solicitar asesoría.
                @endif
            </div>
        </div>
    @endforelse
</div>

<!-- Tabla de asesorías recientes -->
<div class="mt-5">
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-semibold mb-0 titulo-borde-verde">Asesorías realizadas</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Tipo</th>
                            <th>Docente</th>
                            <th>Tema tratado</th>
                            <th>Fecha</th>
                            <th>Acuerdos / Resultados</th>
                            <th>Ver asesoria</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ultimasSesiones as $sesion)
                            @php
                                $esGrupo = $sesion->alumnos->count() > 1;
                                $reporte = $sesion->reporte;
                            @endphp
                            <tr>
                                <td>{{ $esGrupo ? 'Grupal' : 'Individual' }}</td>
                                <td>{{ $sesion->docente->nombres ?? 'N/A' }} {{ $sesion->docente->apellido_paterno ?? '' }}</td>
                                <td>{{ $sesion->tema }}</td>
                                <td>{{ \Carbon\Carbon::parse($sesion->fecha_inicio)->format('d/m/Y') }}</td>
                                <td>{{ $sesion->acuerdos->first()->acuerdo ?? '—' }}</td>
                                <td>
                                    @if($reporte)
                                        <a href="{{ route('reporte.ver', $reporte->id) }}" target="_blank" class="btn-secundario">Ver</a>
                                    @else
                                        <span class="text-muted">Sin PDF</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">
                                    Sin asesorías realizadas aún.
                                </td>
                            </tr>
                        @endforelse
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

@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: '{{ session('error') }}',
        confirmButtonColor: '#d33',
        confirmButtonText: 'Aceptar'
    });
</script>
@endif

@endsection
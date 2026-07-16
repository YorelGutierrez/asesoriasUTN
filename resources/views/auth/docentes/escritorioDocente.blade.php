@extends('Plantilla')

@section('contenido')
<link rel="stylesheet" href="{{ asset('estilos/botones.css') }}">
<link rel="stylesheet" href="{{ asset('estilos/titulos.css') }}">
<link rel="stylesheet" href="{{ asset('estilos/grupos.css') }}">

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
                            <span class="text-muted">Agenda una desde <a href="{{ route('agenda') }}">Agendar</a></span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4">
                <h5 class="fw-semibold mb-0 titulo-borde-verde">ALUMNOS TOTALES</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-end">
                    <h1 class="display-4 fw-bold text-success">{{ $totalAlumnos }}</h1>
                    <span class="text-muted">atendidos</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4">
                <h5 class="fw-semibold mb-0 titulo-borde-verde">GRUPOS ACTIVOS</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-end">
                    <h1 class="display-4 fw-bold text-success">{{ $gruposActivos }}</h1>
                    <i class="bi bi-exclamation-triangle-fill text-warning fs-3"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Mis grupos recientes -->
    <div class="mt-5 mb-3">
        <div class="titulo">
            <h3>Mis grupos recientes</h3>
        </div>
    </div>

    <div class="row">
        @forelse($gruposRecientes as $grupo)
            <div class="col-md-4 col-sm-12 mb-4">
                <div class="card-itid">
                    <div class="card-left">
                        <img src="{{ asset($grupo->carrera->logo ?? 'img/carreras/ITIID-DDH-gJkG.png') }}" alt="Logo carrera" class="logo">
                    </div>
                    <div class="card-right">
                        <div class="card-bg-decoration"></div>

                        <div class="card-content-wrapper">
                            <h3>Grupo: <span>{{ $grupo->nombre }}</span></h3>
                            <div class="stats-row">
                                <i class="bi bi-people-fill"></i> <span>: {{ $grupo->alumnos->count() }}</span>
                            </div>
                            <form method="POST" action="{{ route('grupos.seleccionar', $grupo->id) }}">
                                @csrf
                                <button type="submit" class="btn-principal">Seleccionar grupo</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="bi bi-info-circle me-2"></i>
                    No tienes grupos recientemente seleccionados.
                </div>
            </div>
        @endforelse
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
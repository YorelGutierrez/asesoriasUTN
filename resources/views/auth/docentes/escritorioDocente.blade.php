@extends('Plantilla')

@section('contenido')
<link rel="stylesheet" href="{{ asset('estilos/botones.css') }}">
<link rel="stylesheet" href="{{ asset('estilos/titulos.css') }}">
<link rel="stylesheet" href="{{ asset('estilos/grupos.css') }}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="titulo">
    <h1>Bienvenido {{ Auth::user()->nombres . ' ' . Auth::user()->apellido_paterno . ' ' . Auth::user()->apellido_materno }}!</h1>
</div>

<!-- ============================================================ -->
<!-- TARJETAS DE RESUMEN                                          -->
<!-- ============================================================ -->
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
                    <h1 class="display-4 fw-bold text-success">{{ $totalAlumnos ?? 0 }}</h1>
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
                    <h1 class="display-4 fw-bold text-success">{{ $gruposActivos ?? 0 }}</h1>
                    <i class="bi bi-exclamation-triangle-fill text-warning fs-3"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!-- GRUPOS RECIENTES                                             -->
<!-- ============================================================ -->
<div class="row g-4">
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

<!-- ============================================================ -->
<!-- GRÁFICAS DEL DOCENTE                                         -->
<!-- ============================================================ -->
<div class="row mt-5">
    <div class="col-12">
        <div class="titulo">
            <h3>Estadísticas de asesorías</h3>
        </div>
    </div>
</div>

<div class="row mt-3">
    <!-- Gráfica 1: Mis sesiones por mes -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-3 titulo-borde-verde">
                    <i class="bi bi-calendar-check me-2"></i>Mis asesorías por mes
                </h5>
                <div style="height: 220px; position: relative;">
                    <canvas id="chartMisAsesorias"></canvas>
                </div>
                <div class="mt-2 p-2 bg-light rounded">
                    <small class="text-muted">
                        <i class="bi bi-lightbulb text-warning me-1"></i>
                        <strong>Toma de decisión:</strong> Visualiza tu carga de trabajo mensual y planifica tu tiempo.
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfica 2: Asesorías por materia -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-3 titulo-borde-verde">
                    <i class="bi bi-bar-chart me-2"></i>Asesorías por materia
                </h5>
                <div style="height: 220px; position: relative;">
                    <canvas id="chartMateriasDocente"></canvas>
                </div>
                <div class="mt-2 p-2 bg-light rounded">
                    <small class="text-muted">
                        <i class="bi bi-lightbulb text-warning me-1"></i>
                        <strong>Toma de decisión:</strong> Identifica qué materias requieren más atención de tu parte.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-2">
    <!-- Gráfica 3: Mis alumnos más frecuentes -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-3 titulo-borde-verde">
                    <i class="bi bi-person-lines-fill me-2"></i>Mis alumnos más frecuentes
                </h5>
                <div style="height: 220px; position: relative;">
                    <canvas id="chartMisAlumnos"></canvas>
                </div>
                <div class="mt-2 p-2 bg-light rounded">
                    <small class="text-muted">
                        <i class="bi bi-lightbulb text-warning me-1"></i>
                        <strong>Toma de decisión:</strong> Identifica alumnos que requieren seguimiento personalizado.
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfica 4: Estado de mis solicitudes -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-3 titulo-borde-verde">
                    <i class="bi bi-pie-chart me-2"></i>Estado de mis solicitudes
                </h5>
                <div style="height: 220px; position: relative;">
                    <canvas id="chartEstadoDocente"></canvas>
                </div>
                <div class="mt-2 p-2 bg-light rounded">
                    <small class="text-muted">
                        <i class="bi bi-lightbulb text-warning me-1"></i>
                        <strong>Toma de decisión:</strong> Conoce el estado de tus asesorías y prioriza las pendientes.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!-- ALERTAS DE SWEETALERT                                        -->
<!-- ============================================================ -->
@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: '¡Éxito!',
        text: '{{ session('success') }}',
        confirmButtonColor: '#28a745',
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
        confirmButtonColor: '#28a745',
        confirmButtonText: 'Aceptar'
    });
</script>
@endif

<!-- ============================================================ -->
<!-- SCRIPTS PARA GRÁFICAS                                       -->
<!-- ============================================================ -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Función para crear gráfica
    function crearGrafica(elementId, labels, data, label, color, tipo = 'bar') {
        const ctx = document.getElementById(elementId);
        if (!ctx) return;

        if (!data || data.length === 0) {
            ctx.parentElement.innerHTML = `
                <div class="text-center text-muted py-4">
                    <i class="bi bi-bar-chart-line fs-1"></i>
                    <p class="mt-2">No hay datos disponibles</p>
                </div>
            `;
            return;
        }

        const colores = ['#4CAF50', '#2196F3', '#FF9800', '#9C27B0', '#00BCD4'];
        const backgroundColors = tipo === 'doughnut' ? colores.slice(0, data.length) : color + '80';

        new Chart(ctx, {
            type: tipo,
            data: {
                labels: labels,
                datasets: [{
                    label: label,
                    data: data,
                    backgroundColor: tipo === 'doughnut' ? backgroundColors : color + '80',
                    borderColor: tipo === 'doughnut' ? backgroundColors : color,
                    borderWidth: 2,
                    borderRadius: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: tipo === 'doughnut' ? 'bottom' : 'top',
                        labels: {
                            font: { size: 10 },
                            boxWidth: 12
                        }
                    }
                },
                scales: tipo === 'doughnut' ? undefined : {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    }

    // ========== CREAR GRÁFICAS CON DATOS DE PHP ==========

    // 1. Mis asesorías por mes
    crearGrafica('chartMisAsesorias',
        {!! json_encode($mesesLabels ?? []) !!},
        {!! json_encode($mesesValues ?? []) !!},
        'Asesorías', '#4CAF50'
    );

    // 2. Asesorías por materia
    crearGrafica('chartMateriasDocente',
        {!! json_encode($materiasLabels ?? []) !!},
        {!! json_encode($materiasValues ?? []) !!},
        'Asesorías', '#FF9800'
    );

    // 3. Mis alumnos más frecuentes
    crearGrafica('chartMisAlumnos',
        {!! json_encode($alumnosLabels ?? []) !!},
        {!! json_encode($alumnosValues ?? []) !!},
        'Asesorías', '#2196F3'
    );

    // 4. Estado de mis solicitudes (Dona)
    crearGrafica('chartEstadoDocente',
        {!! json_encode($solicitudesLabels ?? []) !!},
        {!! json_encode($solicitudesValues ?? []) !!},
        'Solicitudes', '#9C27B0', 'doughnut'
    );
});
</script>

@endsection
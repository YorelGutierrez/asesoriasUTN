@extends('Plantilla')

@section('contenido')
<link rel="stylesheet" href="{{ asset('estilos/botones.css') }}">
<link rel="stylesheet" href="{{ asset('estilos/titulos.css') }}">
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

<!-- ============================================================ -->
<!-- GRÁFICAS DEL ALUMNO                                          -->
<!-- ============================================================ -->
<div class="row mt-4">
    <!-- Gráfica 1: Mis sesiones por mes -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-3 titulo-borde-verde">
                    <i class="bi bi-calendar2-week me-2"></i>Mis asesorías por mes
                </h5>
                <div style="height: 220px; position: relative;">
                    <canvas id="chartMisAsesoriasAlumno"></canvas>
                </div>
                <div class="mt-2 p-2 bg-light rounded">
                    <small class="text-muted">
                        <i class="bi bi-lightbulb text-warning me-1"></i>
                        <strong>Toma de decisión:</strong> Visualiza tu progreso académico en asesorías mes a mes.
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfica 2: Mis sesiones por materia -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-3 titulo-borde-verde">
                    <i class="bi bi-book me-2"></i>Asesorías por materia
                </h5>
                <div style="height: 220px; position: relative;">
                    <canvas id="chartMateriasAlumno"></canvas>
                </div>
                <div class="mt-2 p-2 bg-light rounded">
                    <small class="text-muted">
                        <i class="bi bi-lightbulb text-warning me-1"></i>
                        <strong>Toma de decisión:</strong> Identifica qué materias necesitan más atención o refuerzo.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-2">
    <!-- Gráfica 3: Docentes que me asesoran -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-3 titulo-borde-verde">
                    <i class="bi bi-person-video2 me-2"></i>Docentes que me asesoran
                </h5>
                <div style="height: 220px; position: relative;">
                    <canvas id="chartDocentesAlumno"></canvas>
                </div>
                <div class="mt-2 p-2 bg-light rounded">
                    <small class="text-muted">
                        <i class="bi bi-lightbulb text-warning me-1"></i>
                        <strong>Toma de decisión:</strong> Identifica con qué docentes tienes mayor interacción académica.
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
                    <canvas id="chartEstadoAlumno"></canvas>
                </div>
                <div class="mt-2 p-2 bg-light rounded">
                    <small class="text-muted">
                        <i class="bi bi-lightbulb text-warning me-1"></i>
                        <strong>Toma de decisión:</strong> Conoce el estado de tus solicitudes y da seguimiento a las pendientes.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!-- SOLICITAR ASESORÍA RÁPIDA                                    -->
<!-- ============================================================ -->
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

<!-- ============================================================ -->
<!-- TABLA DE ASESORÍAS RECIENTES                                 -->
<!-- ============================================================ -->
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
                        labels: { font: { size: 10 }, boxWidth: 12 }
                    }
                },
                scales: tipo === 'doughnut' ? undefined : {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });
    }

    // ========== CREAR GRÁFICAS CON DATOS DE PHP ==========

    // 1. Mis asesorías por mes
    crearGrafica('chartMisAsesoriasAlumno',
        {!! json_encode($mesesLabels ?? []) !!},
        {!! json_encode($mesesValues ?? []) !!},
        'Asesorías', '#4CAF50'
    );

    // 2. Asesorías por materia
    crearGrafica('chartMateriasAlumno',
        {!! json_encode($materiasLabels ?? []) !!},
        {!! json_encode($materiasValues ?? []) !!},
        'Asesorías', '#FF9800'
    );

    // 3. Docentes que me asesoran
    crearGrafica('chartDocentesAlumno',
        {!! json_encode($docentesLabels ?? []) !!},
        {!! json_encode($docentesValues ?? []) !!},
        'Asesorías', '#2196F3'
    );

    // 4. Estado de mis solicitudes (Dona)
    crearGrafica('chartEstadoAlumno',
        {!! json_encode($solicitudesLabels ?? []) !!},
        {!! json_encode($solicitudesValues ?? []) !!},
        'Solicitudes', '#9C27B0', 'doughnut'
    );
});
</script>

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

@endsection
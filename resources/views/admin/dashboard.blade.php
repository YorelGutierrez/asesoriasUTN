@extends('Plantilla')

@section('contenido')
<link rel="stylesheet" href="{{ asset('estilos/botones.css') }}">
<link rel="stylesheet" href="{{ asset('estilos/titulos.css') }}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="titulo">
    <h1>Bienvenido <span id="nombreUsuario">...</span></h1>
</div>

<div class="row align-items-stretch">
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-3 titulo-borde-verde">Total de usuarios</h5>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="fw-semibold">Administradores</div>
                        <span class="badge rounded-pill text-bg-success"><i class="bi bi-person-fill"></i> {{ $totalAdministradores }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="fw-semibold">Docentes</div>
                        <span class="badge rounded-pill text-bg-success"><i class="bi bi-person-fill"></i> {{ $totalDocentes }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="fw-semibold">Tutores</div>
                        <span class="badge rounded-pill text-bg-success"><i class="bi bi-person-fill"></i> {{ $totalTutores }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="fw-semibold">Alumnos</div>
                        <span class="badge rounded-pill text-bg-success"><i class="bi bi-person-fill"></i> {{ $totalAlumnos }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- RESPALDOS -->
    <div class="col-md-8 mb-4">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="fw-semibold mb-0 titulo-borde-verde">Respaldos del sistema</h5>
                    <button class="btn-restaurar"
                        onclick="mostrarListaRespaldos()"
                        title="Restaurar respaldo">
                        <i class="bi bi-arrow-counterclockwise fs-5"></i>
                    </button>
                </div>

                @if($ultimo)
                <p class="mt-3"><strong>Último respaldo:</strong> {{ $ultimo['fecha'] }}</p>
                <p><strong>Estado:</strong> <span class="text-success">Correcto</span></p>
                @else
                <p class="mt-3"><strong>Último respaldo:</strong> No hay respaldos</p>
                <p><strong>Estado:</strong> <span class="text-danger">Sin respaldos</span></p>
                @endif

                @if($horaProgramada)
                <p><strong>Programado para:</strong> {{ $horaProgramada }}</p>
                @endif

                <div class="row g-2 mt-3">
                    <div class="col d-flex">
                        <form action="{{ route('respaldo.generar') }}" method="POST" class="w-100">
                            @csrf
                            <button class="btn-principal w-100 h-100">Generar respaldo</button>
                        </form>
                    </div>
                    <div class="col d-flex">
                        <button class="btn-secundario w-100 h-100" onclick="toggleCalendar()">Programar</button>
                    </div>
                </div>

                <div id="calendarBox" class="mt-3 d-none">
                    <input type="datetime-local" id="fechaHora" class="form-control">
                    <div class="d-flex gap-2 mt-2">
                        <button class="btn btn-danger w-50 rounded-pill" onclick="cerrarCalendario()">Cancelar</button>
                        <button class="btn-principal w-50" onclick="guardarProgramacion()">Confirmar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row align-items-stretch">
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-body p-4 d-flex flex-column">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-semibold mb-0 titulo-borde-verde">Bitácora de actividad</h5>
                    <button id="btnLimpiarLogs" class="btn btn-danger btn-sm rounded-pill">Limpiar todo</button>
                </div>
                <div id="bitacora" class="flex-grow-1 pe-2" style="max-height: 220px; overflow-y: auto;">
                    <!-- Los logs se cargarán dinámicamente aquí -->
                </div>
            </div>
        </div>
    </div>
    <!-- ACCIONES DE GESTIÓN -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-4 titulo-borde-verde">Gestión del sistema | Acciones rapidas</h5>
                <div class="d-grid gap-2">
                    <a href="{{ route('gestion', ['tab' => 'alumnos']) }}" class="btn-principal">Gestionar alumnos</a>
                    <a href="{{ route('gestion', ['tab' => 'grupos']) }}" class="btn-principal">Gestionar grupos</a>
                    <a href="{{ route('gestion', ['tab' => 'docentes']) }}" class="btn-principal">Gestionar docentes</a>
                    <a href="{{ route('admin.asignaciones') }}" class="btn-principal">Asingaciones academicas</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!--prueba de diseño de graficas-->
<div class="row mt-4">
    <!-- Gráfica 1: Solicitudes por estado -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-3 titulo-borde-verde">
                    <i class="bi bi-clipboard-check me-2"></i>Solicitudes por estado
                </h5>
                <div style="height: 220px; position: relative;">
                    <canvas id="chartSolicitudesEstado"></canvas>
                </div>
                <div class="mt-2 p-2 bg-light rounded">
                    <small class="text-muted">
                        <i class="bi bi-lightbulb text-warning me-1"></i>
                        <strong>Toma de decisión:</strong> Permite priorizar solicitudes pendientes y asignar recursos según la demanda.
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfica 2: Solicitudes por mes -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-3 titulo-borde-verde">
                    <i class="bi bi-calendar3 me-2"></i>Solicitudes por mes
                </h5>
                <div style="height: 220px; position: relative;">
                    <canvas id="chartSolicitudesMes"></canvas>
                </div>
                <div class="mt-2 p-2 bg-light rounded">
                    <small class="text-muted">
                        <i class="bi bi-lightbulb text-warning me-1"></i>
                        <strong>Toma de decisión:</strong> Identifica picos de demanda (ej: antes de exámenes) para planificar recursos anticipadamente.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!-- FILA 3: GRÁFICAS DE DETALLE                                 -->
<!-- ============================================================ -->
<div class="row mt-2">
    <!-- Gráfica 3: Materias con más solicitudes -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-3 titulo-borde-verde">
                    <i class="bi bi-book me-2"></i>Materias con más solicitudes
                </h5>
                <div style="height: 220px; position: relative;">
                    <canvas id="chartSolicitudesMaterias"></canvas>
                </div>
                <div class="mt-2 p-2 bg-light rounded">
                    <small class="text-muted">
                        <i class="bi bi-lightbulb text-warning me-1"></i>
                        <strong>Toma de decisión:</strong> Detecta materias con mayor dificultad para reforzar estrategias de enseñanza.
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfica 4: Alumnos con más solicitudes -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-3 titulo-borde-verde">
                    <i class="bi bi-person-lines-fill me-2"></i>Alumnos con más solicitudes
                </h5>
                <div style="height: 220px; position: relative;">
                    <canvas id="chartSolicitudesAlumnos"></canvas>
                </div>
                <div class="mt-2 p-2 bg-light rounded">
                    <small class="text-muted">
                        <i class="bi bi-lightbulb text-warning me-1"></i>
                        <strong>Toma de decisión:</strong> Identifica alumnos que requieren atención prioritaria o seguimiento personalizado.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!-- FILA 4: GRÁFICAS DE OPERACIÓN                               -->
<!-- ============================================================ -->
<div class="row mt-2">
    <!-- Gráfica 5: Docentes con más asesorías -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-3 titulo-borde-verde">
                    <i class="bi bi-person-video2 me-2"></i>Docentes con más asesorías
                </h5>
                <div style="height: 220px; position: relative;">
                    <canvas id="chartSolicitudesDocentes"></canvas>
                </div>
                <div class="mt-2 p-2 bg-light rounded">
                    <small class="text-muted">
                        <i class="bi bi-lightbulb text-warning me-1"></i>
                        <strong>Toma de decisión:</strong> Permite equilibrar la carga de trabajo entre docentes y reconocer su desempeño.
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfica 6: Solicitudes por día -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-3 titulo-borde-verde">
                    <i class="bi bi-calendar-week me-2"></i>Solicitudes por día
                </h5>
                <div style="height: 220px; position: relative;">
                    <canvas id="chartSolicitudesDia"></canvas>
                </div>
                <div class="mt-2 p-2 bg-light rounded">
                    <small class="text-muted">
                        <i class="bi bi-lightbulb text-warning me-1"></i>
                        <strong>Toma de decisión:</strong> Optimiza la distribución de horarios de atención según los días de mayor demanda.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!-- FILA 5: GRÁFICAS DE RESULTADOS                              -->
<!-- ============================================================ -->
<div class="row mt-2">
    <!-- Gráfica 7: Resultados de asesorías -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-3 titulo-borde-verde">
                    <i class="bi bi-check2-circle me-2"></i>Resultados de asesorías
                </h5>
                <div style="height: 220px; position: relative;">
                    <canvas id="chartResultados"></canvas>
                </div>
                <div class="mt-2 p-2 bg-light rounded">
                    <small class="text-muted">
                        <i class="bi bi-lightbulb text-warning me-1"></i>
                        <strong>Toma de decisión:</strong> Mide la efectividad de las asesorías y permite ajustar estrategias de apoyo académico.
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfica 8: Tiempo promedio de atención -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-3 titulo-borde-verde">
                    <i class="bi bi-clock-history me-2"></i>Tiempo promedio de atención (horas)
                </h5>
                <div style="height: 220px; position: relative;">
                    <canvas id="chartTiempoAtencion"></canvas>
                </div>
                <div class="mt-2 p-2 bg-light rounded">
                    <small class="text-muted">
                        <i class="bi bi-lightbulb text-warning me-1"></i>
                        <strong>Toma de decisión:</strong> Detecta cuellos de botella en el proceso de atención y optimiza tiempos de respuesta.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>


{{-- Alerta de bienvenida (solo después de login) --}}
@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: '¡Bienvenido!',
        text: '{{ session('
        success ') }}',
        confirmButtonColor: '#2c9f49',
        confirmButtonText: 'Aceptar'
    });
</script>
@endif

{{-- Alerta de respaldo exitoso --}}
@if(session('respaldo_success'))
<script>
    Swal.fire({
        icon: 'success',
        title: '¡Respaldo completado!',
        text: '{{ session('
        respaldo_success ') }}',
        confirmButtonColor: '#2c9f49',
        confirmButtonText: 'Aceptar'
    });
</script>
@endif

{{-- Alerta de error general --}}
@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: '{{ session('
        error ') }}',
        confirmButtonColor: '#2c9f49',
        confirmButtonText: 'Aceptar'
    });
</script>
@endif

<script src="{{ asset('js/logs.js') }}"></script>
<script>
    function cargarLogs() {
        fetch('/api/logs', {
                headers: {
                    'Authorization': 'Bearer ' + getCookie('jwt_token'),
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(logs => {
                const container = document.getElementById('bitacora');
                if (!container) return;
                container.innerHTML = '';

                // LIMITAR A 3 REGISTROS VISIBLES (el resto se verá con scroll)
                const logsMostrar = logs.slice(0, 5);

                logsMostrar.forEach(log => {
                    let nombreUsuario = 'Sistema';
                    let fotoUrl = 'https://ui-avatars.com/api/?name=Sistema&background=e9ecef&color=343a40';
                    if (log.user) {
                        const nombres = [log.user.nombres, log.user.apellido_paterno, log.user.apellido_materno].filter(Boolean).join(' ');
                        nombreUsuario = nombres || 'Usuario';
                        fotoUrl = log.user.foto_perfil ? log.user.foto_perfil : `https://ui-avatars.com/api/?name=${encodeURIComponent(nombreUsuario)}&background=e9ecef&color=343a40`;
                    }

                    const item = document.createElement('div');
                    item.className = "d-flex align-items-start mb-3 border-bottom pb-2";
                    item.innerHTML = `
                <img src="${fotoUrl}" class="rounded-circle me-3 mt-1" width="36" height="36">
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-semibold" style="font-size: 0.9rem;">${escapeHtml(nombreUsuario)}</span>
                        <small class="text-black-50" style="font-size: 0.75rem;">${formatearFecha(log.created_at)}</small>
                    </div>
                    <p class="mb-0 text-muted lh-sm" style="font-size: 0.85rem;">${escapeHtml(log.descripcion ?? 'Sin descripción')}</p>
                </div>
                <button class="btn btn-sm text-danger p-0 ms-2 mt-1 border-0 eliminar-log" data-id="${log.id}" title="Eliminar registro">
                    <i class="bi bi-trash3"></i>
                </button>
            `;

                    const deleteBtn = item.querySelector('.eliminar-log');
                    deleteBtn.addEventListener('click', () => {
                        const logId = deleteBtn.dataset.id;
                        fetch(`/api/logs/${logId}`, {
                                method: 'DELETE',
                                headers: {
                                    'Authorization': 'Bearer ' + getCookie('jwt_token'),
                                    'Accept': 'application/json'
                                }
                            })
                            .then(() => item.remove())
                            .catch(err => console.error(err));
                    });

                    container.appendChild(item);
                });
            })
            .catch(err => console.error(err));
    }

    document.addEventListener('DOMContentLoaded', cargarLogs);
</script>

<script>
    window.respaldoAutomaticoUrl = "{{ route('respaldo.automatico.store') }}";
    window.respaldoListarUrl = "{{ route('respaldo.listar') }}";
    window.respaldoRestaurarUrl = "{{ route('respaldo.restaurar') }}";
    window.csrfToken = "{{ csrf_token() }}";
</script>
<script src="{{ asset('js/ajax-respaldos.js') }}"></script>

<script>
    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
    }

    fetch('/api/me', {
            headers: {
                'Authorization': 'Bearer ' + getCookie('jwt_token')
            }
        })
        .then(res => res.json())
        .then(data => {
            console.log('JWT funcionando:', data);
            document.getElementById('nombreUsuario').innerText = data.nombres;
        })
        .catch(err => {
            console.error('Error JWT:', err);
            document.getElementById('nombreUsuario').innerText = 'Administrador';
        });
</script>

<script>
    // Función para guardar la programación del respaldo con alerta
    function guardarProgramacion() {
        const fecha = document.getElementById('fechaHora').value;
        if (!fecha) {
            Swal.fire('Error', 'Selecciona una fecha y hora', 'error');
            return;
        }

        fetch(window.respaldoAutomaticoUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.csrfToken
                },
                body: JSON.stringify({
                    fecha: fecha
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Programado!',
                        text: data.message || 'Respaldo programado correctamente',
                        confirmButtonColor: '#2c9f49'
                    }).then(() => {
                        cerrarCalendario();
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', data.message || 'Error al programar', 'error');
                }
            })
            .catch(err => {
                Swal.fire('Error', 'Error de conexión', 'error');
            });
    }
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Función para crear gráfica
    function crearGrafica(elementId, labels, data, label, color, tipo = 'bar') {
        const ctx = document.getElementById(elementId);
        if (!ctx) return;

        // Verificar si hay datos
        if (!data || data.length === 0) {
            ctx.parentElement.innerHTML = `
                <div class="text-center text-muted py-4">
                    <i class="bi bi-bar-chart-line fs-1"></i>
                    <p class="mt-2">No hay datos disponibles</p>
                </div>
            `;
            return;
        }

        const colores = ['#FF9800', '#2196F3', '#f44336', '#4CAF50', '#9C27B0', '#00BCD4', '#FF5722', '#8BC34A'];
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

    // 1. Solicitudes por estado (Dona)
    crearGrafica('chartSolicitudesEstado',
        {!! json_encode($estadosLabels ?? []) !!},
        {!! json_encode($estadosValues ?? []) !!},
        'Solicitudes', '#4CAF50', 'doughnut'
    );

    // 2. Solicitudes por mes (Barras)
    crearGrafica('chartSolicitudesMes',
        {!! json_encode($mesesLabels ?? []) !!},
        {!! json_encode($mesesValues ?? []) !!},
        'Solicitudes', '#2196F3'
    );

    // 3. Materias con más solicitudes (Barras)
    crearGrafica('chartSolicitudesMaterias',
        {!! json_encode($materiasLabels ?? []) !!},
        {!! json_encode($materiasValues ?? []) !!},
        'Solicitudes', '#FF9800'
    );

    // 4. Alumnos con más solicitudes (Barras)
    crearGrafica('chartSolicitudesAlumnos',
        {!! json_encode($alumnosLabels ?? []) !!},
        {!! json_encode($alumnosValues ?? []) !!},
        'Solicitudes', '#9C27B0'
    );

    // 5. Docentes con más asesorías (Barras)
    crearGrafica('chartSolicitudesDocentes',
        {!! json_encode($docentesLabels ?? []) !!},
        {!! json_encode($docentesValues ?? []) !!},
        'Asesorías', '#00BCD4'
    );

    // 6. Solicitudes por día (Barras)
    crearGrafica('chartSolicitudesDia',
        {!! json_encode($diasLabels ?? []) !!},
        {!! json_encode($diasValues ?? []) !!},
        'Solicitudes', '#FF5722'
    );

    // 7. Resultados de asesorías (Dona)
    crearGrafica('chartResultados',
        {!! json_encode($resultadosLabels ?? []) !!},
        {!! json_encode($resultadosValues ?? []) !!},
        'Asesorías', '#8BC34A', 'doughnut'
    );

    // 8. Tiempo promedio de atención (Barras)
    crearGrafica('chartTiempoAtencion',
        {!! json_encode($tiemposLabels ?? []) !!},
        {!! json_encode($tiemposValues ?? []) !!},
        'Horas promedio', '#E91E63'
    );
});
</script>


@endsection
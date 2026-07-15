@extends('Plantilla')

@section('contenido')
<link rel="stylesheet" href="{{ asset('estilos/botones.css') }}">
<link rel="stylesheet" href="{{ asset('estilos/titulos.css') }}">
<div class="titulo">
    <h1>Registro de Asesorías</h1>
</div>

<div class="mt-4">
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white border-0 pt-4">
            <h4 class="fw-semibold mb-0 titulo-borde-verde"> Formulario de Asesoría</h4>
        </div>
        <div class="card-body p-4">

            <form id="formAsesoria" method="POST" action="{{ route('asesoria.store') }}">
                @csrf

                {{-- Fila 1: Carrera y Tipo de asesoría --}}
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Carrera <span class="text-danger">*</span></label>
                        <select class="form-select" name="carrera_id" id="carrera_id" required>
                            <option value="">-- Seleccionar carrera --</option>
                            @foreach($carreras as $carrera)
                            <option value="{{ $carrera->id }}" {{ old('carrera_id') == $carrera->id ? 'selected' : '' }}>
                                {{ $carrera->nombre }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tipo de asesoría <span class="text-danger">*</span></label>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="tipo_asesoria" id="tipo_individual" value="individual" {{ old('tipo_asesoria') == 'individual' ? 'checked' : '' }} checked>
                                <label class="form-check-label" for="tipo_individual">
                                    <i class="bi bi-person"></i> Individual
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="tipo_asesoria" id="tipo_grupal" value="grupal" {{ old('tipo_asesoria') == 'grupal' ? 'checked' : '' }}>
                                <label class="form-check-label" for="tipo_grupal">
                                    <i class="bi bi-people"></i> Grupal
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Fila 2: Asignatura y Tema --}}
                <div class="row g-3 mt-2">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Asignatura <span class="text-danger">*</span></label>
                        <select class="form-select" name="materia_id" id="materia_id" required>
                            <option value="">-- Seleccionar asignatura --</option>
                            @foreach($materias as $materia)
                            <option value="{{ $materia->id }}">
                                {{ $materia->nombre }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tema <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="tema" placeholder="Ej: Derivadas, Álgebra, Programación..." value="{{ old('tema') }}" required>
                    </div>
                </div>

                {{-- Fila: Motivo y Modalidad --}}
                <div class="row g-3 mt-2">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Motivo de la asesoría <span class="text-danger">*</span></label>
                        <select class="form-select" name="motivo" id="motivo" required>
                            <option value="Reforzamiento">Reforzamiento</option>
                            <option value="Reprobación de materia">Reprobación de materia</option>
                            <option value="Dudas generales">Dudas generales</option>
                            <option value="Preparación para examen">Preparación para examen</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Modalidad <span class="text-danger">*</span></label>
                        <select class="form-select" name="modalidad" id="modalidad" required>
                            <option value="presencial">Presencial</option>
                            <option value="virtual">Virtual</option>
                        </select>
                    </div>
                </div>

                {{-- Fila: Acuerdo (Resultado de la asesoría) --}}
                <div class="row g-3 mt-2">
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Acuerdo / Resultado de la asesoría</label>
                        <textarea class="form-control" name="acuerdo" rows="3" placeholder="Escribe el acuerdo o resultado de la asesoría...">{{ old('acuerdo') }}</textarea>
                    </div>
                </div>

                {{-- Fila 3: Fecha y Horario --}}
                <div class="row g-3 mt-2">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Fecha <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="fecha" id="fecha" value="{{ old('fecha') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Hora de inicio</label>
                        <input type="time" class="form-control" name="hora_inicio" value="{{ old('hora_inicio') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Hora de fin</label>
                        <input type="time" class="form-control" name="hora_fin" value="{{ old('hora_fin') }}">
                    </div>
                </div>

                {{-- Tabla de alumnos con filtro por grupos --}}
                <div class="mt-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <label class="form-label fw-semibold mb-0">Lista de alumnos <span class="text-danger">*</span></label>
                        <span class="form-text mb-0">
                            <i class="bi bi-info-circle"></i> Selecciona los alumnos que asistirán a la asesoría
                        </span>
                    </div>

                    {{-- Barra de herramientas: Todos, Ninguno y Buscador --}}
                    <div class="card mb-3">
                        <div class="card-body py-2">
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <span class="fw-semibold me-2"><i class="bi bi-check2-square"></i> Seleccionar:</span>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-todos">Todos</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-ninguno">Ninguno</button>

                                <span class="mx-2 text-muted">|</span>

                                <div class="input-group" style="max-width: 280px;">
                                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                                    <input type="text" id="buscarAlumno" class="form-control form-control-sm" placeholder="Buscar por nombre...">
                                </div>

                                @if(session('grupo_activo_nombre') && auth()->user()->rol !== 'admin')
                                <span class="badge bg-success ms-2">
                                    <i class="bi bi-people-fill me-1"></i> {{ session('grupo_activo_nombre') }}
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 rounded-4">
                        <div class="card-body p-4">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle" id="tablaAlumnos">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="50">#</th>
                                            <th>Nombre del Alumno</th>
                                            <th width="150">Grupo</th>
                                            <th width="100" class="text-center">Seleccionar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($alumnos as $index => $alumno)
                                        <tr class="alumno-row" data-id="{{ $alumno->id }}" data-grupo="{{ $alumno->grupo ? $alumno->grupo->nombre : 'Sin grupo' }}">
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <i class="bi bi-person-circle me-2"></i>
                                                {{ $alumno->user->nombres }} {{ $alumno->user->apellido_paterno }} {{ $alumno->user->apellido_materno }}
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $alumno->grupo ? $alumno->grupo->nombre : 'Sin grupo' }}</span>
                                            </td>
                                            <td class="text-center">
                                                <input type="checkbox" class="form-check-input seleccionar-alumno" name="alumnos[]" value="{{ $alumno->id }}">
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">
                                                @if(auth()->user()->rol === 'admin')
                                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                                <p class="mb-0">No hay alumnos registrados en el sistema.</p>
                                                @elseif(!session('grupo_activo_id'))
                                                <i class="bi bi-exclamation-triangle fs-1 d-block mb-2 text-warning"></i>
                                                <p class="mb-0">
                                                    <strong>No has seleccionado un grupo.</strong><br>
                                                    <a href="{{ route('grupos') }}" class="alert-link">Selecciona un grupo</a> para ver sus alumnos.
                                                </p>
                                                @else
                                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                                <p class="mb-0">No hay alumnos en este grupo.</p>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Botones --}}
                <div class="row g-3 mt-4">
                    <div class="col-md-6">
                        <button type="button" id="btnGuardar" class="btn-principal w-100 py-2">
                            <i class="bi bi-save me-2"></i> Guardar Asesoría
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button type="button" class="btn-secundario w-100 py-2" onclick="window.history.back();">
                            <i class="bi bi-arrow-left me-2"></i> Cancelar
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

{{-- ===== SCRIPTS ===== --}}

{{-- 1. Lógica de selección de alumnos (checkbox, botones grupo, etc.) --}}
<script src="{{ asset('js/asesoria.js') }}"></script>

{{-- 2. Script principal para manejar el flujo de guardado --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnGuardar = document.getElementById('btnGuardar');
        const form = document.getElementById('formAsesoria');

        btnGuardar.addEventListener('click', function(e) {
            e.preventDefault();

            // Validar que haya al menos un alumno seleccionado
            const alumnosSeleccionados = document.querySelectorAll('.seleccionar-alumno:checked');
            if (alumnosSeleccionados.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Selecciona al menos un alumno',
                    text: 'Debes seleccionar al menos un alumno para la asesoría.',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            // 1. Alerta de confirmación de guardado
            Swal.fire({
                title: '¿Guardar asesoría?',
                text: '¿Estás seguro de que quieres registrar esta asesoría?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2c9f49',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, guardar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Mostrar loading
                    Swal.fire({
                        title: 'Guardando asesoría...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    // Enviar formulario vía AJAX
                    const formData = new FormData(form);

                    fetch('{{ route('asesoria.store') }}', {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // 2. Alerta para decidir sobre el PDF
                                Swal.fire({
                                    title: '¿Generar reporte?',
                                    text: '¿Quieres descargar el reporte de esta asesoría ahora o después?',
                                    icon: 'question',
                                    showDenyButton: true,
                                    confirmButtonText: 'Ahora',
                                    denyButtonText: 'Después',
                                    cancelButtonText: 'No generar',
                                    showCancelButton: true,
                                    confirmButtonColor: '#2c9f49',
                                    denyButtonColor: '#6c757d',
                                    cancelButtonColor: '#d33'
                                }).then((pdfResult) => {
                                    // Mostrar loading para PDF
                                    Swal.fire({
                                        title: 'Generando reporte...',
                                        allowOutsideClick: false,
                                        didOpen: () => Swal.showLoading()
                                    });

                                    // Generar PDF según la elección
                                    const descargar = pdfResult.isConfirmed ? 1 : 0;
                                    const generarSiempre = pdfResult.isConfirmed || pdfResult.isDenied;

                                    if (generarSiempre) {
                                        fetch('{{ route('asesoria.reporte.generar') }}', {
                                                    method: 'POST',
                                                    headers: {
                                                        'Content-Type': 'application/json',
                                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                    },
                                                    body: JSON.stringify({
                                                        sesion_id: data.sesion_id,
                                                        descargar: descargar
                                                    })
                                                })
                                            .then(response => {
                                                if (descargar) {
                                                    // Si es descarga, obtenemos el blob
                                                    return response.blob();
                                                } else {
                                                    return response.json();
                                                }
                                            })
                                            .then(pdfData => {
                                                if (descargar) {
                                                    // Descargar el PDF
                                                    const url = window.URL.createObjectURL(pdfData);
                                                    const a = document.createElement('a');
                                                    a.href = url;
                                                    a.download = 'reporte_asesoria_' + data.sesion_id + '.pdf';
                                                    document.body.appendChild(a);
                                                    a.click();
                                                    a.remove();
                                                    window.URL.revokeObjectURL(url);
                                                }

                                                // 3. Alerta de éxito final
                                                Swal.fire({
                                                    icon: 'success',
                                                    title: '¡Asesoría registrada!',
                                                    text: 'El registro se ha guardado correctamente.',
                                                    confirmButtonColor: '#2c9f49',
                                                    confirmButtonText: 'Aceptar'
                                                }).then(() => {
                                                    // Redirigir según tipo
                                                    if (data.tipo_asesoria === 'individual') {
                                                        window.location.href = '{{ route('expedienteAlumnos', '
                                                        ') }}/' + data.primer_alumno_id;
                                                    } else {
                                                        window.location.href = '{{ route('alumnos') }}';
                                                    }
                                                });
                                            })
                                            .catch(() => {
                                                Swal.fire('Error', 'Hubo un problema al generar el PDF', 'error');
                                            });
                                    } else {
                                        // No generar PDF
                                        Swal.fire({
                                            icon: 'success',
                                            title: '¡Asesoría registrada!',
                                            text: 'El registro se ha guardado correctamente.',
                                            confirmButtonColor: '#2c9f49',
                                            confirmButtonText: 'Aceptar'
                                        }).then(() => {
                                            if (data.tipo_asesoria === 'individual') {
                                                window.location.href = '{{ route('expedienteAlumnos', '
                                                ') }}/' + data.primer_alumno_id;
                                            } else {
                                                window.location.href = '{{ route('alumnos') }}';
                                            }
                                        });
                                    }
                                });
                            } else {
                                Swal.fire('Error', data.message || 'Error al guardar la asesoría', 'error');
                            }
                        })
                        .catch(() => {
                            Swal.fire('Error', 'Error de conexión al guardar la asesoría', 'error');
                        });
                }
            });
        });
    });

    // ===== BUSCADOR =====
    const buscarInput = document.getElementById('buscarAlumno');
    const tabla = document.getElementById('tablaAlumnos');
    const filas = tabla ? tabla.querySelectorAll('tbody tr') : [];

    if (buscarInput) {
        buscarInput.addEventListener('input', function() {
            const term = this.value.toLowerCase().trim();
            filas.forEach(fila => {
                const nombreCelda = fila.querySelector('td:nth-child(2)');
                if (nombreCelda) {
                    const texto = nombreCelda.textContent.toLowerCase();
                    fila.style.display = texto.includes(term) ? '' : 'none';
                }
            });
        });
    }

    // ===== BOTONES "TODOS" Y "NINGUNO" =====
    // (esto ya debería estar en asesoria.js, pero lo ponemos por si acaso)
    document.getElementById('btn-todos')?.addEventListener('click', function() {
        document.querySelectorAll('.seleccionar-alumno').forEach(cb => {
            cb.checked = true;
            cb.closest('tr')?.classList.add('table-success');
        });
    });

    document.getElementById('btn-ninguno')?.addEventListener('click', function() {
        document.querySelectorAll('.seleccionar-alumno').forEach(cb => {
            cb.checked = false;
            cb.closest('tr')?.classList.remove('table-success');
        });
    });
</script>

{{-- Alertas de error por sesión --}}
@if(session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '{{ session('
            error ') }}',
            confirmButtonColor: '#d33',
            confirmButtonText: 'Aceptar'
        });
    });
</script>
@endif

{{-- 3. Alerta de cancelación (al hacer clic en Cancelar) --}}
<script>
    document.querySelector('.btn-secundario')?.addEventListener('click', function(e) {
        Swal.fire({
            icon: 'info',
            title: '¿Cancelar registro?',
            text: 'Los datos no se guardarán. ¿Estás seguro?',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, salir',
            cancelButtonText: 'No, quedarme'
        }).then((result) => {
            if (result.isConfirmed) {
                window.history.back();
            }
        });
    });
</script>

@endsection
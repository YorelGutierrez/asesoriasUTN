@extends('Plantilla')

@section('titulo', 'Agendar Asesoría')

@section('contenido')
<link rel="stylesheet" href="{{ asset('estilos/botones.css') }}">
<link rel="stylesheet" href="{{ asset('estilos/titulos.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>

<style>
    .step-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        transition: transform 0.2s;
    }
    .step-card:hover {
        transform: translateY(-3px);
    }
    .step-number {
        width: 40px;
        height: 40px;
        background: #2c9f49;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1.2rem;
    }
    .docente-card {
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 15px;
        cursor: pointer;
        transition: all 0.2s;
        text-align: center;
    }
    .docente-card:hover, .docente-card.selected {
        border-color: #2c9f49;
        background: #f0fdf4;
    }
    .horario-btn {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 8px 16px;
        margin: 4px;
        background: white;
        transition: all 0.2s;
    }
    .horario-btn:hover, .horario-btn.selected {
        background: #2c9f49;
        color: white;
        border-color: #2c9f49;
    }
    .alumno-checkbox {
        margin-right: 10px;
    }
    .card-alumno {
        border: 1px solid #e9ecef;
        border-radius: 10px;
        padding: 10px;
        margin-bottom: 10px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .card-alumno:hover, .card-alumno.selected {
        background: #f0fdf4;
        border-color: #2c9f49;
    }
    .modalidad-btn {
        flex: 1;
        text-align: center;
        padding: 12px;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .modalidad-btn:hover, .modalidad-btn.selected {
        border-color: #2c9f49;
        background: #f0fdf4;
    }
</style>

<div class="titulo">
    <h1>Programar Nueva Asesoría</h1>
    <p class="text-muted">Completa los siguientes pasos para agendar una asesoría</p>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('agenda.store') }}" method="POST" id="agendarForm">
    @csrf

    <div class="row g-4">
        <!-- ========== PASO 1: SELECCIONAR CARRERA ========== -->
        <div class="col-md-6">
            <div class="step-card p-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="step-number">1</div>
                    <h3 class="h5 fw-semibold mb-0">Seleccionar carrera</h3>
                </div>
                <select class="form-select" name="carrera_id" id="carrera_id" required>
                    <option value="">-- Seleccionar carrera --</option>
                    @foreach($carreras ?? [] as $carrera)
                        <option value="{{ $carrera->id }}">{{ $carrera->nombre }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- ========== PASO 2: TIPO DE ASESORÍA ========== -->
        <div class="col-md-6">
            <div class="step-card p-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="step-number">2</div>
                    <h3 class="h5 fw-semibold mb-0">Tipo de asesoría</h3>
                </div>
                <div class="d-flex gap-3">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="tipo_asesoria" id="individual" value="individual" checked>
                        <label class="form-check-label" for="individual"><i class="bi bi-person"></i> Individual</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="tipo_asesoria" id="grupal" value="grupal">
                        <label class="form-check-label" for="grupal"><i class="bi bi-people"></i> Grupal</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========== PASO 3: MATERIA ========== -->
        <div class="col-md-6">
            <div class="step-card p-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="step-number">3</div>
                    <h3 class="h5 fw-semibold mb-0">Asignatura</h3>
                </div>
                <select class="form-select" name="materia_id" id="materia_id" required>
                    <option value="">-- Seleccionar asignatura --</option>
                    @foreach($materias ?? [] as $materia)
                        <option value="{{ $materia->id }}">{{ $materia->nombre }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- ========== PASO 4: TEMA ========== -->
        <div class="col-md-6">
            <div class="step-card p-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="step-number">4</div>
                    <h3 class="h5 fw-semibold mb-0">Tema / preguntas a tratar</h3>
                </div>
                <textarea class="form-control" name="tema" rows="3" placeholder="Ej: Derivadas, Álgebra, Programación..." required></textarea>
            </div>
        </div>

        <!-- ========== PASO 5: FECHA Y HORA ========== -->
        <div class="col-md-6">
            <div class="step-card p-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="step-number">5</div>
                    <h3 class="h5 fw-semibold mb-0">Fecha y hora</h3>
                </div>
                <div class="mb-3">
                    <label class="fw-semibold mb-2">Fecha</label>
                    <input type="date" name="fecha" id="fecha" class="form-control" required>
                </div>
                <div class="row">
                    <div class="col-6">
                        <label class="fw-semibold mb-2">Hora de inicio</label>
                        <input type="time" name="hora_inicio" id="hora_inicio" class="form-control" required>
                    </div>
                    <div class="col-6">
                        <label class="fw-semibold mb-2">Hora de fin</label>
                        <input type="time" name="hora_fin" id="hora_fin" class="form-control" required>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========== PASO 6: SELECCIONAR ALUMNOS ========== -->
        <div class="col-md-6">
            <div class="step-card p-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="step-number">6</div>
                    <h3 class="h5 fw-semibold mb-0">Lista de alumnos</h3>
                </div>
                
                <div class="mb-3" id="individualSection">
                    <label class="fw-semibold mb-2">Seleccionar alumno (Individual)</label>
                    <select class="form-select" name="alumnos[]" id="alumno_individual">
                        <option value="">Selecciona un alumno</option>
                        @foreach($alumnos ?? [] as $alumno)
                            <option value="{{ $alumno->id }}">{{ $alumno->user->nombres ?? '' }} {{ $alumno->user->apellido_paterno ?? '' }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3 d-none" id="grupalSection">
                    <label class="fw-semibold mb-2">Seleccionar alumnos (Grupal)</label>
                    <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                        @foreach($alumnos ?? [] as $alumno)
                            <div class="form-check">
                                <input class="form-check-input alumno-checkbox" type="checkbox" name="alumnos[]" value="{{ $alumno->id }}" id="alumno_{{ $alumno->id }}">
                                <label class="form-check-label" for="alumno_{{ $alumno->id }}">
                                    {{ $alumno->user->nombres ?? '' }} {{ $alumno->user->apellido_paterno ?? '' }} - Grupo: {{ $alumno->grupo->nombre ?? 'N/A' }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- ========== PASO 7: MODALIDAD ========== -->
        <div class="col-md-6">
            <div class="step-card p-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="step-number">7</div>
                    <h3 class="h5 fw-semibold mb-0">Modalidad</h3>
                </div>
                <div class="d-flex gap-3">
                    <div class="modalidad-btn" data-modalidad="presencial">
                        <i class="bi bi-building fs-4"></i>
                        <div>Presencial</div>
                    </div>
                    <div class="modalidad-btn" data-modalidad="virtual">
                        <i class="bi bi-camera-video-fill fs-4"></i>
                        <div>En línea</div>
                    </div>
                </div>
                <input type="hidden" name="modalidad" id="modalidadSeleccionada" value="presencial">
            </div>
        </div>
    </div>

    <!-- Botones de acción -->
    <div class="d-flex justify-content-between mt-4">
        <a href="@if(auth()->user()->rol === 'admin') {{ route('admin.dashboard') }} @else {{ route('docente.dashboard') }} @endif" class="btn-principal bg-secondary">
            <i class="bi bi-arrow-left"></i> Cancelar
        </a>
        <button type="submit" class="btn-principal">
            <i class="bi bi-calendar-check-fill me-2"></i> Agendar asesoría
        </button>
    </div>
</form>

<script>
    // Toggle individual/grupal
    const individualRadio = document.getElementById('individual');
    const grupalRadio = document.getElementById('grupal');
    const individualSection = document.getElementById('individualSection');
    const grupalSection = document.getElementById('grupalSection');
    const alumnoIndividual = document.getElementById('alumno_individual');

    individualRadio.addEventListener('change', function() {
        if(this.checked) {
            individualSection.classList.remove('d-none');
            grupalSection.classList.add('d-none');
            // Habilitar el select individual
            if(alumnoIndividual) alumnoIndividual.disabled = false;
            // Deshabilitar checkboxes grupales
            document.querySelectorAll('.alumno-checkbox').forEach(cb => cb.disabled = true);
        }
    });

    grupalRadio.addEventListener('change', function() {
        if(this.checked) {
            individualSection.classList.add('d-none');
            grupalSection.classList.remove('d-none');
            // Deshabilitar el select individual
            if(alumnoIndividual) alumnoIndividual.disabled = true;
            // Habilitar checkboxes grupales
            document.querySelectorAll('.alumno-checkbox').forEach(cb => cb.disabled = false);
        }
    });

    // Inicializar estado
    if(alumnoIndividual) alumnoIndividual.disabled = false;
    document.querySelectorAll('.alumno-checkbox').forEach(cb => cb.disabled = true);

    // Selección de modalidad
    document.querySelectorAll('.modalidad-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.modalidad-btn').forEach(b => b.classList.remove('selected'));
            this.classList.add('selected');
            document.getElementById('modalidadSeleccionada').value = this.dataset.modalidad;
        });
    });

    // Validación antes de enviar
    document.getElementById('submitBtn')?.addEventListener('click', function(e) {
        const fecha = document.getElementById('fecha')?.value;
        const horaInicio = document.getElementById('hora_inicio')?.value;
        const horaFin = document.getElementById('hora_fin')?.value;
        
        if(!fecha) {
            e.preventDefault();
            Swal.fire('Error', 'Selecciona una fecha', 'error');
            return;
        }
        if(!horaInicio) {
            e.preventDefault();
            Swal.fire('Error', 'Selecciona hora de inicio', 'error');
            return;
        }
        if(!horaFin) {
            e.preventDefault();
            Swal.fire('Error', 'Selecciona hora de fin', 'error');
            return;
        }
    });
</script>
@endsection
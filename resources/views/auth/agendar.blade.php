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
    .docente-avatar {
        width: 60px;
        height: 60px;
        background: #e9ecef;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 10px;
        font-size: 24px;
        font-weight: bold;
        color: #2c9f49;
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
    .tag-input {
        display: inline-block;
        background: #f8f9fa;
        border-radius: 20px;
        padding: 8px 15px;
        margin: 4px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .tag-input:hover, .tag-input.selected {
        background: #2c9f49;
        color: white;
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
    .calendar-container {
        background: white;
        border-radius: 16px;
        padding: 15px;
    }
    .flatpickr-day.selected {
        background: #2c9f49 !important;
        border-color: #2c9f49 !important;
    }
</style>

<div class="titulo">
    <h1>Programar Nueva Asesoría</h1>
    <p class="text-muted">Completa los siguientes pasos para agendar una asesoría</p>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert" style="background: #d4edda; border-color: #c3e6cb; color: #155724; border-radius: 12px;">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<form action="{{ route('agenda.store') }}" method="POST" id="agendarForm">
    @csrf

    <div class="row g-4">
        <!-- ========== PASO 1: SELECCIONAR ASESOR ========== -->
        <div class="col-12">
            <div class="step-card p-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="step-number">1</div>
                    <h3 class="h5 fw-semibold mb-0">Seleccionar asesor</h3>
                </div>
                
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" id="searchDocente" class="form-control border-start-0" placeholder="Buscar por nombre o materia...">
                    </div>
                </div>

                <div class="row g-3" id="docentesList">
                    <!-- Docentes precargados (esto debería venir de BD) -->
                    @php
                        $docentes = [
                            ['nombre' => 'Dr. García', 'materia' => 'Matemáticas II', 'inicial' => 'G'],
                            ['nombre' => 'Dra. Rodríguez', 'materia' => 'Historia', 'inicial' => 'R'],
                            ['nombre' => 'Ing. López', 'materia' => 'Programación', 'inicial' => 'L'],
                            ['nombre' => 'Ing. Vípez', 'materia' => 'Matemáticas II', 'inicial' => 'V'],
                            ['nombre' => 'Dr. García', 'materia' => 'Matemáticas I', 'inicial' => 'G'],
                            ['nombre' => 'Dra. Cinen', 'materia' => 'Programación', 'inicial' => 'C'],
                            ['nombre' => 'Ing. López', 'materia' => 'Programación', 'inicial' => 'L'],
                        ];
                    @endphp
                    @foreach($docentes as $index => $docente)
                    <div class="col-md-3 col-sm-6">
                        <div class="docente-card" data-nombre="{{ $docente['nombre'] }}" data-materia="{{ $docente['materia'] }}">
                            <div class="docente-avatar">{{ substr($docente['nombre'], -3, 1) }}</div>
                            <div class="fw-semibold">{{ $docente['nombre'] }}</div>
                            <small class="text-muted">{{ $docente['materia'] }}</small>
                            <div class="mt-2">
                                <span class="badge bg-light text-dark">{{ $docente['inicial'] }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <input type="hidden" name="docente_id" id="docenteSeleccionado" required>
            </div>
        </div>

        <!-- ========== PASO 2: FECHA Y HORA ========== -->
        <div class="col-md-6">
            <div class="step-card p-4 h-100">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="step-number">2</div>
                    <h3 class="h5 fw-semibold mb-0">Elegir fecha y hora</h3>
                </div>

                <div class="calendar-container mb-3">
                    <input type="date" id="fechaInput" name="fecha" class="form-control" style="display: none;">
                    <div id="calendar"></div>
                </div>

                <div class="mt-3">
                    <label class="fw-semibold mb-2">Horarios disponibles</label>
                    <div class="d-flex flex-wrap">
                        @php
                            $horarios = ['10:00 AM', '11:30 AM', '3:00 PM', '4:30 PM'];
                        @endphp
                        @foreach($horarios as $hora)
                        <button type="button" class="horario-btn" data-hora="{{ $hora }}">{{ $hora }}</button>
                        @endforeach
                    </div>
                    <input type="hidden" name="hora" id="horaSeleccionada" required>
                </div>
            </div>
        </div>

        <!-- ========== PASO 3: DETALLES DE LA SESIÓN ========== -->
        <div class="col-md-6">
            <div class="step-card p-4 h-100">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="step-number">3</div>
                    <h3 class="h5 fw-semibold mb-0">Detalles de la sesión</h3>
                </div>

                <div class="mb-3">
                    <label class="fw-semibold mb-2">Tipo de asesoría</label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tipo" id="individual" value="individual" checked>
                            <label class="form-check-label" for="individual"><i class="bi bi-person"></i> Individual</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tipo" id="grupal" value="grupal">
                            <label class="form-check-label" for="grupal"><i class="bi bi-people"></i> Grupal</label>
                        </div>
                    </div>
                </div>

                <div class="mb-3" id="individualSection">
                    <label class="fw-semibold mb-2">Seleccionar alumno</label>
                    <select class="form-select" name="alumno_id">
                        <option value="">Selecciona un alumno</option>
                        @foreach($alumnos ?? [] as $alumno)
                            <option value="{{ $alumno->id }}">{{ $alumno->nombres }} {{ $alumno->apellido_paterno }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3 d-none" id="grupalSection">
                    <label class="fw-semibold mb-2">Seleccionar grupo</label>
                    <select class="form-select" name="grupo_id">
                        <option value="">Selecciona un grupo</option>
                        @foreach($grupos ?? [] as $grupo)
                            <option value="{{ $grupo->id }}">{{ $grupo->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="fw-semibold mb-2">Materia</label>
                    <select class="form-select" name="materia" required>
                        <option value="">Selecciona una materia</option>
                        <option>Matemáticas para ingeniería II</option>
                        <option>Estructura de base de datos</option>
                        <option>Programación Web</option>
                        <option>Inglés I</option>
                        <option>Redes</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="fw-semibold mb-2">Tema / preguntas a tratar</label>
                    <div class="d-flex flex-wrap mb-2" id="preguntasContainer">
                        @php
                            $preguntasSugeridas = ['Consultas SQL', 'Derivadas', 'Flexbox', 'Modelo relacional', 'JavaScript'];
                        @endphp
                        @foreach($preguntasSugeridas as $pregunta)
                        <span class="tag-input" data-pregunta="{{ $pregunta }}">{{ $pregunta }}</span>
                        @endforeach
                    </div>
                    <div class="input-group">
                        <input type="text" id="nuevaPregunta" class="form-control" placeholder="Escribe una pregunta...">
                        <button type="button" id="agregarPregunta" class="btn btn-outline-secondary">Agregar</button>
                    </div>
                    <input type="hidden" name="preguntas" id="preguntasFinal">
                </div>

                <div class="mb-3">
                    <label class="fw-semibold mb-2">Modalidad</label>
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
    </div>

    <!-- Botones de acción -->
    <div class="d-flex justify-content-between mt-4">
        <a href="@if(auth()->user()->rol === 'admin') {{ route('admin.dashboard') }} @else {{ route('docente.dashboard') }} @endif" class="btn-principal bg-secondary">
            <i class="bi bi-arrow-left"></i> Cancelar
        </a>
        <button type="submit" class="btn-principal" id="submitBtn">
            <i class="bi bi-calendar-check-fill me-2"></i> Agendar asesoría
        </button>
    </div>
</form>

<script>
    // Inicializar Flatpickr para el calendario
    flatpickr("#calendar", {
        inline: true,
        locale: "es",
        minDate: "today",
        dateFormat: "Y-m-d",
        onChange: function(selectedDates, dateStr) {
            document.getElementById('fechaInput').value = dateStr;
        }
    });

    // Selección de docente
    let docenteSeleccionado = null;
    document.querySelectorAll('.docente-card').forEach(card => {
        card.addEventListener('click', function() {
            document.querySelectorAll('.docente-card').forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
            docenteSeleccionado = this.querySelector('.fw-semibold').innerText;
            document.getElementById('docenteSeleccionado').value = docenteSeleccionado;
        });
    });

    // Búsqueda de docente
    document.getElementById('searchDocente').addEventListener('input', function(e) {
        const term = e.target.value.toLowerCase();
        document.querySelectorAll('.docente-card').forEach(card => {
            const nombre = card.dataset.nombre.toLowerCase();
            const materia = card.dataset.materia.toLowerCase();
            if (nombre.includes(term) || materia.includes(term)) {
                card.closest('.col-md-3').style.display = '';
            } else {
                card.closest('.col-md-3').style.display = 'none';
            }
        });
    });

    // Selección de horario
    let horaSeleccionada = null;
    document.querySelectorAll('.horario-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.horario-btn').forEach(b => b.classList.remove('selected'));
            this.classList.add('selected');
            horaSeleccionada = this.dataset.hora;
            document.getElementById('horaSeleccionada').value = horaSeleccionada;
        });
    });

    // Toggle individual/grupal
    const individualRadio = document.getElementById('individual');
    const grupalRadio = document.getElementById('grupal');
    const individualSection = document.getElementById('individualSection');
    const grupalSection = document.getElementById('grupalSection');

    individualRadio.addEventListener('change', function() {
        if(this.checked) {
            individualSection.classList.remove('d-none');
            grupalSection.classList.add('d-none');
        }
    });

    grupalRadio.addEventListener('change', function() {
        if(this.checked) {
            individualSection.classList.add('d-none');
            grupalSection.classList.remove('d-none');
        }
    });

    // Preguntas dinámicas
    let preguntas = [];
    const preguntasContainer = document.getElementById('preguntasContainer');
    const preguntasFinal = document.getElementById('preguntasFinal');

    function actualizarPreguntas() {
        preguntasFinal.value = preguntas.join(',');
    }

    document.querySelectorAll('.tag-input').forEach(tag => {
        tag.addEventListener('click', function() {
            const pregunta = this.dataset.pregunta;
            if(this.classList.contains('selected')) {
                this.classList.remove('selected');
                preguntas = preguntas.filter(p => p !== pregunta);
            } else {
                this.classList.add('selected');
                preguntas.push(pregunta);
            }
            actualizarPreguntas();
        });
    });

    document.getElementById('agregarPregunta').addEventListener('click', function() {
        const input = document.getElementById('nuevaPregunta');
        const nuevaPregunta = input.value.trim();
        if(nuevaPregunta && !preguntas.includes(nuevaPregunta)) {
            preguntas.push(nuevaPregunta);
            const newTag = document.createElement('span');
            newTag.className = 'tag-input selected';
            newTag.setAttribute('data-pregunta', nuevaPregunta);
            newTag.innerText = nuevaPregunta;
            newTag.addEventListener('click', function() {
                this.remove();
                preguntas = preguntas.filter(p => p !== nuevaPregunta);
                actualizarPreguntas();
            });
            preguntasContainer.appendChild(newTag);
            input.value = '';
            actualizarPreguntas();
        }
    });

    // Selección de modalidad
    document.querySelectorAll('.modalidad-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.modalidad-btn').forEach(b => b.classList.remove('selected'));
            this.classList.add('selected');
            document.getElementById('modalidadSeleccionada').value = this.dataset.modalidad;
        });
    });

    // Validación antes de enviar
    document.getElementById('submitBtn').addEventListener('click', function(e) {
        if(!docenteSeleccionado) {
            e.preventDefault();
            Swal.fire('Error', 'Selecciona un docente', 'error');
            return;
        }
        if(!document.getElementById('fechaInput').value) {
            e.preventDefault();
            Swal.fire('Error', 'Selecciona una fecha', 'error');
            return;
        }
        if(!horaSeleccionada) {
            e.preventDefault();
            Swal.fire('Error', 'Selecciona una hora', 'error');
            return;
        }
    });
</script>
@endsection
@extends('Plantilla')

@section('contenido')
<link rel="stylesheet" href="{{ asset('estilos/botones.css') }}">
<link rel="stylesheet" href="{{ asset('estilos/titulos.css') }}">
<link rel="stylesheet" href="{{ asset('estilos/agendar.css') }}">

<div class="schedule-container">
    <div class="titulo">
        <h1>Programación de Asesorías</h1>
    </div>

    <div class="steps-container">
        <div class="step active" id="step1-indicator">
            <div class="step-number">1</div>
            <span>Seleccionar {{ Auth::user()->rol == 'alumno' ? 'Docente' : (Auth::user()->rol == 'docente' ? 'Alumno' : 'Alumno') }}</span>
        </div>
        <div class="step" id="step2-indicator">
            <div class="step-number">2</div>
            <span>Elegir Fecha y Hora</span>
        </div>
        <div class="step" id="step3-indicator">
            <div class="step-number">3</div>
            <span>Detalles de la Sesión</span>
        </div>
    </div>

    <form method="POST" action="{{ route('asesoria.store') }}" id="asesoriaForm">
        @csrf

        <div class="main-grid">
            <!-- Columna Izquierda -->
            <div>
                <div class="card-moderno" id="step1-card">
                    <div class="card-title">
                        <span>Seleccionar {{ Auth::user()->rol == 'alumno' ? 'Docente' : (Auth::user()->rol == 'docente' ? 'Alumno' : 'Alumno') }}</span>
                    </div>

                    <div class="search-box">
                        <input type="text" id="searchAsesor" placeholder="Buscar por nombre...">
                    </div>

                    <div class="asesores-list" id="asesoresList">
                        @if(Auth::user()->rol == 'alumno')
                        {{-- Si es ALUMNO, mostrar DOCENTES --}}
                        @if(isset($docentes) && $docentes->count() > 0)
                        @foreach($docentes as $docente)
                        <div class="asesor-item" data-id="{{ $docente->id }}" data-nombre="{{ $docente->user->nombres }} {{ $docente->user->apellido_paterno }}" data-tipo="docente">
                            <div class="asesor-info">
                                <h4>{{ $docente->user->nombres }} {{ $docente->user->apellido_paterno }}</h4>
                                <p>{{ $docente->carrera ? $docente->carrera->nombre : 'Docente' }}</p>
                            </div>
                            <div class="badge-disponible">Disponible</div>
                        </div>
                        @endforeach
                        @else
                        <div class="alert-info">No hay docentes registrados. Contacta al administrador.</div>
                        @endif
                        @elseif(Auth::user()->rol == 'docente')
                        {{-- Si es DOCENTE, mostrar ALUMNOS --}}
                        @if(isset($alumnos) && $alumnos->count() > 0)
                        @foreach($alumnos as $alumno)
                        <div class="asesor-item" data-id="{{ $alumno->id }}" data-nombre="{{ $alumno->user->nombres }} {{ $alumno->user->apellido_paterno }}" data-tipo="alumno">
                            <div class="asesor-info">
                                <h4>{{ $alumno->user->nombres }} {{ $alumno->user->apellido_paterno }}</h4>
                                <p>{{ $alumno->carrera ? $alumno->carrera->nombre : 'Alumno' }} - {{ $alumno->matricula }}</p>
                            </div>
                            <div class="badge-disponible">Disponible</div>
                        </div>
                        @endforeach
                        @else
                        <div class="alert-info">No hay alumnos registrados. Contacta al administrador.</div>
                        @endif
                        @else
                        {{-- Si es ADMIN, mostrar ALUMNOS --}}
                        @if(isset($alumnos) && $alumnos->count() > 0)
                        @foreach($alumnos as $alumno)
                        <div class="asesor-item" data-id="{{ $alumno->id }}" data-nombre="{{ $alumno->user->nombres }} {{ $alumno->user->apellido_paterno }}" data-tipo="alumno">
                            <div class="asesor-info">
                                <h4>{{ $alumno->user->nombres }} {{ $alumno->user->apellido_paterno }}</h4>
                                <p>{{ $alumno->carrera ? $alumno->carrera->nombre : 'Alumno' }} - {{ $alumno->matricula }}</p>
                            </div>
                            <div class="badge-disponible">Disponible</div>
                        </div>
                        @endforeach
                        @else
                        <div class="alert-info">No hay alumnos registrados. Contacta al administrador.</div>
                        @endif
                        @endif
                    </div>

                    <input type="hidden" name="destinatario_id" id="destinatario_id" required>
                    <input type="hidden" name="tipo_destinatario" id="tipo_destinatario" required>
                    <div class="error-message" id="docenteError"></div>
                </div>

                <div class="card-moderno" id="step3-card" style="margin-top: 20px;">
                    <div class="card-title">
                        <span>Detalles de la Sesión</span>
                    </div>

                    <div class="alert-info">
                        <strong>Consejo:</strong> Prepara las preguntas que quieras resolver durante la asesoría
                    </div>

                    <label style="font-weight: 500; margin-bottom: 8px; display: block;">¿Qué quieres aprender? *</label>
                    <textarea name="tema" class="topic-input" rows="3" placeholder="Ej: Me gustaría repasar derivadas, necesito ayuda con programación, etc." required></textarea>

                    <label style="font-weight: 500; margin-bottom: 8px; display: block;">Objetivo específico de la asesoría</label>
                    <textarea name="pregunta_objetivo" class="topic-input" rows="2" placeholder="¿Qué esperas lograr al final de esta asesoría?"></textarea>

                    <div class="questions-list">
                        <div class="question-item">
                            <input type="checkbox" name="pregunta_conocimiento" id="pregunta_conocimiento" value="1">
                            <label for="pregunta_conocimiento">¿Ya tienes conocimiento previo sobre el tema?</label>
                        </div>
                        <div class="question-item">
                            <input type="checkbox" name="pregunta_material" id="pregunta_material" value="1">
                            <label for="pregunta_material">¿Necesitas material de apoyo o ejercicios?</label>
                        </div>
                        <div class="question-item">
                            <input type="checkbox" name="pregunta_ejercicios" id="pregunta_ejercicios" value="1">
                            <label for="pregunta_ejercicios">¿Tienes ejercicios específicos que quieras resolver?</label>
                        </div>
                    </div>

                    <label style="font-weight: 500; margin-bottom: 8px; display: block;">Modalidad *</label>
                    <select name="modalidad" class="modalidad-select" required>
                        <option value="presencial">Presencial</option>
                        <option value="en_linea">En línea</option>
                    </select>
                </div>
            </div>

            <!-- Columna Derecha -->
            <div>
                <div class="card-moderno" id="step2-card">
                    <div class="card-title">
                        <span>Elegir Fecha y Hora</span>
                    </div>

                    <div class="calendar-header">
                        <span style="font-weight: 600;" id="currentMonth"></span>
                        <div class="calendar-nav">
                            <button type="button" id="prevMonth">◀</button>
                            <button type="button" id="nextMonth">▶</button>
                        </div>
                    </div>

                    <div class="calendar-weekdays">
                        <div>L</div>
                        <div>M</div>
                        <div>M</div>
                        <div>J</div>
                        <div>V</div>
                        <div>S</div>
                        <div>D</div>
                    </div>
                    <div class="calendar-days" id="calendarDays"></div>

                    <input type="hidden" name="fecha" id="fechaSeleccionada" required>
                    <div class="error-message" id="fechaError"></div>

                    <div class="time-slots" id="timeSlots">
                        <div class="time-slot" data-time="09:00">09:00</div>
                        <div class="time-slot" data-time="10:00">10:00</div>
                        <div class="time-slot" data-time="11:00">11:00</div>
                        <div class="time-slot" data-time="12:00">12:00</div>
                        <div class="time-slot" data-time="14:00">14:00</div>
                        <div class="time-slot" data-time="15:00">15:00</div>
                        <div class="time-slot" data-time="16:00">16:00</div>
                        <div class="time-slot" data-time="17:00">17:00</div>
                    </div>

                    <input type="hidden" name="hora_inicio" id="horaSeleccionada" required>
                    <div class="error-message" id="horaError"></div>

                    <div class="action-buttons">
                        <button type="button" class="btn-cancelar" onclick="window.history.back();">Cancelar</button>
                        <button type="submit" class="btn-agendar">Agendar Sesión</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
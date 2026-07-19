@extends('Plantilla')

@section('estilos')
<link rel="stylesheet" href="{{ asset('estilos/botones.css') }}">
<link rel="stylesheet" href="{{ asset('estilos/titulos.css') }}">
<link rel="stylesheet" href="{{ asset('estilos/agendar.css') }}">
<style>
    /* Estilos para scroll de la lista */
    .asesores-list {
        max-height: 220px;
        overflow-y: auto;
    }

    .asesores-list::-webkit-scrollbar {
        width: 6px;
    }

    .asesores-list::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 10px;
    }

    .asesores-list::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }

    /* Calendario compacto */
    .calendar-wrapper {
        max-width: 100%;
    }

    .calendar-day {
        aspect-ratio: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 12px;
        padding: 1px;
    }

    .calendar-day:hover {
        background: #ecfdf5;
    }

    .calendar-day.selected {
        background: #10b981;
        color: white;
    }

    .calendar-day.past {
        opacity: 0.3;
        cursor: not-allowed;
    }

    .calendar-day.weekend {
        color: #ef4444;
    }

    .calendar-header {
        margin-bottom: 4px;
    }

    .calendar-header span {
        font-size: 13px;
    }

    .calendar-nav button {
        padding: 2px 8px;
        font-size: 12px;
    }

    .calendar-weekdays div {
        font-size: 9px;
    }

    /* Select de hora */
    .hora-select {
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        padding: 8px 12px;
        font-size: 14px;
        width: 100%;
        background: white;
    }

    .hora-select:focus {
        border-color: #10b981;
        outline: none;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    }

    /* ============================================================ */
    /* ESTILOS PARA INDICADORES DE ASESORÍAS                       */
    /* ============================================================ */
    .badge-asesorias {
        font-size: 11px;
        padding: 4px 10px;
        border-radius: 20px;
        font-weight: 600;
        background: #f3f4f6;
        color: #6b7280;
    }
    .badge-asesorias.bg-naranja {
        background: #fef3c7;
        color: #92400e;
    }
    .badge-asesorias.bg-rojo {
        background: #fecaca;
        color: #991b1b;
    }

    .indicador-color {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 6px;
        flex-shrink: 0;
        border: 1px solid #e5e7eb;
    }
    .indicador-color.naranja {
        background: #f59e0b;
        border-color: #f59e0b;
    }
    .indicador-color.rojo {
        background: #ef4444;
        border-color: #ef4444;
    }
    .indicador-color.blanco {
        background: #ffffff;
        border-color: #d1d5db;
    }

    .asesor-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 14px;
        border-radius: 8px;
        margin-bottom: 4px;
        cursor: pointer;
        transition: all 0.2s;
        border: 2px solid transparent;
    }

    .asesor-item:hover {
        background: #f3f4f6;
    }

    .asesor-item.seleccionado {
        border-color: #10b981;
        background: #ecfdf5;
    }

    .asesor-item .info-asesorias {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
    }

    .asesor-item .total-asesorias {
        font-weight: 600;
        font-size: 13px;
        min-width: 30px;
        text-align: center;
    }

    .asesor-item .estado-texto {
        font-size: 11px;
        font-weight: 500;
    }
</style>
@endsection

@section('contenido')
<div class="schedule-container">
    <div class="titulo">
        <h1>{{ Auth::user()->rol == 'alumno' ? 'Solicitar Asesoría' : 'Programación de Asesorías' }}</h1>
    </div>

    <!-- Instrucciones -->
    <div class="alert alert-info py-2 mb-3" style="background: #ecfdf5; border: 1px solid #d1fae5; border-radius: 10px;">
        <i class="bi bi-info-circle me-2"></i>
        <strong>Instrucciones:</strong>
        1. Selecciona la persona con quien deseas la asesoría.
        2. Elige una fecha y hora disponible.
        3. Completa los detalles y envía la solicitud.
    </div>

    <form method="POST" action="{{ route('agenda.store') }}" id="asesoriaForm">
        @csrf

        <div class="main-grid">
            <!-- Columna Izquierda -->
            <div>
                <!-- Paso 1: Seleccionar persona -->
                <div class="card-moderno" id="step1-card">
                    <div class="card-title">
                        <span>Seleccionar {{ Auth::user()->rol == 'alumno' ? 'Docente' : (Auth::user()->rol == 'docente' ? 'Alumno' : 'Alumno') }}</span>
                    </div>

                    <!-- Buscador -->
                    <div class="search-box">
                        <input type="text" id="searchAsesor" placeholder="Buscar por nombre..." style="width: 100%; padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px;">
                    </div>

                    <!-- Lista de personas -->
                    <div class="asesores-list" id="asesoresList">
                        @if(Auth::user()->rol == 'alumno')
                        {{-- Alumno: ver docentes --}}
                        @if(isset($docentes) && $docentes->count() > 0)
                        @foreach($docentes as $docente)
                        <div class="asesor-item" data-id="{{ $docente->user_id }}" data-nombre="{{ strtolower($docente->user->nombres . ' ' . $docente->user->apellido_paterno) }}" data-tipo="docente">
                            <div class="asesor-info">
                                <h4>{{ $docente->user->nombres }} {{ $docente->user->apellido_paterno }}</h4>
                                <p style="font-size: 13px; color: #6b7280;">{{ $docente->carrera ? $docente->carrera->nombre : 'Docente' }}</p>
                            </div>
                            <div class="badge-disponible">Disponible</div>
                        </div>
                        @endforeach
                        @else
                        <div class="alert-info">No hay docentes registrados. Contacta al administrador.</div>
                        @endif

                        @else
                        {{-- Docente/Admin: ver alumnos con indicador de color --}}
                        @if(!session('grupo_activo_id') && Auth::user()->rol == 'docente')
                        <div class="alert-info">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            Debes <a href="{{ route('grupos') }}">seleccionar un grupo</a> primero.
                        </div>
                        @else
                        @if(isset($alumnos) && $alumnos->count() > 0)
                        @foreach($alumnos as $alumno)
                        @php
                            $info = $coloresAlumnos[$alumno->id] ?? ['color' => 'blanco', 'total' => 0, 'estado' => 'Normal'];
                            
                            $colorClase = 'blanco';
                            $badgeClase = '';
                            $estadoTexto = 'Normal';
                            
                            if ($info['color'] === 'danger') {
                                $colorClase = 'rojo';
                                $badgeClase = 'bg-rojo';
                                $estadoTexto = '⚠️ Crítico';
                            } elseif ($info['color'] === 'warning') {
                                $colorClase = 'naranja';
                                $badgeClase = 'bg-naranja';
                                $estadoTexto = '⚡ Alerta';
                            }
                        @endphp
                        <div class="asesor-item" data-id="{{ $alumno->user_id }}" data-nombre="{{ strtolower($alumno->user->apellido_paterno . ' ' . $alumno->user->nombres) }}" data-tipo="alumno">
                            <div class="asesor-info">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="indicador-color {{ $colorClase }}"></span>
                                    <div>
                                        <h4>{{ $alumno->user->nombres }} {{ $alumno->user->apellido_paterno }}</h4>
                                        <p style="font-size: 13px; color: #6b7280;">{{ $alumno->matricula }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="info-asesorias">
                                <span class="badge-asesorias {{ $badgeClase }}">
                                    {{ $info['total'] }} asesoría(s)
                                </span>
                                @if($info['color'] === 'danger' || $info['color'] === 'warning')
                                <span class="estado-texto" style="color: {{ $info['color'] === 'danger' ? '#991b1b' : '#92400e' }}">
                                    {{ $estadoTexto }}
                                </span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                        @else
                        <div class="alert-info">No hay alumnos en este grupo.</div>
                        @endif
                        @endif
                        @endif
                    </div>

                    <input type="hidden" name="destinatario_id" id="destinatario_id" value="">
                    <input type="hidden" name="tipo_destinatario" id="tipo_destinatario" value="">
                    <div class="error-message" id="docenteError"></div>
                </div>

                <!-- Paso 3: Detalles de la sesión -->
                <div class="card-moderno" id="step3-card">
                    <div class="card-title">
                        <span>Detalles de la Sesión</span>
                    </div>

                    <div class="alert-info">
                        <strong>Consejo:</strong> Prepara las preguntas que quieras resolver durante la asesoría
                    </div>

                    @if(Auth::user()->rol == 'alumno')
                    <label style="font-weight: 500; margin-bottom: 6px; display: block;">¿Qué quieres aprender? <span class="text-danger">*</span></label>
                    <textarea name="tema" class="topic-input" rows="3" placeholder="Ej: Me gustaría repasar derivadas, necesito ayuda con programación..." required></textarea>

                    <div class="questions-list">
                        <div class="question-item">
                            <input type="checkbox" name="pregunta_conocimiento" id="pregunta_conocimiento" value="1">
                            <label for="pregunta_conocimiento" style="font-size: 13px;">¿Ya tienes conocimiento previo sobre el tema?</label>
                        </div>
                        <div class="question-item">
                            <input type="checkbox" name="pregunta_material" id="pregunta_material" value="1">
                            <label for="pregunta_material" style="font-size: 13px;">¿Necesitas material de apoyo o ejercicios?</label>
                        </div>
                        <div class="question-item">
                            <input type="checkbox" name="pregunta_ejercicios" id="pregunta_ejercicios" value="1">
                            <label for="pregunta_ejercicios" style="font-size: 13px;">¿Tienes ejercicios específicos que quieras resolver?</label>
                        </div>
                    </div>
                    @else
                    <label style="font-weight: 500; margin-bottom: 6px; display: block;">Tema de la asesoría <span class="text-danger">*</span></label>
                    <textarea name="tema" class="topic-input" rows="3" placeholder="Ej: Derivadas, Álgebra, Programación..." required></textarea>
                    @endif

                    <label style="font-weight: 500; margin-bottom: 6px; display: block; margin-top: 12px;">Objetivo específico de la asesoría</label>
                    <textarea name="pregunta_objetivo" class="topic-input" rows="2" placeholder="¿Qué esperas lograr al final de esta asesoría?"></textarea>

                    <label style="font-weight: 500; margin-bottom: 6px; display: block; margin-top: 12px;">Modalidad <span class="text-danger">*</span></label>
                    <select name="modalidad" class="modalidad-select" required>
                        <option value="presencial">Presencial</option>
                        <option value="virtual">Virtual</option>
                    </select>
                </div>
            </div>

            <!-- Columna Derecha -->
            <div>
                <!-- Paso 2: Fecha y Hora -->
                <div class="card-moderno" id="step2-card">
                    <div class="card-title">
                        <span>Elegir Fecha y Hora</span>
                    </div>

                    <!-- Calendario compacto -->
                    <div class="mb-3">
                        <div class="calendar-wrapper">
                            <div class="calendar-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                                <span style="font-weight: 600; font-size: 13px;" id="currentMonth"></span>
                                <div class="calendar-nav" style="display: flex; gap: 4px;">
                                    <button type="button" id="prevMonth" style="background: #f3f4f6; border: none; padding: 2px 8px; border-radius: 4px; cursor: pointer; font-size: 12px;">◀</button>
                                    <button type="button" id="nextMonth" style="background: #f3f4f6; border: none; padding: 2px 8px; border-radius: 4px; cursor: pointer; font-size: 12px;">▶</button>
                                </div>
                            </div>
                            <div class="calendar-weekdays" style="display: grid; grid-template-columns: repeat(7, 1fr); text-align: center; font-size: 9px; font-weight: 600; color: #6b7280; margin-bottom: 2px;">
                                <div>L</div>
                                <div>M</div>
                                <div>M</div>
                                <div>J</div>
                                <div>V</div>
                                <div>S</div>
                                <div>D</div>
                            </div>
                            <div class="calendar-days" id="calendarDays" style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 2px;"></div>
                        </div>
                        <input type="hidden" name="fecha" id="fechaSeleccionada" value="">
                        <div class="error-message" id="fechaError"></div>
                    </div>

                    <!-- Hora (select) -->
                    <div class="mb-3">
                        <label style="font-weight: 500; display: block; margin-bottom: 4px;">Hora</label>
                        <input type="time" name="hora_inicio" id="horaSeleccionada" class="hora-select" required>
                        <div class="error-message" id="horaError"></div>
                    </div>

                    <!-- Botón de acción -->
                    <div class="action-buttons" style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px; padding-top: 15px; border-top: 1px solid #e2e8f0; flex-wrap: wrap;">
                        <button type="submit" class="btn-principal" id="btnAgendar">
                            <i class="bi bi-{{ Auth::user()->rol == 'alumno' ? 'send' : 'calendar-check' }} me-1"></i>
                            {{ Auth::user()->rol == 'alumno' ? 'Enviar solicitud' : 'Agendar Sesión' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Mostrar errores de validación del servidor -->
@if($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let errores = `{!! implode('<br>', $errors->all()) !!}`;
        Swal.fire({
            icon: 'error',
            title: 'Errores en el formulario',
            html: errores,
            confirmButtonColor: '#2c9f49',
            confirmButtonText: 'Aceptar'
        });
    });
</script>
@endif

@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: '{{ session('success') }}',
            confirmButtonColor: '#2c9f49',
            confirmButtonText: 'Aceptar'
        });
    });
</script>
@endif

@if(session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '{{ session('error') }}',
            confirmButtonColor: '#2c9f49',
            confirmButtonText: 'Aceptar'
        });
    });
</script>
@endif

@endsection

@section('scripts')
<script src="{{ asset('js/agendar.js') }}"></script>
@endsection
@extends('Plantilla')

@section('contenido')
<link rel="stylesheet" href="{{ asset('estilos/botones.css') }}">
<link rel="stylesheet" href="{{ asset('estilos/titulos.css') }}">

<style>
    /* Estilos con el color VERDE de tu imagen */
    :root {
        --verde-principal: #10b981;
        --verde-oscuro: #059669;
        --verde-claro: #34d399;
        --verde-bg: #ecfdf5;
    }
    
    .schedule-container {
        width: 100%;
        max-width: 1800px;
        margin: 0 auto;
        padding: 15px;
    }
    
    @media (max-width: 768px) {
        .schedule-container { padding: 10px; }
    }
    
    .titulo {
        display: flex;
        width: 100%;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.5); 
        border-radius: 10px;
        background: linear-gradient(to left, #00937f, #2c9f49);
        color: #fff;
        margin-bottom: 20px;
        padding: 10px;
    }
    
    .titulo h1 { font-size: 1.5rem; }
    
    @media (max-width: 768px) { .titulo h1 { font-size: 1.2rem; } }
    
    .card-moderno {
        background: white;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border: 1px solid #e2e8f0;
        margin-bottom: 20px;
    }
    
    @media (max-width: 768px) { .card-moderno { padding: 15px; } }
    
    .card-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        border-left: 4px solid var(--verde-principal);
        padding-left: 15px;
    }
    
    @media (max-width: 768px) { .card-title { font-size: 1rem; padding-left: 10px; } }
    
    .steps-container {
        display: flex;
        gap: 20px;
        margin-bottom: 30px;
        border-bottom: 2px solid #e2e8f0;
        padding-bottom: 15px;
        flex-wrap: wrap;
        justify-content: center;
    }
    
    @media (max-width: 640px) {
        .steps-container { gap: 10px; }
        .step span { font-size: 12px; }
        .step-number { width: 24px; height: 24px; font-size: 12px; }
    }
    
    .step {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #a0aec0;
        font-weight: 500;
    }
    
    .step.active { color: var(--verde-principal); }
    
    .step-number {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: #edf2f7;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }
    
    .step.active .step-number { background: var(--verde-principal); color: white; }
    
    .main-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
    }
    
    @media (max-width: 768px) { .main-grid { grid-template-columns: 1fr; gap: 20px; } }
    
    .btn-agendar {
        background: linear-gradient(135deg, var(--verde-principal) 0%, var(--verde-oscuro) 100%);
        color: white;
        border: none;
        padding: 12px 28px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 2px 5px rgba(16, 185, 129, 0.3);
    }
    
    .btn-agendar:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(16, 185, 129, 0.4);
        background: linear-gradient(135deg, var(--verde-oscuro) 0%, var(--verde-principal) 100%);
    }
    
    .btn-cancelar {
        background: #ef4444;
        color: white;
        border: none;
        padding: 12px 28px;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .btn-cancelar:hover { background: #dc2626; transform: translateY(-2px); }
    
    @media (max-width: 480px) { .btn-agendar, .btn-cancelar { padding: 10px 20px; font-size: 14px; } }
    
    .search-box input, .topic-input, .modalidad-select, textarea {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        font-size: 14px;
        transition: all 0.3s ease;
    }
    
    .search-box input:focus, .topic-input:focus, .modalidad-select:focus, textarea:focus {
        border-color: var(--verde-principal);
        outline: none;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    }
    
    .asesores-list {
        max-height: 400px;
        overflow-y: auto;
    }
    
    .asesor-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px;
        border-bottom: 1px solid #edf2f7;
        cursor: pointer;
        transition: background 0.2s;
        border-radius: 10px;
        flex-wrap: wrap;
        gap: 10px;
    }
    
    @media (max-width: 480px) {
        .asesor-item { flex-direction: column; text-align: center; }
        .asesor-info h4 { font-size: 14px; }
        .asesor-info p { font-size: 12px; }
    }
    
    .asesor-item:hover { background: var(--verde-bg); }
    .asesor-item.selected { background: var(--verde-bg); border-left: 3px solid var(--verde-principal); }
    
    .badge-disponible {
        background: #d1fae5;
        color: var(--verde-oscuro);
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }
    
    .calendar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 10px;
    }
    
    .calendar-nav { display: flex; gap: 10px; }
    
    .calendar-nav button {
        background: #f3f4f6;
        border: none;
        padding: 8px 12px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .calendar-nav button:hover { background: var(--verde-principal); color: white; }
    
    .calendar-weekdays {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        text-align: center;
        margin-bottom: 10px;
        font-weight: 600;
        color: #6b7280;
    }
    
    .calendar-days {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 5px;
    }
    
    @media (max-width: 480px) {
        .calendar-days { gap: 2px; }
        .calendar-day { font-size: 12px; }
        .calendar-weekdays div { font-size: 11px; }
    }
    
    .calendar-day {
        aspect-ratio: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 14px;
    }
    
    .calendar-day:hover { background: var(--verde-bg); }
    .calendar-day.selected { background: var(--verde-principal); color: white; }
    .calendar-day.past { opacity: 0.3; cursor: not-allowed; }
    .calendar-day.weekend { color: #ef4444; }
    
    .time-slots {
        margin-top: 20px;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    
    .time-slot {
        padding: 8px 18px;
        border: 1px solid #e2e8f0;
        border-radius: 25px;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.3s ease;
    }
    
    @media (max-width: 480px) { .time-slot { padding: 6px 12px; font-size: 12px; } }
    
    .time-slot:hover { border-color: var(--verde-principal); background: var(--verde-bg); }
    .time-slot.selected { background: var(--verde-principal); color: white; border-color: var(--verde-principal); }
    
    .questions-list { margin-top: 20px; }
    
    .question-item {
        margin-bottom: 10px;
        padding: 12px;
        background: var(--verde-bg);
        border-radius: 10px;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    @media (max-width: 480px) {
        .question-item { font-size: 13px; }
        .question-item label { font-size: 13px; }
    }
    
    .question-item:hover { background: #d1fae5; }
    .question-item input { margin-right: 10px; accent-color: var(--verde-principal); }
    
    .alert-info {
        background: var(--verde-bg);
        border-left: 4px solid var(--verde-principal);
        padding: 12px;
        border-radius: 10px;
        margin-bottom: 20px;
        color: var(--verde-oscuro);
    }
    
    .error-message { color: #ef4444; font-size: 12px; margin-top: 5px; }
    
    .action-buttons {
        display: flex;
        gap: 15px;
        justify-content: flex-end;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #e2e8f0;
        flex-wrap: wrap;
    }
    
    @media (max-width: 480px) {
        .action-buttons { justify-content: center; }
        .action-buttons button { width: 100%; }
    }
    
    input[type="checkbox"] { width: 18px; height: 18px; cursor: pointer; accent-color: var(--verde-principal); }
    
    @media (max-width: 768px) {
        label, p, .form-text, .alert-info { font-size: 14px; }
        h4 { font-size: 16px; }
        .badge-disponible { font-size: 10px; }
    }
</style>

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
                        <div>L</div><div>M</div><div>M</div><div>J</div><div>V</div><div>S</div><div>D</div>
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

<script>
    // Validación antes de enviar
    document.getElementById('asesoriaForm').addEventListener('submit', function(e) {
        let isValid = true;
        
        if (!document.getElementById('destinatario_id').value) {
            document.getElementById('docenteError').textContent = 'Por favor, selecciona un ' + 
                (@json(Auth::user()->rol == 'alumno' ? 'docente' : (Auth::user()->rol == 'docente' ? 'alumno' : 'alumno')));
            isValid = false;
        } else {
            document.getElementById('docenteError').textContent = '';
        }
        
        if (!document.getElementById('fechaSeleccionada').value) {
            document.getElementById('fechaError').textContent = 'Por favor, selecciona una fecha';
            isValid = false;
        } else {
            document.getElementById('fechaError').textContent = '';
        }
        
        if (!document.getElementById('horaSeleccionada').value) {
            document.getElementById('horaError').textContent = 'Por favor, selecciona una hora';
            isValid = false;
        } else {
            document.getElementById('horaError').textContent = '';
        }
        
        if (!isValid) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Campos incompletos',
                text: 'Por favor, completa todos los campos requeridos',
                confirmButtonColor: '#d33'
            });
        }
    });
    
    // Búsqueda de asesores
    const searchInput = document.getElementById('searchAsesor');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const asesores = document.querySelectorAll('.asesor-item');
            
            asesores.forEach(asesor => {
                const nombre = asesor.dataset.nombre.toLowerCase();
                if (nombre.includes(searchTerm)) {
                    asesor.style.display = 'flex';
                } else {
                    asesor.style.display = 'none';
                }
            });
        });
    }
    
    // Selección de asesor
    document.querySelectorAll('.asesor-item').forEach(asesor => {
        asesor.addEventListener('click', function() {
            document.querySelectorAll('.asesor-item').forEach(a => a.classList.remove('selected'));
            this.classList.add('selected');
            document.getElementById('destinatario_id').value = this.dataset.id;
            document.getElementById('tipo_destinatario').value = this.dataset.tipo;
            document.getElementById('docenteError').textContent = '';
        });
    });
    
    // Calendario
    let currentDate = new Date();
    
    function renderCalendar() {
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();
        
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        
        const monthNames = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        document.getElementById('currentMonth').textContent = `${monthNames[month]} ${year}`;
        
        let days = '';
        const startOffset = firstDay.getDay() === 0 ? 6 : firstDay.getDay() - 1;
        
        for (let i = 0; i < startOffset; i++) {
            days += '<div></div>';
        }
        
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        for (let i = 1; i <= lastDay.getDate(); i++) {
            const cellDate = new Date(year, month, i);
            const isPast = cellDate < today;
            const isWeekend = cellDate.getDay() === 0 || cellDate.getDay() === 6;
            
            let extraClass = '';
            if (isPast) extraClass = 'past';
            if (isWeekend) extraClass = 'weekend';
            
            days += `<div class="calendar-day ${extraClass}" data-day="${i}" data-date="${year}-${String(month+1).padStart(2,'0')}-${String(i).padStart(2,'0')}">${i}</div>`;
        }
        
        document.getElementById('calendarDays').innerHTML = days;
        
        document.querySelectorAll('.calendar-day[data-day]').forEach(day => {
            if (!day.classList.contains('past') && !day.classList.contains('weekend')) {
                day.addEventListener('click', function() {
                    document.querySelectorAll('.calendar-day').forEach(d => d.classList.remove('selected'));
                    this.classList.add('selected');
                    document.getElementById('fechaSeleccionada').value = this.dataset.date;
                    document.getElementById('fechaError').textContent = '';
                });
            }
        });
    }
    
    document.getElementById('prevMonth').addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar();
    });
    
    document.getElementById('nextMonth').addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar();
    });
    
    renderCalendar();
    
    // Selección de hora
    document.querySelectorAll('.time-slot').forEach(slot => {
        slot.addEventListener('click', function() {
            document.querySelectorAll('.time-slot').forEach(s => s.classList.remove('selected'));
            this.classList.add('selected');
            document.getElementById('horaSeleccionada').value = this.dataset.time;
            document.getElementById('horaError').textContent = '';
        });
    });
    
    // Resaltar paso activo
    function updateActiveStep() {
        const scrollPosition = window.scrollY;
        const step1Card = document.getElementById('step1-card');
        const step2Card = document.getElementById('step2-card');
        const step3Card = document.getElementById('step3-card');
        
        if (!step1Card || !step2Card || !step3Card) return;
        
        const step1Top = step1Card.offsetTop - 100;
        const step2Top = step2Card.offsetTop - 100;
        const step3Top = step3Card.offsetTop - 100;
        
        if (scrollPosition >= step3Top) {
            setActiveStep(2);
        } else if (scrollPosition >= step2Top) {
            setActiveStep(1);
        } else {
            setActiveStep(0);
        }
    }
    
    function setActiveStep(index) {
        document.querySelectorAll('.step').forEach((step, i) => {
            if (i === index) {
                step.classList.add('active');
            } else {
                step.classList.remove('active');
            }
        });
    }
    
    window.addEventListener('scroll', updateActiveStep);
    updateActiveStep();
</script>
@endsection
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
            <h4 class="fw-semibold mb-0"> Formulario de Asesoría</h4>
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
                    <label class="form-label fw-semibold">Lista de alumnos <span class="text-danger">*</span></label>
                    
                    {{-- Botones para seleccionar por grupos (Bootstrap) --}}
                    <div class="card mb-3">
                        <div class="card-body py-2">
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <span class="fw-semibold me-2"><i class="bi bi-filter"></i> Seleccionar por grupo:</span>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-todos">Todos</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-ninguno">Ninguno</button>
                                @php
                                    $gruposUnicos = $alumnos->pluck('grupo.nombre')->unique()->filter();
                                @endphp
                                @foreach($gruposUnicos as $grupo)
                                    <button type="button" class="btn btn-sm btn-outline-primary btn-grupo-filtro" data-grupo="{{ $grupo }}">
                                        Grupo {{ $grupo }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th width="50">#</th>
                                    <th>Nombre del Alumno</th>
                                    <th width="150">Grupo</th>
                                    <th width="80">Seleccionar</th>
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
                                        <i class="bi bi-inbox fs-1"></i>
                                        <p class="mb-0">No hay alumnos registrados</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="form-text">
                        <i class="bi bi-info-circle"></i> Selecciona los alumnos que asistirán a la asesoría
                    </div>
                </div>

                {{-- Botones --}}
<div class="row g-3 mt-4">
    <div class="col-md-6">
        <button type="submit" class="btn-principal w-100 py-2">
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
<script>
    document.getElementById('formAsesoria').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    Swal.fire({
        title: 'Guardando...',
        text: 'Por favor espera',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    fetch('{{ route('asesoria.store') }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.blob())
    .then(blob => {
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'asesoria.pdf';
        document.body.appendChild(a);
        a.click();
        a.remove();
        window.URL.revokeObjectURL(url);
        
        Swal.fire({
            icon: 'success',
            title: '¡Asesoría registrada!',
            text: 'El PDF se ha descargado correctamente',
            confirmButtonColor: '#3085d6'
        });
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Ocurrió un error al registrar la asesoría',
            confirmButtonColor: '#d33'
        });
    });
});
</script>
<script>
    @if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: '¡Asesoría registrada!',
        text: '{{ session('success') }}',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Aceptar'
    }).then((result) => {
        @if(session('pdf_path'))
        window.location.href = '{{ route('descargar.pdf') }}?path={{ base64_encode(session('pdf_path')) }}';
        @endif
    });
</script>
@endif

    // Alerta de error
    @if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: '{{ session('error') }}',
        confirmButtonColor: '#d33',
        confirmButtonText: 'Aceptar'
    });
    @endif

    // Alerta de errores de validación
    @if($errors->any())
    Swal.fire({
        icon: 'error',
        title: 'Errores en el formulario',
        html: `{!! implode('<br>', $errors->all()) !!}`,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Aceptar'
    });
    @endif

    // Alerta de cancelación (al hacer clic en cancelar)
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

<script src="{{ asset('js/asesoria.js') }}"></script>
@endsection()


@extends('Plantilla')

@section('contenido')
<link rel="stylesheet" href="{{ asset('estilos/botones.css') }}">
<link rel="stylesheet" href="{{ asset('estilos/titulos.css') }}">

<div class="titulo">
    <h1>Historial General de asesorías</h1>
</div>

<!-- Filtros principales -->
<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-semibold mb-0 titulo-borde-verde">Filtrado de búsqueda</h5>
        </div>

        <form method="GET" action="{{ route('historial') }}" id="formFiltros">
            <div class="row g-3 align-items-end">
                <!-- Cuatrimestre -->
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Cuatrimestre</label>
                    <select class="form-select" name="cuatrimestre" id="cuatrimestre">
                        <option value="">Todos</option>
                        @foreach($cuatrimestres as $c)
                        <option value="{{ $c }}" {{ request('cuatrimestre') == $c ? 'selected' : '' }}>
                            {{ $c }}er Cuatrimestre
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Materia -->
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Materia</label>
                    <select class="form-select" name="materia" id="materia">
                        <option value="">Todas las materias</option>
                        @foreach($materias as $materia)
                        <option value="{{ $materia }}" {{ request('materia') == $materia ? 'selected' : '' }}>
                            {{ $materia }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Buscar alumno -->
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Buscar alumno</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="buscar_alumno" id="buscar_alumno"
                            placeholder="Nombre o matrícula..." value="{{ request('buscar_alumno') }}">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>

                <!-- Fecha -->
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Fecha</label>
                    <input type="date" class="form-control" name="fecha" id="fecha" value="{{ request('fecha') }}">
                </div>

                <!-- Botones de acción -->
                <div class="col-md-2 d-flex gap-2 align-items-end">
                    <button type="button" class="btn-secundario flex-grow-1" id="btnLimpiarFiltros">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Limpiar
                    </button>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-12 d-flex gap-3">
                    <button type="submit" class="btn-principal">
                        <i class="bi bi-funnel me-1"></i> Filtrar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Tabla de asesorías realizadas -->
<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-semibold mb-0 titulo-borde-verde">Asesorías realizadas</h5>
            <div>
                <button class="btn-principal" id="btnReporteCuatrimestral">
                    <i class="bi bi-file-earmark-bar-graph me-1"></i> Generar reporte cuatrimestral
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light"> <!-- Usa table-light, sobrescrito con degradado verde -->
                    <tr>
                        <th>Tipo</th>
                        <th>Grupo / Alumno</th>
                        <th>Docente</th>
                        <th>Tema tratado</th>
                        <th>Fecha</th>
                        <th>Acuerdos / Resultados</th>
                        <th>Ver asesoria</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sesiones as $sesion)
                    @php
                    $esGrupo = $sesion->alumnos->count() > 1;
                    $reporte = $sesion->reporte;
                    $primerAlumno = $sesion->alumnos->first();
                    $nombreAlumno = $primerAlumno ? $primerAlumno->nombres . ' ' . $primerAlumno->apellido_paterno : 'N/A';
                    $grupoNombre = $primerAlumno && $primerAlumno->alumno && $primerAlumno->alumno->grupo
                    ? $primerAlumno->alumno->grupo->nombre
                    : 'N/A';
                    @endphp
                    <tr>
                        <td>{{ $esGrupo ? 'Grupal' : 'Individual' }}</td>
                        <td>
                            @if($esGrupo)
                            {{ $grupoNombre }}
                            @else
                            {{ $nombreAlumno }}
                            @endif
                        </td>
                        <td>{{ $sesion->docente->nombres ?? 'N/A' }} {{ $sesion->docente->apellido_paterno ?? '' }}</td>
                        <td>{{ $sesion->tema }}</td>
                        <td>{{ \Carbon\Carbon::parse($sesion->fecha_inicio)->format('d/m/Y') }}</td>
                        <td>{{ $sesion->acuerdos->first()->acuerdo ?? '—' }}</td>
                        <td>
                            @if($reporte)
                            <a href="{{ route('reporte.ver', $reporte->id) }}" target="_blank" class="btn-secundario">
                                <i class="bi bi-eye me-1"></i> Ver
                            </a>
                            @else
                            <span class="text-muted">Sin PDF</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-3">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            No hay asesorías realizadas que coincidan con los filtros.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        @if($sesiones->hasPages())
        <div class="d-flex justify-content-end mt-3">
            {{ $sesiones->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Botón regreso -->
<a href="@if(auth()->user()->rol === 'admin') {{ route('admin.dashboard') }}
@elseif(auth()->user()->rol === 'docente') {{ route('docente.dashboard') }}
@else {{ route('alumno.dashboard') }} @endif" class="btn-principal">
    <i class="bi bi-arrow-left me-1"></i> Regresar al escritorio
</a>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Botón "Limpiar filtros"
        document.getElementById('btnLimpiarFiltros')?.addEventListener('click', function() {
            // Limpiar todos los campos del formulario
            const form = document.getElementById('formFiltros');
            const inputs = form.querySelectorAll('input, select');
            inputs.forEach(input => {
                if (input.tagName === 'SELECT') {
                    input.selectedIndex = 0;
                } else if (input.type === 'text' || input.type === 'date') {
                    input.value = '';
                }
            });
            // Enviar el formulario (sin filtros)
            form.submit();
        });

        // Botón "Generar reporte cuatrimestral"
        document.getElementById('btnReporteCuatrimestral')?.addEventListener('click', function() {
            Swal.fire({
                icon: 'info',
                title: 'Reporte cuatrimestral',
                text: 'Esta funcionalidad estará disponible próximamente.',
                confirmButtonColor: '#2c9f49',
                confirmButtonText: 'Aceptar'
            });
        });
    });
</script>
@endsection
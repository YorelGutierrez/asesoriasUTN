@extends('Plantilla')

@section('contenido')
<link rel="stylesheet" href="{{ asset('estilos/botones.css') }}">
<link rel="stylesheet" href="{{ asset('estilos/titulos.css') }}">

<div class="titulo">
    <h1>Asignaciones académicas</h1>
</div>

<!-- Filtros -->
<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-body p-4">
        <h5 class="fw-semibold mb-3 titulo-borde-verde">Filtros de búsqueda</h5>
        <form method="GET" action="{{ route('admin.asignaciones') }}" id="formFiltros">
            <div class="row g-3">
                <!-- Fila 1: Inputs -->
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Docente</label>
                    <input type="text" class="form-control" name="docente" placeholder="Nombre..." value="{{ request('docente') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Carrera</label>
                    <select class="form-select" name="carrera">
                        <option value="">Todas</option>
                        @foreach($carreras as $carrera)
                        <option value="{{ $carrera->id }}" {{ request('carrera') == $carrera->id ? 'selected' : '' }}>
                            {{ $carrera->nombre }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Materia</label>
                    <select class="form-select" name="materia">
                        <option value="">Todas</option>
                        @foreach($materias as $materia)
                        <option value="{{ $materia->id }}" {{ request('materia') == $materia->id ? 'selected' : '' }}>
                            {{ $materia->nombre }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Grupo</label>
                    <select class="form-select" name="grupo">
                        <option value="">Todos</option>
                        @foreach($grupos as $grupo)
                        <option value="{{ $grupo->id }}" {{ request('grupo') == $grupo->id ? 'selected' : '' }}>
                            {{ $grupo->nombre }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Fila 2: Botones de acción (debajo de los inputs) -->
                <div class="col-12 d-flex gap-3 mt-2">
                    <button type="submit" class="btn-principal">
                        <i class="bi bi-funnel me-1"></i> Filtrar
                    </button>
                    <a href="{{ route('admin.asignaciones') }}" class="btn-secundario">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Limpiar filtros
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Tabla -->
<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-semibold mb-0 titulo-borde-verde">Asignaciones actuales</h5>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Docente</th>
                        <th>Carrera</th>
                        <th>Materias</th>
                        <th>Grupos</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($asignaciones as $docente)
                    <tr>
                        <td>{{ $docente->nombres }} {{ $docente->apellido_paterno }}</td>
                        <td>{{ $docente->docente->carrera->nombre ?? 'N/A' }}</td>
                        <td>
                            @foreach($docente->materias as $materia)
                            <span class="badge bg-success">{{ $materia->nombre }}</span>
                            @endforeach
                            @if($docente->materias->isEmpty())
                            <span class="text-muted">Sin materias</span>
                            @endif
                        </td>
                        <td>
                            @foreach($docente->grupos as $grupo)
                            <span class="badge bg-secondary">{{ $grupo->nombre }}</span>
                            @endforeach
                            @if($docente->grupos->isEmpty())
                            <span class="text-muted">Sin grupos</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-outline-warning editar-asignacion"
                                    data-id="{{ $docente->id }}"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalAsignacion">
                                    Editar
                                </button>
                                <button class="btn btn-sm btn-outline-danger eliminar-asignacion"
                                    data-id="{{ $docente->id }}"
                                    data-nombre="{{ $docente->nombres }}">
                                    Eliminar
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-3">
                            No hay asignaciones registradas.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end mt-3">
            {{ $asignaciones->appends(request()->query())->links() }}
        </div>

        <div class="d-flex justify-content-end mt-3">
            <button class="btn-principal" data-bs-toggle="modal" data-bs-target="#modalAsignacion" id="btnNuevaAsignacion">
                <i class="bi bi-plus-circle me-1"></i> Nueva asignación
            </button>
        </div>
    </div>
</div>

<!-- ===== MODAL ===== -->
<div class="modal fade" id="modalAsignacion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(to left, #00937f, #2c9f49); color: white;">
                <h5 class="modal-title" id="modalLabel">
                    <i class="bi bi-journal-text me-2"></i> Nueva asignación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formAsignacion" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Carrera <span class="text-danger">*</span></label>
                            <select class="form-select" name="carrera_id" id="carreraSelect" required>
                                <option value="">Seleccionar carrera</option>
                                @foreach($carreras as $carrera)
                                <option value="{{ $carrera->id }}">{{ $carrera->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Docente <span class="text-danger">*</span></label>
                            <select class="form-select" name="docente_id" id="docenteSelect" required>
                                <option value="">Primero selecciona una carrera</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Materias asignadas</label>
                            <div id="materiasContainer" style="height:160px; overflow-y:auto; border:1px solid #dee2e6; border-radius:8px; padding:12px; background:#f8f9fa;">
                                <p class="text-muted small">Selecciona un docente para ver materias.</p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Grupos asignados</label>
                            <div id="gruposContainer" style="height:160px; overflow-y:auto; border:1px solid #dee2e6; border-radius:8px; padding:12px; background:#f8f9fa;">
                                <p class="text-muted small">Selecciona una carrera para ver grupos.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secundario" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn-principal" id="btnGuardar">
                        <i class="bi bi-save me-1"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Formulario oculto para eliminar -->
<form id="formEliminar" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

@endsection

@section('scripts')
<script>
    window.ASIGNACIONES_ROUTES = {
        docentes: '{{ route("admin.asignaciones.docentes") }}',
        grupos: '{{ route("admin.asignaciones.grupos") }}',
        materias: '{{ route("admin.asignaciones.materias", ["docenteId" => 0]) }}',
        editar: '{{ route("admin.asignaciones.editar", ["id" => 0]) }}',
        store: '{{ route("admin.asignaciones.store") }}',
        update: '{{ route("admin.asignaciones.update", ["id" => 0]) }}',
        destroy: '{{ route("admin.asignaciones.destroy", ["id" => 0]) }}'
    };
</script>
<script src="{{ asset('js/asignacion.js') }}"></script>
@endsection
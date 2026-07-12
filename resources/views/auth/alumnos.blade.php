@extends('Plantilla')

@section('contenido')
<link rel="stylesheet" href="{{ asset('estilos/botones.css') }}">
<link rel="stylesheet" href="{{ asset('estilos/titulos.css') }}">
<link rel="stylesheet" href="{{ asset('estilos/alumnos.css') }}">

<div class="titulo">
    <h1>Alumnos{{ $grupo ? ' del grupo ' . $grupo->nombre : '' }}</h1>
</div>

{{-- Si no hay grupo activo y es docente, avisamos --}}
@if(!$grupo && auth()->user()->rol === 'docente')
    <div class="alert alert-warning rounded-3">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        Debes <a href="{{ route('grupos') }}">seleccionar un grupo</a> primero para ver sus alumnos.
    </div>
@else

    <!-- barra de busqueda -->
    <div class="search-card bg-white rounded-3 p-3 mb-4 shadow-sm">
        <div class="row align-items-center">
            <div class="col-auto">
                <span class="fw-semibold">Buscar alumno:</span>
            </div>
            <div class="col">
                <div class="input-group">
                    <input type="text" class="form-control" id="search-input" placeholder="Escribe para buscar...">
                    <button class="btn btn-outline-secondary" type="button">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cards alumnos -->
    @if($alumnos->isEmpty())
        <div class="text-center py-5 text-muted">
            <i class="bi bi-people" style="font-size:3rem;"></i>
            <p class="mt-3">No hay alumnos en este grupo.</p>
        </div>
    @else
        <div class="row" id="lista-alumnos">
            @foreach($alumnos as $alumno)
                <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4 alumno-card"
                     data-nombre="{{ strtolower($alumno->user->nombres . ' ' . $alumno->user->apellido_paterno . ' ' . $alumno->user->apellido_materno) }}">
                    <div class="card">
                        <div class="header-curve">
                            <div class="profile-pic-container">
                                <img src="{{ $alumno->user->foto_url }}" alt="Foto de perfil" class="profile-pic-img">
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="user-name">
                                {{ $alumno->user->nombres }} {{ $alumno->user->apellido_paterno }} {{ $alumno->user->apellido_materno }}
                            </p>
                            <a class="btn-principal" href="{{ route('expedienteAlumnos', ['alumno_id' => $alumno->id]) }}">
                                Seleccionar alumno
                            </a>
                            <div class="background-pattern"></div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endif

@endsection

@section('scripts')
<script>
    document.getElementById('search-input')?.addEventListener('input', function () {
        const term = this.value.toLowerCase();
        document.querySelectorAll('.alumno-card').forEach(card => {
            card.style.display = card.dataset.nombre.includes(term) ? '' : 'none';
        });
    });
</script>
@endsection
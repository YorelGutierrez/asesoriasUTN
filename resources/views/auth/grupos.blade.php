@extends('Plantilla')

@section('contenido')
<link rel="stylesheet" href="{{ asset('estilos/grupos.css') }}">
<link rel="stylesheet" href="{{ asset('estilos/botones.css') }}">
<link rel="stylesheet" href="{{ asset('estilos/titulos.css') }}">

<div class="titulo">
    <h1>Grupos asignados</h1>
</div>

@if($gruposLista->isEmpty())
    <div class="text-center py-5 text-muted">
        <i class="bi bi-folder-x" style="font-size:3rem;"></i>
        <p class="mt-3">No tienes grupos asignados. Contacta al administrador.</p>
    </div>
@else
    <div class="row">
        @foreach($gruposLista as $grupo)
            <div class="col-md-4 col-sm-12 mb-4">
                <div class="card-itid">
                    <div class="card-left">
                        <img src="{{ asset($grupo->carrera->logo ?? 'img/carreras/ITIID-DDH-gJkG.png') }}"
                             alt="Logo carrera" class="logo">
                    </div>
                    <div class="card-right">
                        <div class="card-bg-decoration"></div>

                        <div class="card-content-wrapper">
                            <h3>Grupo: <span>{{ $grupo->nombre }}</span></h3>
                            <div class="stats-row">
                                <i class="bi bi-people-fill"></i> <span>: {{ $grupo->alumnos->count() }}</span>
                            </div>

                            @if($grupoActivoId == $grupo->id)
                                <button class="btn-principal" disabled style="opacity:.6;cursor:not-allowed;">
                                    Grupo activo
                                </button>
                            @else
                                <form method="POST" action="{{ route('grupos.seleccionar', $grupo->id) }}">
                                    @csrf
                                    <button type="submit" class="btn-principal">Seleccionar grupo</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

@endsection
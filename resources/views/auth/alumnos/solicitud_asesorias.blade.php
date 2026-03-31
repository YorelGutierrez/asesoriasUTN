@extends('Plantilla')

@section('contenido')

<link rel="stylesheet" href="{{ asset('estilos/grupos.css') }}">
<link rel="stylesheet" href="{{ asset('estilos/botones.css') }}">
<link rel="stylesheet" href="{{ asset('estilos/titulos.css') }}">
<link rel="stylesheet" href="{{ asset('estilos/formularios.css') }}">

<div class="titulo">
    <h1>Solicitud de asesorías</h1>
</div>

<div class="row justify-content-center">
    <div class="col-md-10 col-sm-12">
        <div class="card-itid p-4">

            <form action="{{ route('solicitudes.store') }}" method="POST">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label>Nombre del alumno:</label>
                        <input type="text" class="form-control" 
                               value="{{ auth()->user()->name }}" readonly>
                    </div>

                    <div class="col-md-2">
                        <label>Grupo:</label>
                        <input type="text" class="form-control" 
                               name="grupo" value="{{ $grupo ?? '' }}" readonly>
                    </div>

                    <div class="col-md-3">
                        <label>Nombre del docente:</label>
                        <input type="text" name="docente" class="form-control" placeholder="Nombre">
                    </div>

                    <div class="col-md-3">
                        <label>Materia:</label>
                        <select name="materia_id" class="form-control">
                            <option value="">Seleccionar</option>
                            @foreach($materias as $materia)
                                <option value="{{ $materia->id }}">
                                    {{ $materia->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <label>Motivo:</label>
                        <select name="motivo" class="form-control">
                            <option value="Remedial">Remedial</option>
                            <option value="Regularización">Regularización</option>
                            <option value="Asesoría">Asesoría</option>
                        </select>
                    </div>

                    <div class="col-md-9">
                        <label>Tema:</label>
                        <input type="text" name="tema" class="form-control" 
                               placeholder="Ej. Derivadas implícitas">
                    </div>
                </div>

                <div class="mb-3">
                    <label>Describe qué no entiendes o en qué necesitas ayuda:</label>
                    <textarea name="descripcion" rows="4" class="form-control"
                              placeholder="Escribe aquí..."></textarea>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn-principal">Enviar</button>
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary">Cancelar</a>
                </div>

            </form>

        </div>
    </div>
</div>
@endsection()

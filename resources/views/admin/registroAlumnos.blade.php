@extends('Plantilla')

@section('contenido')

<link rel="stylesheet" href="{{ asset('estilos/titulos.css') }}">
<link rel="stylesheet" href="{{ asset('estilos/botones.css') }}">

<div class="titulo">
    <h1>Registro de Alumno</h1>
</div>

<div class="mt-4">
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body p-4">

            <form>

                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-3">
                        <label>Matrícula:</label>
                        <input type="text" class="form-control">
                    </div>

                    <div class="col-12 col-md-5">
                        <label>Nombre completo:</label>
                        <input type="text" class="form-control">
                    </div>

                    <div class="col-12 col-md-2">
                        <label>Grupo:</label>
                        <input type="text" class="form-control">
                    </div>

                    <div class="col-12 col-md-2">
                        <label>Carrera:</label>
                        <input type="text" class="form-control">
                    </div>
                </div>

                <div class="mb-3">
                    <label>Correo:</label>
                    <input type="email" class="form-control">
                </div>

                <div class="row mt-3 g-2">
                    <div class="col-12 col-md-6">
                        <button type="submit" class="btn-principal">Registrar</button>
                    </div>
                    <div class="col-12 col-md-6">
                        <button type="button" class="btn-secundario">Cancelar</button>
                    </div>
                </div>

            </form>

        </div>
    </div>
</div>

@endsection
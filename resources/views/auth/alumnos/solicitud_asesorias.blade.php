@extends('Plantilla')

@section('contenido')

<link rel="stylesheet" href="{{ asset('estilos/menu-lateral.css') }}">
<link rel="stylesheet" href="{{ asset('estilos/titulos.css') }}">
<link rel="stylesheet" href="{{ asset('estilos/botones.css') }}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="titulo">
    <h1>Solicitud de asesorías</h1>
</div>

<div class="mt-4">

    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body p-4">

            <form>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label>Nombre del alumno:</label>
                        <input type="text" class="form-control" value="Nombre del alumno" readonly>
                    </div>

                    <div class="col-md-2">
                        <label>Grupo:</label>
                        <input type="text" class="form-control" value="IDGS" readonly>
                    </div>

                    <div class="col-md-3">
                        <label>Nombre del docente:</label>
                        <input type="text" class="form-control" value="Nombre del docente" readonly>
                    </div>

                    <div class="col-md-3">
                        <label>Materia:</label>
                        <select class="form-control">
                            <option>Seleccionar</option>
                            <option>Base de datos</option>
                            <option>Integradora</option>
                            <option>Inglés</option>
                        </select>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label>Motivo:</label>
                        <select class="form-control">
                            <option>Remedial</option>
                            <option>Regularización</option>
                            <option>Asesoría</option>
                        </select>
                    </div>

                    <div class="col-md-9">
                        <label>Tema:</label>
                        <input type="text" class="form-control" placeholder="Ej. Derivadas implícitas">
                    </div>
                </div>
                <div class="mb-3">
                    <label>Describe qué no entiendes o en qué necesitas ayuda:</label>
                    <textarea class="form-control" rows="4" placeholder="Escribe aquí..."></textarea>
                </div>

                <div class="row mt-3 g-2">
                    <div class="col-12 col-md-6 d-flex">
                        <button type="submit" class="btn-principal w-100">Enviar</button>
                    </div>

                    <div class="col-12 col-md-6 d-flex">
                        <button type="button" class="btn-secundario w-100">Cancelar</button>
                    </div>

                </div>
            </form>

        </div>
    </div>

</div>

@endsection
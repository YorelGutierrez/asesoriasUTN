@extends('Plantilla')

@section('contenido')
<link rel="stylesheet" href="{{ asset('estilos/grupos.css') }}">
<link rel="stylesheet" href="{{ asset('estilos/botones.css') }}">
<link rel="stylesheet" href="{{ asset('estilos/titulos.css') }}">

<div class="titulo">
    <h1>Grupos asignados</h1>
</div>

<div class="row">
    <!-- Cada tarjeta ocupará 4 columnas en pantallas medianas y superiores (col-md-4) -->
    <div class="col-md-4 col-sm-12 mb-4">
        <div class="card-itid">
            <div class="card-left">
                <img src="https://www.utnay.edu.mx/assets/ITIID-DDH-gJkG.png" alt="Logo carrera" class="logo">
            </div>
            <div class="card-right">
                <div class="card-bg-decoration"></div>

                <div class="card-content-wrapper">
                    <h3>Grupo: <span>IDGS - 81</span></h3>
                    <div class="stats-row">
                        <i class="bi bi-people-fill"></i> <span>: 25</span>
                    </div>
                    <button class="btn-principal">Seleccionar grupo</button>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card-itid">
            <div class="card-left">
                <img src="https://www.utnay.edu.mx/assets/ITIID-DDH-gJkG.png" alt="Logo carrera" class="logo">
            </div>
            <div class="card-right">
                <div class="card-bg-decoration"></div>

                <div class="card-content-wrapper">
                    <h3>Grupo: <span>IDGS - 82</span></h3>
                    <div class="stats-row">
                        <i class="bi bi-people-fill"></i> <span>: 25</span>
                    </div>
                    <button class="btn-principal">Seleccionar grupo</button>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card-itid">
            <div class="card-left">
                <img src="https://www.utnay.edu.mx/assets/ITIID-DDH-gJkG.png" alt="Logo carrera" class="logo">
            </div>
            <div class="card-right">
                <div class="card-bg-decoration"></div>

                <div class="card-content-wrapper">
                    <h3>Grupo: <span>IDGS - 83</span></h3>
                    <div class="stats-row">
                        <i class="bi bi-people-fill"></i> <span>: 25</span>
                    </div>
                    <button class="btn-principal">Seleccionar grupo</button>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card-itid">
            <div class="card-left">
                <img src="https://www.utnay.edu.mx/assets/ITIID-DDH-gJkG.png" alt="Logo carrera" class="logo">
            </div>
            <div class="card-right">
                <div class="card-bg-decoration"></div>

                <div class="card-content-wrapper">
                    <h3>Grupo: <span>IDGS - 84</span></h3>
                    <div class="stats-row">
                        <i class="bi bi-people-fill"></i> <span>: 25</span>
                    </div>
                    <button class="btn-principal">Seleccionar grupo</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Más tarjetas se agregarán en nuevas filas automáticamente -->
</div>

@endsection()
@extends('Plantilla')

@section('contenido')
<link rel="stylesheet" href="{{ asset('estilos/botones.css') }}">
<link rel="stylesheet" href="{{ asset('estilos/titulos.css') }}">
<link rel="stylesheet" href="{{ asset('estilos/grupos.css') }}">

<div class="titulo">
    <h1>Bienvenido {{ Auth::user()->nombres . ' ' . Auth::user()->apellido_paterno . ' ' . Auth::user()->apellido_materno }}!</h1>
</div>

<!-- Tarjetas de resumen -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4">
                <h5 class="fw-semibold mb-0 titulo-borde-verde">PRÓXIMA ASESORÍA</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    <i class="bi bi-calendar-event-fill fs-1 text-success"></i>
                    <div>
                        <p class="mb-0 fw-bold">Mañana</p>
                        <span class="text-muted">17:00 h</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4">
                <h5 class="fw-semibold mb-0 titulo-borde-verde">ALUMNOS TOTALES</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-end">
                    <h1 class="display-4 fw-bold text-success">125</h1>
                    <span class="text-muted">atendidos</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4">
                <h5 class="fw-semibold mb-0 titulo-borde-verde">GRUPOS ACTIVOS</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-end">
                    <h1 class="display-4 fw-bold text-success">4</h1>
                    <i class="bi bi-exclamation-triangle-fill text-warning fs-3"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Mis grupos recientes -->
    <div class="mt-5 mb-3">
        <div class="titulo">
            <h3>Mis grupos recientes</h3>
        </div>
    </div>

    <div class="row">
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
        <div class="col-md-4 col-sm-12 mb-4">
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
        <div class="col-md-4 col-sm-12 mb-4">
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
    </div>

    @endsection()
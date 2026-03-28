@extends('Plantilla')

@section('contenido')
<link rel="stylesheet" href="{{ asset('estilos/botones.css') }}">
<link rel="stylesheet" href="{{ asset('estilos/titulos.css') }}">
<link rel="stylesheet" href="{{ asset('estilos/alumnos.css') }}">

<div class="titulo">
    <h1>Alumnos del grupo IDGS-84</h1>
</div>

<!-- barra de busqueda -->
  <div class="search-card bg-white rounded-3 p-3 mb-4 shadow-sm">
    <div class="row align-items-center">
      <div class="col-auto">
        <span class="fw-semibold">Buscar alumno:</span>
      </div>
      <div class="col">
        <div class="barra-busqueda">
          <form>
            <i class="bi bi-search"></i>
            <input type="search" placeholder="Escribe el nombre del alumno...">
          </form>
        </div>
      </div>
    </div>
  </div>

<!-- Cards alumnos -->
  <div class="row">
    <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
      <!-- Tarjeta de alumno -->
      <div class="card">
        <div class="header-curve">
          <div class="profile-pic-container">
            <img src="/img/default-avatar.png" alt="Foto de perfil" class="profile-pic-img">
          </div>
        </div>
        <div class="card-body">
          <p class="user-name">Nombre del alumno</p>
          <a class="btn-principal" id="btn-select-alumno" href="{{ route('expedienteAlumnos') }}">Seleccionar alumno</a>
          <div class="background-pattern"></div>
        </div>
      </div>
    </div>
    <!-- Repite para cada alumno -->
         <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
      <!-- Tarjeta de alumno -->
      <div class="card">
        <div class="header-curve">
          <div class="profile-pic-container">
            <img src="/img/default-avatar.png" alt="Foto de perfil" class="profile-pic-img">
          </div>
        </div>
        <div class="card-body">
          <p class="user-name">Nombre del alumno</p>
          <button class="btn-principal">Seleccionar alumno</button>
          <div class="background-pattern"></div>
        </div>
      </div>
    </div>
        <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
      <!-- Tarjeta de alumno -->
      <div class="card">
        <div class="header-curve">
          <div class="profile-pic-container">
            <img src="/img/default-avatar.png" alt="Foto de perfil" class="profile-pic-img">
          </div>
        </div>
        <div class="card-body">
          <p class="user-name">Nombre del alumno</p>
          <button class="btn-principal">Seleccionar alumno</button>
          <div class="background-pattern"></div>
        </div>
      </div>
    </div>
        <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
      <!-- Tarjeta de alumno -->
      <div class="card">
        <div class="header-curve">
          <div class="profile-pic-container">
            <img src="/img/default-avatar.png" alt="Foto de perfil" class="profile-pic-img">
          </div>
        </div>
        <div class="card-body">
          <p class="user-name">Nombre del alumno</p>
          <button class="btn-principal">Seleccionar alumno</button>
          <div class="background-pattern"></div>
        </div>
      </div>
    </div>
        <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
      <!-- Tarjeta de alumno -->
      <div class="card">
        <div class="header-curve">
          <div class="profile-pic-container">
            <img src="/img/default-avatar.png" alt="Foto de perfil" class="profile-pic-img">
          </div>
        </div>
        <div class="card-body">
          <p class="user-name">Nombre del alumno</p>
          <button class="btn-principal">Seleccionar alumno</button>
          <div class="background-pattern"></div>
        </div>
      </div>
    </div>
        <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
      <!-- Tarjeta de alumno -->
      <div class="card">
        <div class="header-curve">
          <div class="profile-pic-container">
            <img src="/img/default-avatar.png" alt="Foto de perfil" class="profile-pic-img">
          </div>
        </div>
        <div class="card-body">
          <p class="user-name">Nombre del alumno</p>
          <button class="btn-principal">Seleccionar alumno</button>
          <div class="background-pattern"></div>
        </div>
      </div>
    </div>
  </div>

@endsection()
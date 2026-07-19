@extends('Plantilla')

@section('contenido')
<link rel="stylesheet" href="{{ asset('estilos/botones.css') }}">
<link rel="stylesheet" href="{{ asset('estilos/titulos.css') }}">

<style>
    .btn-verde {
        background-color: #2c9f49;
        color: white;
        border: none;
        padding: 8px 24px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        transition: background-color 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        min-width: 120px;
    }
    .btn-verde:hover {
        background-color: #218838;
        color: white;
    }
</style>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-0 pt-4">
                    <h4 class="fw-semibold titulo-borde-verde">
                        <i class="bi bi-person-circle me-2"></i>Mi Perfil
                    </h4>
                </div>
                <div class="card-body p-4">

                    {{-- ============================================================ --}}
                    {{-- FOTO DE PERFIL CON OPCIÓN PARA CAMBIAR                        --}}
                    {{-- ============================================================ --}}
                    <div class="text-center mb-4">
                        <div class="position-relative d-inline-block">
                            <img src="{{ $user->foto_perfil ? asset('storage/' . $user->foto_perfil) : 'https://ui-avatars.com/api/?name=' . urlencode($user->nombres . '+' . $user->apellido_paterno) . '&background=2c9f49&color=fff&size=120' }}" 
                                 alt="Foto de perfil" 
                                 class="rounded-circle border border-3 border-success" 
                                 style="width: 120px; height: 120px; object-fit: cover;"
                                 id="fotoPerfil">
                            
                            {{-- Botón para cambiar foto --}}
                            <button class="btn btn-success btn-sm rounded-circle position-absolute bottom-0 end-0 p-1 border border-white" 
                                    style="width: 32px; height: 32px;"
                                    onclick="document.getElementById('inputFoto').click();"
                                    title="Cambiar foto">
                                <i class="bi bi-camera-fill" style="font-size: 14px;"></i>
                            </button>
                            
                            <form id="formFoto" method="POST" action="{{ route('perfil.cambiar.foto') }}" enctype="multipart/form-data" style="display:none;">
                                @csrf
                                <input type="file" id="inputFoto" name="foto" accept="image/*" onchange="document.getElementById('formFoto').submit();">
                            </form>
                        </div>
                        <h5 class="mt-2">{{ $user->nombres . ' ' . $user->apellido_paterno . ' ' . $user->apellido_materno }}</h5>
                        <span class="badge bg-success">{{ ucfirst($user->rol) }}</span>
                    </div>

                    {{-- Información personal (SOLO LECTURA) --}}
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Los datos personales no pueden ser modificados. Solo puedes cambiar tu contraseña o foto de perfil.
                    </div>

                    {{-- ============================================================ --}}
                    {{-- DATOS PERSONALES (SOLO LECTURA)                              --}}
                    {{-- ============================================================ --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombre(s)</label>
                            <input type="text" class="form-control bg-light" value="{{ $user->nombres }}" readonly disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Apellido Paterno</label>
                            <input type="text" class="form-control bg-light" value="{{ $user->apellido_paterno }}" readonly disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Apellido Materno</label>
                            <input type="text" class="form-control bg-light" value="{{ $user->apellido_materno }}" readonly disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Correo Electrónico</label>
                            <input type="email" class="form-control bg-light" value="{{ $user->email }}" readonly disabled>
                        </div>

                        {{-- Datos específicos según rol --}}
                        @if($user->rol === 'alumno')
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Matrícula</label>
                                <input type="text" class="form-control bg-light" value="{{ $datosAdicionales['matricula'] ?? 'N/A' }}" readonly disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Carrera</label>
                                <input type="text" class="form-control bg-light" value="{{ $datosAdicionales['carrera'] ?? 'N/A' }}" readonly disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Grupo</label>
                                <input type="text" class="form-control bg-light" value="{{ $datosAdicionales['grupo'] ?? 'N/A' }}" readonly disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Cuatrimestre</label>
                                <input type="text" class="form-control bg-light" value="{{ $datosAdicionales['cuatrimestre'] ?? 'N/A' }}" readonly disabled>
                            </div>
                        @elseif($user->rol === 'docente')
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Número de Empleado</label>
                                <input type="text" class="form-control bg-light" value="{{ $datosAdicionales['numero_empleado'] ?? 'N/A' }}" readonly disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Carrera</label>
                                <input type="text" class="form-control bg-light" value="{{ $datosAdicionales['carrera'] ?? 'N/A' }}" readonly disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Departamento</label>
                                <input type="text" class="form-control bg-light" value="{{ $datosAdicionales['departamento'] ?? 'N/A' }}" readonly disabled>
                            </div>
                        @endif
                    </div>

                    <hr class="my-4">

                    {{-- ============================================================ --}}
                    {{-- FORMULARIO PARA CAMBIAR CONTRASEÑA                          --}}
                    {{-- ============================================================ --}}
                    <h5 class="fw-semibold titulo-borde-verde mb-3">
                        <i class="bi bi-key me-2"></i>Cambiar Contraseña
                    </h5>

                    @if(session('success'))
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('perfil.cambiar.password') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="password_actual" class="form-label fw-semibold">Contraseña Actual</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                                <input type="password" class="form-control @error('password_actual') is-invalid @enderror" 
                                       id="password_actual" name="password_actual" required>
                            </div>
                            @error('password_actual')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold">Nueva Contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-key"></i></span>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password" required minlength="8">
                            </div>
                            <small class="text-muted">La contraseña debe tener al menos 8 caracteres.</small>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label fw-semibold">Confirmar Nueva Contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-check-circle"></i></span>
                                <input type="password" class="form-control" 
                                       id="password_confirmation" name="password_confirmation" required>
                            </div>
                        </div>

                        {{-- ============================================================ --}}
                        {{-- BOTONES VERDES DEL MISMO TAMAÑO                           --}}
                        {{-- ============================================================ --}}
                        <div class="d-flex justify-content-end gap-3 mt-4">
                            <a href="{{ route('dashboard') }}" class="btn-verde">
                                <i class="bi bi-arrow-left"></i> Volver
                            </a>
                            <button type="submit" class="btn-verde">
                                <i class="bi bi-save"></i> Actualizar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SweetAlert para éxito --}}
@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: '{{ session('success') }}',
            confirmButtonColor: '#28a745',
            confirmButtonText: 'Aceptar'
        });
    });
</script>
@endif

{{-- SweetAlert para errores --}}
@if($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let errores = '';
        @foreach($errors->all() as $error)
            errores += '• {{ $error }}<br>';
        @endforeach
        
        Swal.fire({
            icon: 'error',
            title: 'Errores en el formulario',
            html: errores,
            confirmButtonColor: '#28a745',
            confirmButtonText: 'Aceptar'
        });
    });
</script>

@endif

@endsection
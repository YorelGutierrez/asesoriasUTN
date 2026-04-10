@extends('Plantilla')

@section('contenido')

<link rel="stylesheet" href="{{ asset('estilos/titulos.css') }}">
<link rel="stylesheet" href="{{ asset('estilos/botones.css') }}">
<link rel="stylesheet" href="{{ asset('estilos/sweetalert.css') }}">

<div class="titulo">
    <h1>Registro de Docente</h1>
</div>

<div class="mt-4">
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body p-4">

            <form action="{{ route('docentes.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Foto de perfil --}}
                <div class="mb-3">
                    <label class="form-label">Foto de perfil</label>
                    <input type="file" class="form-control" name="foto_perfil" accept="image/*">
                </div>

                {{-- Fila 1: Nombres y Apellido paterno --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nombres <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nombres') is-invalid @enderror" name="nombres" placeholder="Nombres" value="{{ old('nombres') }}">
                        @error('nombres')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Apellido paterno <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('apellido_paterno') is-invalid @enderror" name="apellido_paterno" placeholder="Apellido paterno" value="{{ old('apellido_paterno') }}">
                        @error('apellido_paterno')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Fila 2: Apellido materno y Número de empleado --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Apellido materno</label>
                        <input type="text" class="form-control" name="apellido_materno" placeholder="Apellido materno" value="{{ old('apellido_materno') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Número de empleado <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('numero_empleado') is-invalid @enderror" name="numero_empleado" placeholder="Número de empleado" value="{{ old('numero_empleado') }}">
                        @error('numero_empleado')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Fila 3: Carrera y Correo electrónico --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Carrera <span class="text-danger">*</span></label>
                        <select class="form-control @error('carrera_id') is-invalid @enderror" name="carrera_id">
                            <option value="">Seleccionar carrera</option>
                            @foreach($carreras as $carrera)
                                <option value="{{ $carrera->id }}">
                                    {{ $carrera->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('carrera_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Correo electrónico <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" placeholder="Correo electrónico" value="{{ old('email') }}">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Fila 4: Teléfono y Fecha de nacimiento --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Teléfono <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control @error('telefono') is-invalid @enderror" name="telefono" placeholder="Teléfono (10 dígitos)" value="{{ old('telefono') }}" maxlength="10">
                        @error('telefono')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Fecha de nacimiento <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('fecha_nacimiento') is-invalid @enderror" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}" max="{{ date('Y-m-d') }}">
                        @error('fecha_nacimiento')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Fila 5: Contraseña y Confirmar contraseña --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Contraseña <span class="text-danger">*</span></label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Contraseña">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Confirmar contraseña <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" name="password_confirmation" placeholder="Confirmar contraseña">
                    </div>
                </div>

              {{-- Botones --}}
<div class="row mt-3 g-2">
    <div class="col-12 col-md-6">
        <button type="submit" class="btn-principal w-100" style="padding: 10px 20px; font-size: 16px;">
            Registrar
        </button>
    </div>
    <div class="col-12 col-md-6">
        <button type="button" class="btn-secundario w-100" style="padding: 10px 20px; font-size: 16px;" onclick="window.history.back();">
            Cancelar
        </button>
    </div>
</div>
            </form>

        </div>
    </div>
</div>

<script>
    // Alerta de éxito
    @if(session('success'))
    Swal.fire({
        icon: 'success',
        title: '¡Registrado!',
        text: '{{ session('success') }}',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Aceptar'
    });
    @endif

    // Alerta de error general
    @if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: '{{ session('error') }}',
        confirmButtonColor: '#d33',
        confirmButtonText: 'Aceptar'
    });
    @endif

    // Alerta de errores de validación
    @if($errors->any())
    Swal.fire({
        icon: 'error',
        title: 'Errores en el formulario',
        confirmButtonColor: '#d33',
        confirmButtonText: 'Aceptar'
    });
    @endif
</script>

@endsection
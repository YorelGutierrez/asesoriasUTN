@extends('Plantilla')

@section('contenido')
<link rel="stylesheet" href="{{ asset('estilos/titulos.css') }}">
<link rel="stylesheet" href="{{ asset('estilos/botones.css') }}">

<div class="titulo">
    <h1>Actualizar Docente</h1>
</div>

<div class="mt-4">
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body p-4">

            <form action="{{ route('docentes.update', $docente->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nombres <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nombres') is-invalid @enderror" name="nombres" value="{{ old('nombres', $docente->user->nombres) }}" required>
                        @error('nombres')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Apellido paterno <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('apellido_paterno') is-invalid @enderror" name="apellido_paterno" value="{{ old('apellido_paterno', $docente->user->apellido_paterno) }}" required>
                        @error('apellido_paterno')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Apellido materno</label>
                        <input type="text" class="form-control" name="apellido_materno" value="{{ old('apellido_materno', $docente->user->apellido_materno) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Número de empleado <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('numero_empleado') is-invalid @enderror" name="numero_empleado" value="{{ old('numero_empleado', $docente->numero_empleado) }}" required>
                        @error('numero_empleado')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Carrera <span class="text-danger">*</span></label>
                        <select class="form-control @error('carrera_id') is-invalid @enderror" name="carrera_id" required>
                            <option value="">Seleccionar carrera</option>
                            @foreach($carreras as $carrera)
                                <option value="{{ $carrera->id }}" {{ old('carrera_id', $docente->carrera_id) == $carrera->id ? 'selected' : '' }}>
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
                        <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $docente->user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Teléfono</label>
                        <input type="tel" class="form-control" name="telefono" value="{{ old('telefono', $docente->user->telefono) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Fecha de nacimiento</label>
                        <input type="date" class="form-control" name="fecha_nacimiento" value="{{ old('fecha_nacimiento', $docente->user->fecha_nacimiento ? \Carbon\Carbon::parse($docente->user->fecha_nacimiento)->format('Y-m-d') : '') }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Foto de perfil</label>
                        <input type="file" class="form-control" name="foto_perfil" accept="image/*">
                        @if($docente->user->foto_perfil)
                            <small class="text-muted">Archivo actual: {{ basename($docente->user->foto_perfil) }}</small>
                        @endif
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nueva contraseña (dejar en blanco para no cambiar)</label>
                        <input type="password" class="form-control" name="password" placeholder="Nueva contraseña">
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <button type="submit" class="btn-principal w-100 py-2">
                            <i class="bi bi-save me-2"></i> Actualizar Docente
                        </button>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ route('gestion') }}" class="btn-secundario w-100 py-2 d-block text-center">
                            <i class="bi bi-arrow-left me-2"></i> Cancelar
                        </a>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

@endsection
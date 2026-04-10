@extends('Plantilla')

@section('contenido')
<link rel="stylesheet" href="{{ asset('estilos/titulos.css') }}">
<link rel="stylesheet" href="{{ asset('estilos/botones.css') }}">

<div class="titulo">
    <h1>Actualizar Alumno</h1>
</div>

<div class="mt-4">
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body p-4">

            <form action="{{ route('alumnos.update', $alumno->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nombres <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nombres') is-invalid @enderror" name="nombres" value="{{ old('nombres', $alumno->user->nombres) }}" required>
                        @error('nombres')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Apellido paterno <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('apellido_paterno') is-invalid @enderror" name="apellido_paterno" value="{{ old('apellido_paterno', $alumno->user->apellido_paterno) }}" required>
                        @error('apellido_paterno')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Apellido materno</label>
                        <input type="text" class="form-control" name="apellido_materno" value="{{ old('apellido_materno', $alumno->user->apellido_materno) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Matrícula <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('matricula') is-invalid @enderror" name="matricula" value="{{ old('matricula', $alumno->matricula) }}" required>
                        @error('matricula')
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
                                <option value="{{ $carrera->id }}" {{ old('carrera_id', $alumno->carrera_id) == $carrera->id ? 'selected' : '' }}>
                                    {{ $carrera->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('carrera_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Grupo</label>
                        <select class="form-control @error('grupo_id') is-invalid @enderror" name="grupo_id">
                            <option value="">Seleccionar grupo</option>
                            @foreach($grupos as $grupo)
                                <option value="{{ $grupo->id }}" {{ old('grupo_id', $alumno->grupo_id) == $grupo->id ? 'selected' : '' }}>
                                    {{ $grupo->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('grupo_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Correo electrónico <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $alumno->user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Teléfono</label>
                        <input type="tel" class="form-control" name="telefono" value="{{ old('telefono', $alumno->user->telefono) }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Fecha de nacimiento</label>
                        <input type="date" class="form-control" name="fecha_nacimiento" value="{{ old('fecha_nacimiento', $alumno->user->fecha_nacimiento ? \Carbon\Carbon::parse($alumno->user->fecha_nacimiento)->format('Y-m-d') : '') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Cuatrimestre</label>
                        <input type="number" class="form-control" name="cuatrimestre" value="{{ old('cuatrimestre', $alumno->cuatrimestre) }}" min="1" max="12">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Foto de perfil</label>
                        <input type="file" class="form-control" name="foto_perfil" accept="image/*">
                        @if($alumno->user->foto_perfil)
                            <small class="text-muted">Archivo actual: {{ basename($alumno->user->foto_perfil) }}</small>
                        @endif
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nueva contraseña (dejar en blanco para no cambiar)</label>
                        <input type="password" class="form-control" name="password" placeholder="Nueva contraseña">
                    </div>
                </div>

                {{-- BOTONES CON ESPACIADO CORRECTO --}}
                <div class="row mt-4 g-3">
                    <div class="col-12 col-md-6">
                        <button type="submit" class="btn-principal w-100 py-2">
                            <i class="bi bi-save me-2"></i> Actualizar Alumno
                        </button>
                    </div>
                    <div class="col-12 col-md-6">
                        <a href="{{ route('gestion', ['tab' => 'alumnos']) }}" class="btn-secundario w-100 py-2 d-block text-center">
                            <i class="bi bi-arrow-left me-2"></i> Cancelar
                        </a>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

@endsection
@extends('Plantilla')

@section('contenido')

<link rel="stylesheet" href="{{ asset('estilos/titulos.css') }}">
<link rel="stylesheet" href="{{ asset('estilos/botones.css') }}">

<div class="titulo">
    <h1>Registro de Docente</h1>
</div>

<div class="mt-4">
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body p-4">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('docentes.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Tus campos aquí --}}
                <div class="mb-3 input-group">
                    <span class="input-group-text"><i class="bi bi-image-fill"></i></span>
                    <input type="file" class="form-control" name="foto_perfil" accept="image/*">
                </div>

                <div class="mb-3 input-group">
                    <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                    <input type="text" class="form-control" name="nombres" placeholder="Nombres" value="{{ old('nombres') }}" required>
                </div>

                <div class="mb-3 input-group">
                    <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                    <input type="text" class="form-control" name="apellido_paterno" placeholder="Apellido paterno" value="{{ old('apellido_paterno') }}" required>
                </div>

                <div class="mb-3 input-group">
                    <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                    <input type="text" class="form-control" name="apellido_materno" placeholder="Apellido materno" value="{{ old('apellido_materno') }}">
                </div>

                <div class="mb-3 input-group">
                    <span class="input-group-text"><i class="bi bi-hash"></i></span>
                    <input type="text" class="form-control" name="numero_empleado" placeholder="Número de empleado" value="{{ old('numero_empleado') }}" required>
                </div>

                <div class="mb-3 input-group">
                    <span class="input-group-text"><i class="bi bi-building"></i></span>
                    <select class="form-control" name="carrera" required>
                        <option value="">Seleccionar carrera</option>
                        @foreach($carreras as $carrera)
                            <option value="{{ $carrera->id }}" {{ old('carrera') == $carrera->id ? 'selected' : '' }}>
                                {{ $carrera->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3 input-group">
                    <span class="input-group-text"><i class="bi bi-book-fill"></i></span>
                    <select class="form-control" name="materia">
                        <option value="">Seleccionar materia</option>
                        @foreach($materias as $materia)
                            <option value="{{ $materia->id }}" {{ old('materia') == $materia->id ? 'selected' : '' }}>
                                {{ $materia->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3 input-group">
                    <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                    <input type="email" class="form-control" name="email" placeholder="Correo electrónico (@utnay.edu.mx)" value="{{ old('email') }}" required>
                </div>

                <div class="mb-3 input-group">
                    <span class="input-group-text"><i class="bi bi-telephone-fill"></i></span>
                    <input type="text" class="form-control" name="telefono" placeholder="Teléfono (10 dígitos)" value="{{ old('telefono') }}">
                </div>

                <div class="mb-3 input-group">
                    <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                    <input type="date" class="form-control" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}">
                </div>

                <div class="mb-3 input-group">
                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" class="form-control" name="password" placeholder="Contraseña (mín. 8 caracteres, mayúscula, minúscula y número)" required>
                </div>

                <div class="mb-3 input-group">
                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" class="form-control" name="password_confirmation" placeholder="Confirmar contraseña" required>
                </div>

                <div class="row mt-3 g-2">
                    <div class="col-12 col-md-6">
                        <button type="submit" class="btn-principal w-100">Registrar</button>
                    </div>
                    <div class="col-12 col-md-6">
                        <button type="button" class="btn-secundario w-100" onclick="window.history.back();">Cancelar</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

@endsection
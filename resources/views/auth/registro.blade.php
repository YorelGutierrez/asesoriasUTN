<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSKLc3LGB1aSLdgvmI7TwAd0-rJLeTNqExKUw&s">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .bg-design {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .btn-login {
            background: #667eea;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            font-size: 16px;
            margin-bottom: 10px;
        }

        .btn-login:hover {
            background: #5a67d8;
        }
    </style>
</head>

<body>
    <div class="container-fluid vh-100">
        <div class="row h-100">
            <!-- Sección izquierda -->
            <div class="col-md-7 d-none d-md-flex align-items-center justify-content-center bg-design">
                <div class="text-center">
                    <h1 class="text-white">Diseño mamalon</h1>
                </div>
            </div>

            <!-- Sección derecha -->
            <div class="col-md-5 d-flex align-items-center justify-content-center bg-light">
                <div class="w-75">
                    <h2 class="mb-3">Nombre del proyecto <img src="https://i.pinimg.com/originals/57/61/5b/57615b8c0092a66c1d4058b1692955cc.gif" alt="Logo" style="height: 50px;"></h2>
                    <p class="mb-4">¡Crea tu cuenta!</p>
                    <h3>Registro</h3>

                    <form method="POST" enctype="multipart/form-data" action="{{ route('registro.procesar') }}">
                        @csrf

                        <div class="mb-3 input-group">
                            <span class="input-group-text"><i class="bi bi-image-fill"></i></span>
                            <input type="file" class="form-control" name="foto_perfil" accept="image/*">
                        </div>

                        <div class="mb-3 input-group">
                            <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                            <input type="text" class="form-control" name="nombres" placeholder="Nombres" value="{{ old('nombres') }}" required> <!-- ← AGREGAR old() -->
                        </div>

                        <div class="mb-3 input-group">
                            <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                            <input type="text" class="form-control" name="apellido_paterno" placeholder="Apellido paterno" value="{{ old('apellido_paterno') }}" required> <!-- ← AGREGAR old() -->
                        </div>

                        <div class="mb-3 input-group">
                            <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                            <input type="text" class="form-control" name="apellido_materno" placeholder="Apellido materno" value="{{ old('apellido_materno') }}"> <!-- ← AGREGAR old() -->
                        </div>

                        <div class="mb-3 input-group">
                            <span class="input-group-text"><i class="bi bi-at"></i></span>
                            <input type="text" class="form-control" name="nickname" placeholder="Nickname (opcional)" value="{{ old('nickname') }}"> <!-- ← AGREGAR old() -->
                        </div>

                        <div class="mb-3 input-group">
                            <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                            <input type="email" class="form-control" name="email" placeholder="Correo electrónico" value="{{ old('email') }}" required> <!-- ← AGREGAR old() -->
                        </div>

                        <div class="mb-3 input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" class="form-control" name="password" placeholder="Contraseña (mín. 8 caracteres)" required>
                        </div>

                        <div class="mb-3 input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" class="form-control" name="password_confirmation" placeholder="Confirmar contraseña" required>
                        </div>

                        <div class="mb-3 input-group">
                            <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                            <input type="date" class="form-control" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}"> <!-- ← AGREGAR old() -->
                        </div>

                        <div class="mb-3 input-group">
                            <span class="input-group-text"><i class="bi bi-telephone-fill"></i></span>
                            <input type="text" class="form-control" name="telefono" placeholder="Teléfono" value="{{ old('telefono') }}"> <!-- ← AGREGAR old() -->
                        </div>

                        <button type="submit" class="btn-login w-100">Registrarse</button>
                    </form>

                    <hr>
                    <div class="mt-3 text-center">
                        <p>¿Ya tienes cuenta? <a href="#">Inicia sesión</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
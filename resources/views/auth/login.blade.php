<!DOCTYPE html>
<html lang="es">
<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Metadatos -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sitio administrativo y gestion de tutorias para la Univesidad Tecnologica de Nayarit.">
    <meta name="keywords" content="Administracion, Gestion, UT, Nayarit, Universidad, Tutorias, Educación">
    <meta name="author" content="Corona Alain, Bernal Brandon, Gonzales Rubi, Gutierrez Yorel, Rivera Vanessa, Samaniego Andy">
    <!-- Titulos de la pagina -->
    <title>@yield('titulo', 'Asesorias-UTN')</title>
    <!-- Link del icono en el navegador -->
    <link rel="icon" type="image/x-icon" href="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSKLc3LGB1aSLdgvmI7TwAd0-rJLeTNqExKUw&s">
    <!-- Links aparte -->
    <link rel="stylesheet" href="estilos/Login.css">
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
                    <p class="mb-4">Bienvenido! Es momento de iniciar sesión</p>
                    <h3>Iniciar Sesión</h3>

                    <form method="POST" action="{{ route('login.procesar') }}">
                        @csrf
                        
                        <div class="mb-3 input-group">
                            <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                            <input type="email" class="form-control" name="email" placeholder="Correo electrónico" required>
                        </div>

                        <div class="mb-3 input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" class="form-control" name="password" placeholder="Contraseña" required>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="remember" id="recordar">
                            <label class="form-check-label" for="recordar">Recuérdame</label>
                        </div>

                        <button type="submit" class="btn-login w-100">Iniciar sesión</button>
                    </form>

                    <hr>
                    <div class="mt-3 text-center">
                        <p>¿No tienes una cuenta? <a href="{{ route('register') }}">Crea una aquí</a></p>
                        <a href="#">Recuperar contraseña</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
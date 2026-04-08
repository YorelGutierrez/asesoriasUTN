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
    <meta name="description" content="Sitio administrativo y gestion de tutorias para la Universidad Tecnologica de Nayarit.">
    <meta name="keywords" content="Administracion, Gestion, UT, Nayarit, Universidad, Tutorias, Educación">
    <meta name="author" content="Corona Alain, Bernal Brandon, Gonzales Rubi, Gutierrez Yorel, Rivera Vanessa, Samaniego Andy">
    
    <title>Asesorías UTN</title>
    
    <link rel="stylesheet" href="{{ asset('estilos/Login.css') }}">
    <link rel="stylesheet" href="{{ asset('estilos/titulos.css') }}">
    <link rel="stylesheet" href="{{ asset('estilos/botones.css') }}">
    <link rel="icon" type="image/x-icon" href="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSKLc3LGB1aSLdgvmI7TwAd0-rJLeTNqExKUw&s">
    
    <!-- SweetAlerts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    
</head>
<body>
    <div class="container-fluid vh-100">
    <div class="row h-100">
        <!-- Sección izquierda -->
<div class="col-md-7 d-none d-md-flex align-items-center justify-content-center bg-design position-relative overflow-hidden">
    
    <!-- Círculos decorativos -->
    <div class="position-absolute top-0 start-0" style="width: 200px; height: 200px; background: rgba(255,255,255,0.05); border-radius: 50%; transform: translate(-30%, -30%);"></div>
    <div class="position-absolute bottom-0 end-0" style="width: 300px; height: 300px; background: rgba(255,255,255,0.05); border-radius: 50%; transform: translate(30%, 30%);"></div>
    
    <div class="text-center graphic-placeholder position-relative z-1">
        <!-- Logo / Icono principal -->
        <div class="bg-white-transparent-20 rounded-4 d-inline-flex p-4 mb-4 shadow-lg">
            <i class="bi bi-mortarboard-fill display-1 text-white"></i>
        </div>
        
        <!-- Título -->
        <h1 class="text-white display-4 fw-bold mb-3">Asesorías UTN</h1>
        
        <!-- Línea decorativa -->
        <div class="d-flex justify-content-center gap-2 mb-4">
            <div class="separator-line"></div>
            <div class="separator-line-sm"></div>
            <div class="separator-line-xs"></div>
        </div>
        
        <!-- Descripción -->
        <p class="text-white lead mb-4">Sistema de gestión académica</p>
        
        <!-- Características -->
        <div class="row g-3 mt-4">
            <div class="col-6">
                <div class="bg-white-transparent-15 rounded-3 p-3">
                    <i class="bi bi-calendar-check fs-3 text-white"></i>
                    <p class="text-white small mb-0 mt-2">Agenda tu asesoría</p>
                </div>
            </div>
            <div class="col-6">
                <div class="bg-white-transparent-15 rounded-3 p-3">
                    <i class="bi bi-people fs-3 text-white"></i>
                    <p class="text-white small mb-0 mt-2">Seguimiento personalizado</p>
                </div>
            </div>
            <div class="col-6">
                <div class="bg-white-transparent-15 rounded-3 p-3">
                    <i class="bi bi-graph-up fs-3 text-white"></i>
                    <p class="text-white small mb-0 mt-2">Progreso académico</p>
                </div>
            </div>
            <div class="col-6">
                <div class="bg-white-transparent-15 rounded-3 p-3">
                    <i class="bi bi-chat-dots fs-3 text-white"></i>
                    <p class="text-white small mb-0 mt-2">Comunicación directa</p>
                </div>
            </div>
        </div>
        
        <!-- Universidad -->
        <div class="mt-5 pt-3">
            <p class="text-white-50 small mb-0">
                <i class="bi bi-building me-1"></i> Universidad Tecnológica de Nayarit
            </p>
        </div>
    </div>
</div>

            <!-- Sección derecha -->
<div class="col-md-5 d-flex align-items-center justify-content-center bg-white">
    <div class="w-75">
        <div class="text-center mb-4">
            <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                <i class="bi bi-mortarboard-fill display-6 text-success"></i>
            </div>
            <h2 class="fw-bold text-success">Asesorías UTN</h2>
            <p class="text-muted">Bienvenido. Inicia sesión para continuar</p>
        </div>

        <form method="POST" action="{{ route('login.procesar') }}">
            @csrf
            
            <div class="mb-3">
                <label class="form-label fw-semibold text-secondary">
                    <i class="bi bi-envelope-fill me-1 text-success"></i> Correo electrónico
                </label>
                <input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" 
                       name="email" placeholder="correo@utnay.edu.mx" value="{{ old('email') }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold text-secondary">
                    <i class="bi bi-lock-fill me-1 text-success"></i> Contraseña
                </label>
                <input type="password" class="form-control form-control-lg" 
                       name="password" placeholder="Ingresa tu contraseña" required>
            </div>

            <button type="submit" class="btn-principal">
                <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar sesión
            </button>
        </form>
            </div>
        </div>
    </div>
    @if(session('error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Cuenta bloqueada',
            text: '{{ session('error') }}',
            confirmButtonColor: '#3FD4A4',
            confirmButtonText: 'Aceptar'
        });
    </script>
    @endif

    @if(session('warning'))
        <script>
            Swal.fire({
                icon: 'warning',
                title: 'Atención',
                text: '{{ session('warning') }}',
                confirmButtonColor: '#ffc107',
                confirmButtonText: 'Entendido'
            });
        </script>
        @endif

    @if($errors->has('email') && !session('error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '{{ $errors->first('email') }}',
            confirmButtonColor: '#3FD4A4'
        });
    </script>
    @endif
</body>
</html>
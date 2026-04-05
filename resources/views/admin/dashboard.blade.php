@extends('Plantilla')

@section('contenido')
<link rel="stylesheet" href="{{ asset('estilos/botones.css') }}">
<link rel="stylesheet" href="{{ asset('estilos/titulos.css') }}">

<div class="titulo">
    <h1>Bienvenido <span id="nombreUsuario">...</span></h1>
</div>

<div class="row mb-4 align-items-stretch">
    <div class="col-md-4">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-3">Total de usuarios</h5>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fw-semibold">Administradores</div>
                        </div>
                        <span class="badge rounded-pill text-bg-success"><i class="bi bi-person-fill"></i> 3</span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fw-semibold">Docentes</div>
                        </div>
                        <span class="badge rounded-pill text-bg-success"><i class="bi bi-person-fill"></i> 8</span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fw-semibold">Tutores</div>
                        </div>
                        <span class="badge rounded-pill text-bg-success"><i class="bi bi-person-fill"></i> 5</span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fw-semibold">Alumnos</div>
                        </div>
                        <span class="badge rounded-pill text-bg-success"><i class="bi bi-person-fill"></i> 120</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- RESPALDOS -->
    <div class="col-md-8">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-4">Respaldos del sistema</h5>

                @if($ultimo)
                <p><strong>Último respaldo:</strong> {{ $ultimo['fecha'] }}</p>
                <p><strong>Estado:</strong> <span class="text-success">Correcto</span></p>
                @else
                <p><strong>Último respaldo:</strong> No hay respaldos</p>
                <p><strong>Estado:</strong> <span class="text-danger">Sin respaldos</span></p>
                @endif

                @if($horaProgramada)
                <p><strong>Programado para:</strong> {{ $horaProgramada }}</p>
                @endif

                <div class="row g-2 mt-3">
                    <div class="col d-flex">
                        <form action="{{ route('respaldo.generar') }}" method="POST" class="w-100">
                            @csrf
                            <button class="btn-principal w-100 h-100">Generar respaldo</button>
                        </form>
                    </div>
                    <div class="col d-flex">
                        <button class="btn-secundario w-100 h-100" onclick="toggleCalendar()">Programar</button>
                    </div>
                </div>

                <div id="calendarBox" class="mt-3 d-none">
                    <input type="datetime-local" id="fechaHora" class="form-control">
                    <div class="d-flex gap-2 mt-2">
                        <button class="btn btn-danger w-50 rounded-pill" onclick="cerrarCalendario()">Cancelar</button>
                        <button class="btn-principal w-50" onclick="guardarProgramacion()">Confirmar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4 align-items-stretch">
    <!-- BITÁCORA -->
    <div class="col-md-6">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-body p-4 d-flex flex-column">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-semibold mb-0">Bitácora de actividad</h5>
                    <button id="btnLimpiarLogs" class="btn btn-danger btn-sm rounded-pill">Limpiar todo</button>
                </div>
                <div id="bitacora" class="flex-grow-1 pe-2" style="height: 255px; overflow-y: auto; display: block;">
                    <div class="text-center text-muted py-3">Cargando...</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ASIGNACIÓN ACADÉMICA -->
<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">
        <h5 class="fw-semibold mb-4">Asignación académica | Asignaciones actuales</h5>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Docente</th>
                        <th>Clave</th>
                        <th>Carreras</th>
                        <th>Materias</th>
                        <th>Grupos</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Juan Manuel Tovar Sanchez</td>
                        <td>19cJICP</td>
                        <td>Ingenieria en desarrollo de software</td>
                        <td>Programación</td>
                        <td>IDGS-84</td>
                        <td>
                            <button class="btn btn-outline-warning btn-sm">Editar</button>
                            <button class="btn btn-outline-danger btn-sm">Eliminar</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mt-3 d-flex justify-content-end">
            <button class="btn-principal">+ Nueva asignación</button>
        </div>
    </div>
</div>
<script src="{{ asset('js/logs.js') }}"></script>
<script>
function cargarLogs() {
    fetch('/api/logs', {
        headers: {
            'Authorization': 'Bearer ' + getCookie('jwt_token'),
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(logs => {
        const container = document.getElementById('bitacora');
        if (!container) return;
        container.innerHTML = '';

        // LIMITAR A 3 REGISTROS VISIBLES (el resto se verá con scroll)
        const logsMostrar = logs.slice(0, 5);

        logsMostrar.forEach(log => {
            let nombreUsuario = 'Sistema';
            let fotoUrl = 'https://ui-avatars.com/api/?name=Sistema&background=e9ecef&color=343a40';
            if (log.user) {
                const nombres = [log.user.nombres, log.user.apellido_paterno, log.user.apellido_materno].filter(Boolean).join(' ');
                nombreUsuario = nombres || 'Usuario';
                fotoUrl = log.user.foto_perfil ? log.user.foto_perfil : `https://ui-avatars.com/api/?name=${encodeURIComponent(nombreUsuario)}&background=e9ecef&color=343a40`;
            }

            const item = document.createElement('div');
            item.className = "d-flex align-items-start mb-3 border-bottom pb-2";
            item.innerHTML = `
                <img src="${fotoUrl}" class="rounded-circle me-3 mt-1" width="36" height="36">
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-semibold" style="font-size: 0.9rem;">${escapeHtml(nombreUsuario)}</span>
                        <small class="text-black-50" style="font-size: 0.75rem;">${formatearFecha(log.created_at)}</small>
                    </div>
                    <p class="mb-0 text-muted lh-sm" style="font-size: 0.85rem;">${escapeHtml(log.descripcion ?? 'Sin descripción')}</p>
                </div>
                <button class="btn btn-sm text-danger p-0 ms-2 mt-1 border-0 eliminar-log" data-id="${log.id}" title="Eliminar registro">
                    <i class="bi bi-trash3"></i>
                </button>
            `;

            const deleteBtn = item.querySelector('.eliminar-log');
            deleteBtn.addEventListener('click', () => {
                const logId = deleteBtn.dataset.id;
                fetch(`/api/logs/${logId}`, {
                    method: 'DELETE',
                    headers: {
                        'Authorization': 'Bearer ' + getCookie('jwt_token'),
                        'Accept': 'application/json'
                    }
                })
                .then(() => item.remove())
                .catch(err => console.error(err));
            });

            container.appendChild(item);
        });
    })
    .catch(err => console.error(err));
}

document.addEventListener('DOMContentLoaded', cargarLogs);
</script>

<script>
    window.respaldoAutomaticoUrl = "{{ route('respaldo.automatico.store') }}";
    window.csrfToken = "{{ csrf_token() }}";
</script>
<script src="{{ asset('js/ajax-respaldos.js') }}"></script>

<script>
    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
    }

    fetch('/api/me', {
        headers: {
            'Authorization': 'Bearer ' + getCookie('jwt_token')
        }
    })
    .then(res => res.json())
    .then(data => {
        console.log('JWT funcionando:', data);
        document.getElementById('nombreUsuario').innerText = data.nombres;
    })
    .catch(err => {
        console.error('Error JWT:', err);
        document.getElementById('nombreUsuario').innerText = 'Administrador';
    });
</script>

@endsection
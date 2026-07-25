function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
}

function formatearFecha(fecha) {
    const ahora = new Date();
    const f = new Date(fecha);
    const diff = Math.floor((ahora - f) / 1000);
    if (diff < 60) return "Hace unos segundos";
    if (diff < 3600) return "Hace " + Math.floor(diff / 60) + " min";
    if (diff < 86400) return "Hace " + Math.floor(diff / 3600) + " hrs";
    if (diff < 172800) return "Ayer";
    return f.toLocaleDateString();
}

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    });
}

function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content || '';
}

function cargarLogs() {
    fetch('/api/logs', {
        headers: {
            'Authorization': 'Bearer ' + getCookie('jwt_token'),
            'Accept': 'application/json'
        }
    })
    .then(res => {
        if (!res.ok) {
            console.error('Error HTTP:', res.status);
            return;
        }
        return res.json();
    })
    .then(logs => {
        const container = document.getElementById('bitacora');
        if (!container) return;
        container.innerHTML = '';

        logs.forEach(log => {
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

            // Eliminación individual: llamar al backend con DELETE
            const deleteBtn = item.querySelector('.eliminar-log');
            deleteBtn.addEventListener('click', () => {
                const logId = deleteBtn.dataset.id;
                fetch(`/api/logs/${logId}`, {
                    method: 'DELETE',
                    headers: {
                        'Authorization': 'Bearer ' + getCookie('jwt_token'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    }
                })
                .then(res => {
                    if (!res.ok) throw new Error('Error al eliminar log');
                    return res.json();
                })
                .then(() => {
                    item.remove();  // Eliminar visualmente tras confirmación
                })
                .catch(err => console.error(err));
            });

            container.appendChild(item);
        });
    })
    .catch(err => console.error(err));
}

// Botón "Limpiar todo"
document.getElementById('btnLimpiarLogs')?.addEventListener('click', function() {
    if (confirm('¿Estás seguro de que deseas eliminar TODOS los logs? Esta acción no se puede deshacer.')) {
        fetch('/api/logs', {
            method: 'DELETE',
            headers: {
                'Authorization': 'Bearer ' + getCookie('jwt_token'),
                'Accept': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            }
        })
        .then(res => {
            if (!res.ok) throw new Error('Error al limpiar logs');
            return res.json();
        })
        .then(() => {
            cargarLogs();  // Recargar lista (vacía)
        })
        .catch(err => console.error(err));
    }
});

document.addEventListener('DOMContentLoaded', cargarLogs);
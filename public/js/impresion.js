// ============================================================
// FUNCIONES DE SELECCIÓN Y GENERACIÓN DE PDF
// ============================================================

function actualizarContador() {
    var checks = document.querySelectorAll('.seleccion-grafica:checked');
    var contador = document.getElementById('contadorSeleccionadas');
    if (contador) {
        var total = checks.length;
        contador.textContent = total + ' seleccionada' + (total !== 1 ? 's' : '');
    }
}

function seleccionarTodas() {
    document.querySelectorAll('.seleccion-grafica').forEach(function(cb) {
        cb.checked = true;
    });
    actualizarContador();
}

function deseleccionarTodas() {
    document.querySelectorAll('.seleccion-grafica').forEach(function(cb) {
        cb.checked = false;
    });
    actualizarContador();
}

function imprimirSeleccionadas() {
    var checks = document.querySelectorAll('.seleccion-grafica:checked');
    
    if (checks.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Selecciona gráficas',
            text: 'Debes seleccionar al menos una gráfica para generar el PDF.',
            confirmButtonColor: '#28a745',
            confirmButtonText: 'Aceptar'
        });
        return;
    }

    Swal.fire({
        icon: 'question',
        title: 'Generar PDF',
        text: '¿Generar PDF con las ' + checks.length + ' gráfica' + (checks.length !== 1 ? 's' : '') + ' seleccionada' + (checks.length !== 1 ? 's' : '') + '?',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#28a745',
        confirmButtonText: 'Sí, generar PDF',
        cancelButtonText: 'Cancelar'
    }).then(function(result) {
        if (result.isConfirmed) {
            var ids = [];
            checks.forEach(function(cb) {
                ids.push(cb.value);
            });
            
            Swal.fire({
                title: 'Generando PDF...',
                text: 'Por favor espera',
                allowOutsideClick: false,
                didOpen: function() {
                    Swal.showLoading();
                }
            });
            
            // Obtener el token CSRF
            var metaToken = document.querySelector('meta[name="csrf-token"]');
            var token = metaToken ? metaToken.getAttribute('content') : '';
            
            // Determinar la URL según la URL actual
            var url = '';
            var path = window.location.pathname;
            
            if (path.includes('/admin/')) {
                url = '/imprimir-graficas-admin-pdf';
            } else if (path.includes('/docente/')) {
                url = '/imprimir-graficas-docente-pdf';
            } else {
                // Fallback: usar la ruta genérica
                url = '/imprimir-graficas-pdf';
            }
            
            // Usar fetch para descargar el PDF
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ ids: ids })
            })
            .then(function(response) {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor: ' + response.status);
                }
                return response.blob();
            })
            .then(function(blob) {
                var downloadUrl = window.URL.createObjectURL(blob);
                var a = document.createElement('a');
                a.href = downloadUrl;
                a.download = 'graficas_' + new Date().getTime() + '.pdf';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                window.URL.revokeObjectURL(downloadUrl);
                
                // Limpiar la URL del historial (eliminar cualquier ruta de PDF)
                window.history.replaceState(null, '', window.location.pathname);
                
                Swal.fire({
                    icon: 'success',
                    title: '¡PDF generado!',
                    text: 'El PDF se ha descargado correctamente.',
                    confirmButtonColor: '#28a745',
                    confirmButtonText: 'Aceptar'
                });
            })
            .catch(function(error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al generar el PDF: ' + error.message,
                    confirmButtonColor: '#28a745',
                    confirmButtonText: 'Aceptar'
                });
            });
        }
    });
}

// Inicialización
document.addEventListener('DOMContentLoaded', function() {
    actualizarContador();
    
    document.querySelectorAll('.seleccion-grafica').forEach(function(cb) {
        cb.addEventListener('change', actualizarContador);
    });
});
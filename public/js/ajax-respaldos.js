function toggleCalendar() {
        const box = document.getElementById('calendarBox');
        box.classList.toggle('d-none');
    }

    function cerrarCalendario() {
        document.getElementById('calendarBox').classList.add('d-none');
    }

function guardarProgramacion() {
    let fechaHora = document.getElementById('fechaHora').value;
    if (!fechaHora) return alert('Selecciona fecha y hora');

    const btn = event.target;
    const originalText = btn.innerText;
    btn.innerText = 'Guardando...';
    btn.disabled = true;

    fetch(window.respaldoAutomaticoUrl, {
        method: "POST",
        headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": window.csrfToken },
        body: JSON.stringify({ fecha: fechaHora })
    })
    .then(res => res.json())
    .then(() => {
        alert('Respaldo programado correctamente');
        location.reload();
    })
    .catch(err => {
        alert('Error al programar');
        console.error(err);
    })
    .finally(() => {
        btn.innerText = originalText;
        btn.disabled = false;
    });
}

// ===== RESTAURAR RESPALDO =====
function mostrarListaRespaldos() {
    Swal.fire({
        title: 'Selecciona un respaldo',
        html: '<div id="listaRespaldos" class="text-start">Cargando respaldos...</div>',
        showCancelButton: true,
        confirmButtonText: 'Restaurar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        preConfirm: () => {
            const selected = document.querySelector('input[name="respaldo"]:checked');
            if (!selected) {
                Swal.showValidationMessage('Selecciona un archivo de respaldo');
                return false;
            }
            return selected.value;
        }
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            restaurarRespaldo(result.value);
        }
    });

    fetch(window.respaldoListarUrl)
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('listaRespaldos');
            if (!data || data.length === 0) {
                container.innerHTML = '<p class="text-muted text-center">No hay respaldos disponibles.</p>';
                return;
            }
            let html = '<ul class="list-group">';
            data.forEach((respaldo, index) => {
                html += `
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="respaldo" value="${respaldo.nombre}" id="respaldo_${index}">
                            <label class="form-check-label" for="respaldo_${index}">
                                <strong>${respaldo.nombre}</strong><br>
                                <small class="text-muted">${respaldo.fecha} - ${respaldo.tamano}</small>
                            </label>
                        </div>
                    </li>
                `;
            });
            html += '</ul>';
            container.innerHTML = html;
        })
        .catch(() => {
            document.getElementById('listaRespaldos').innerHTML = '<p class="text-danger text-center">Error al cargar respaldos.</p>';
        });
}

function restaurarRespaldo(archivo) {
    Swal.fire({
        title: '¿Restaurar respaldo?',
        text: `Se restaurará la base de datos desde el archivo: ${archivo}. Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, restaurar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Restaurando...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            // 🔥 USAR LA VARIABLE GLOBAL
            fetch(window.respaldoRestaurarUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.csrfToken
                },
                body: JSON.stringify({ archivo: archivo })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Restaurado!',
                        text: data.message,
                        confirmButtonColor: '#3085d6'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Error al restaurar el respaldo.',
                        confirmButtonColor: '#d33'
                    });
                }
            })
            .catch(() => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No se pudo conectar con el servidor.',
                    confirmButtonColor: '#d33'
                });
            });
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const STORE_URL = window.rutas.asesoriaStore;
    const REPORTE_URL = window.rutas.asesoriaReporte;
    const EXPEDIENTE_URL = window.rutas.expedienteAlumnos;
    const ALUMNOS_URL = window.rutas.alumnos;
    const CSRF_TOKEN = window.csrfToken;

    const btnGuardar = document.getElementById('btnGuardar');
    const form = document.getElementById('formAsesoria');

    btnGuardar.addEventListener('click', function(e) {
        e.preventDefault();

        const alumnosSeleccionados = document.querySelectorAll('.seleccionar-alumno:checked');
        if (alumnosSeleccionados.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Selecciona al menos un alumno',
                text: 'Debes seleccionar al menos un alumno para la asesoría.',
                confirmButtonColor: '#3085d6'
            });
            return;
        }

        Swal.fire({
            title: '¿Guardar asesoría?',
            text: '¿Estás seguro de que quieres registrar esta asesoría?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#2c9f49',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, guardar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Guardando asesoría...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                const formData = new FormData(form);

                fetch(STORE_URL, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: '¿Generar reporte?',
                            text: '¿Quieres descargar el reporte de esta asesoría ahora o después?',
                            icon: 'question',
                            showDenyButton: true,
                            confirmButtonText: 'Ahora',
                            denyButtonText: 'Después',
                            cancelButtonText: 'No generar',
                            showCancelButton: true,
                            confirmButtonColor: '#2c9f49',
                            denyButtonColor: '#6c757d',
                            cancelButtonColor: '#d33'
                        }).then((pdfResult) => {
                            Swal.fire({
                                title: 'Generando reporte...',
                                allowOutsideClick: false,
                                didOpen: () => Swal.showLoading()
                            });

                            const descargar = pdfResult.isConfirmed ? 1 : 0;
                            const generarSiempre = pdfResult.isConfirmed || pdfResult.isDenied;

                            if (generarSiempre) {
                                fetch(REPORTE_URL, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': CSRF_TOKEN
                                    },
                                    body: JSON.stringify({
                                        sesion_id: data.sesion_id,
                                        descargar: descargar
                                    })
                                })
                                .then(response => {
                                    if (descargar) {
                                        return response.blob();
                                    } else {
                                        return response.json();
                                    }
                                })
                                .then(pdfData => {
                                    if (descargar) {
                                        const url = window.URL.createObjectURL(pdfData);
                                        const a = document.createElement('a');
                                        a.href = url;
                                        a.download = 'reporte_asesoria_' + data.sesion_id + '.pdf';
                                        document.body.appendChild(a);
                                        a.click();
                                        a.remove();
                                        window.URL.revokeObjectURL(url);
                                    }

                                    Swal.fire({
                                        icon: 'success',
                                        title: '¡Asesoría registrada!',
                                        text: 'El registro se ha guardado correctamente.',
                                        confirmButtonColor: '#2c9f49',
                                        confirmButtonText: 'Aceptar'
                                    }).then(() => {
                                        if (data.tipo_asesoria === 'individual') {
                                            window.location.href = EXPEDIENTE_URL.replace('/0', '/' + data.primer_alumno_id);
                                        } else {
                                            window.location.href = ALUMNOS_URL;
                                        }
                                    });
                                })
                                .catch(() => {
                                    Swal.fire('Error', 'Hubo un problema al generar el PDF', 'error');
                                });
                            } else {
                                Swal.fire({
                                    icon: 'success',
                                    title: '¡Asesoría registrada!',
                                    text: 'El registro se ha guardado correctamente.',
                                    confirmButtonColor: '#2c9f49',
                                    confirmButtonText: 'Aceptar'
                                }).then(() => {
                                    if (data.tipo_asesoria === 'individual') {
                                        window.location.href = EXPEDIENTE_URL.replace('/0', '/' + data.primer_alumno_id);
                                    } else {
                                        window.location.href = ALUMNOS_URL;
                                    }
                                });
                            }
                        });
                    } else {
                        Swal.fire('Error', data.message || 'Error al guardar la asesoría', 'error');
                    }
                })
                .catch(() => {
                    Swal.fire('Error', 'Error de conexión al guardar la asesoría', 'error');
                });
            }
        });
    });

    // ===== BUSCADOR =====
    const buscarInput = document.getElementById('buscarAlumno');
    const tabla = document.getElementById('tablaAlumnos');
    const filas = tabla ? tabla.querySelectorAll('tbody tr') : [];

    if (buscarInput) {
        buscarInput.addEventListener('input', function() {
            const term = this.value.toLowerCase().trim();
            filas.forEach(fila => {
                const nombreCelda = fila.querySelector('td:nth-child(2)');
                if (nombreCelda) {
                    const texto = nombreCelda.textContent.toLowerCase();
                    fila.style.display = texto.includes(term) ? '' : 'none';
                }
            });
        });
    }

    // ===== BOTONES "TODOS" Y "NINGUNO" =====
    document.getElementById('btn-todos')?.addEventListener('click', function() {
        document.querySelectorAll('.seleccionar-alumno').forEach(cb => {
            cb.checked = true;
            cb.closest('tr')?.classList.add('table-success');
        });
    });

    document.getElementById('btn-ninguno')?.addEventListener('click', function() {
        document.querySelectorAll('.seleccionar-alumno').forEach(cb => {
            cb.checked = false;
            cb.closest('tr')?.classList.remove('table-success');
        });
    });
});
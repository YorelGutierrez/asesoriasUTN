document.addEventListener('DOMContentLoaded', function() {

    // ===== ELEMENTOS =====
    const carreraSelect = document.getElementById('carreraSelect');
    const docenteSelect = document.getElementById('docenteSelect');
    const materiasContainer = document.getElementById('materiasContainer');
    const gruposContainer = document.getElementById('gruposContainer');
    const form = document.getElementById('formAsignacion');
    const modalLabel = document.getElementById('modalLabel');
    const btnGuardar = document.getElementById('btnGuardar');
    const btnNueva = document.getElementById('btnNuevaAsignacion');
    const modal = document.getElementById('modalAsignacion');

    // ===== FUNCIÓN PARA LLENAR DOCENTES =====
    function llenarDocentes(docentes, seleccionadoId) {
        // Limpiar select
        docenteSelect.innerHTML = '';
        
        // Opción por defecto
        const defaultOpt = document.createElement('option');
        defaultOpt.value = '';
        defaultOpt.textContent = 'Seleccionar docente';
        docenteSelect.appendChild(defaultOpt);

        // Si no hay docentes, mostrar mensaje
        if (!docentes || docentes.length === 0) {
            const opt = document.createElement('option');
            opt.value = '';
            opt.textContent = '⚠️ No hay docentes en esta carrera';
            opt.disabled = true;
            docenteSelect.appendChild(opt);
            return;
        }

        // Agregar docentes
        docentes.forEach(d => {
            const opt = document.createElement('option');
            opt.value = d.id;
            opt.textContent = `${d.nombres} ${d.apellido_paterno}`.trim();
            if (seleccionadoId && d.id == seleccionadoId) {
                opt.selected = true;
            }
            docenteSelect.appendChild(opt);
        });
    }

    // ===== EVENTO: Cambio de carrera =====
    carreraSelect.addEventListener('change', function() {
        const carreraId = this.value;
        console.log('🟢 Carrera seleccionada:', carreraId);
        
        // Si no hay carrera seleccionada, limpiar todo
        if (!carreraId) {
            llenarDocentes([]);
            gruposContainer.innerHTML = '<p class="text-muted small">Selecciona una carrera para ver grupos.</p>';
            materiasContainer.innerHTML = '<p class="text-muted small">Selecciona un docente para ver materias.</p>';
            return;
        }

        // 🔥 PETICIÓN PARA DOCENTES
        const urlDocentes = window.ASIGNACIONES_ROUTES.docentes + '?carrera_id=' + carreraId;
        console.log('📡 Fetch docentes:', urlDocentes);

        fetch(urlDocentes)
            .then(response => response.json())
            .then(data => {
                console.log('✅ Docentes recibidos:', data);
                llenarDocentes(data);
            })
            .catch(error => {
                console.error('❌ Error al cargar docentes:', error);
                llenarDocentes([]);
            });

        // 🔥 PETICIÓN PARA GRUPOS
        const urlGrupos = window.ASIGNACIONES_ROUTES.grupos + '?carrera_id=' + carreraId;
        console.log('📡 Fetch grupos:', urlGrupos);

        fetch(urlGrupos)
            .then(response => response.json())
            .then(data => {
                console.log('✅ Grupos recibidos:', data);
                let html = '';
                if (data.length === 0) {
                    html = '<p class="text-muted small">⚠️ No hay grupos en esta carrera.</p>';
                } else {
                    data.forEach(g => {
                        html += `
                            <div class="form-check mb-1">
                                <input class="form-check-input" type="checkbox" name="grupos[]" value="${g.id}" id="grupo_${g.id}">
                                <label class="form-check-label small" for="grupo_${g.id}">${g.nombre}</label>
                            </div>
                        `;
                    });
                }
                gruposContainer.innerHTML = html;
            })
            .catch(error => {
                console.error('❌ Error al cargar grupos:', error);
                gruposContainer.innerHTML = '<p class="text-muted small">❌ Error al cargar grupos.</p>';
            });

        // Limpiar materias (hasta que se seleccione un docente)
        materiasContainer.innerHTML = '<p class="text-muted small">Selecciona un docente para ver materias.</p>';
    });

    // ===== EVENTO: Cambio de docente =====
    docenteSelect.addEventListener('change', function() {
        const docenteId = this.value;
        console.log('🟢 Docente seleccionado:', docenteId);
        
        if (!docenteId) {
            materiasContainer.innerHTML = '<p class="text-muted small">Selecciona un docente para ver materias.</p>';
            return;
        }

        // 🔥 PETICIÓN PARA MATERIAS
        const url = window.ASIGNACIONES_ROUTES.materias.replace('/0', '/' + docenteId);
        console.log('📡 Fetch materias:', url);

        fetch(url)
            .then(response => response.json())
            .then(data => {
                console.log('✅ Materias recibidas:', data);
                let html = '';
                if (data.allMaterias.length === 0) {
                    html = '<p class="text-muted small">⚠️ No hay materias disponibles.</p>';
                } else {
                    data.allMaterias.forEach(m => {
                        const checked = data.materiasAsignadas.includes(m.id) ? 'checked' : '';
                        html += `
                            <div class="form-check mb-1">
                                <input class="form-check-input" type="checkbox" name="materias[]" value="${m.id}" id="materia_${m.id}" ${checked}>
                                <label class="form-check-label small" for="materia_${m.id}">${m.nombre}</label>
                            </div>
                        `;
                    });
                }
                materiasContainer.innerHTML = html;
            })
            .catch(error => {
                console.error('❌ Error al cargar materias:', error);
                materiasContainer.innerHTML = '<p class="text-muted small">❌ Error al cargar materias.</p>';
            });
    });

    // ===== NUEVA ASIGNACIÓN =====
    if (btnNueva) {
        btnNueva.addEventListener('click', function() {
            console.log('🟢 Abriendo modal para NUEVA asignación');
            form.reset();
            form.action = window.ASIGNACIONES_ROUTES.store;
            form.method = 'POST';
            
            const put = document.getElementById('method_put');
            if (put) put.remove();

            carreraSelect.value = '';
            llenarDocentes([]);
            gruposContainer.innerHTML = '<p class="text-muted small">Selecciona una carrera para ver grupos.</p>';
            materiasContainer.innerHTML = '<p class="text-muted small">Selecciona un docente para ver materias.</p>';
            modalLabel.innerHTML = '<i class="bi bi-journal-text me-2"></i> Nueva asignación';
            btnGuardar.innerHTML = '<i class="bi bi-save me-1"></i> Guardar';
        });
    }

    // ===== EDITAR =====
    document.querySelectorAll('.editar-asignacion').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            console.log('🟢 Editando asignación ID:', id);

            form.action = window.ASIGNACIONES_ROUTES.update.replace('/0', '/' + id);
            let put = document.getElementById('method_put');
            if (!put) {
                put = document.createElement('input');
                put.type = 'hidden';
                put.name = '_method';
                put.id = 'method_put';
                put.value = 'PUT';
                form.appendChild(put);
            }

            modalLabel.innerHTML = '<i class="bi bi-pencil me-2"></i> Editar asignación';
            btnGuardar.innerHTML = '<i class="bi bi-save me-1"></i> Actualizar';

            const url = window.ASIGNACIONES_ROUTES.editar.replace('/0', '/' + id);
            console.log('📡 Fetch editar:', url);

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    console.log('✅ Datos de edición:', data);
                    
                    // 1. Seleccionar carrera (esto dispara el change y carga docentes/grupos)
                    carreraSelect.value = data.carrera_id;
                    carreraSelect.dispatchEvent(new Event('change'));

                    // 2. Después de un breve retraso, seleccionar el docente y cargar materias
                    setTimeout(() => {
                        docenteSelect.value = data.docente_id;
                        docenteSelect.dispatchEvent(new Event('change'));
                        
                        // 3. Marcar grupos seleccionados (ya cargados por el change de carrera)
                        // Esperar a que se carguen los grupos y luego marcar los checkboxes
                        setTimeout(() => {
                            const checkboxes = document.querySelectorAll('#gruposContainer input[type="checkbox"]');
                            checkboxes.forEach(cb => {
                                const grupoId = parseInt(cb.value);
                                if (data.gruposAsignados.includes(grupoId)) {
                                    cb.checked = true;
                                }
                            });
                        }, 300);
                    }, 400);
                })
                .catch(error => {
                    console.error('❌ Error al cargar datos para editar:', error);
                    alert('Error al cargar los datos para editar.');
                });
        });
    });

    // ===== ELIMINAR =====
    document.querySelectorAll('.eliminar-asignacion').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const nombre = this.dataset.nombre;

            Swal.fire({
                title: '¿Eliminar asignación?',
                text: `¿Quitar todas las materias y grupos de ${nombre}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then(result => {
                if (result.isConfirmed) {
                    const formEliminar = document.getElementById('formEliminar');
                    formEliminar.action = window.ASIGNACIONES_ROUTES.destroy.replace('/0', '/' + id);
                    formEliminar.submit();
                }
            });
        });
    });

    // ===== RESET AL CERRAR MODAL =====
    modal.addEventListener('hidden.bs.modal', function() {
        console.log('🟢 Modal cerrado, reseteando...');
        form.reset();
        carreraSelect.value = '';
        llenarDocentes([]);
        gruposContainer.innerHTML = '<p class="text-muted small">Selecciona una carrera para ver grupos.</p>';
        materiasContainer.innerHTML = '<p class="text-muted small">Selecciona un docente para ver materias.</p>';
        const put = document.getElementById('method_put');
        if (put) put.remove();
        form.action = window.ASIGNACIONES_ROUTES.store;
        form.method = 'POST';
        modalLabel.innerHTML = '<i class="bi bi-journal-text me-2"></i> Nueva asignación';
        btnGuardar.innerHTML = '<i class="bi bi-save me-1"></i> Guardar';
    });
});
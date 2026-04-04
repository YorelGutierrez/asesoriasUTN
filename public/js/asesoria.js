// public/js/asesoria.js

document.addEventListener('DOMContentLoaded', function() {
    
    // Resaltar fila al seleccionar alumno
    document.querySelectorAll('.seleccionar-alumno').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const row = this.closest('tr');
            if (this.checked) {
                row.classList.add('table-success');
            } else {
                row.classList.remove('table-success');
            }
        });
    });

    // Botón "Todos"
    const btnTodos = document.getElementById('btn-todos');
    if (btnTodos) {
        btnTodos.addEventListener('click', function() {
            document.querySelectorAll('.seleccionar-alumno').forEach(cb => {
                cb.checked = true;
                cb.closest('tr').classList.add('table-success');
            });
        });
    }

    // Botón "Ninguno"
    const btnNinguno = document.getElementById('btn-ninguno');
    if (btnNinguno) {
        btnNinguno.addEventListener('click', function() {
            document.querySelectorAll('.seleccionar-alumno').forEach(cb => {
                cb.checked = false;
                cb.closest('tr').classList.remove('table-success');
            });
        });
    }

    // Botones para seleccionar por grupo
    document.querySelectorAll('.btn-grupo-filtro').forEach(btn => {
        btn.addEventListener('click', function() {
            const grupo = this.getAttribute('data-grupo');
            const checkboxes = document.querySelectorAll(`.alumno-row[data-grupo="${grupo}"] .seleccionar-alumno`);
            
            const todosSeleccionados = Array.from(checkboxes).every(cb => cb.checked);
            
            checkboxes.forEach(cb => {
                if (todosSeleccionados) {
                    cb.checked = false;
                    cb.closest('tr').classList.remove('table-success');
                    this.classList.remove('btn-primary');
                    this.classList.add('btn-outline-primary');
                } else {
                    cb.checked = true;
                    cb.closest('tr').classList.add('table-success');
                    this.classList.remove('btn-outline-primary');
                    this.classList.add('btn-primary');
                }
            });
        });
    });

    // Generar PDF
    const btnPDF = document.getElementById('btnGenerarPDF');
    if (btnPDF) {
        btnPDF.addEventListener('click', function() {
            const form = document.getElementById('formAsesoria');
            const formData = new FormData(form);
            
            Swal.fire({
                title: 'Generando PDF...',
                text: 'Por favor espera',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            fetch('/docente/asesoria/pdf', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error al generar PDF');
                }
                return response.blob();
            })
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'asesoria_' + new Date().toISOString().slice(0,19).replace(/:/g, '-') + '.pdf';
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url);
                
                Swal.fire({
                    icon: 'success',
                    title: 'PDF Generado',
                    text: 'El PDF se ha descargado correctamente',
                    confirmButtonColor: '#3085d6'
                });
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo generar el PDF: ' + error.message,
                    confirmButtonColor: '#d33'
                });
            });
        });
    }
});
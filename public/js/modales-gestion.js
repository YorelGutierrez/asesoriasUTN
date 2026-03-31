
    // Ejemplo de filtrado básico (puedes expandirlo según necesidades)
    document.getElementById('filtrar-btn').addEventListener('click', function() {
        let searchTerm = document.getElementById('search-input').value.toLowerCase();
        let carrera = document.getElementById('carrera-filter').value.toLowerCase();
        let estado = document.getElementById('estado-filter').value.toLowerCase();

        // Lógica de filtrado según la pestaña activa
        let activeTab = document.querySelector('#gestionTab .nav-link.active').getAttribute('data-bs-target');
        if (activeTab === '#grupos') {
            filtrarTabla('tabla-grupos', searchTerm, carrera, estado);
        } else if (activeTab === '#alumnos') {
            filtrarTabla('tabla-alumnos', searchTerm, carrera, estado);
        } else if (activeTab === '#docentes') {
            filtrarTabla('tabla-docentes', searchTerm, carrera, estado);
        }
    });

    function filtrarTabla(tablaId, searchTerm, carrera, estado) {
        let filas = document.querySelectorAll(`#${tablaId} tr`);
        filas.forEach(fila => {
            let textoFila = fila.innerText.toLowerCase();
            let carreraFila = fila.cells[2]?.innerText.toLowerCase() || '';
            let estadoFila = fila.cells[5]?.innerText.toLowerCase() || '';
            let cumpleBusqueda = textoFila.includes(searchTerm);
            let cumpleCarrera = carrera === '' || carreraFila.includes(carrera);
            let cumpleEstado = estado === '' || estadoFila.includes(estado);
            fila.style.display = (cumpleBusqueda && cumpleCarrera && cumpleEstado) ? '' : 'none';
        });
    }

    // Aquí agregarías lógica para abrir modales de edición, eliminar, etc.
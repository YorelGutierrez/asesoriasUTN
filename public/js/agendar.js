// public/js/agendar.js

document.addEventListener('DOMContentLoaded', function () {

    // ===== SELECCIÓN DE PERSONA =====
    document.querySelectorAll('.asesor-item').forEach(item => {
        item.addEventListener('click', function () {
            document.querySelectorAll('.asesor-item').forEach(a => a.classList.remove('selected'));
            this.classList.add('selected');
            document.getElementById('destinatario_id').value = this.dataset.id;
            document.getElementById('tipo_destinatario').value = this.dataset.tipo;
            document.getElementById('docenteError').textContent = '';
        });
    });

    // ===== BUSCADOR =====
    document.getElementById('searchAsesor')?.addEventListener('input', function () {
        const term = this.value.toLowerCase();
        document.querySelectorAll('.asesor-item').forEach(item => {
            item.style.display = item.dataset.nombre.includes(term) ? 'flex' : 'none';
        });
    });

    // ===== CALENDARIO =====
    let currentDate = new Date();

    function renderCalendar() {
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

        document.getElementById('currentMonth').textContent = meses[month] + ' ' + year;

        const startOffset = firstDay.getDay() === 0 ? 6 : firstDay.getDay() - 1;
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        let html = '';
        for (let i = 0; i < startOffset; i++) html += '<div></div>';

        for (let i = 1; i <= lastDay.getDate(); i++) {
            const cellDate = new Date(year, month, i);
            const isPast = cellDate < today;
            const isWeekend = cellDate.getDay() === 0 || cellDate.getDay() === 6;
            const dateStr = year + '-' + String(month + 1).padStart(2, '0') + '-' + String(i).padStart(2, '0');
            let cls = isPast ? 'past' : isWeekend ? 'weekend' : '';
            html += `<div class="calendar-day ${cls}" data-date="${dateStr}">${i}</div>`;
        }

        document.getElementById('calendarDays').innerHTML = html;

        document.querySelectorAll('.calendar-day:not(.past):not(.weekend)').forEach(day => {
            day.addEventListener('click', function () {
                document.querySelectorAll('.calendar-day').forEach(d => d.classList.remove('selected'));
                this.classList.add('selected');
                document.getElementById('fechaSeleccionada').value = this.dataset.date;
                document.getElementById('fechaError').textContent = '';
            });
        });
    }

    document.getElementById('prevMonth')?.addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar();
    });

    document.getElementById('nextMonth')?.addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar();
    });

    renderCalendar();

    // ===== BOTÓN DE ENVÍO: VALIDACIÓN Y LOADING =====
    document.getElementById('btnAgendar')?.addEventListener('click', function (e) {
        const destinatario = document.getElementById('destinatario_id').value;
        const fecha = document.getElementById('fechaSeleccionada').value;
        const hora = document.getElementById('horaSeleccionada').value;
        const tema = document.querySelector('textarea[name="tema"]')?.value;

        let errors = [];

        if (!destinatario) {
            document.getElementById('docenteError').textContent = 'Por favor, selecciona una persona.';
            errors.push('Debes seleccionar una persona.');
        } else {
            document.getElementById('docenteError').textContent = '';
        }

        if (!fecha) {
            document.getElementById('fechaError').textContent = 'Por favor, selecciona una fecha.';
            errors.push('Debes seleccionar una fecha.');
        } else {
            document.getElementById('fechaError').textContent = '';
        }

        if (!hora) {
            document.getElementById('horaError').textContent = 'Por favor, selecciona una hora.';
            errors.push('Debes seleccionar una hora.');
        } else {
            document.getElementById('horaError').textContent = '';
        }

        if (!tema) {
            errors.push('Debes escribir el tema de la asesoría.');
        }

        if (errors.length > 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Campos incompletos',
                html: errors.join('<br>'),
                confirmButtonColor: '#d33'
            });
        } else {
            // Mostrar loading antes de enviar
            Swal.fire({
                title: 'Procesando...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
            // El formulario se envía automáticamente
        }
    });
});
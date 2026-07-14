// public/js/agendar.js

document.addEventListener('DOMContentLoaded', function () {

    // ===== PASOS =====
    const steps      = document.querySelectorAll('.step');
    const step1Card  = document.getElementById('step1-card');
    const step2Card  = document.getElementById('step2-card');
    const step3Card  = document.getElementById('step3-card');

    function setActiveStep(index) {
        steps.forEach((s, i) => s.classList.toggle('active', i === index));
    }

    function updateActiveStep() {
        if (!step1Card) return;
        const scroll = window.scrollY + 100;
        const top3   = step3Card ? step3Card.offsetTop : Infinity;
        const top2   = step2Card ? step2Card.offsetTop : Infinity;

        if (scroll >= top3)      setActiveStep(2);
        else if (scroll >= top2) setActiveStep(1);
        else                     setActiveStep(0);
    }

    window.addEventListener('scroll', updateActiveStep);
    updateActiveStep();

    // ===== SELECCIÓN DE PERSONA =====
    document.querySelectorAll('.asesor-item').forEach(item => {
        item.addEventListener('click', function () {
            document.querySelectorAll('.asesor-item').forEach(a => a.classList.remove('selected'));
            this.classList.add('selected');
            document.getElementById('destinatario_id').value  = this.dataset.id;
            document.getElementById('tipo_destinatario').value = this.dataset.tipo;
            document.getElementById('docenteError').textContent = '';
        });
    });

    // Búsqueda de persona
    document.getElementById('searchAsesor')?.addEventListener('input', function () {
        const term = this.value.toLowerCase();
        document.querySelectorAll('.asesor-item').forEach(item => {
            item.style.display = item.dataset.nombre.toLowerCase().includes(term) ? 'flex' : 'none';
        });
    });

    // ===== CALENDARIO =====
    let currentDate = new Date();

    function renderCalendar() {
        const year      = currentDate.getFullYear();
        const month     = currentDate.getMonth();
        const firstDay  = new Date(year, month, 1);
        const lastDay   = new Date(year, month + 1, 0);
        const meses     = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];

        document.getElementById('currentMonth').textContent = meses[month] + ' ' + year;

        const startOffset = firstDay.getDay() === 0 ? 6 : firstDay.getDay() - 1;
        const today       = new Date(); today.setHours(0, 0, 0, 0);

        let html = '';
        for (let i = 0; i < startOffset; i++) html += '<div></div>';

        for (let i = 1; i <= lastDay.getDate(); i++) {
            const cellDate = new Date(year, month, i);
            const isPast   = cellDate < today;
            const isWeekend = cellDate.getDay() === 0 || cellDate.getDay() === 6;
            const dateStr  = year + '-' + String(month + 1).padStart(2, '0') + '-' + String(i).padStart(2, '0');
            const cls      = isPast ? 'past' : isWeekend ? 'weekend' : '';
            html += `<div class="calendar-day ${cls}" data-date="${dateStr}">${i}</div>`;
        }

        document.getElementById('calendarDays').innerHTML = html;

        document.querySelectorAll('.calendar-day:not(.past):not(.weekend)').forEach(day => {
            day.addEventListener('click', function () {
                document.querySelectorAll('.calendar-day').forEach(d => d.classList.remove('selected'));
                this.classList.add('selected');
                document.getElementById('fechaSeleccionada').value  = this.dataset.date;
                document.getElementById('fechaError').textContent   = '';
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

    // ===== SLOTS DE HORA =====
    document.querySelectorAll('.time-slot').forEach(slot => {
        slot.addEventListener('click', function () {
            document.querySelectorAll('.time-slot').forEach(s => s.classList.remove('selected'));
            this.classList.add('selected');
            document.getElementById('horaSeleccionada').value = this.dataset.time;
            document.getElementById('horaError').textContent  = '';
        });
    });

    // ===== VALIDACIÓN =====
    document.getElementById('asesoriaForm')?.addEventListener('submit', function (e) {
        let valid = true;

        if (!document.getElementById('destinatario_id').value) {
            document.getElementById('docenteError').textContent = 'Por favor, selecciona una persona.';
            valid = false;
        }
        if (!document.getElementById('fechaSeleccionada').value) {
            document.getElementById('fechaError').textContent = 'Por favor, selecciona una fecha.';
            valid = false;
        }
        if (!document.getElementById('horaSeleccionada').value) {
            document.getElementById('horaError').textContent = 'Por favor, selecciona una hora.';
            valid = false;
        }

        if (!valid) {
            e.preventDefault();
            Swal.fire({ icon: 'error', title: 'Campos incompletos', text: 'Completa todos los campos requeridos.', confirmButtonColor: '#d33' });
        }
    });
});
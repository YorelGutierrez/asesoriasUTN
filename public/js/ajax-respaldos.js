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

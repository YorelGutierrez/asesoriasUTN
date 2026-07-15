// public/js/notificaciones.js

document.addEventListener('DOMContentLoaded', function () {
    const iconoNotif = document.getElementById('icono-notif');
    const dropNotif = document.getElementById('notif-dropdown');
    const iconoCal = document.getElementById('icono-cal');
    const dropCal = document.getElementById('cal-dropdown');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const usuarioRol = document.querySelector('meta[name="user-rol"]')?.content;

    // ===== CERRAR TODO =====
    function cerrarTodo() {
        if (dropNotif) dropNotif.style.display = 'none';
        if (dropCal) dropCal.style.display = 'none';
    }

    window.addEventListener('click', e => {
        if (!e.target.closest('#icono-notif') && !e.target.closest('#notif-dropdown'))
            if (dropNotif) dropNotif.style.display = 'none';
        if (!e.target.closest('#icono-cal') && !e.target.closest('#cal-dropdown'))
            if (dropCal) dropCal.style.display = 'none';
    });

    // ===== NOTIFICACIONES =====
    function cargarNotificaciones() {
        fetch('/notificaciones', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json())
            .then(data => {
                // 🔥 BADGE
                const badge = document.getElementById('notif-badge');
                if (badge) {
                    badge.textContent = data.no_leidas > 0 ? data.no_leidas : '';
                    badge.style.display = data.no_leidas > 0 ? 'block' : 'none';
                }

                // 🔥 LISTA
                const lista = document.getElementById('notif-lista');
                if (!lista) return;

                if (data.notificaciones.length === 0) {
                    lista.innerHTML = `<div class="notif-vacia"><i class="bi bi-bell-slash" style="font-size:2rem;display:block;margin-bottom:8px;"></i>Sin notificaciones</div>`;
                    return;
                }

                lista.innerHTML = data.notificaciones.map(n => {
                    // 🔥 SOLO DOCENTES pueden ver botones de confirmar/rechazar
                    const esDocente = usuarioRol === 'docente';
                    const esSolicitudPendiente = n.tipo === 'solicitud_asesoria' && n.accion === 'pendiente';
                    
                    let accionesHtml = '';
                    if (esDocente && esSolicitudPendiente) {
                        accionesHtml = `
                            <div class="notif-acciones" style="display:flex; gap:6px; margin-top:6px;">
                                <button class="btn-confirmar" data-id="${n.id}" style="background:#d1fae5; border:none; padding:4px 12px; border-radius:6px; font-size:12px; font-weight:600; cursor:pointer; color:#065f46;">
                                    <i class="bi bi-check-lg"></i> Confirmar
                                </button>
                                <button class="btn-rechazar" data-id="${n.id}" style="background:#fee2e2; border:none; padding:4px 12px; border-radius:6px; font-size:12px; font-weight:600; cursor:pointer; color:#991b1b;">
                                    <i class="bi bi-x-lg"></i> No disponible
                                </button>
                            </div>
                        `;
                    } else if (n.accion === 'confirmada') {
                        accionesHtml = `<span style="color:#2c9f49;font-size:11px;font-weight:600;">✓ Confirmada</span>`;
                    } else if (n.accion === 'rechazada') {
                        accionesHtml = `<span style="color:#ef4444;font-size:11px;font-weight:600;">✗ No disponible</span>`;
                    }

                    const tiempo = new Date(n.created_at).toLocaleDateString('es-MX', { day:'2-digit', month:'short', hour:'2-digit', minute:'2-digit' });
                    const noLeida = n.leido ? '' : 'no-leida';

                    return `
                        <div class="notif-item ${noLeida}" data-id="${n.id}" style="padding:12px 16px; border-bottom:1px solid #f0f0f0; cursor:pointer; transition:background .15s; font-size:13px; ${n.leido ? '' : 'background:#f0fdf4; border-left:3px solid #2c9f49;'}">
                            <div>${n.mensaje}</div>
                            ${accionesHtml}
                            <div class="notif-tiempo" style="font-size:11px; color:#9ca3af; margin-top:3px;">${tiempo}</div>
                        </div>
                    `;
                }).join('');

                // 🔥 ASIGNAR EVENTOS A LOS BOTONES DESPUÉS DE RENDERIZAR
                lista.querySelectorAll('.btn-confirmar').forEach(btn => {
                    btn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        const id = this.dataset.id;
                        confirmarNotif(id);
                    });
                });

                lista.querySelectorAll('.btn-rechazar').forEach(btn => {
                    btn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        const id = this.dataset.id;
                        rechazarNotif(id);
                    });
                });

                // 🔥 CLIC EN LA NOTIFICACIÓN PARA MARCAR COMO LEÍDA
                lista.querySelectorAll('.notif-item').forEach(item => {
                    item.addEventListener('click', function() {
                        const id = this.dataset.id;
                        marcarLeida(id);
                    });
                });
            })
            .catch(() => {});
    }

    // ===== FUNCIONES DE ACCIÓN =====
    function marcarLeida(id) {
        fetch(`/notificaciones/${id}/leer`, { 
            method: 'POST', 
            headers: { 'X-CSRF-TOKEN': csrf } 
        })
        .then(() => cargarNotificaciones());
    }

    function confirmarNotif(id) {
        fetch(`/notificaciones/${id}/confirmar`, { 
            method: 'POST', 
            headers: { 'X-CSRF-TOKEN': csrf } 
        })
        .then(r => r.json())
        .then(d => {
            if (d.ok) {
                Swal.fire({ icon: 'success', title: d.mensaje, timer: 1800, showConfirmButton: false });
                cargarNotificaciones();
            }
        });
    }

    function rechazarNotif(id) {
        fetch(`/notificaciones/${id}/rechazar`, { 
            method: 'POST', 
            headers: { 'X-CSRF-TOKEN': csrf } 
        })
        .then(r => r.json())
        .then(d => {
            if (d.ok) {
                Swal.fire({ icon: 'info', title: d.mensaje, timer: 1800, showConfirmButton: false });
                cargarNotificaciones();
            }
        });
    }

    // ===== TOGGLE NOTIFICACIONES =====
    if (iconoNotif) {
        iconoNotif.addEventListener('click', e => {
            e.stopPropagation();
            const visible = dropNotif.style.display === 'block';
            cerrarTodo();
            if (!visible) { 
                dropNotif.style.display = 'block'; 
                cargarNotificaciones(); 
            }
        });
    }

    // ===== MARCAR TODAS COMO LEÍDAS =====
    document.getElementById('btn-todas-leidas')?.addEventListener('click', function(e) {
        e.stopPropagation();
        fetch('/notificaciones/leer-todas', { 
            method: 'POST', 
            headers: { 'X-CSRF-TOKEN': csrf } 
        })
        .then(() => cargarNotificaciones());
    });

    // ===== MINI CALENDARIO =====
    let calMes = new Date().getMonth();
    let calAnio = new Date().getFullYear();
    let diasConSesion = [];

    function renderMiniCal() {
        const meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
        const firstDay = new Date(calAnio, calMes, 1);
        const lastDay = new Date(calAnio, calMes + 1, 0);
        const hoy = new Date();
        const offset = firstDay.getDay() === 0 ? 6 : firstDay.getDay() - 1;

        const titulo = document.getElementById('cal-mini-titulo');
        if (titulo) titulo.textContent = meses[calMes] + ' ' + calAnio;

        const grid = document.getElementById('cal-mini-days');
        if (!grid) return;

        let html = '';
        for (let i = 0; i < offset; i++) html += '<div></div>';
        for (let d = 1; d <= lastDay.getDate(); d++) {
            const esHoy = d === hoy.getDate() && calMes === hoy.getMonth() && calAnio === hoy.getFullYear();
            const conSesion = diasConSesion.includes(d);
            const esWeekend = new Date(calAnio, calMes, d).getDay() === 0 || new Date(calAnio, calMes, d).getDay() === 6;
            let cls = '';
            let estilo = '';
            if (conSesion) { cls = 'con-sesion'; estilo = 'background:linear-gradient(135deg,#2c9f49,#00937f); color:white; font-weight:700; border-radius:50%;'; }
            else if (esHoy) { cls = 'hoy'; estilo = 'background:#e2e8f0; font-weight:700;'; }
            else if (esWeekend) { cls = 'weekend'; estilo = 'color:#ef4444;'; }
            html += `<div class="cal-mini-day ${cls}" style="aspect-ratio:1; display:flex; align-items:center; justify-content:center; border-radius:6px; font-size:12px; color:#374151; ${estilo}" title="${conSesion ? 'Tienes asesoría este día' : ''}">${d}</div>`;
        }
        grid.innerHTML = html;
    }

    function cargarSesionesMes() {
        fetch(`/calendario/sesiones?mes=${calMes+1}&anio=${calAnio}`)
            .then(r => r.json())
            .then(d => { diasConSesion = d.dias; renderMiniCal(); })
            .catch(() => renderMiniCal());
    }

    if (iconoCal) {
        iconoCal.addEventListener('click', e => {
            e.stopPropagation();
            const visible = dropCal.style.display === 'block';
            cerrarTodo();
            if (!visible) { dropCal.style.display = 'block'; cargarSesionesMes(); }
        });
    }

    document.getElementById('cal-prev')?.addEventListener('click', e => { e.stopPropagation(); calMes--; if (calMes < 0) { calMes = 11; calAnio--; } cargarSesionesMes(); });
    document.getElementById('cal-next')?.addEventListener('click', e => { e.stopPropagation(); calMes++; if (calMes > 11) { calMes = 0; calAnio++; } cargarSesionesMes(); });

    // Cargar al inicio
    cargarNotificaciones();
});
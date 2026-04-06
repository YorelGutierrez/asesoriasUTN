// Función para abrir/cerrar el menú lateral con overlay
function toggleMenu() {
    const menu = document.getElementById("menu-lateral");
    const content = document.getElementById("main-content");
    const menuIcon = document.querySelector(".menu-icon");
    const overlay = document.querySelector(".overlay");
    const isMobile = window.innerWidth <= 768;

    if (menu.style.width === "250px") {
        // Cerrar menú
        menu.style.width = "0";
        content.classList.remove("shifted");
        if (menuIcon) menuIcon.style.display = "block";
        if (overlay) overlay.style.display = "none";
        if (!isMobile) {
            localStorage.setItem('menuOpen', 'false');
        }
    } else {
        // Abrir menú
        menu.style.width = "250px";
        content.classList.add("shifted");
        if (menuIcon) menuIcon.style.display = "none";
        if (overlay) overlay.style.display = "block";
        if (!isMobile) {
            localStorage.setItem('menuOpen', 'true');
        }
    }
}

// Cerrar menú al hacer clic en el overlay
const overlay = document.querySelector(".overlay");
if (overlay) {
    overlay.addEventListener("click", () => {
        const menu = document.getElementById("menu-lateral");
        const content = document.getElementById("main-content");
        const menuIcon = document.querySelector(".menu-icon");
        const isMobile = window.innerWidth <= 768;

        menu.style.width = "0";
        content.classList.remove("shifted");
        if (overlay) overlay.style.display = "none";
        if (menuIcon) menuIcon.style.display = "block";
        if (!isMobile) {
            localStorage.setItem('menuOpen', 'false');
        }
    });
}

// Lógica para subsecciones (toggle y cambio de ícono)
document.querySelectorAll('.menu-principal').forEach(item => {
    item.addEventListener('click', (e) => {
        e.preventDefault();

        const menuSeccion = item.parentElement;
        const icono = item.querySelector('i.bi-chevron-compact-down, i.bi-chevron-compact-up');

        // Cerrar todas las demás secciones
        document.querySelectorAll('.menu-seccion').forEach(sec => {
            if (sec !== menuSeccion) {
                sec.classList.remove('active');
                const otherIcon = sec.querySelector('.menu-principal i.bi-chevron-compact-down, .menu-principal i.bi-chevron-compact-up');
                if (otherIcon) {
                    otherIcon.classList.remove('bi-chevron-compact-up');
                    otherIcon.classList.add('bi-chevron-compact-down');
                }
            }
        });

        // Toggle de la sección actual
        menuSeccion.classList.toggle('active');

        // Cambiar ícono
        if (menuSeccion.classList.contains('active')) {
            icono.classList.remove('bi-chevron-compact-down');
            icono.classList.add('bi-chevron-compact-up');
        } else {
            icono.classList.remove('bi-chevron-compact-up');
            icono.classList.add('bi-chevron-compact-down');
        }
        
        // Guardar qué subsección está abierta (opcional, por si quieres persistencia)
        if (menuSeccion.classList.contains('active')) {
            localStorage.setItem('openSubsection', menuSeccion.querySelector('.menu-principal span').innerText.trim());
        } else {
            localStorage.removeItem('openSubsection');
        }
    });
});

// Función para activar la subsección según la URL actual
function activateSubsectionByUrl() {
    const currentUrl = window.location.href;
    const subsections = document.querySelectorAll('.subseccion a');
    
    for (let link of subsections) {
        if (link.href === currentUrl) {
            const menuSeccion = link.closest('.menu-seccion');
            if (menuSeccion && !menuSeccion.classList.contains('active')) {
                // Activar esta sección
                menuSeccion.classList.add('active');
                const icono = menuSeccion.querySelector('.menu-principal i.bi-chevron-compact-down, .menu-principal i.bi-chevron-compact-up');
                if (icono) {
                    icono.classList.remove('bi-chevron-compact-down');
                    icono.classList.add('bi-chevron-compact-up');
                }
            }
            break;
        }
    }
}

// Inicializar estado del menú según localStorage y tamaño de pantalla
document.addEventListener('DOMContentLoaded', function() {
    const menu = document.getElementById('menu-lateral');
    const content = document.getElementById('main-content');
    const menuIcon = document.querySelector('.menu-icon');
    const overlay = document.querySelector('.overlay');
    const isMobile = window.innerWidth <= 768;

    if (isMobile) {
        // En móvil siempre cerrado al inicio
        menu.style.width = '0';
        content.classList.remove('shifted');
        if (menuIcon) menuIcon.style.display = 'block';
        if (overlay) overlay.style.display = 'none';
    } else {
        // En escritorio, restaurar estado guardado (por defecto abierto)
        const menuWasOpen = localStorage.getItem('menuOpen');
        if (menuWasOpen === 'false') {
            menu.style.width = '0';
            content.classList.remove('shifted');
            if (menuIcon) menuIcon.style.display = 'block';
            if (overlay) overlay.style.display = 'none';
        } else {
            menu.style.width = '250px';
            content.classList.add('shifted');
            if (menuIcon) menuIcon.style.display = 'none';
            if (overlay) overlay.style.display = 'none'; // overlay solo en móvil
        }
    }
    
    // Activar la subsección correspondiente a la ruta actual
    activateSubsectionByUrl();
});

document.querySelectorAll('#menu-lateral a').forEach(link => {
    link.addEventListener('click', function(e) {
        if (window.innerWidth <= 768 && !this.classList.contains('menu-principal')) {
            // Cerrar menú después de navegar (la página recargará)
            localStorage.removeItem('menuOpen'); // No persistir en móvil
        }
    });
});


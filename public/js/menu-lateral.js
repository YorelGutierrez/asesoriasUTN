// Función para abrir/cerrar el menú lateral con overlay
function toggleMenu() {
    const menu = document.getElementById("menu-lateral");
    const content = document.getElementById("main-content");
    const menuIcon = document.querySelector(".menu-icon"); // icono de hamburguesa
    const overlay = document.querySelector(".overlay");    // overlay
    const isMobile = window.innerWidth <= 768; // detecta móvil

    if (menu.style.width === "250px") {
        menu.style.width = "0";
        content.classList.remove("shifted");
        menuIcon.style.display = "block";
        overlay.style.display = "none";   // ocultar overlay
        if (isMobile) {
            menuIcon.style.display = "block"; // solo en móvil
        }
    } else {
        menu.style.width = "250px";
        content.classList.add("shifted");
        menuIcon.style.display = "none";
        overlay.style.display = "block";  // mostrar overlay
        if (isMobile) {
            menuIcon.style.display = "block";  // solo en móvil
        }
    }
}

// Para cerrar menú si se hace clic en el overlay
document.querySelector(".overlay").addEventListener("click", () => {
    const menu = document.getElementById("menu-lateral");
    const content = document.getElementById("main-content");
    const menuIcon = document.querySelector(".menu-icon");
    const overlay = document.querySelector(".overlay");
    const isMobile = window.innerWidth <= 768; // detecta móvil

    menu.style.width = "0";
    content.classList.remove("shifted");
    overlay.style.display = "none";
    if (isMobile) {
        menuIcon.style.display = "block"; // solo en móvil
    }
});

// Para mostrar/ocultar subsecciones y cambiar el ícono
document.querySelectorAll('.menu-principal').forEach(item => {
    item.addEventListener('click', (e) => {
        e.preventDefault(); // evita el salto del enlace
        const parent = item.parentElement;
        const icono = item.querySelector('i');

        // Alternar clase "active" (para abrir/cerrar subsección)
        parent.classList.toggle('active');

        // Cambiar ícono segun estado
        if (parent.classList.contains('active')) {
            icono.classList.remove('bi-chevron-compact-down');
            icono.classList.add('bi-chevron-compact-up');
        } else {
            icono.classList.remove('bi-chevron-compact-up');
            icono.classList.add('bi-chevron-compact-down');
        }
    });
});


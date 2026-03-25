document.addEventListener('DOMContentLoaded', function () {
    const lectorSwitch = document.getElementById('lector-switch'); // El interruptor para activar/desactivar
    let lectorActivado = false;

    // Objeto para manejar la síntesis de voz
    const synth = window.speechSynthesis;
    let utterance = new SpeechSynthesisUtterance();

    // Función para leer el texto
    function leerTexto(texto) {
        if (synth.speaking) {
            synth.cancel(); // Si ya está hablando, lo cancela para empezar de nuevo
        }
        utterance.text = texto;
        utterance.lang = 'es-MX'; // Puedes ajustar el idioma
        synth.speak(utterance);
    }

    // Evento para el interruptor
    if (lectorSwitch) {
        lectorSwitch.addEventListener('change', function() {
            lectorActivado = this.checked;
            if (!lectorActivado) {
                synth.cancel(); // Si se desactiva, se calla
            }
        });
    }

    // Seleccionamos todos los elementos que queremos que se lean
    const elementosLeibles = document.querySelectorAll('.texto-leible');

    elementosLeibles.forEach(elem => {
        // Cuando el mouse entra en el elemento
        elem.addEventListener('mouseenter', () => {
            if (lectorActivado) {
                leerTexto(elem.innerText);
            }
        });

        // Cuando el mouse sale, deja de hablar
        elem.addEventListener('mouseleave', () => {
            if (lectorActivado) {
                synth.cancel();
            }
        });
    });
});
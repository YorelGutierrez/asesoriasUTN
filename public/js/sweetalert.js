// public/js/sweetalert.js

document.addEventListener('DOMContentLoaded', function() {
    
    // Mensaje de éxito
    if (typeof window.successMessage !== 'undefined' && window.successMessage) {
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: window.successMessage,
            confirmButtonColor: '#1d8917',
            confirmButtonText: 'Aceptar'
        });
    }
    
    // Mensaje de error
    if (typeof window.errorMessage !== 'undefined' && window.errorMessage) {
        Swal.fire({
            icon: 'error',
            title: '¡Error!',
            text: window.errorMessage,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Aceptar'
        });
    }
    
    // Errores de validación
    if (typeof window.validationErrors !== 'undefined' && window.validationErrors.length > 0) {
        let errores = '';
        window.validationErrors.forEach(function(error) {
            errores += '• ' + error + '<br>';
        });
        
        Swal.fire({
            icon: 'error',
            title: 'Errores en el formulario',
            html: errores,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Corregir'
        });
    }
});
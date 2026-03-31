document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const tab = urlParams.get('tab');
    if (tab) {
        let targetId = '';
        switch(tab) {
            case 'alumnos': targetId = '#alumnos'; break;
            case 'docentes': targetId = '#docentes'; break;
            default: targetId = '#grupos'; break;
        }
        const triggerEl = document.querySelector(`button[data-bs-target="${targetId}"]`);
        if (triggerEl) {
            const tabInstance = new bootstrap.Tab(triggerEl);
            tabInstance.show();
        }
    }
});

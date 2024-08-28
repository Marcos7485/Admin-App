document.addEventListener('DOMContentLoaded', function() {
    // Selecciona todas las imágenes en la página
    document.querySelectorAll('img').forEach(img => {
        // Deshabilita el arrastre
        img.setAttribute('draggable', 'false');
    });

    // Deshabilita el menú contextual al hacer clic derecho sobre imágenes
    document.addEventListener('contextmenu', function(e) {
        if (e.target.tagName === 'IMG') {
            e.preventDefault();
        }
    });
});

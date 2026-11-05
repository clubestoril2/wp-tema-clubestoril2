/**
 * Control de categorías del Personalizador: al marcar o desmarcar una
 * casilla, guarda los slugs marcados, separados por comas, en el campo
 * oculto enlazado a la opción.
 */
(function () {
    'use strict';

    document.addEventListener('change', function (evento) {
        var casilla = evento.target;
        if (!(casilla instanceof HTMLInputElement) || casilla.type !== 'checkbox') {
            return;
        }
        var control = casilla.closest('.ce-control-categorias');
        if (!control) {
            return;
        }
        var oculto = control.querySelector('input[type="hidden"]');
        var marcadas = Array.prototype.map.call(
            control.querySelectorAll('input[type="checkbox"]:checked'),
            function (marcada) {
                return marcada.value;
            }
        );
        oculto.value = marcadas.join(',');
        oculto.dispatchEvent(new Event('change', { bubbles: true }));
    });
})();

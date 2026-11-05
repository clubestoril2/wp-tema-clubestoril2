/**
 * Conmutador de modo claro / oscuro.
 *
 * El modo efectivo es el de data-theme en <html> (elección guardada o forzada
 * en el Personalizador) o, si no hay, el del sistema. La elección se guarda en
 * localStorage; el script en línea de <head> la aplica antes de pintar.
 */
(function () {
    'use strict';

    var CLAVE = 'ce-modo-color';
    var raiz = document.documentElement;
    var sistemaOscuro = window.matchMedia('(prefers-color-scheme: dark)');
    var botones = document.querySelectorAll('[data-ce-conmutador]');

    function modoActual() {
        var forzado = raiz.getAttribute('data-theme');
        if (forzado === 'light' || forzado === 'dark') {
            return forzado;
        }
        return sistemaOscuro.matches ? 'dark' : 'light';
    }

    function actualizarBotones() {
        var oscuro = modoActual() === 'dark';
        botones.forEach(function (boton) {
            boton.setAttribute('aria-pressed', oscuro ? 'true' : 'false');
        });
    }

    function guardar(modo) {
        try {
            localStorage.setItem(CLAVE, modo);
        } catch (e) {
            // Sin almacenamiento (modo privado): el cambio dura hasta recargar.
        }
    }

    botones.forEach(function (boton) {
        boton.hidden = false;
        boton.addEventListener('click', function () {
            var nuevo = modoActual() === 'dark' ? 'light' : 'dark';
            raiz.setAttribute('data-theme', nuevo);
            guardar(nuevo);
            actualizarBotones();
        });
    });

    if (typeof sistemaOscuro.addEventListener === 'function') {
        sistemaOscuro.addEventListener('change', actualizarBotones);
    }

    actualizarBotones();
})();

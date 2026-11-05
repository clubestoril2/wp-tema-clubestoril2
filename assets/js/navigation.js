/**
 * Menú principal plegable en pantallas estrechas.
 *
 * Muestra el botón «Menú», que abre y cierra el panel con el menú y el
 * buscador, y añade a cada elemento con submenú un botón para desplegarlo.
 * Los estilos (header.css) solo pliegan el panel cuando la navegación lleva
 * la clase ce-nav--plegable, que pone este script: sin JavaScript el menú se
 * ve completo.
 */
(function () {
    'use strict';

    var nav = document.querySelector('.ce-nav');
    var boton = nav && nav.querySelector('[data-ce-menu]');
    if (!boton) {
        return;
    }
    var panel = document.getElementById(boton.getAttribute('aria-controls'));

    function abrirMenu(abrir) {
        boton.setAttribute('aria-expanded', abrir ? 'true' : 'false');
        nav.classList.toggle('ce-nav--abierta', abrir);
    }

    nav.classList.add('ce-nav--plegable');
    boton.hidden = false;
    boton.addEventListener('click', function () {
        abrirMenu(boton.getAttribute('aria-expanded') !== 'true');
    });

    // Escape cierra el menú y devuelve el foco al botón.
    nav.addEventListener('keydown', function (evento) {
        if (evento.key === 'Escape' && boton.getAttribute('aria-expanded') === 'true') {
            abrirMenu(false);
            boton.focus();
        }
    });

    // Botón para desplegar cada submenú (menús de WordPress y de páginas).
    var etiqueta = nav.getAttribute('data-ce-submenu') || 'Submenú';
    panel.querySelectorAll('.menu-item-has-children, .page_item_has_children').forEach(function (elemento) {
        var enlace = elemento.querySelector(':scope > a');
        var submenu = elemento.querySelector(':scope > .sub-menu, :scope > .children');
        if (!enlace || !submenu) {
            return;
        }
        var desplegar = document.createElement('button');
        desplegar.type = 'button';
        desplegar.className = 'ce-nav__desplegar';
        desplegar.setAttribute('aria-expanded', 'false');
        desplegar.setAttribute('aria-label', etiqueta + ': ' + enlace.textContent.trim());
        enlace.after(desplegar);
        desplegar.addEventListener('click', function () {
            var abierto = desplegar.getAttribute('aria-expanded') !== 'true';
            desplegar.setAttribute('aria-expanded', abierto ? 'true' : 'false');
            elemento.classList.toggle('ce-nav__elemento--abierto', abierto);
        });
    });
})();

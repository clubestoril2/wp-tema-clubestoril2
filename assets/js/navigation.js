/**
 * Menú principal plegable en pantallas estrechas y buscador desplegable en
 * pantallas anchas.
 *
 * Muestra el botón «Menú», que abre y cierra el panel con el menú y el
 * buscador, y añade a cada elemento con submenú un botón para desplegarlo.
 * En pantallas anchas muestra la lupa, que despliega el campo de búsqueda
 * sobre el menú. Los estilos (header.css) solo pliegan el panel y el buscador
 * cuando la navegación lleva las clases ce-nav--plegable y
 * ce-nav--buscador-plegable, que pone este script: sin JavaScript todo se ve
 * completo.
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

    // Lupa (pantallas anchas): despliega el campo sobre el menú. Se pliega al
    // pulsarla otra vez, al pulsar fuera, con Escape o al salir con el foco.
    var lupa = nav.querySelector('[data-ce-lupa]');
    var buscar = lupa && lupa.parentElement;
    var campo = buscar && buscar.querySelector('input[type="search"]');
    if (lupa && campo) {
        var abrirBuscador = function (abrir) {
            lupa.setAttribute('aria-expanded', abrir ? 'true' : 'false');
            nav.classList.toggle('ce-nav--buscando', abrir);
            if (abrir) {
                campo.focus();
            }
        };
        var buscadorAbierto = function () {
            return lupa.getAttribute('aria-expanded') === 'true';
        };

        nav.classList.add('ce-nav--buscador-plegable');
        lupa.hidden = false;
        lupa.addEventListener('click', function () {
            abrirBuscador(!buscadorAbierto());
        });
        document.addEventListener('click', function (evento) {
            if (buscadorAbierto() && !buscar.contains(evento.target)) {
                abrirBuscador(false);
            }
        });
        buscar.addEventListener('keydown', function (evento) {
            if (evento.key === 'Escape' && buscadorAbierto()) {
                evento.stopPropagation();
                abrirBuscador(false);
                lupa.focus();
            }
        });
        buscar.addEventListener('focusout', function (evento) {
            if (buscadorAbierto() && evento.relatedTarget && !buscar.contains(evento.relatedTarget)) {
                abrirBuscador(false);
            }
        });
    }

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

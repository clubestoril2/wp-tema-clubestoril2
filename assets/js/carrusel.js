/**
 * Carrusel de la portada.
 *
 * Baraja las imágenes en cada visita y, cada 5 segundos, desplaza la tira a
 * la izquierda el ancho medio de una imagen. Las imágenes que salen por la
 * izquierda pasan al final, así que el movimiento es continuo e infinito.
 *
 * Se para al pasar el ratón, al llevar el foco dentro, con el botón de pausa
 * y mientras la pestaña está oculta. Con «reducir movimiento» empieza
 * parado.
 */
(function () {
    'use strict';

    var pista = document.querySelector('[data-ce-carrusel]');
    if (!pista || pista.children.length < 2) {
        return;
    }
    var carrusel = pista.parentElement;
    var boton = carrusel.querySelector('[data-ce-carrusel-pausa]');
    var INTERVALO = 5000;

    // Orden aleatorio (Fisher-Yates).
    var imagenes = Array.prototype.slice.call(pista.children);
    for (var i = imagenes.length - 1; i > 0; i--) {
        var j = Math.floor(Math.random() * (i + 1));
        var temporal = imagenes[i];
        imagenes[i] = imagenes[j];
        imagenes[j] = temporal;
    }
    imagenes.forEach(function (imagen) {
        pista.appendChild(imagen);
    });

    carrusel.classList.add('ce-carrusel--activo');

    function hueco() {
        return parseFloat(getComputedStyle(pista).columnGap) || 0;
    }

    // Ancho medio de las imágenes originales, con el hueco entre ellas.
    function paso() {
        var total = 0;
        imagenes.forEach(function (imagen) {
            total += imagen.getBoundingClientRect().width;
        });
        return total / imagenes.length + hueco();
    }

    // Si las imágenes no llenan el ancho más dos pasos, se repiten (copias
    // ocultas a los lectores de pantalla) para que nunca quede un hueco.
    function rellenar() {
        var necesario = carrusel.clientWidth + 2 * paso();
        var vueltas = 0;
        while (pista.scrollWidth < necesario && vueltas < 10) {
            imagenes.forEach(function (imagen) {
                var copia = imagen.cloneNode(true);
                copia.alt = '';
                copia.setAttribute('aria-hidden', 'true');
                pista.appendChild(copia);
            });
            vueltas++;
        }
    }

    var desplazamiento = 0;

    function colocar(animar) {
        pista.style.transition = animar ? '' : 'none';
        pista.style.transform = 'translateX(' + -desplazamiento + 'px)';
    }

    // Tras cada movimiento, las imágenes que ya han salido por la izquierda
    // pasan al final sin que se note.
    pista.addEventListener('transitionend', function (evento) {
        if (evento.target !== pista) {
            return;
        }
        var primera = pista.firstElementChild;
        var ancho = primera.getBoundingClientRect().width + hueco();
        while (desplazamiento >= ancho) {
            desplazamiento -= ancho;
            pista.appendChild(primera);
            primera = pista.firstElementChild;
            ancho = primera.getBoundingClientRect().width + hueco();
        }
        colocar(false);
    });

    var pausado = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var encima = false;

    function avanzar() {
        if (pausado || encima || document.hidden) {
            return;
        }
        rellenar();
        desplazamiento += paso();
        colocar(true);
    }

    function pintarBoton() {
        if (!boton) {
            return;
        }
        boton.setAttribute('aria-pressed', pausado ? 'true' : 'false');
        boton.setAttribute(
            'aria-label',
            boton.getAttribute(pausado ? 'data-ce-texto-reanudar' : 'data-ce-texto-pausar')
        );
    }

    if (boton) {
        boton.hidden = false;
        boton.addEventListener('click', function () {
            pausado = !pausado;
            pintarBoton();
        });
        pintarBoton();
    }

    // Solo con ratón: en pantallas táctiles un toque no debe dejarlo parado.
    carrusel.addEventListener('pointerenter', function (evento) {
        if (evento.pointerType === 'mouse') {
            encima = true;
        }
    });
    carrusel.addEventListener('pointerleave', function (evento) {
        if (evento.pointerType === 'mouse') {
            encima = false;
        }
    });
    carrusel.addEventListener('focusin', function () {
        encima = true;
    });
    carrusel.addEventListener('focusout', function () {
        encima = false;
    });

    rellenar();
    window.setInterval(avanzar, INTERVALO);
})();

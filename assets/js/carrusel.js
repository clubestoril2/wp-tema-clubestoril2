/**
 * Carrusel de la portada.
 *
 * Baraja las imágenes en cada visita y, cada cierto tiempo, desplaza la tira
 * a la izquierda un porcentaje del ancho medio de las imágenes (opciones
 * «carrusel_avance» y «carrusel_segundos», en los atributos data-ce-avance y
 * data-ce-segundos). Las imágenes que salen por la izquierda pasan al final,
 * así que el movimiento es continuo e infinito.
 *
 * El botón «Siguiente» avanza al momento (también en pausa). El avance
 * automático se para al pasar el ratón, al llevar el foco dentro, con el
 * botón de pausa y mientras la pestaña está oculta; con «reducir
 * movimiento» empieza parado.
 */
(function () {
    'use strict';

    var pista = document.querySelector('[data-ce-carrusel]');
    if (!pista || pista.children.length < 2) {
        return;
    }
    var carrusel = pista.parentElement;
    var controles = carrusel.querySelector('[data-ce-carrusel-controles]');
    var botonPausa = carrusel.querySelector('[data-ce-carrusel-pausa]');
    var botonSiguiente = carrusel.querySelector('[data-ce-carrusel-siguiente]');

    function numero(atributo, defecto, minimo, maximo) {
        var valor = parseInt(pista.getAttribute(atributo), 10);
        if (isNaN(valor)) {
            return defecto;
        }
        return Math.min(maximo, Math.max(minimo, valor));
    }
    var AVANCE = numero('data-ce-avance', 200, 25, 500) / 100;
    var INTERVALO = numero('data-ce-segundos', 5, 2, 60) * 1000;

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

    // Desplazamiento de cada avance: el ancho medio de las imágenes
    // originales (con el hueco entre ellas) por el porcentaje elegido.
    function paso() {
        var total = 0;
        imagenes.forEach(function (imagen) {
            total += imagen.getBoundingClientRect().width;
        });
        return (total / imagenes.length + hueco()) * AVANCE;
    }

    // Si las imágenes no llenan el ancho más dos avances, se repiten (copias
    // ocultas a los lectores de pantalla) para que nunca quede un hueco.
    function rellenar() {
        var necesario = carrusel.clientWidth + 2 * paso();
        var vueltas = 0;
        while (pista.scrollWidth < necesario && vueltas < 20) {
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
    var moviendo = false;

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
        moviendo = false;
    });

    // Un avance; mientras dura la animación no se encadena otro.
    function mover() {
        if (moviendo) {
            return;
        }
        rellenar();
        moviendo = true;
        desplazamiento += paso();
        colocar(true);
    }

    var pausado = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var encima = false;

    function automatico() {
        if (!pausado && !encima && !document.hidden) {
            mover();
        }
    }

    var temporizador = window.setInterval(automatico, INTERVALO);

    function reiniciarTemporizador() {
        window.clearInterval(temporizador);
        temporizador = window.setInterval(automatico, INTERVALO);
    }

    function pintarPausa() {
        botonPausa.setAttribute('aria-pressed', pausado ? 'true' : 'false');
        botonPausa.setAttribute(
            'aria-label',
            botonPausa.getAttribute(pausado ? 'data-ce-texto-reanudar' : 'data-ce-texto-pausar')
        );
    }

    if (controles) {
        controles.hidden = false;
    }
    if (botonPausa) {
        botonPausa.addEventListener('click', function () {
            pausado = !pausado;
            pintarPausa();
        });
        pintarPausa();
    }
    if (botonSiguiente) {
        botonSiguiente.addEventListener('click', function () {
            mover();
            reiniciarTemporizador();
        });
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
})();

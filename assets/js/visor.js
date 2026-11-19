/**
 * Visor de imágenes en primer plano.
 *
 * Se activa en los enlaces a imágenes (con una miniatura dentro) de:
 * - una galería de tarjetas del tema («ce-galeria»);
 * - una galería de WordPress, de bloques o clásica, cuyas imágenes enlacen
 *   al archivo de medios;
 * - cualquier elemento con la clase «ce-visor».
 * Los enlaces a otra cosa (una página, Facebook…) no se tocan. El título es
 * el texto alternativo de la miniatura o, si no tiene, su pie o el atributo
 * title del enlace. Las imágenes de un mismo contenedor
 * forman un grupo: se pasa de una a otra con los botones, con las flechas
 * del teclado o deslizando con el dedo (la pista usa scroll-snap, así que el
 * gesto es el desplazamiento nativo del navegador). Un clic o toque en la
 * imagen la amplía a lo ancho para leerla; Esc, el botón de cerrar o un clic
 * fuera de la imagen cierran el visor.
 *
 * Sin JavaScript, o sin <dialog>, los enlaces abren la imagen como siempre.
 * Los textos llegan en window.ceVisor (inc/enqueue.php).
 */
(function () {
    'use strict';

    if (typeof HTMLDialogElement === 'undefined') {
        return;
    }

    var TEXTOS = window.ceVisor || {};
    var IMAGEN = /\.(jpe?g|png|webp|avif|gif)$/i;
    var CONTENEDORES = '.ce-galeria, .ce-visor, .wp-block-gallery, .gallery';
    var ICONOS = {
        cerrar: '<path d="M6 6l12 12M18 6L6 18"/>',
        anterior: '<path d="M15 5l-7 7 7 7"/>',
        siguiente: '<path d="M9 5l7 7-7 7"/>',
        original: '<path d="M14 5h5v5M19 5l-8 8M10 7H6a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1v-4"/>'
    };

    var dialogo = null;
    var pista = null;
    var contador = null;
    var titulo = null;
    var original = null;
    var botonAnterior = null;
    var botonSiguiente = null;
    var grupo = [];
    var indice = 0;

    function texto(clave, defecto) {
        return typeof TEXTOS[clave] === 'string' ? TEXTOS[clave] : defecto;
    }

    function icono(nombre) {
        return '<svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" '
            + 'stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" '
            + 'focusable="false">' + ICONOS[nombre] + '</svg>';
    }

    function boton(clase, nombre, etiqueta) {
        var elemento = document.createElement('button');
        elemento.type = 'button';
        elemento.className = 'ce-visor__boton ' + clase;
        elemento.setAttribute('aria-label', etiqueta);
        elemento.title = etiqueta;
        elemento.innerHTML = icono(nombre);
        return elemento;
    }

    /** Título de una imagen: alt de la miniatura, su pie o el title del enlace. */
    function tituloDe(enlace) {
        var miniatura = enlace.querySelector('img');
        if (miniatura && miniatura.alt) {
            return miniatura.alt;
        }
        var figura = enlace.closest('figure, li, dl');
        var pie = figura ? figura.querySelector('figcaption, .wp-caption-text, span') : null;
        return (pie ? pie.textContent.trim() : '') || enlace.title || '';
    }

    function enlacesDe(contenedor) {
        return Array.prototype.filter.call(contenedor.querySelectorAll('a[href]'), function (enlace) {
            return IMAGEN.test(enlace.pathname) && enlace.querySelector('img');
        });
    }

    function crear() {
        dialogo = document.createElement('dialog');
        dialogo.className = 'ce-visor';
        dialogo.setAttribute('aria-label', texto('visor', 'Visor de imágenes'));

        var barra = document.createElement('div');
        barra.className = 'ce-visor__barra';
        contador = document.createElement('span');
        contador.className = 'ce-visor__contador';
        contador.setAttribute('aria-live', 'polite');
        titulo = document.createElement('span');
        titulo.className = 'ce-visor__titulo';
        original = document.createElement('a');
        original.className = 'ce-visor__boton ce-visor__original';
        original.target = '_blank';
        original.rel = 'noopener';
        original.title = texto('original', 'Abrir a tamaño completo');
        original.setAttribute('aria-label', original.title);
        original.innerHTML = icono('original');
        var cerrar = boton('ce-visor__cerrar', 'cerrar', texto('cerrar', 'Cerrar'));
        barra.append(contador, titulo, original, cerrar);

        pista = document.createElement('div');
        pista.className = 'ce-visor__pista';
        pista.tabIndex = -1;

        botonAnterior = boton('ce-visor__anterior', 'anterior', texto('anterior', 'Anterior'));
        botonSiguiente = boton('ce-visor__siguiente', 'siguiente', texto('siguiente', 'Siguiente'));

        dialogo.append(barra, pista, botonAnterior, botonSiguiente);
        document.body.appendChild(dialogo);

        cerrar.addEventListener('click', function () {
            dialogo.close();
        });
        botonAnterior.addEventListener('click', function () {
            ir(indice - 1, true);
        });
        botonSiguiente.addEventListener('click', function () {
            ir(indice + 1, true);
        });
        // Clic en la imagen: la amplía a lo ancho (o la vuelve a encajar). Fuera de ella, cierra.
        pista.addEventListener('click', function (evento) {
            if (evento.target.tagName === 'IMG') {
                evento.target.parentNode.classList.toggle('ce-visor__lamina--ampliada');
            } else {
                dialogo.close();
            }
        });
        pista.addEventListener('scroll', function () {
            var ancho = pista.clientWidth;
            if (ancho > 0) {
                marcar(Math.round(pista.scrollLeft / ancho));
            }
        }, { passive: true });
        dialogo.addEventListener('keydown', function (evento) {
            if (evento.key === 'ArrowLeft') {
                evento.preventDefault();
                ir(indice - 1, true);
            } else if (evento.key === 'ArrowRight') {
                evento.preventDefault();
                ir(indice + 1, true);
            }
        });
        dialogo.addEventListener('close', function () {
            // El aviso de cierre llega con retraso: si ya se ha vuelto a abrir, no se toca.
            if (dialogo.open) {
                return;
            }
            document.documentElement.classList.remove('ce-visor-abierto');
            var enlace = grupo[indice];
            pista.textContent = '';
            if (enlace) {
                enlace.focus();
            }
        });
        window.addEventListener('resize', function () {
            if (dialogo.open) {
                ir(indice, false);
            }
        });
    }

    /** Actualiza contador, título, enlace y botones para la imagen i. */
    function marcar(i) {
        indice = Math.max(0, Math.min(grupo.length - 1, i));
        var enlace = grupo[indice];
        contador.textContent = (indice + 1) + ' / ' + grupo.length;
        titulo.textContent = tituloDe(enlace);
        original.href = enlace.href;
        botonAnterior.disabled = indice === 0;
        botonSiguiente.disabled = indice === grupo.length - 1;
        var unica = grupo.length < 2;
        botonAnterior.hidden = unica;
        botonSiguiente.hidden = unica;
        contador.hidden = unica;
    }

    function ir(i, animado) {
        marcar(i);
        var suave = animado && !window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        pista.scrollTo({ left: indice * pista.clientWidth, behavior: suave ? 'smooth' : 'instant' });
    }

    function abrir(enlaces, inicio) {
        if (!dialogo) {
            crear();
        }
        grupo = enlaces;
        pista.textContent = '';
        grupo.forEach(function (enlace, i) {
            var lamina = document.createElement('div');
            lamina.className = 'ce-visor__lamina';
            var imagen = document.createElement('img');
            imagen.alt = tituloDe(enlace);
            imagen.decoding = 'async';
            // Solo se descargan la imagen abierta y sus vecinas; el resto, al acercarse.
            if (Math.abs(i - inicio) > 1) {
                imagen.loading = 'lazy';
            }
            imagen.src = enlace.href;
            lamina.appendChild(imagen);
            pista.appendChild(lamina);
        });
        document.documentElement.classList.add('ce-visor-abierto');
        dialogo.showModal();
        ir(inicio, false);
        pista.focus();
    }

    document.addEventListener('click', function (evento) {
        if (evento.defaultPrevented || evento.button !== 0
            || evento.ctrlKey || evento.metaKey || evento.shiftKey || evento.altKey) {
            return;
        }
        var enlace = evento.target.closest ? evento.target.closest('a[href]') : null;
        // El contenedor más externo: una galería de bloques anida una figura por imagen.
        var contenedor = null;
        var candidato = enlace ? enlace.closest(CONTENEDORES) : null;
        while (candidato) {
            contenedor = candidato;
            candidato = candidato.parentElement ? candidato.parentElement.closest(CONTENEDORES) : null;
        }
        if (!contenedor || contenedor.tagName === 'DIALOG') {
            return;
        }
        var enlaces = enlacesDe(contenedor);
        var inicio = enlaces.indexOf(enlace);
        if (inicio < 0) {
            return;
        }
        evento.preventDefault();
        abrir(enlaces, inicio);
    });
})();

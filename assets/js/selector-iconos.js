/**
 * Buscador de iconos del menú (editor de páginas y Apariencia › Menús).
 *
 * Convierte cada <select class="ce-icono-menu__selector"> en un campo de
 * texto con lista desplegable: al escribir se filtran los iconos por su
 * nombre, sin distinguir mayúsculas ni tildes, y cada opción muestra su
 * dibujo. Se maneja con el ratón o con el teclado (flechas, Inicio, Fin,
 * Intro para elegir y Esc para cerrar sin cambiar nada). Debajo hay una
 * cuadrícula con todos los dibujos, sin nombre (sale al pasar el ratón), que
 * se filtra con el mismo texto y permite elegir con un clic.
 *
 * El <select> sigue en el formulario, oculto, y es el que se envía: al elegir
 * un icono se cambia su valor y se lanza su evento «change», que actualiza la
 * muestra que hay al lado. Sin JavaScript se ve y se usa el <select> normal.
 *
 * Los elementos que Apariencia › Menús añade después de cargar la página
 * también se convierten. La URL del sprite y los textos llegan en
 * window.ceSelectorIconos (inc/menu-iconos.php).
 */
(function () {
    'use strict';

    var DATOS = window.ceSelectorIconos || {};
    var SPRITE = DATOS.sprite || '';
    var TEXTOS = DATOS.textos || {};
    var SELECTOR = 'select.ce-icono-menu__selector';
    var contador = 0;

    /** Texto en minúsculas y sin tildes, para comparar. */
    function normalizar(texto) {
        return String(texto).toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '');
    }

    function crear(etiqueta, atributos) {
        var elemento = document.createElement(etiqueta);
        Object.keys(atributos || {}).forEach(function (nombre) {
            elemento.setAttribute(nombre, atributos[nombre]);
        });
        return elemento;
    }

    function dibujo(icono) {
        var svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
        svg.setAttribute('class', 'ce-selector-iconos__dibujo');
        svg.setAttribute('aria-hidden', 'true');
        svg.setAttribute('focusable', 'false');
        if (icono) {
            var uso = document.createElementNS('http://www.w3.org/2000/svg', 'use');
            uso.setAttribute('href', SPRITE + '#' + icono);
            svg.appendChild(uso);
        }
        return svg;
    }

    function mejorar(select) {
        if (select.dataset.ceBuscador) {
            return;
        }
        select.dataset.ceBuscador = '1';
        contador += 1;
        var id = 'ce-selector-iconos-' + contador;

        var opciones = Array.prototype.map.call(select.options, function (opcion, indice) {
            var texto = opcion.textContent.trim();
            return {
                valor: opcion.value,
                texto: texto,
                busqueda: normalizar(texto + ' ' + opcion.value.replace(/-/g, ' ')),
                id: id + '-opcion-' + indice
            };
        });

        var contenedor = crear('span', { 'class': 'ce-selector-iconos' });
        var campo = crear('input', {
            type: 'text',
            'class': 'ce-selector-iconos__campo',
            role: 'combobox',
            'aria-autocomplete': 'list',
            'aria-expanded': 'false',
            'aria-controls': id + '-lista',
            autocomplete: 'off',
            spellcheck: 'false',
            placeholder: TEXTOS.buscar || ''
        });
        var lista = crear('ul', { 'class': 'ce-selector-iconos__lista', id: id + '-lista', role: 'listbox' });
        lista.hidden = true;
        var vacio = crear('li', { 'class': 'ce-selector-iconos__vacio' });
        vacio.textContent = TEXTOS.sinResultados || '';

        // Cuadrícula con todos los dibujos, sin nombre (aparece al pasar el ratón).
        var rejilla = crear('span', {
            'class': 'ce-selector-iconos__rejilla',
            role: 'group',
            'aria-label': TEXTOS.rejilla || ''
        });
        var sinCoincidencias = crear('span', { 'class': 'ce-selector-iconos__rejilla-vacia' });
        sinCoincidencias.textContent = TEXTOS.sinResultados || '';
        sinCoincidencias.hidden = true;
        var botones = opciones.map(function (opcion) {
            var boton = crear('button', {
                type: 'button',
                'class': 'ce-selector-iconos__casilla' + (opcion.valor === '' ? ' ce-selector-iconos__casilla--ninguno' : ''),
                title: opcion.texto,
                'aria-label': opcion.texto,
                'aria-pressed': 'false',
                'data-valor': opcion.valor
            });
            boton.appendChild(dibujo(opcion.valor));
            rejilla.appendChild(boton);
            return boton;
        });
        rejilla.appendChild(sinCoincidencias);

        // El <label> del <select> pasa a nombrar el campo de búsqueda.
        if (select.id) {
            campo.id = select.id;
            select.removeAttribute('id');
        }

        var visibles = [];
        var activa = -1;

        function textoActual() {
            var actual = opciones.filter(function (opcion) {
                return opcion.valor === select.value;
            })[0];
            return actual ? actual.texto : '';
        }

        /** Deja en la cuadrícula solo los iconos que coinciden con la búsqueda. */
        function filtrarRejilla(consulta) {
            var termino = normalizar(consulta.trim());
            var hay = false;
            botones.forEach(function (boton, indice) {
                var coincide = termino === '' || opciones[indice].busqueda.indexOf(termino) !== -1;
                boton.hidden = !coincide;
                hay = hay || coincide;
            });
            sinCoincidencias.hidden = hay;
        }

        /** Resalta en la cuadrícula el icono elegido. */
        function marcarRejilla() {
            botones.forEach(function (boton) {
                boton.setAttribute('aria-pressed', boton.dataset.valor === select.value ? 'true' : 'false');
            });
        }

        function marcar(indice) {
            var anterior = lista.querySelector('[aria-selected="true"]');
            if (anterior) {
                anterior.setAttribute('aria-selected', 'false');
            }
            activa = indice;
            if (indice < 0 || !visibles[indice]) {
                campo.removeAttribute('aria-activedescendant');
                return;
            }
            var elemento = document.getElementById(visibles[indice].id);
            elemento.setAttribute('aria-selected', 'true');
            campo.setAttribute('aria-activedescendant', elemento.id);
            elemento.scrollIntoView({ block: 'nearest' });
        }

        function pintar(consulta) {
            var termino = normalizar(consulta.trim());
            visibles = opciones.filter(function (opcion) {
                return termino === '' || opcion.busqueda.indexOf(termino) !== -1;
            });
            lista.textContent = '';
            visibles.forEach(function (opcion) {
                var elemento = crear('li', {
                    'class': 'ce-selector-iconos__opcion',
                    role: 'option',
                    id: opcion.id,
                    'aria-selected': 'false',
                    'data-valor': opcion.valor
                });
                elemento.appendChild(dibujo(opcion.valor));
                elemento.appendChild(document.createTextNode(opcion.texto));
                if (opcion.valor === select.value) {
                    elemento.classList.add('ce-selector-iconos__opcion--elegida');
                }
                lista.appendChild(elemento);
            });
            if (!visibles.length) {
                lista.appendChild(vacio);
            }
            var elegida = -1;
            visibles.forEach(function (opcion, indice) {
                if (opcion.valor === select.value) {
                    elegida = indice;
                }
            });
            marcar(termino === '' ? elegida : (visibles.length ? 0 : -1));
        }

        function abrir() {
            if (!lista.hidden) {
                return;
            }
            lista.hidden = false;
            campo.setAttribute('aria-expanded', 'true');
            pintar('');
        }

        function cerrar() {
            lista.hidden = true;
            campo.setAttribute('aria-expanded', 'false');
            campo.removeAttribute('aria-activedescendant');
            campo.value = textoActual();
            filtrarRejilla('');
        }

        function elegir(indice) {
            var opcion = visibles[indice];
            if (opcion && opcion.valor !== select.value) {
                select.value = opcion.valor;
                select.dispatchEvent(new Event('change', { bubbles: true }));
            }
            cerrar();
        }

        campo.addEventListener('focus', function () {
            abrir();
            campo.select();
        });
        // Con el campo ya enfocado (tras elegir con Intro) no llega «focus».
        campo.addEventListener('click', function () {
            if (lista.hidden) {
                abrir();
                campo.select();
            }
        });
        campo.addEventListener('input', function () {
            lista.hidden = false;
            campo.setAttribute('aria-expanded', 'true');
            pintar(campo.value);
            filtrarRejilla(campo.value);
        });
        campo.addEventListener('keydown', function (evento) {
            var total = visibles.length;
            // Escribir sobre el nombre del icono elegido empieza una búsqueda nueva.
            var tecla = evento.key.length === 1 && !evento.ctrlKey && !evento.metaKey && !evento.altKey;
            if (tecla && campo.value === textoActual() && campo.selectionStart === campo.value.length) {
                campo.value = '';
            }
            switch (evento.key) {
                case 'ArrowDown':
                    evento.preventDefault();
                    if (lista.hidden) {
                        abrir();
                    } else if (total) {
                        marcar((activa + 1) % total);
                    }
                    break;
                case 'ArrowUp':
                    evento.preventDefault();
                    if (lista.hidden) {
                        abrir();
                    } else if (total) {
                        marcar(activa <= 0 ? total - 1 : activa - 1);
                    }
                    break;
                case 'Home':
                case 'End':
                    if (!lista.hidden && total) {
                        evento.preventDefault();
                        marcar(evento.key === 'Home' ? 0 : total - 1);
                    }
                    break;
                case 'Enter':
                    // Sin esto, Intro enviaría el formulario de Apariencia › Menús.
                    if (!lista.hidden) {
                        evento.preventDefault();
                        if (activa >= 0) {
                            elegir(activa);
                        }
                    }
                    break;
                case 'Escape':
                    if (!lista.hidden) {
                        evento.preventDefault();
                        evento.stopPropagation();
                        cerrar();
                    }
                    break;
                case 'Tab':
                    cerrar();
                    break;
            }
        });
        campo.addEventListener('blur', function () {
            cerrar();
        });

        // mousedown en lugar de click: así el campo no pierde el foco antes de elegir.
        lista.addEventListener('mousedown', function (evento) {
            evento.preventDefault();
            var opcion = evento.target.closest('.ce-selector-iconos__opcion');
            if (!opcion) {
                return;
            }
            var indice = visibles.map(function (visible) {
                return visible.id;
            }).indexOf(opcion.id);
            elegir(indice);
        });
        // En Apariencia › Menús todo va dentro de un <label>: un clic en la lista
        // volvería a activar el campo y a abrirla.
        lista.addEventListener('click', function (evento) {
            evento.preventDefault();
            evento.stopPropagation();
        });

        // mousedown: el campo de búsqueda no pierde el foco, así el filtro se mantiene
        // hasta que se elige un icono.
        rejilla.addEventListener('mousedown', function (evento) {
            if (evento.target.closest('.ce-selector-iconos__casilla') && document.activeElement === campo) {
                evento.preventDefault();
            }
        });
        rejilla.addEventListener('click', function (evento) {
            var boton = evento.target.closest('.ce-selector-iconos__casilla');
            if (!boton) {
                return;
            }
            evento.preventDefault();
            evento.stopPropagation();
            if (boton.dataset.valor !== select.value) {
                select.value = boton.dataset.valor;
                select.dispatchEvent(new Event('change', { bubbles: true }));
            }
            cerrar();
        });

        select.addEventListener('change', function () {
            marcarRejilla();
            if (document.activeElement !== campo) {
                campo.value = textoActual();
            }
        });

        select.hidden = true;
        select.setAttribute('tabindex', '-1');
        contenedor.appendChild(campo);
        contenedor.appendChild(lista);
        contenedor.appendChild(rejilla);
        select.parentNode.insertBefore(contenedor, select);
        campo.value = textoActual();
        marcarRejilla();
    }

    function mejorarTodos(raiz) {
        if (raiz.matches && raiz.matches(SELECTOR)) {
            mejorar(raiz);
        }
        if (raiz.querySelectorAll) {
            Array.prototype.forEach.call(raiz.querySelectorAll(SELECTOR), mejorar);
        }
    }

    // Muestra junto al selector: sigue al <select>, cambie desde el buscador o no.
    document.addEventListener('change', function (evento) {
        var select = evento.target;
        if (!select.classList || !select.classList.contains('ce-icono-menu__selector')) {
            return;
        }
        var muestra = select.parentNode.querySelector('.ce-icono-menu__muestra');
        if (!muestra) {
            return;
        }
        muestra.hidden = select.value === '';
        muestra.querySelector('use').setAttribute('href', SPRITE + '#' + select.value);
    });

    function iniciar() {
        mejorarTodos(document);
        new MutationObserver(function (cambios) {
            cambios.forEach(function (cambio) {
                Array.prototype.forEach.call(cambio.addedNodes, function (nodo) {
                    if (nodo.nodeType === 1) {
                        mejorarTodos(nodo);
                    }
                });
            });
        }).observe(document.body, { childList: true, subtree: true });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', iniciar);
    } else {
        iniciar();
    }
})();

<?php

/**
 * Carga de hojas de estilo y scripts.
 *
 * @package ClubEstoril2
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

/**
 * Hojas de estilo del tema, en orden de carga.
 */
const CE_ESTILOS = ['tokens', 'base', 'layout', 'header', 'components', 'content'];

/**
 * Versión de un recurso: la del tema o, con WP_DEBUG, la fecha del fichero
 * para evitar la caché del navegador durante el desarrollo.
 */
function ce_version_recurso(string $relativa): string
{
    if (defined('WP_DEBUG') && WP_DEBUG) {
        $fecha = @filemtime(CE_DIR . '/' . $relativa);
        if ($fecha !== false) {
            return (string) $fecha;
        }
    }
    return CE_VERSION;
}

add_action('wp_enqueue_scripts', 'ce_encolar_recursos');

function ce_encolar_recursos(): void
{
    $anterior = [];
    foreach (CE_ESTILOS as $nombre) {
        $relativa = 'assets/css/' . $nombre . '.css';
        $handle = 'clubestoril2-' . $nombre;
        wp_enqueue_style($handle, CE_URI . '/' . $relativa, $anterior, ce_version_recurso($relativa));
        $anterior = [$handle];
    }

    // Vista limpia para imprimir, después del resto de hojas para poder ajustarlas.
    $relativa = 'assets/css/print.css';
    wp_enqueue_style(
        'clubestoril2-impresion',
        CE_URI . '/' . $relativa,
        $anterior,
        ce_version_recurso($relativa),
        'print'
    );

    $relativa = 'assets/js/navigation.js';
    wp_enqueue_script(
        'clubestoril2-navegacion',
        CE_URI . '/' . $relativa,
        [],
        ce_version_recurso($relativa),
        ['strategy' => 'defer', 'in_footer' => true]
    );

    if (is_front_page()) {
        $relativa = 'assets/js/carrusel.js';
        wp_enqueue_script(
            'clubestoril2-carrusel',
            CE_URI . '/' . $relativa,
            [],
            ce_version_recurso($relativa),
            ['strategy' => 'defer', 'in_footer' => true]
        );
    }

    if (ce_usa_visor()) {
        $relativa = 'assets/js/visor.js';
        wp_enqueue_script(
            'clubestoril2-visor',
            CE_URI . '/' . $relativa,
            [],
            ce_version_recurso($relativa),
            ['strategy' => 'defer', 'in_footer' => true]
        );
        wp_add_inline_script(
            'clubestoril2-visor',
            'window.ceVisor = ' . wp_json_encode(
                [
                    'visor' => __('Visor de imágenes', 'clubestoril2'),
                    'cerrar' => __('Cerrar', 'clubestoril2'),
                    'anterior' => __('Anterior', 'clubestoril2'),
                    'siguiente' => __('Siguiente', 'clubestoril2'),
                    'original' => __('Abrir a tamaño completo', 'clubestoril2'),
                ]
            ) . ';',
            'before'
        );
    }

    if (ce_opcion_booleano('mostrar_conmutador')) {
        $relativa = 'assets/js/theme-toggle.js';
        wp_enqueue_script(
            'clubestoril2-modo-color',
            CE_URI . '/' . $relativa,
            [],
            ce_version_recurso($relativa),
            ['strategy' => 'defer', 'in_footer' => true]
        );
    }
}

/**
 * Marcas que delatan en el contenido una galería que el visor sabe abrir:
 * las clases del tema, la galería de bloques y la galería clásica.
 */
const CE_MARCAS_VISOR = ['ce-galeria', 'ce-visor', 'wp-block-gallery', '[gallery'];

/**
 * Si la página o entrada que se muestra puede usar el visor de imágenes.
 */
function ce_usa_visor(): bool
{
    if (!is_singular()) {
        return false;
    }
    $contenido = (string) get_post_field('post_content', get_queried_object_id());
    foreach (CE_MARCAS_VISOR as $marca) {
        if (str_contains($contenido, $marca)) {
            return true;
        }
    }
    return false;
}

add_action('wp_head', 'ce_script_modo_color', 1);

/**
 * Aplica el modo de color elegido por el visitante antes de pintar la página,
 * para que no haya un destello del modo contrario.
 */
function ce_script_modo_color(): void
{
    if (!ce_opcion_booleano('mostrar_conmutador')) {
        return;
    }
    wp_print_inline_script_tag(
        <<<'JS'
(function () {
    try {
        var modo = localStorage.getItem('ce-modo-color');
        if (modo === 'light' || modo === 'dark') {
            document.documentElement.setAttribute('data-theme', modo);
        }
    } catch (e) {}
})();
JS,
        ['id' => 'clubestoril2-modo-color-inicial']
    );
}

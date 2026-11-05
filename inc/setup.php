<?php

/**
 * Configuración básica del tema: soportes, menús, áreas de widgets e imágenes.
 *
 * @package ClubEstoril2
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

/**
 * Ancho máximo del contenido, en píxeles (incrustados y medios del núcleo).
 */
$GLOBALS['content_width'] = 760;

add_action('after_setup_theme', 'ce_configurar_tema');

function ce_configurar_tema(): void
{
    load_theme_textdomain('clubestoril2', CE_DIR . '/languages');

    add_theme_support('title-tag');
    add_theme_support('automatic-feed-links');
    add_theme_support('post-thumbnails');
    add_theme_support('responsive-embeds');
    add_theme_support('align-wide');
    add_theme_support(
        'html5',
        ['search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets']
    );
    add_theme_support(
        'custom-logo',
        [
            'height' => 128,
            'width' => 128,
            'flex-height' => true,
            'flex-width' => true,
            'unlink-homepage-logo' => false,
        ]
    );
    add_theme_support('editor-styles');
    add_editor_style('assets/css/editor.css');

    register_nav_menus(
        [
            // «primary»: WordPress le asigna el menú principal del tema anterior al cambiar de tema.
            'primary' => __('Menú principal', 'clubestoril2'),
            'accesos' => __('Accesos rápidos (barra superior)', 'clubestoril2'),
            'pie' => __('Pie de página', 'clubestoril2'),
        ]
    );

    // Tamaños de imagen de los listados: apertura de portada, tarjeta y fila.
    // Todos en 1,91:1, la proporción de las vistas previas en redes sociales.
    add_image_size('ce-apertura', 1200, 630, true);
    add_image_size('ce-tarjeta', 600, 315, true);
    add_image_size('ce-fila', 400, 210, true);
}

add_action('widgets_init', 'ce_registrar_widgets');

function ce_registrar_widgets(): void
{
    $areas = [
        'pie-1' => __('Pie: columna 1', 'clubestoril2'),
        'pie-2' => __('Pie: columna 2', 'clubestoril2'),
        'pie-3' => __('Pie: columna 3', 'clubestoril2'),
    ];
    foreach ($areas as $id => $nombre) {
        register_sidebar(
            [
                'id' => $id,
                'name' => $nombre,
                'before_widget' => '<section id="%1$s" class="ce-widget %2$s">',
                'after_widget' => '</section>',
                'before_title' => '<h2 class="ce-widget__titulo">',
                'after_title' => '</h2>',
            ]
        );
    }
}

add_filter('excerpt_length', 'ce_longitud_extracto', 20);

function ce_longitud_extracto(): int
{
    return ce_opcion_entero('extracto_palabras');
}

add_filter('excerpt_more', 'ce_final_extracto');

function ce_final_extracto(): string
{
    return '…';
}

add_filter('language_attributes', 'ce_atributo_modo_color');

/**
 * Añade data-theme a <html> cuando el modo de color está forzado.
 */
function ce_atributo_modo_color(string $atributos): string
{
    $modo = ce_modo_color();
    if ($modo === '') {
        return $atributos;
    }
    return $atributos . ' data-theme="' . esc_attr($modo) . '"';
}

add_filter('get_the_archive_title_prefix', '__return_empty_string');

add_action('pre_get_posts', 'ce_excluir_de_publicaciones');

/**
 * Quita de la página de entradas las categorías de la opción
 * «listados_excluir» (por defecto, ninguna).
 */
function ce_excluir_de_publicaciones(WP_Query $consulta): void
{
    if (is_admin() || !$consulta->is_main_query() || !$consulta->is_home()) {
        return;
    }
    $excluir = ce_opcion_lista('listados_excluir');
    if ($excluir === []) {
        return;
    }
    $consulta->set(
        'tax_query', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
        [
            [
                'taxonomy' => 'category',
                'field' => 'slug',
                'terms' => $excluir,
                'operator' => 'NOT IN',
            ],
        ]
    );
}

add_filter('the_content', 'ce_envolver_tablas', 20);

/**
 * Envuelve las tablas del contenido para que se desplacen en horizontal solo
 * si no caben (estilos en content.css, «.ce-tabla»).
 */
function ce_envolver_tablas(mixed $contenido): mixed
{
    return is_string($contenido) ? \ClubEstoril2\Contenido::envolverTablas($contenido) : $contenido;
}


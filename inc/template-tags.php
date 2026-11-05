<?php

/**
 * Funciones de plantilla reutilizables.
 *
 * Las funciones ce_*() que imprimen HTML escapan siempre su salida.
 *
 * @package ClubEstoril2
 */

declare(strict_types=1);

use ClubEstoril2\Novedad;
use ClubEstoril2\PrimeraImagen;

defined('ABSPATH') || exit;

/**
 * Icono del sprite assets/img/icons.svg, decorativo (oculto a lectores).
 */
function ce_icono(string $nombre, string $clase = ''): void
{
    $clases = trim('ce-icono ' . $clase);
    printf(
        '<svg class="%1$s" aria-hidden="true" focusable="false"><use href="%2$s"></use></svg>',
        esc_attr($clases),
        esc_url(CE_URI . '/assets/img/icons.svg#' . $nombre)
    );
}

/**
 * Redes sociales admitidas: sufijo de la opción red_* y nombre visible.
 */
const CE_REDES_SOCIALES = [
    'facebook' => 'Facebook',
    'instagram' => 'Instagram',
    'x' => 'X',
    'youtube' => 'YouTube',
    'telegram' => 'Telegram',
    'whatsapp' => 'WhatsApp',
];

/**
 * Redes sociales configuradas, en el orden en que se muestran.
 * Solo incluye las que tienen dirección.
 *
 * @return list<array{url: string, nombre: string, red: string}>
 */
function ce_redes_sociales(): array
{
    $enlaces = [];
    foreach (CE_REDES_SOCIALES as $red => $nombre) {
        $url = ce_opcion_url('red_' . $red);
        if ($url !== '') {
            $enlaces[] = ['url' => $url, 'nombre' => $nombre, 'red' => $red];
        }
    }
    $correo = ce_opcion_texto('red_correo');
    if ($correo !== '') {
        $enlaces[] = ['url' => 'mailto:' . $correo, 'nombre' => __('Correo', 'clubestoril2'), 'red' => 'correo'];
    }
    return $enlaces;
}

/**
 * Fecha de publicación como elemento <time>.
 */
function ce_fecha(?WP_Post $post = null): void
{
    $post = get_post($post);
    if (!$post instanceof WP_Post) {
        return;
    }
    printf(
        '<time class="ce-fecha" datetime="%1$s">%2$s</time>',
        esc_attr((string) get_the_date(DATE_W3C, $post)),
        esc_html((string) get_the_date('', $post))
    );
}

/**
 * Indica si la publicación tiene como mucho $dias días de antigüedad.
 */
function ce_es_novedad(int $dias, ?WP_Post $post = null): bool
{
    $post = get_post($post);
    if (!$post instanceof WP_Post) {
        return false;
    }
    $fecha = get_post_datetime($post);
    if ($fecha === false) {
        return false;
    }
    return Novedad::esNovedad($fecha, $dias, current_datetime());
}

/**
 * Distintivo «Novedad» si la publicación entra en el plazo indicado.
 */
function ce_distintivo_novedad(int $dias, ?WP_Post $post = null): void
{
    if (ce_es_novedad($dias, $post)) {
        echo '<span class="ce-distintivo ce-distintivo--novedad">' . esc_html__('Novedad', 'clubestoril2') . '</span>';
    }
}

/**
 * Sección (término) principal de una entrada: la primera categoría.
 */
function ce_seccion(?WP_Post $post = null): ?WP_Term
{
    $post = get_post($post);
    if (!$post instanceof WP_Post) {
        return null;
    }
    $terminos = get_the_terms($post, 'category');
    if (!is_array($terminos) || $terminos === []) {
        return null;
    }
    return $terminos[0];
}

/**
 * Enlace a la sección de la entrada con forma de etiqueta («chip»).
 */
function ce_chip_seccion(?WP_Post $post = null): void
{
    $seccion = ce_seccion($post);
    if ($seccion === null) {
        return;
    }
    $enlace = get_term_link($seccion);
    if (!is_string($enlace)) {
        return;
    }
    printf(
        '<a class="ce-chip ce-chip--%1$s" href="%2$s">%3$s</a>',
        esc_attr($seccion->slug),
        esc_url($enlace),
        esc_html($seccion->name)
    );
}

/**
 * Metadatos de una entrada: fecha y, si está activado, autor.
 */
function ce_meta_entrada(?WP_Post $post = null): void
{
    $post = get_post($post);
    if (!$post instanceof WP_Post) {
        return;
    }
    echo '<p class="ce-meta">';
    ce_fecha($post);
    if (ce_opcion_booleano('mostrar_autor')) {
        printf(
            ' <span class="ce-meta__autor">%1$s %2$s</span>',
            esc_html__('por', 'clubestoril2'),
            esc_html(get_the_author_meta('display_name', (int) $post->post_author))
        );
    }
    echo '</p>';
}

/**
 * Imagen de la entrada para listados.
 *
 * Por orden: la imagen destacada, la primera imagen del contenido o, si no
 * hay ninguna, el escudo del club.
 */
function ce_miniatura(string $tamano, ?WP_Post $post = null): void
{
    $post = get_post($post);
    if (!$post instanceof WP_Post) {
        return;
    }
    $atributos = ['class' => 'ce-miniatura', 'loading' => 'lazy'];
    if (has_post_thumbnail($post)) {
        echo get_the_post_thumbnail($post, $tamano, $atributos);
        return;
    }

    $imagen = PrimeraImagen::buscar($post->post_content);
    if ($imagen !== null) {
        // Imagen de la biblioteca de medios: se usa el tamaño pedido con srcset.
        if ($imagen['id'] !== null && wp_attachment_is_image($imagen['id'])) {
            $html = wp_get_attachment_image($imagen['id'], $tamano, false, $atributos);
            if ($html !== '') {
                echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML generado y escapado por WordPress.
                return;
            }
        }
        // Imagen externa o adjunto ya borrado: se usa la URL tal cual.
        $src = $imagen['src'] !== null ? esc_url($imagen['src']) : '';
        if ($src !== '') {
            printf('<img class="ce-miniatura" src="%1$s" alt="" loading="lazy">', $src);
            return;
        }
    }

    printf(
        '<img class="ce-miniatura ce-miniatura--respaldo" src="%1$s" alt="" width="150" height="150" loading="lazy">',
        esc_url(CE_URI . '/assets/img/escudo-150.png')
    );
}

/**
 * Muestra una lista de entradas con una pieza de plantilla.
 *
 * Prepara cada entrada como la actual (para the_title(), the_permalink()...)
 * y restaura la entrada del bucle principal al terminar.
 *
 * @param array<mixed> $entradas Entradas (WP_Post); se ignora cualquier otro valor.
 * @param string $pieza Ruta de la pieza en template-parts/content/ (p. ej. «card-fila»).
 * @param array<string, mixed> $args Argumentos para la pieza.
 */
function ce_mostrar_entradas(array $entradas, string $pieza, array $args = []): void
{
    global $post;
    foreach ($entradas as $entrada) {
        if (!$entrada instanceof WP_Post) {
            continue;
        }
        // Se restaura con wp_reset_postdata() al terminar.
        $post = $entrada; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
        setup_postdata($entrada);
        get_template_part('template-parts/content/' . $pieza, null, $args);
    }
    wp_reset_postdata();
}

/**
 * Enlaces a las páginas internas de una entrada dividida con «Salto de página».
 */
function ce_paginas_internas(): void
{
    wp_link_pages(
        [
            'before' => '<nav class="ce-paginas-entrada" aria-label="' . esc_attr__('Páginas', 'clubestoril2') . '">',
            'after' => '</nav>',
        ]
    );
}

/**
 * Paginación numérica de los listados.
 */
function ce_paginacion(): void
{
    the_posts_pagination(
        [
            'mid_size' => 2,
            'prev_text' => __('Anterior', 'clubestoril2'),
            'next_text' => __('Siguiente', 'clubestoril2'),
            'screen_reader_text' => __('Navegación de entradas', 'clubestoril2'),
            'class' => 'ce-paginacion',
        ]
    );
}

/**
 * Menú de respaldo cuando no hay un menú asignado a la ubicación principal:
 * muestra las páginas publicadas. WordPress le pasa los argumentos de
 * wp_nav_menu(), que no se necesitan.
 */
function ce_menu_respaldo(): void
{
    wp_page_menu(
        [
            'container' => '',
            'before' => '<ul class="ce-menu">',
            'after' => '</ul>',
            'show_home' => true,
            'echo' => true,
        ]
    );
}

/**
 * Logotipo del sitio: el del Personalizador o el escudo incluido en el tema.
 */
function ce_logotipo(): void
{
    if (has_custom_logo()) {
        the_custom_logo();
        return;
    }
    printf(
        '<a class="ce-marca__logo" href="%1$s" rel="home"><img src="%2$s" alt="%3$s" width="300" height="300"></a>',
        esc_url(home_url('/')),
        esc_url(CE_URI . '/assets/img/escudo-300.png'),
        esc_attr(sprintf(
            /* translators: %s: nombre del sitio */
            __('Escudo de %s', 'clubestoril2'),
            get_bloginfo('name')
        ))
    );
}

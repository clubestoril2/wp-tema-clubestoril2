<?php

/**
 * Funciones de plantilla reutilizables.
 *
 * Las funciones ce_*() que imprimen HTML escapan siempre su salida.
 *
 * @package ClubEstoril2
 */

declare(strict_types=1);

use ClubEstoril2\Categorias;
use ClubEstoril2\Novedad;
use ClubEstoril2\PrimeraImagen;

defined('ABSPATH') || exit;

/**
 * Icono del sprite assets/img/icons.svg, decorativo (oculto a lectores).
 *
 * La URL del sprite lleva la versión del tema para que el navegador no siga
 * usando uno antiguo, sin los iconos nuevos, tras actualizar el tema.
 */
function ce_icono(string $nombre, string $clase = ''): void
{
    $clases = trim('ce-icono ' . $clase);
    printf(
        '<svg class="%1$s" aria-hidden="true" focusable="false"><use href="%2$s"></use></svg>',
        esc_attr($clases),
        esc_url(
            add_query_arg('ver', ce_version_recurso('assets/img/icons.svg'), CE_URI . '/assets/img/icons.svg')
            . '#' . $nombre
        )
    );
}

/**
 * Redes sociales admitidas: sufijo de la opción red_* y nombre visible.
 */
const CE_REDES_SOCIALES = [
    'telegram' => 'Telegram',
    'facebook' => 'Facebook',
    'instagram' => 'Instagram',
    'maps' => 'Google Maps',
    'x' => 'X',
    'youtube' => 'YouTube',
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
 * Sección (término) principal de una entrada: la primera categoría, sin
 * contar la de destacados salvo que sea la única.
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
    foreach ($terminos as $termino) {
        if ($termino->slug !== CE_CATEGORIA_DESTACADO) {
            return $termino;
        }
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
 * hay ninguna, el escudo del club. Si la primera imagen es el fotograma de un
 * vídeo, lleva la clase «ce-miniatura--video» para superponer el botón de
 * reproducción.
 */
function ce_miniatura(string $tamano, ?WP_Post $post = null): void
{
    $post = get_post($post);
    if (!$post instanceof WP_Post) {
        return;
    }
    if (has_post_thumbnail($post)) {
        echo get_the_post_thumbnail($post, $tamano, ['class' => 'ce-miniatura', 'loading' => 'lazy']);
        return;
    }

    $imagen = PrimeraImagen::buscar($post->post_content);
    if ($imagen !== null) {
        $clase = $imagen['video'] ? 'ce-miniatura ce-miniatura--video' : 'ce-miniatura';
        // Imagen de la biblioteca de medios: se usa el tamaño pedido con srcset,
        // solo si la URL de la imagen es de verdad la del adjunto.
        if (
            $imagen['id'] !== null
            && wp_attachment_is_image($imagen['id'])
            && ($imagen['src'] === null
                || PrimeraImagen::esDelAdjunto($imagen['src'], (string) wp_get_attachment_url($imagen['id'])))
        ) {
            $html = wp_get_attachment_image(
                $imagen['id'],
                $tamano,
                false,
                ['class' => $clase, 'loading' => 'lazy']
            );
            if ($html !== '') {
                // HTML generado y escapado por WordPress.
                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                echo $html;
                return;
            }
        }
        // Imagen externa o adjunto ya borrado: se usa la URL tal cual.
        $src = $imagen['src'] ?? '';
        if (esc_url($src) !== '') {
            printf('<img class="%1$s" src="%2$s" alt="" loading="lazy">', esc_attr($clase), esc_url($src));
            return;
        }
    }

    printf(
        '<img class="ce-miniatura ce-miniatura--respaldo" src="%1$s" alt="" width="150" height="150" loading="lazy">',
        esc_url(CE_URI . '/assets/img/escudo-150.png')
    );
}

/**
 * Slug de la categoría que lleva una entrada a «Destacado» en la portada.
 */
const CE_CATEGORIA_DESTACADO = 'destacado';

/**
 * Entradas de la portada, de la más reciente a la más antigua y sin las
 * categorías excluidas.
 *
 * - Con $destacadas, solo las de la categoría de destacados.
 * - Sin $destacadas, las de cualquier categoría (también destacadas) salvo
 *   las de $omitir: así «Últimas publicaciones» muestra todo lo que no ha
 *   cabido en el bloque «Destacado».
 *
 * @param bool $destacadas Solo entradas de la categoría de destacados.
 * @param int $cantidad Número máximo de entradas; 0 o menos no devuelve ninguna.
 * @param string[] $excluir Slugs de categorías excluidas.
 * @param array<mixed> $omitir Entradas (WP_Post) que no se repiten.
 * @return array<mixed>
 */
function ce_entradas_portada(bool $destacadas, int $cantidad, array $excluir, array $omitir = []): array
{
    if ($cantidad <= 0) {
        return [];
    }
    $tax_query = [];
    if ($destacadas) {
        $tax_query[] = [
            'taxonomy' => 'category',
            'field' => 'slug',
            'terms' => [CE_CATEGORIA_DESTACADO],
            'operator' => 'IN',
        ];
    }
    $ids_omitidos = [];
    foreach ($omitir as $entrada) {
        if ($entrada instanceof WP_Post) {
            $ids_omitidos[] = $entrada->ID;
        }
    }
    if ($excluir !== []) {
        $tax_query[] = [
            'taxonomy' => 'category',
            'field' => 'slug',
            'terms' => $excluir,
            'operator' => 'NOT IN',
        ];
    }
    $consulta = new WP_Query(
        [
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => $cantidad,
            'ignore_sticky_posts' => true,
            'no_found_rows' => true,
            'post__not_in' => $ids_omitidos,
            'tax_query' => $tax_query, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
        ]
    );
    return $consulta->posts;
}

/**
 * URL de la página de entradas (Ajustes › Lectura › Página de entradas), o
 * cadena vacía si la portada no es una página estática o no hay ninguna
 * elegida.
 */
function ce_url_publicaciones(): string
{
    if (get_option('show_on_front') !== 'page') {
        return '';
    }
    $pagina = (int) get_option('page_for_posts');
    if ($pagina <= 0 || get_post_status($pagina) !== 'publish') {
        return '';
    }
    $url = get_permalink($pagina);
    return is_string($url) ? $url : '';
}

/**
 * Slug de la página con el listado de categorías (plantilla page-categorias.php).
 */
const CE_PAGINA_CATEGORIAS = 'categorias';

/**
 * URL de la página «Categorías», o cadena vacía si no existe o no está
 * publicada.
 */
function ce_url_categorias(): string
{
    $pagina = get_page_by_path(CE_PAGINA_CATEGORIAS);
    if (!$pagina instanceof WP_Post || $pagina->post_status !== 'publish') {
        return '';
    }
    $url = get_permalink($pagina);
    return is_string($url) ? $url : '';
}

/**
 * Categorías con entradas, sin las de la opción «categorias_ocultas» y en
 * orden alfabético.
 *
 * @return list<array{slug: string, nombre: string, total: int, url: string}>
 */
function ce_categorias_visibles(): array
{
    $terminos = get_terms(['taxonomy' => 'category', 'hide_empty' => true]);
    if (!is_array($terminos)) {
        return [];
    }
    $categorias = [];
    $urls = [];
    foreach ($terminos as $termino) {
        if (!$termino instanceof WP_Term) {
            continue;
        }
        $url = get_term_link($termino);
        if (!is_string($url)) {
            continue;
        }
        $categorias[] = ['slug' => $termino->slug, 'nombre' => $termino->name, 'total' => $termino->count];
        $urls[$termino->slug] = $url;
    }
    $listado = [];
    foreach (Categorias::listado($categorias, ce_opcion_lista('categorias_ocultas')) as $categoria) {
        $listado[] = $categoria + ['url' => $urls[$categoria['slug']]];
    }
    return $listado;
}

/**
 * URL de la imagen de una categoría: assets/img/categorias/<slug>.svg o, si
 * no existe, generica.svg.
 */
function ce_imagen_categoria(string $slug): string
{
    $fichero = 'assets/img/categorias/' . sanitize_file_name($slug) . '.svg';
    if (!is_readable(CE_DIR . '/' . $fichero)) {
        $fichero = 'assets/img/categorias/generica.svg';
    }
    return add_query_arg('ver', ce_version_recurso($fichero), CE_URI . '/' . $fichero);
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
 * muestra solo las páginas de primer nivel. WordPress le pasa los argumentos
 * de wp_nav_menu(), que no se necesitan.
 */
function ce_menu_respaldo(): void
{
    wp_page_menu(
        [
            'depth' => 1,
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

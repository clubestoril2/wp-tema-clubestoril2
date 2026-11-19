<?php

/**
 * Shortcodes del tema.
 *
 * [ce_publicaciones]: últimas publicaciones de una o varias categorías o
 * etiquetas, con las mismas tarjetas y filas que la portada. Pensado para
 * páginas como «Restauración», que así muestran sus últimas entradas sin
 * código ni plugins. Los atributos se sanean en ClubEstoril2\Publicaciones.
 *
 * Con «destacadas» se comporta como la portada: una sección «Destacado» con
 * las publicaciones de la categoría de destacados (en tarjetas) y otra
 * «Últimas publicaciones» con las demás, sin repetir (en filas).
 *
 * Ejemplos:
 *   [ce_publicaciones categoria="restauracion"]
 *   [ce_publicaciones categoria="restauracion" cantidad="6" formato="fila"]
 *   [ce_publicaciones etiqueta="padel" hijas="no" dias="0"]
 *   [ce_publicaciones categoria="restauracion" destacadas="3" cantidad="6"]
 *
 * @package ClubEstoril2
 */

declare(strict_types=1);

use ClubEstoril2\Publicaciones;

defined('ABSPATH') || exit;

add_shortcode('ce_publicaciones', 'ce_shortcode_publicaciones');

/**
 * @param array<string|int, mixed>|string $atributos Atributos del shortcode («» si no hay ninguno).
 */
function ce_shortcode_publicaciones(array|string $atributos): string
{
    $atributos = Publicaciones::sanear(is_array($atributos) ? $atributos : []);
    $dias      = $atributos['dias'] === Publicaciones::DIAS_DEL_TEMA
        ? ce_opcion_entero('portada_dias_novedad')
        : $atributos['dias'];

    if ($atributos['destacadas'] <= 0) {
        $entradas = ce_entradas_shortcode($atributos, false, $atributos['cantidad']);
        if ($entradas === []) {
            return '';
        }
        return $atributos['formato'] === Publicaciones::FORMATO_FILA
            ? ce_publicaciones_filas($entradas, $dias)
            : ce_publicaciones_tarjetas($entradas, $dias);
    }

    // Como la portada: destacadas en tarjetas y el resto, sin repetir, en filas.
    $destacadas = ce_entradas_shortcode($atributos, true, $atributos['destacadas']);
    $anteriores = ce_entradas_shortcode($atributos, false, $atributos['cantidad'], $destacadas);
    if ($destacadas === [] && $anteriores === []) {
        return '';
    }

    $salida = '';
    if ($destacadas !== []) {
        $salida .= ce_publicaciones_seccion(
            'destacadas',
            $atributos['titulo_destacadas'] ?? __('Destacado', 'clubestoril2'),
            ce_publicaciones_tarjetas($destacadas, $dias)
        );
    }
    if ($anteriores !== []) {
        $salida .= ce_publicaciones_seccion(
            'anteriores',
            $atributos['titulo_anteriores'] ?? __('Últimas publicaciones', 'clubestoril2'),
            ce_publicaciones_filas($anteriores, $dias)
        );
    }
    return $salida;
}

/**
 * Sección con título (si no está vacío) y su listado ya pintado.
 *
 * @param string $tipo «destacadas» o «anteriores», para la clase CSS.
 * @param string $titulo Título de la sección; vacío no imprime título.
 * @param string $listado HTML del listado, ya escapado.
 */
function ce_publicaciones_seccion(string $tipo, string $titulo, string $listado): string
{
    $salida = '<section class="ce-publicaciones__seccion ce-publicaciones__seccion--' . esc_attr($tipo) . '">';
    if ($titulo !== '') {
        $salida .= '<h2 class="ce-titulo-seccion">' . esc_html($titulo) . '</h2>';
    }
    return $salida . $listado . '</section>';
}

/**
 * Rejilla de tarjetas, como «Destacado» en la portada.
 *
 * @param array<mixed> $entradas Entradas (WP_Post).
 */
function ce_publicaciones_tarjetas(array $entradas, int $dias): string
{
    ob_start();
    echo '<div class="ce-publicaciones ce-rejilla ce-rejilla--tarjetas ce-rejilla--carrusel">';
    ce_mostrar_entradas($entradas, 'card-tarjeta', ['dias' => $dias]);
    echo '</div>';
    return (string) ob_get_clean();
}

/**
 * Lista de filas, como «Últimas publicaciones» en la portada.
 *
 * @param array<mixed> $entradas Entradas (WP_Post).
 */
function ce_publicaciones_filas(array $entradas, int $dias): string
{
    ob_start();
    echo '<div class="ce-publicaciones ce-filas">';
    ce_mostrar_entradas($entradas, 'card-fila', ['dias' => $dias]);
    echo '</div>';
    return (string) ob_get_clean();
}

/**
 * Entradas que pide el shortcode, de la más reciente a la más antigua.
 *
 * Si se indican categorías y etiquetas, la entrada debe cumplir ambas. Con
 * $destacadas, además debe estar en la categoría de destacados. La
 * publicación en la que se escribe el shortcode nunca se lista a sí misma.
 *
 * @param array{categoria: list<string>, etiqueta: list<string>, hijas: bool} $atributos
 * @param bool $destacadas Solo entradas de la categoría de destacados.
 * @param int $cantidad Número máximo de entradas; 0 o menos no devuelve ninguna.
 * @param array<mixed> $omitir Entradas (WP_Post) que no se repiten.
 * @return array<mixed>
 */
function ce_entradas_shortcode(array $atributos, bool $destacadas, int $cantidad, array $omitir = []): array
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
        ];
    }
    if ($atributos['categoria'] !== []) {
        $tax_query[] = [
            'taxonomy' => 'category',
            'field' => 'slug',
            'terms' => $atributos['categoria'],
            'include_children' => $atributos['hijas'],
        ];
    }
    if ($atributos['etiqueta'] !== []) {
        $tax_query[] = [
            'taxonomy' => 'post_tag',
            'field' => 'slug',
            'terms' => $atributos['etiqueta'],
        ];
    }
    if (count($tax_query) > 1) {
        $tax_query['relation'] = 'AND';
    }

    $ids_omitidos = [];
    $actual       = get_the_ID();
    if (is_int($actual) && $actual > 0) {
        $ids_omitidos[] = $actual;
    }
    foreach ($omitir as $entrada) {
        if ($entrada instanceof WP_Post) {
            $ids_omitidos[] = $entrada->ID;
        }
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

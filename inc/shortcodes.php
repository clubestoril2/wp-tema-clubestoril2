<?php

/**
 * Shortcodes del tema.
 *
 * [ce_publicaciones]: últimas publicaciones de una o varias categorías o
 * etiquetas, con las mismas tarjetas y filas que la portada. Pensado para
 * páginas como «Restauración», que así muestran sus últimas entradas sin
 * código ni plugins. Los atributos se sanean en ClubEstoril2\Publicaciones.
 *
 * Ejemplos:
 *   [ce_publicaciones categoria="restauracion"]
 *   [ce_publicaciones categoria="restauracion" cantidad="6" formato="fila"]
 *   [ce_publicaciones etiqueta="padel" hijas="no" dias="0"]
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
    $entradas  = ce_entradas_shortcode($atributos);
    if ($entradas === []) {
        return '';
    }

    $dias = $atributos['dias'] === Publicaciones::DIAS_DEL_TEMA
        ? ce_opcion_entero('portada_dias_novedad')
        : $atributos['dias'];

    ob_start();
    if ($atributos['formato'] === Publicaciones::FORMATO_FILA) {
        echo '<div class="ce-publicaciones ce-filas">';
        ce_mostrar_entradas($entradas, 'card-fila', ['dias' => $dias]);
    } else {
        echo '<div class="ce-publicaciones ce-rejilla ce-rejilla--tarjetas ce-rejilla--carrusel">';
        ce_mostrar_entradas($entradas, 'card-tarjeta', ['dias' => $dias]);
    }
    echo '</div>';
    return (string) ob_get_clean();
}

/**
 * Entradas que pide el shortcode, de la más reciente a la más antigua.
 *
 * Si se indican categorías y etiquetas, la entrada debe cumplir ambas. La
 * publicación en la que se escribe el shortcode nunca se lista a sí misma.
 *
 * @param array{categoria: list<string>, etiqueta: list<string>, cantidad: int, hijas: bool} $atributos
 * @return array<mixed>
 */
function ce_entradas_shortcode(array $atributos): array
{
    $tax_query = [];
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

    $actual = get_the_ID();
    $consulta = new WP_Query(
        [
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => $atributos['cantidad'],
            'ignore_sticky_posts' => true,
            'no_found_rows' => true,
            'post__not_in' => is_int($actual) && $actual > 0 ? [$actual] : [],
            'tax_query' => $tax_query, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
        ]
    );
    return $consulta->posts;
}

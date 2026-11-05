<?php

/**
 * Portada.
 *
 * Estructura base: muestra las noticias destacadas y las anteriores según las
 * opciones del tema. La apertura de tipo portal de noticias, la franja de
 * utilidad y las franjas de sección se añaden en la fase de portada.
 *
 * @package ClubEstoril2
 */

use ClubEstoril2\Novedad;

get_header();

$ce_destacadas = ce_opcion_entero('portada_destacadas');
$ce_anteriores = ce_opcion_entero('portada_anteriores');
$ce_dias       = ce_opcion_entero('portada_dias_novedad');
$ce_excluir    = ce_opcion_lista('portada_excluir');

$ce_consulta_args = [
    'post_type' => 'post',
    'post_status' => 'publish',
    'posts_per_page' => $ce_destacadas + $ce_anteriores,
    'no_found_rows' => true,
];
if ($ce_excluir !== []) {
    $ce_consulta_args['tax_query'] = [ // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
        [
            'taxonomy' => 'category',
            'field' => 'slug',
            'terms' => $ce_excluir,
            'operator' => 'NOT IN',
        ],
    ];
}
$ce_consulta = new WP_Query($ce_consulta_args);
$ce_reparto  = Novedad::repartir(array_values($ce_consulta->posts), $ce_destacadas, $ce_anteriores);
?>
<div class="ce-contenedor ce-portada">
    <?php if ($ce_reparto['destacadas'] !== []) : ?>
        <section class="ce-portada__destacadas" aria-labelledby="ce-titulo-destacadas">
            <h2 id="ce-titulo-destacadas" class="ce-titulo-seccion">
                <?php esc_html_e('Destacado', 'clubestoril2'); ?>
            </h2>
            <div class="ce-rejilla ce-rejilla--tarjetas ce-rejilla--carrusel">
                <?php ce_mostrar_entradas($ce_reparto['destacadas'], 'card-tarjeta', ['dias' => $ce_dias]); ?>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($ce_reparto['anteriores'] !== []) : ?>
        <section class="ce-portada__anteriores" aria-labelledby="ce-titulo-anteriores">
            <h2 id="ce-titulo-anteriores" class="ce-titulo-seccion">
                <?php esc_html_e('Últimas noticias', 'clubestoril2'); ?>
            </h2>
            <div class="ce-filas">
                <?php ce_mostrar_entradas($ce_reparto['anteriores'], 'card-fila', ['dias' => $ce_dias]); ?>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($ce_reparto['destacadas'] === [] && $ce_reparto['anteriores'] === []) : ?>
        <?php get_template_part('template-parts/content/content-none'); ?>
    <?php endif; ?>
</div>
<?php
get_footer();

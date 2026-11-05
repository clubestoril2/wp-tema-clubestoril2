<?php

/**
 * Portada.
 *
 * «Destacado» muestra las entradas con la etiqueta «Destacados» y «Últimas
 * publicaciones», el resto. Las cantidades y las categorías excluidas son
 * opciones del tema.
 *
 * @package ClubEstoril2
 */

get_header();

$ce_dias    = ce_opcion_entero('portada_dias_novedad');
$ce_excluir = ce_opcion_lista('portada_excluir');

$ce_destacadas = ce_entradas_portada(true, ce_opcion_entero('portada_destacadas'), $ce_excluir);
$ce_anteriores = ce_entradas_portada(false, ce_opcion_entero('portada_anteriores'), $ce_excluir);
?>
<div class="ce-contenedor ce-portada">
    <?php if ($ce_destacadas !== []) : ?>
        <section class="ce-portada__destacadas" aria-labelledby="ce-titulo-destacadas">
            <h2 id="ce-titulo-destacadas" class="ce-titulo-seccion">
                <?php esc_html_e('Destacado', 'clubestoril2'); ?>
            </h2>
            <div class="ce-rejilla ce-rejilla--tarjetas ce-rejilla--carrusel">
                <?php ce_mostrar_entradas($ce_destacadas, 'card-tarjeta', ['dias' => $ce_dias]); ?>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($ce_anteriores !== []) : ?>
        <section class="ce-portada__anteriores" aria-labelledby="ce-titulo-anteriores">
            <h2 id="ce-titulo-anteriores" class="ce-titulo-seccion">
                <?php esc_html_e('Últimas publicaciones', 'clubestoril2'); ?>
            </h2>
            <div class="ce-filas">
                <?php ce_mostrar_entradas($ce_anteriores, 'card-fila', ['dias' => $ce_dias]); ?>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($ce_destacadas === [] && $ce_anteriores === []) : ?>
        <?php get_template_part('template-parts/content/content-none'); ?>
    <?php endif; ?>
</div>
<?php
get_footer();

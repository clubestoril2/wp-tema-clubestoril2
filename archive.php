<?php

/**
 * Listados de categorías, etiquetas, fechas y autores.
 *
 * Estructura base: la apertura con destacadas por sección se añade en la
 * fase de listados.
 *
 * @package ClubEstoril2
 */

get_header();
?>
<div class="ce-contenedor ce-listado">
    <header class="ce-listado__cabecera">
        <?php the_archive_title('<h1 class="ce-listado__titulo">', '</h1>'); ?>
        <?php the_archive_description('<div class="ce-listado__descripcion">', '</div>'); ?>
    </header>

    <?php if (have_posts()) : ?>
        <div class="ce-filas">
            <?php
            while (have_posts()) :
                the_post();
                get_template_part(
                    'template-parts/content/card-fila',
                    null,
                    ['dias' => ce_opcion_entero('seccion_dias_novedad')]
                );
            endwhile;
            ?>
        </div>
        <?php ce_paginacion(); ?>
    <?php else : ?>
        <?php get_template_part('template-parts/content/content-none'); ?>
    <?php endif; ?>
</div>
<?php
get_footer();

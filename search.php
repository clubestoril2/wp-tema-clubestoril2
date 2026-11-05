<?php

/**
 * Resultados de búsqueda.
 *
 * @package ClubEstoril2
 */

get_header();
?>
<div class="ce-contenedor ce-listado">
    <header class="ce-listado__cabecera">
        <h1 class="ce-listado__titulo">
            <?php
            printf(
                /* translators: %s: términos buscados */
                esc_html__('Resultados de «%s»', 'clubestoril2'),
                esc_html(get_search_query())
            );
            ?>
        </h1>
        <?php get_search_form(); ?>
    </header>

    <?php if (have_posts()) : ?>
        <div class="ce-filas">
            <?php
            while (have_posts()) :
                the_post();
                get_template_part('template-parts/content/card-fila');
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

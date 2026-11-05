<?php

/**
 * Plantilla de respaldo para cualquier listado.
 *
 * @package ClubEstoril2
 */

get_header();
?>
<div class="ce-contenedor ce-listado">
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

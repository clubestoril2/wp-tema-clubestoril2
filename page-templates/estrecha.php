<?php

/**
 * Template Name: Página estrecha
 * Template Post Type: page, post
 *
 * Plantilla con un ancho de lectura de 960 px en lugar del ancho completo de
 * la cabecera (1240 px) que usan por defecto páginas y entradas. Pensada
 * para textos largos sin tablas ni imágenes anchas. Se elige en el editor,
 * en Plantilla.
 *
 * @package ClubEstoril2
 */

get_header();
?>
<div class="ce-contenedor ce-contenedor--estrecho">
    <?php
    while (have_posts()) :
        the_post();
        get_template_part(
            'template-parts/content/' . (get_post_type() === 'post' ? 'content-single' : 'content-page')
        );
    endwhile;
    ?>
</div>
<?php
get_footer();

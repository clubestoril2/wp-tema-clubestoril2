<?php

/**
 * Template Name: Página ancha
 * Template Post Type: page, post
 *
 * Plantilla con el ancho completo de la cabecera y la portada (1240 px) en
 * lugar del ancho de lectura de páginas y entradas (960 px). Pensada para
 * contenido con tablas anchas, como «Tarifas». Se elige en el editor, en
 * Plantilla.
 *
 * @package ClubEstoril2
 */

get_header();
?>
<div class="ce-contenedor">
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

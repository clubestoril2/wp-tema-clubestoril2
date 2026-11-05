<?php

/**
 * Página de entradas (Ajustes › Lectura › Página de entradas): todas las
 * publicaciones, de la más reciente a la más antigua, sin las categorías de
 * la opción «listados_excluir».
 *
 * @package ClubEstoril2
 */

get_header();
?>
<div class="ce-contenedor ce-listado">
    <?php // Misma cabecera que los bloques de la portada. ?>
    <h1 class="ce-titulo-seccion">
        <?php
        $ce_titulo = single_post_title('', false);
        if (!is_string($ce_titulo) || $ce_titulo === '') {
            $ce_titulo = __('Publicaciones', 'clubestoril2');
        }
        echo esc_html($ce_titulo);
        ?>
    </h1>

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

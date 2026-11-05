<?php

/**
 * Página no encontrada.
 *
 * @package ClubEstoril2
 */

get_header();
?>
<div class="ce-contenedor ce-contenedor--estrecho ce-error">
    <h1><?php esc_html_e('Página no encontrada', 'clubestoril2'); ?></h1>
    <p>
        <?php
        esc_html_e(
            'La dirección no existe o ha cambiado. Prueba a buscar lo que necesitas o vuelve a la portada.',
            'clubestoril2'
        );
        ?>
    </p>
    <?php get_search_form(); ?>
    <p>
        <a class="ce-boton ce-boton--primario" href="<?php echo esc_url(home_url('/')); ?>">
            <?php esc_html_e('Ir a la portada', 'clubestoril2'); ?>
        </a>
    </p>
</div>
<?php
get_footer();

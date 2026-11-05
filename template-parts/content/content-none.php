<?php

/**
 * Mensaje cuando un listado no tiene resultados.
 *
 * @package ClubEstoril2
 */

?>
<section class="ce-sin-resultados">
    <h2><?php esc_html_e('No hay contenido que mostrar', 'clubestoril2'); ?></h2>
    <?php if (is_search()) : ?>
        <p>
            <?php
            esc_html_e('No se ha encontrado nada con esos términos. Prueba con otras palabras.', 'clubestoril2');
            ?>
        </p>
    <?php else : ?>
        <p><?php esc_html_e('Puedes usar el buscador para encontrar lo que necesitas.', 'clubestoril2'); ?></p>
    <?php endif; ?>
    <?php get_search_form(); ?>
</section>

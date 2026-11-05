<?php

/**
 * Barra de navegación principal.
 *
 * Estructura base: el mega-menú de escritorio y el panel lateral para móviles
 * se implementan en la fase de cabecera y navegación.
 *
 * @package ClubEstoril2
 */

?>
<nav class="ce-nav" aria-label="<?php esc_attr_e('Menú principal', 'clubestoril2'); ?>">
    <div class="ce-contenedor ce-nav__interior">
        <?php
        wp_nav_menu(
            [
                'theme_location' => 'primary',
                'container' => false,
                'menu_class' => 'ce-menu',
                'menu_id' => 'ce-menu-principal',
                'depth' => 4,
                'fallback_cb' => 'ce_menu_respaldo',
            ]
        );
        ?>
        <div class="ce-nav__buscar">
            <?php get_search_form(); ?>
        </div>
    </div>
</nav>

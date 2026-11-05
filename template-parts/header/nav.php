<?php

/**
 * Barra de navegación principal.
 *
 * En pantallas estrechas el menú y el buscador se pliegan tras el botón
 * «Menú» (lo gestiona assets/js/navigation.js). Sin JavaScript el botón no
 * aparece y el menú se muestra completo.
 *
 * @package ClubEstoril2
 */

?>
<nav
    class="ce-nav"
    aria-label="<?php esc_attr_e('Menú principal', 'clubestoril2'); ?>"
    data-ce-submenu="<?php esc_attr_e('Submenú', 'clubestoril2'); ?>"
>
    <div class="ce-contenedor ce-nav__interior">
        <button
            type="button"
            class="ce-nav__boton"
            data-ce-menu
            aria-expanded="false"
            aria-controls="ce-nav-panel"
            hidden
        >
            <?php ce_icono('menu', 'ce-nav__icono-abrir'); ?>
            <?php ce_icono('cerrar', 'ce-nav__icono-cerrar'); ?>
            <?php esc_html_e('Menú', 'clubestoril2'); ?>
        </button>
        <div class="ce-nav__panel" id="ce-nav-panel">
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
    </div>
</nav>

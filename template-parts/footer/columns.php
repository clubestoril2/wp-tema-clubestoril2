<?php

/**
 * Columnas del pie. Si no hay widgets, muestra la información del club.
 *
 * @package ClubEstoril2
 */

$ce_hay_widgets = is_active_sidebar('pie-1') || is_active_sidebar('pie-2') || is_active_sidebar('pie-3');
?>
<div class="ce-contenedor ce-pie__columnas">
    <?php if ($ce_hay_widgets) : ?>
        <?php foreach (['pie-1', 'pie-2', 'pie-3'] as $ce_area) : ?>
            <div class="ce-pie__columna">
                <?php dynamic_sidebar($ce_area); ?>
            </div>
        <?php endforeach; ?>
    <?php else : ?>
        <div class="ce-pie__columna ce-pie__club">
            <?php ce_logotipo(); ?>
            <p class="ce-pie__nombre"><?php bloginfo('name'); ?></p>
            <?php if (ce_opcion_texto('cabecera_eslogan') !== '') : ?>
                <p><?php echo esc_html(ce_opcion_texto('cabecera_eslogan')); ?></p>
            <?php endif; ?>
        </div>
        <div class="ce-pie__columna">
            <?php
            wp_nav_menu(
                [
                    'theme_location' => 'pie',
                    'container' => 'nav',
                    'container_aria_label' => __('Menú del pie', 'clubestoril2'),
                    'menu_class' => 'ce-pie__menu',
                    'depth' => 1,
                    'fallback_cb' => false,
                ]
            );
            ?>
        </div>
        <div class="ce-pie__columna">
            <?php get_template_part('template-parts/header/social-links'); ?>
        </div>
    <?php endif; ?>
</div>

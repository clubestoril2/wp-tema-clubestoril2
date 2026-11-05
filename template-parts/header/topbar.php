<?php

/**
 * Barra superior de información rápida: datos de contacto, redes sociales y
 * conmutador de modo de color. El eslogan no se muestra aquí para no repetir
 * la identidad del club, que ya está en la franja de marca; sigue en el pie.
 *
 * @package ClubEstoril2
 */

$ce_horario   = ce_opcion_texto('cabecera_horario');
$ce_telefono  = ce_opcion_texto('cabecera_telefono');
$ce_direccion = ce_opcion_texto('cabecera_direccion');
$ce_mapa      = ce_opcion_url('cabecera_mapa_url');
?>
<div class="ce-topbar">
    <div class="ce-contenedor ce-topbar__interior">
        <ul class="ce-topbar__datos">
            <?php if ($ce_horario !== '') : ?>
                <li class="ce-topbar__dato">
                    <?php ce_icono('reloj'); ?>
                    <span><?php echo esc_html($ce_horario); ?></span>
                </li>
            <?php endif; ?>
            <?php if ($ce_telefono !== '') : ?>
                <li class="ce-topbar__dato">
                    <?php ce_icono('telefono'); ?>
                    <a href="<?php echo esc_url('tel:' . preg_replace('/[^0-9+]/', '', $ce_telefono)); ?>">
                        <?php echo esc_html($ce_telefono); ?>
                    </a>
                </li>
            <?php endif; ?>
            <?php if ($ce_direccion !== '') : ?>
                <li class="ce-topbar__dato ce-topbar__dato--direccion">
                    <?php ce_icono('ubicacion'); ?>
                    <?php if ($ce_mapa !== '') : ?>
                        <a href="<?php echo esc_url($ce_mapa); ?>"><?php echo esc_html($ce_direccion); ?></a>
                    <?php else : ?>
                        <span><?php echo esc_html($ce_direccion); ?></span>
                    <?php endif; ?>
                </li>
            <?php endif; ?>
        </ul>

        <?php get_template_part('template-parts/header/social-links'); ?>

        <?php if (ce_opcion_booleano('mostrar_conmutador')) : ?>
            <?php get_template_part('template-parts/header/theme-toggle'); ?>
        <?php endif; ?>
    </div>
</div>

<?php

/**
 * Línea final del pie: derechos y política de privacidad.
 *
 * @package ClubEstoril2
 */

?>
<div class="ce-pie__info">
    <div class="ce-contenedor ce-pie__info-interior">
        <p>
            &copy; <?php echo esc_html((string) wp_date('Y')); ?>
            <?php echo esc_html(ce_opcion_texto('pie_texto')); ?>
        </p>
        <?php the_privacy_policy_link('<p>', '</p>'); ?>
    </div>
</div>

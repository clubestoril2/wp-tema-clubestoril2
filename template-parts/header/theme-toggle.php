<?php

/**
 * Conmutador entre modo claro y oscuro (lo gestiona assets/js/theme-toggle.js).
 *
 * @package ClubEstoril2
 */

?>
<button type="button" class="ce-conmutador" data-ce-conmutador aria-pressed="false" hidden>
    <?php ce_icono('luna', 'ce-conmutador__luna'); ?>
    <?php ce_icono('sol', 'ce-conmutador__sol'); ?>
    <span class="ce-oculto"><?php esc_html_e('Modo oscuro', 'clubestoril2'); ?></span>
</button>

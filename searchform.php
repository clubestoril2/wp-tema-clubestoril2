<?php

/**
 * Formulario de búsqueda.
 *
 * @package ClubEstoril2
 */

$ce_id_campo = wp_unique_id('ce-buscar-');
?>
<form role="search" method="get" class="ce-buscador" action="<?php echo esc_url(home_url('/')); ?>">
    <label class="ce-oculto" for="<?php echo esc_attr($ce_id_campo); ?>">
        <?php esc_html_e('Buscar en la web', 'clubestoril2'); ?>
    </label>
    <input type="search" id="<?php echo esc_attr($ce_id_campo); ?>" class="ce-buscador__campo" name="s"
           value="<?php echo esc_attr(get_search_query()); ?>"
           placeholder="<?php esc_attr_e('Buscar…', 'clubestoril2'); ?>">
    <button type="submit" class="ce-buscador__boton">
        <?php ce_icono('buscar'); ?>
        <span class="ce-oculto"><?php esc_html_e('Buscar', 'clubestoril2'); ?></span>
    </button>
</form>

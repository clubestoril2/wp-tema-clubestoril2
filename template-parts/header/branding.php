<?php

/**
 * Franja de marca: escudo, nombre, lema y botones destacados.
 *
 * @package ClubEstoril2
 */

$ce_socios_url    = ce_opcion_url('boton_socios_url');
$ce_contacto_url  = ce_opcion_url('boton_contacto_url');
$ce_etiqueta_nombre = is_front_page() ? 'h1' : 'p';
?>
<div class="ce-marca">
    <div class="ce-contenedor ce-marca__interior">
        <?php ce_logotipo(); ?>
        <div class="ce-marca__textos">
            <<?php echo tag_escape($ce_etiqueta_nombre); ?> class="ce-marca__nombre">
                <a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php bloginfo('name'); ?></a>
            </<?php echo tag_escape($ce_etiqueta_nombre); ?>>
            <?php if (get_bloginfo('description') !== '') : ?>
                <p class="ce-marca__lema"><?php bloginfo('description'); ?></p>
            <?php endif; ?>
        </div>
        <div class="ce-marca__botones">
            <?php if ($ce_socios_url !== '') : ?>
                <a class="ce-boton ce-boton--primario" href="<?php echo esc_url($ce_socios_url); ?>">
                    <?php echo esc_html(ce_opcion_texto('boton_socios_texto')); ?>
                </a>
            <?php endif; ?>
            <?php if ($ce_contacto_url !== '') : ?>
                <a class="ce-boton ce-boton--secundario" href="<?php echo esc_url($ce_contacto_url); ?>">
                    <?php echo esc_html(ce_opcion_texto('boton_contacto_texto')); ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

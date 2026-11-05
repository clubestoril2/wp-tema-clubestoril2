<?php

/**
 * Enlaces a las redes sociales configuradas en el Personalizador.
 *
 * En escritorio se muestran el icono y el nombre de cada red; en móvil, solo
 * el icono (el nombre sigue disponible para lectores de pantalla).
 *
 * @package ClubEstoril2
 */

$ce_enlaces = ce_redes_sociales();
if ($ce_enlaces === []) {
    return;
}
?>
<ul class="ce-redes" aria-label="<?php esc_attr_e('Redes sociales', 'clubestoril2'); ?>">
    <?php foreach ($ce_enlaces as $ce_enlace) : ?>
        <li>
            <a class="ce-redes__enlace ce-redes__enlace--<?php echo esc_attr($ce_enlace['red']); ?>"
               href="<?php echo esc_url($ce_enlace['url'], ['http', 'https', 'mailto']); ?>"
               rel="noopener me" target="_blank">
                <?php ce_icono('red-' . $ce_enlace['red'], 'ce-redes__icono'); ?>
                <span class="ce-redes__texto"><?php echo esc_html($ce_enlace['nombre']); ?></span>
            </a>
        </li>
    <?php endforeach; ?>
</ul>

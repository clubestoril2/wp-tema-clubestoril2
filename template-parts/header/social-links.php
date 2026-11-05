<?php

/**
 * Enlaces a las redes sociales configuradas en el Personalizador.
 *
 * Los iconos de marca se añadirán al sprite en la fase de cabecera; mientras,
 * se muestra el nombre de la red.
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
                <?php echo esc_html($ce_enlace['nombre']); ?>
            </a>
        </li>
    <?php endforeach; ?>
</ul>

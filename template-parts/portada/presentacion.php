<?php

/**
 * Presentación del club en la portada, entre el carrusel y «Destacado»:
 * titular, tres motivos para venir (con enlace a la cuota de socios o a su
 * categoría) y los botones de la cabecera, sobre un fondo de iconos de
 * deporte y cultura (assets/img/patron-presentacion.svg).
 *
 * @package ClubEstoril2
 */

/**
 * URL de una categoría por su slug, o cadena vacía si no existe.
 */
$ce_url_categoria = static function (string $slug): string {
    $categoria = get_category_by_slug($slug);
    if (!$categoria instanceof WP_Term) {
        return '';
    }
    $url = get_term_link($categoria);
    return is_string($url) ? $url : '';
};

$ce_motivos = [
    [
        'imagen' => 'ofertas',
        'titulo' => __('Para niños y familias', 'clubestoril2'),
        'texto' => __(
            'Consulta las ofertas de la cuota de socios para niños y familias, también monoparentales.',
            'clubestoril2'
        ),
        'enlace' => __('Ver cuotas', 'clubestoril2'),
        // La página de cuotas: la misma que el botón «Tarifas y socios».
        'url' => ce_opcion_url('boton_socios_url'),
    ],
    [
        'imagen' => 'deportes',
        'titulo' => __('Deporte y cultura todo el año', 'clubestoril2'),
        'texto' => __(
            'Disfruta de nuestras constantes actividades deportivas y socioculturales durante todo el año.',
            'clubestoril2'
        ),
        'enlace' => __('Ver actividades', 'clubestoril2'),
        'url' => $ce_url_categoria('actividades'),
    ],
    [
        'imagen' => 'restauracion',
        'titulo' => __('Un lugar para encontrarse', 'clubestoril2'),
        'texto' => __(
            'Piscina, jardines, pistas y nuestro restaurante cafetería te esperan para compartir buenos ratos.',
            'clubestoril2'
        ),
        'enlace' => __('Ver menús', 'clubestoril2'),
        'url' => $ce_url_categoria('menu-del-dia'),
    ],
];

$ce_socios_url   = ce_opcion_url('boton_socios_url');
$ce_contacto_url = ce_opcion_url('boton_contacto_url');
?>
<section class="ce-presentacion" aria-labelledby="ce-titulo-presentacion">
    <div class="ce-contenedor ce-presentacion__interior">
        <h2 id="ce-titulo-presentacion" class="ce-presentacion__titulo">
            <?php esc_html_e('¡Tu club deportivo y social de Móstoles!', 'clubestoril2'); ?>
        </h2>
        <p class="ce-presentacion__lema">
            <?php
            esc_html_e('Deporte, cultura y ocio para socios y familias, todo en un mismo lugar.', 'clubestoril2');
            ?>
        </p>

        <ul class="ce-presentacion__motivos">
            <?php foreach ($ce_motivos as $ce_motivo) : ?>
                <li class="ce-presentacion__motivo">
                    <img class="ce-presentacion__icono"
                         src="<?php echo esc_url(ce_imagen_categoria($ce_motivo['imagen'])); ?>"
                         alt="" width="56" height="56" loading="lazy">
                    <h3 class="ce-presentacion__subtitulo"><?php echo esc_html($ce_motivo['titulo']); ?></h3>
                    <p class="ce-presentacion__texto"><?php echo esc_html($ce_motivo['texto']); ?></p>
                    <?php if ($ce_motivo['url'] !== '') : ?>
                        <a class="ce-presentacion__enlace" href="<?php echo esc_url($ce_motivo['url']); ?>">
                            <?php echo esc_html($ce_motivo['enlace']); ?>
                        </a>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>

        <?php if ($ce_socios_url !== '' || $ce_contacto_url !== '') : ?>
            <p class="ce-presentacion__acciones">
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
            </p>
        <?php endif; ?>
    </div>
</section>

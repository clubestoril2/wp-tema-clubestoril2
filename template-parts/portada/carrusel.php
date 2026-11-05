<?php

/**
 * Carrusel de la portada, bajo el menú: las imágenes de la galería de la
 * página «carrusel». El movimiento, el orden aleatorio, la pausa y el botón
 * «Siguiente» los pone assets/js/carrusel.js; sin JavaScript la tira de
 * imágenes se puede desplazar a mano. El avance y los segundos entre avances
 * son opciones del tema (Personalizador › Carrusel).
 *
 * @package ClubEstoril2
 */

$ce_imagenes = ce_imagenes_carrusel();
if ($ce_imagenes === []) {
    return;
}
?>
<section
    class="ce-carrusel"
    aria-roledescription="<?php esc_attr_e('carrusel', 'clubestoril2'); ?>"
    aria-label="<?php esc_attr_e('Imágenes del club', 'clubestoril2'); ?>"
>
    <div
        class="ce-carrusel__pista"
        data-ce-carrusel
        data-ce-avance="<?php echo esc_attr((string) ce_opcion_entero('carrusel_avance')); ?>"
        data-ce-segundos="<?php echo esc_attr((string) ce_opcion_entero('carrusel_segundos')); ?>"
    >
        <?php
        foreach ($ce_imagenes as $ce_imagen) {
            echo wp_get_attachment_image(
                $ce_imagen,
                'medium_large',
                false,
                [
                    'class' => 'ce-carrusel__imagen',
                    'loading' => 'lazy',
                    'decoding' => 'async',
                    'sizes' => '(min-width: 900px) 320px, 140px',
                ]
            );
        }
        ?>
    </div>
    <div class="ce-carrusel__controles" data-ce-carrusel-controles hidden>
        <button
            type="button"
            class="ce-carrusel__boton"
            data-ce-carrusel-pausa
            data-ce-texto-pausar="<?php esc_attr_e('Pausar el carrusel', 'clubestoril2'); ?>"
            data-ce-texto-reanudar="<?php esc_attr_e('Reanudar el carrusel', 'clubestoril2'); ?>"
            aria-label="<?php esc_attr_e('Pausar el carrusel', 'clubestoril2'); ?>"
        >
            <?php ce_icono('pausa', 'ce-carrusel__icono-pausa'); ?>
            <?php ce_icono('reproducir', 'ce-carrusel__icono-reproducir'); ?>
        </button>
        <button
            type="button"
            class="ce-carrusel__boton"
            data-ce-carrusel-siguiente
            aria-label="<?php esc_attr_e('Siguientes imágenes', 'clubestoril2'); ?>"
        >
            <?php ce_icono('siguiente'); ?>
        </button>
    </div>
</section>

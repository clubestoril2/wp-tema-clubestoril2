<?php

/**
 * Tarjeta vertical: imagen, sección, titular, fecha y extracto.
 *
 * Argumentos: dias (int) plazo en días para el distintivo «Novedad»;
 * tamano (string) tamaño de imagen.
 *
 * @package ClubEstoril2
 */

$ce_dias   = isset($args['dias']) && is_int($args['dias']) ? $args['dias'] : 0;
$ce_tamano = isset($args['tamano']) && is_string($args['tamano']) ? $args['tamano'] : 'ce-tarjeta';
?>
<article <?php post_class('ce-tarjeta'); ?>>
    <a class="ce-tarjeta__imagen" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
        <?php ce_miniatura($ce_tamano); ?>
    </a>
    <div class="ce-tarjeta__cuerpo">
        <div class="ce-tarjeta__etiquetas">
            <?php ce_distintivo_novedad($ce_dias); ?>
            <?php ce_chip_seccion(); ?>
        </div>
        <h3 class="ce-tarjeta__titulo"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
        <?php ce_meta_entrada(); ?>
        <div class="ce-tarjeta__extracto"><?php the_excerpt(); ?></div>
    </div>
</article>

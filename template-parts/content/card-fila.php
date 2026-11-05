<?php

/**
 * Fila de listado: miniatura a la izquierda, sección, titular y fecha.
 *
 * Argumentos: dias (int) plazo en días para el distintivo «Novedad»;
 * extracto (bool) mostrar el extracto.
 *
 * @package ClubEstoril2
 */

$ce_dias     = isset($args['dias']) && is_int($args['dias']) ? $args['dias'] : 0;
$ce_extracto = !isset($args['extracto']) || $args['extracto'] === true;
?>
<article <?php post_class('ce-fila'); ?>>
    <a class="ce-fila__imagen" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
        <?php ce_miniatura('ce-fila'); ?>
    </a>
    <div class="ce-fila__cuerpo">
        <div class="ce-fila__etiquetas">
            <?php ce_distintivo_novedad($ce_dias); ?>
            <?php ce_chip_seccion(); ?>
        </div>
        <h3 class="ce-fila__titulo"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
        <?php ce_meta_entrada(); ?>
        <?php if ($ce_extracto) : ?>
            <div class="ce-fila__extracto"><?php the_excerpt(); ?></div>
        <?php endif; ?>
    </div>
</article>

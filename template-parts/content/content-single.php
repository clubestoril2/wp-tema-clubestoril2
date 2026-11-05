<?php

/**
 * Contenido de una entrada individual.
 *
 * @package ClubEstoril2
 */

?>
<article id="post-<?php the_ID(); ?>" <?php post_class('ce-articulo'); ?>>
    <header class="ce-articulo__cabecera">
        <?php ce_chip_seccion(); ?>
        <?php the_title('<h1 class="ce-articulo__titulo">', '</h1>'); ?>
        <?php ce_meta_entrada(); ?>
    </header>

    <?php if (has_post_thumbnail()) : ?>
        <figure class="ce-articulo__imagen">
            <?php the_post_thumbnail('large'); ?>
        </figure>
    <?php endif; ?>

    <div class="ce-contenido">
        <?php
        the_content();
        ce_paginas_internas();
        ?>
    </div>

    <?php if (has_tag()) : ?>
        <footer class="ce-articulo__pie">
            <?php
            the_tags('<p class="ce-etiquetas">' . esc_html__('Etiquetas:', 'clubestoril2') . ' ', ', ', '</p>');
            ?>
        </footer>
    <?php endif; ?>
</article>

<?php
$ce_rotulo = '<span class="ce-nav-entradas__rotulo">%s</span> %%title';
the_post_navigation(
    [
        'in_same_term' => true,
        'prev_text' => sprintf($ce_rotulo, esc_html__('Anterior', 'clubestoril2')),
        'next_text' => sprintf($ce_rotulo, esc_html__('Siguiente', 'clubestoril2')),
        'screen_reader_text' => __('Navegación de entradas', 'clubestoril2'),
        'class' => 'ce-nav-entradas',
    ]
);

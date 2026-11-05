<?php

/**
 * Contenido de una página.
 *
 * @package ClubEstoril2
 */

?>
<article id="post-<?php the_ID(); ?>" <?php post_class('ce-articulo ce-articulo--pagina'); ?>>
    <header class="ce-articulo__cabecera">
        <?php the_title('<h1 class="ce-articulo__titulo">', '</h1>'); ?>
    </header>

    <div class="ce-contenido">
        <?php
        the_content();
        ce_paginas_internas();
        ?>
    </div>
</article>

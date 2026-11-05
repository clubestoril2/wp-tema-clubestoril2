<?php

/**
 * Página «Categorías» (dirección /categorias/): tarjetas con la imagen, el
 * nombre y el número de publicaciones de cada categoría, en orden
 * alfabético y sin las de la opción «categorias_ocultas».
 *
 * La primera tarjeta, «Todas», enlaza a la página de entradas (Ajustes ›
 * Lectura) si está elegida. La imagen de cada categoría es
 * assets/img/categorias/<slug>.svg (o generica.svg si no hay una propia).
 * El contenido que se escriba en la página, si lo hay, se muestra encima de
 * las tarjetas.
 *
 * @package ClubEstoril2
 */

get_header();
?>
<div class="ce-contenedor ce-listado">
    <?php
    while (have_posts()) :
        the_post();
        ?>
        <h1 class="ce-titulo-seccion"><?php the_title(); ?></h1>
        <?php if (trim(get_the_content()) !== '') : ?>
            <div class="ce-contenido"><?php the_content(); ?></div>
        <?php endif; ?>
    <?php endwhile; ?>

    <?php
    $ce_categorias = ce_categorias_visibles();
    // «Todas» va la primera y lleva a la página de entradas, si está elegida.
    $ce_url_todas = ce_url_publicaciones();
    if ($ce_url_todas !== '') {
        array_unshift(
            $ce_categorias,
            [
                'slug' => 'todas',
                'nombre' => __('Todas', 'clubestoril2'),
                'total' => (int) wp_count_posts('post')->publish,
                'url' => $ce_url_todas,
            ]
        );
    }
    ?>
    <?php if ($ce_categorias !== []) : ?>
        <ul class="ce-rejilla ce-rejilla--categorias">
            <?php foreach ($ce_categorias as $ce_categoria) : ?>
                <li>
                    <a class="ce-categoria" href="<?php echo esc_url($ce_categoria['url']); ?>">
                        <img class="ce-categoria__imagen"
                             src="<?php echo esc_url(ce_imagen_categoria($ce_categoria['slug'])); ?>"
                             alt="" width="96" height="96" loading="lazy">
                        <span class="ce-categoria__nombre"><?php echo esc_html($ce_categoria['nombre']); ?></span>
                        <span class="ce-categoria__total">
                            <?php
                            echo esc_html(
                                sprintf(
                                    /* translators: %s: número de publicaciones de la categoría. */
                                    _n('%s publicación', '%s publicaciones', $ce_categoria['total'], 'clubestoril2'),
                                    number_format_i18n($ce_categoria['total'])
                                )
                            );
                            ?>
                        </span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else : ?>
        <?php get_template_part('template-parts/content/content-none'); ?>
    <?php endif; ?>
</div>
<?php
get_footer();

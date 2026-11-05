<?php

/**
 * Listados de categorías, etiquetas, fechas y autores.
 *
 * En las categorías, «Ver categorías» junto al título enlaza a la página
 * «Categorías» si existe.
 *
 * @package ClubEstoril2
 */

get_header();
?>
<div class="ce-contenedor ce-listado">
    <header class="ce-listado__cabecera">
        <div class="ce-listado__encabezado">
            <?php the_archive_title('<h1 class="ce-listado__titulo">', '</h1>'); ?>
            <?php $ce_url_categorias = is_category() ? ce_url_categorias() : ''; ?>
            <?php if ($ce_url_categorias !== '') : ?>
                <a class="ce-boton ce-boton--secundario" href="<?php echo esc_url($ce_url_categorias); ?>">
                    <?php esc_html_e('Ver categorías', 'clubestoril2'); ?>
                </a>
            <?php endif; ?>
        </div>
        <?php the_archive_description('<div class="ce-listado__descripcion">', '</div>'); ?>
    </header>

    <?php if (have_posts()) : ?>
        <div class="ce-filas">
            <?php
            while (have_posts()) :
                the_post();
                get_template_part(
                    'template-parts/content/card-fila',
                    null,
                    ['dias' => ce_opcion_entero('seccion_dias_novedad')]
                );
            endwhile;
            ?>
        </div>
        <?php ce_paginacion(); ?>
    <?php else : ?>
        <?php get_template_part('template-parts/content/content-none'); ?>
    <?php endif; ?>
</div>
<?php
get_footer();

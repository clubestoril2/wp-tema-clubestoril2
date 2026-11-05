<?php

/**
 * Portada.
 *
 * Bajo el menú, el carrusel con las imágenes de la página «carrusel».
 * «Destacado» muestra las últimas entradas de la categoría «Destacado» y
 * «Últimas publicaciones», todas las demás, de cualquier categoría (también
 * destacadas que no han cabido arriba). Las cantidades y las categorías
 * excluidas son opciones del tema. Al final, «Ver más publicaciones» enlaza
 * a la página de entradas si está elegida en Ajustes › Lectura y «Ver
 * categorías», a la página «Categorías» si existe.
 *
 * @package ClubEstoril2
 */

get_header();
get_template_part('template-parts/portada/carrusel');

$ce_dias    = ce_opcion_entero('portada_dias_novedad');
$ce_excluir = ce_opcion_lista('portada_excluir');

$ce_destacadas = ce_entradas_portada(true, ce_opcion_entero('portada_destacadas'), $ce_excluir);
$ce_anteriores = ce_entradas_portada(false, ce_opcion_entero('portada_anteriores'), $ce_excluir, $ce_destacadas);
?>
<div class="ce-contenedor ce-portada">
    <?php if ($ce_destacadas !== []) : ?>
        <section class="ce-portada__destacadas" aria-labelledby="ce-titulo-destacadas">
            <h2 id="ce-titulo-destacadas" class="ce-titulo-seccion">
                <?php esc_html_e('Destacado', 'clubestoril2'); ?>
            </h2>
            <div class="ce-rejilla ce-rejilla--tarjetas ce-rejilla--carrusel">
                <?php ce_mostrar_entradas($ce_destacadas, 'card-tarjeta', ['dias' => $ce_dias]); ?>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($ce_anteriores !== []) : ?>
        <section class="ce-portada__anteriores" aria-labelledby="ce-titulo-anteriores">
            <h2 id="ce-titulo-anteriores" class="ce-titulo-seccion">
                <?php esc_html_e('Últimas publicaciones', 'clubestoril2'); ?>
            </h2>
            <div class="ce-filas">
                <?php ce_mostrar_entradas($ce_anteriores, 'card-fila', ['dias' => $ce_dias]); ?>
            </div>
            <?php
            $ce_url_mas        = ce_url_publicaciones();
            $ce_url_categorias = ce_url_categorias();
            ?>
            <?php if ($ce_url_mas !== '' || $ce_url_categorias !== '') : ?>
                <p class="ce-portada__mas">
                    <?php if ($ce_url_mas !== '') : ?>
                        <a class="ce-boton ce-boton--secundario" href="<?php echo esc_url($ce_url_mas); ?>">
                            <?php esc_html_e('Ver más publicaciones', 'clubestoril2'); ?>
                        </a>
                    <?php endif; ?>
                    <?php if ($ce_url_categorias !== '') : ?>
                        <a class="ce-boton ce-boton--secundario" href="<?php echo esc_url($ce_url_categorias); ?>">
                            <?php esc_html_e('Ver categorías', 'clubestoril2'); ?>
                        </a>
                    <?php endif; ?>
                </p>
            <?php endif; ?>
        </section>
    <?php endif; ?>

    <?php if ($ce_destacadas === [] && $ce_anteriores === []) : ?>
        <?php get_template_part('template-parts/content/content-none'); ?>
    <?php endif; ?>
</div>
<?php
get_footer();

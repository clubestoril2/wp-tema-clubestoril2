<?php

/**
 * Cabecera común: barra de información rápida, marca y navegación.
 *
 * @package ClubEstoril2
 */

?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="ce-saltar" href="#contenido"><?php esc_html_e('Saltar al contenido', 'clubestoril2'); ?></a>

<header class="ce-cabecera">
    <?php get_template_part('template-parts/header/topbar'); ?>
    <?php get_template_part('template-parts/header/branding'); ?>
    <?php get_template_part('template-parts/header/nav'); ?>
</header>

<main id="contenido" class="ce-principal" tabindex="-1">

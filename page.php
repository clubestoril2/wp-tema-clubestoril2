<?php

/**
 * Página.
 *
 * @package ClubEstoril2
 */

get_header();
?>
<div class="ce-contenedor ce-contenedor--estrecho">
    <?php
    while (have_posts()) :
        the_post();
        get_template_part('template-parts/content/content-page');
    endwhile;
    ?>
</div>
<?php
get_footer();

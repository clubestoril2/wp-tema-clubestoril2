<?php

/**
 * Atributo nativo desde PHP 8.2. Los stubs de WordPress lo usan (por ejemplo
 * en WP_Customize_Control); al analizar con PHP 8.1, Phan necesita conocerlo.
 * Solo lo carga .phan/config.php con PHP < 8.2; no forma parte del tema.
 */

#[Attribute(Attribute::TARGET_CLASS)]
final class AllowDynamicProperties
{
}

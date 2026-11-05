<?php

/**
 * Arranque del tema Club Estoril II.
 *
 * La lógica pura (sin dependencias de WordPress) vive en src/ bajo el espacio
 * de nombres ClubEstoril2 y se prueba con PHPUnit. El código que se integra con
 * WordPress (ganchos, consultas, plantillas) vive en inc/.
 *
 * @package ClubEstoril2
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

define('CE_VERSION', '0.1.0');
define('CE_DIR', get_template_directory());
define('CE_URI', get_template_directory_uri());

require_once CE_DIR . '/inc/autoload.php';
require_once CE_DIR . '/inc/options.php';
require_once CE_DIR . '/inc/setup.php';
require_once CE_DIR . '/inc/enqueue.php';
require_once CE_DIR . '/inc/template-tags.php';

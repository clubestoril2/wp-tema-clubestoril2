<?php

/**
 * Cargador automático de las clases de src/ (espacio de nombres ClubEstoril2).
 *
 * El tema no distribuye vendor/: Composer solo se usa en desarrollo para las
 * herramientas de calidad, así que en producción se registra este cargador.
 *
 * @package ClubEstoril2
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

const CE_ESPACIO_NOMBRES = 'ClubEstoril2\\';

spl_autoload_register(
    static function (string $clase): void {
        if (!str_starts_with($clase, CE_ESPACIO_NOMBRES)) {
            return;
        }
        $relativa = str_replace('\\', '/', substr($clase, strlen(CE_ESPACIO_NOMBRES)));
        $fichero  = CE_DIR . '/src/' . $relativa . '.php';
        if (is_readable($fichero)) {
            require_once $fichero;
        }
    }
);

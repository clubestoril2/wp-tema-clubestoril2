<?php

/**
 * Genera assets/img/iconos-menu.svg con los iconos de src/IconosMenu.php.
 *
 * Uso: php bin/iconos-menu.php
 *
 * Descarga cada icono de Tabler Icons (versión IconosMenu::VERSION_TABLER) y
 * lo convierte en un <symbol> del sprite. Hay que volver a ejecutarlo tras
 * añadir o quitar iconos del catálogo.
 *
 * @package ClubEstoril2
 */

declare(strict_types=1);

use ClubEstoril2\IconosMenu;

require dirname(__DIR__) . '/src/IconosMenu.php';

const CE_TABLER_URL = 'https://cdn.jsdelivr.net/npm/@tabler/icons@%s/icons/outline/%s.svg';

$simbolos = [];
foreach (array_keys(IconosMenu::todos()) as $icono) {
    $url = sprintf(CE_TABLER_URL, IconosMenu::VERSION_TABLER, $icono);
    // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- herramienta de línea de órdenes, sin WordPress.
    $svg = file_get_contents($url);
    if ($svg === false) {
        fwrite(STDERR, "No se ha podido descargar «{$icono}» ({$url}).\n");
        exit(1);
    }

    $documento = new DOMDocument();
    if (!$documento->loadXML($svg)) {
        fwrite(STDERR, "El icono «{$icono}» no es un SVG válido.\n");
        exit(1);
    }

    $trazos = [];
    $raiz = $documento->documentElement;
    if ($raiz === null) {
        fwrite(STDERR, "El icono «{$icono}» está vacío.\n");
        exit(1);
    }
    foreach ($raiz->childNodes as $nodo) {
        if (!$nodo instanceof DOMElement) {
            continue;
        }
        // Tabler añade un rectángulo transparente de 24×24 para fijar el tamaño.
        if ($nodo->getAttribute('stroke') === 'none' && $nodo->getAttribute('d') === 'M0 0h24v24H0z') {
            continue;
        }
        $nodo->removeAttribute('class');
        $trazos[] = '      ' . trim((string) $documento->saveXML($nodo));
    }

    $simbolos[] = sprintf(
        "  <symbol id=\"%s\" viewBox=\"0 0 24 24\">\n"
        . "    <g fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\""
        . " stroke-linecap=\"round\" stroke-linejoin=\"round\">\n%s\n    </g>\n  </symbol>",
        $icono,
        implode("\n", $trazos)
    );
}

$sprite = sprintf(
    "<svg xmlns=\"http://www.w3.org/2000/svg\">\n"
    . "  <!-- Iconos del menú: Tabler Icons %s (https://tabler.io/icons), licencia MIT,\n"
    . "       © 2020-2024 Paweł Kuna. Generado con bin/iconos-menu.php; no editar a mano. -->\n"
    . "%s\n</svg>\n",
    IconosMenu::VERSION_TABLER,
    implode("\n", $simbolos)
);

$destino = dirname(__DIR__) . '/assets/img/iconos-menu.svg';
// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents -- herramienta de línea de órdenes, sin WordPress.
if (file_put_contents($destino, $sprite) === false) {
    fwrite(STDERR, "No se ha podido escribir {$destino}.\n");
    exit(1);
}
fwrite(STDOUT, sprintf("Generado %s con %d iconos.\n", $destino, count($simbolos)));

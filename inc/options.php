<?php

/**
 * Lectura de las opciones del tema guardadas con el Personalizador.
 *
 * Los valores por defecto y el saneamiento están en ClubEstoril2\Opciones.
 *
 * @package ClubEstoril2
 */

declare(strict_types=1);

use ClubEstoril2\Opciones;

defined('ABSPATH') || exit;

/**
 * Valor saneado de una opción del tema.
 *
 * @return int|bool|string|list<string>
 */
function ce_opcion(string $clave): int|bool|string|array
{
    return Opciones::sanear($clave, get_theme_mod($clave, Opciones::defecto($clave)));
}

function ce_opcion_entero(string $clave): int
{
    $valor = ce_opcion($clave);
    return is_int($valor) ? $valor : 0;
}

function ce_opcion_booleano(string $clave): bool
{
    return ce_opcion($clave) === true;
}

function ce_opcion_texto(string $clave): string
{
    $valor = ce_opcion($clave);
    return is_string($valor) ? $valor : '';
}

/**
 * @return list<string>
 */
function ce_opcion_lista(string $clave): array
{
    $valor = ce_opcion($clave);
    return is_array($valor) ? $valor : [];
}

/**
 * URL absoluta de una opción de tipo URL. Las rutas («/ruta/») se resuelven
 * contra la dirección del sitio.
 */
function ce_opcion_url(string $clave): string
{
    $url = ce_opcion_texto($clave);
    if ($url === '') {
        return '';
    }
    return str_starts_with($url, '/') ? home_url($url) : $url;
}

/**
 * Modo de color forzado («light» o «dark») o cadena vacía si es automático.
 */
function ce_modo_color(): string
{
    $modo = ce_opcion_texto('modo_color');
    return $modo === 'auto' ? '' : $modo;
}

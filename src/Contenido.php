<?php

declare(strict_types=1);

namespace ClubEstoril2;

/**
 * Ajustes al HTML del contenido de entradas y páginas.
 */
final class Contenido
{
    /**
     * Envuelve cada tabla en <div class="ce-tabla">, que la deja desplazarse
     * en horizontal solo si no cabe, sin que la tabla deje de ocupar todo el
     * ancho. Las que ya están envueltas se dejan como están.
     */
    public static function envolverTablas(string $html): string
    {
        if (stripos($html, '<table') === false || str_contains($html, 'class="ce-tabla"')) {
            return $html;
        }
        $html = (string) preg_replace('/<table\b/i', '<div class="ce-tabla"><table', $html);
        return (string) preg_replace('#</table\s*>#i', '</table></div>', $html);
    }

    /**
     * Valores del atributo «type» de <ol> y su estilo de numeración en CSS.
     */
    private const TIPOS_DE_LISTA = [
        '1' => 'decimal',
        'a' => 'lower-alpha',
        'A' => 'upper-alpha',
        'i' => 'lower-roman',
        'I' => 'upper-roman',
    ];

    /**
     * Copia el atributo «type» de cada <ol> en data-ce-lista con el nombre
     * del estilo CSS (type="A" → data-ce-lista="upper-alpha").
     *
     * En HTML los selectores de atributo no distinguen mayúsculas en «type» y
     * Chrome no admite el indicador «s», así que solo con CSS no se puede
     * separar type="a" de type="A". El tema pinta el número de las listas
     * dentro de un círculo y necesita saber el estilo de cada una.
     */
    public static function marcarTiposDeLista(string $html): string
    {
        if (stripos($html, '<ol') === false) {
            return $html;
        }
        return (string) preg_replace_callback(
            '/<ol\b[^>]*>/i',
            static function (array $coincidencia): string {
                $etiqueta = $coincidencia[0];
                if (stripos($etiqueta, 'data-ce-lista') !== false) {
                    return $etiqueta;
                }
                $tipo = preg_match('/\stype\s*=\s*(["\']?)([1aAiI])\1(?=[\s\/>])/i', $etiqueta, $partes) === 1
                    ? (self::TIPOS_DE_LISTA[$partes[2]] ?? null)
                    : null;
                if ($tipo === null) {
                    return $etiqueta;
                }
                return substr($etiqueta, 0, 3) . ' data-ce-lista="' . $tipo . '"' . substr($etiqueta, 3);
            },
            $html
        );
    }
}

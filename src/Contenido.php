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
}

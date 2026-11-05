<?php

declare(strict_types=1);

namespace ClubEstoril2;

/**
 * Imágenes del carrusel de la portada (galería de la página «carrusel»).
 */
final class Carrusel
{
    /**
     * Identificadores de una lista separada por comas, como el atributo «ids»
     * del shortcode [gallery]. Sin repetidos y en el orden recibido.
     *
     * @return list<int>
     */
    public static function ids(string $lista): array
    {
        $ids = [];
        foreach (explode(',', $lista) as $parte) {
            $parte = trim($parte);
            if (preg_match('/^\d+$/', $parte) === 1 && (int) $parte > 0) {
                $ids[] = (int) $parte;
            }
        }
        return array_values(array_unique($ids));
    }

    /**
     * Identificadores de las imágenes de la biblioteca de medios que aparecen
     * en un contenido HTML (clase «wp-image-N» de los editores de WordPress).
     * Sin repetidos y en orden de aparición.
     *
     * @return list<int>
     */
    public static function idsDeContenido(string $html): array
    {
        if (preg_match_all('/(?:^|[\s"\'])wp-image-(\d+)(?=[\s"\'])/', $html, $coincidencias) === false) {
            return [];
        }
        return self::ids(implode(',', $coincidencias[1]));
    }
}

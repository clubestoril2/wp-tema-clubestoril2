<?php

declare(strict_types=1);

namespace ClubEstoril2;

/**
 * Listado de categorías de la página «Categorías».
 */
final class Categorias
{
    /**
     * Quita las categorías ocultas y las ordena alfabéticamente por nombre,
     * sin distinguir mayúsculas ni tildes y con la «ñ» tras la «n».
     *
     * @param list<array{slug: string, nombre: string, total: int}> $categorias
     * @param list<string> $ocultas Slugs de las categorías que no se muestran.
     * @return list<array{slug: string, nombre: string, total: int}>
     */
    public static function listado(array $categorias, array $ocultas): array
    {
        $visibles = array_values(
            array_filter(
                $categorias,
                static fn(array $categoria): bool => !in_array($categoria['slug'], $ocultas, true)
            )
        );
        usort(
            $visibles,
            static fn(array $a, array $b): int => strcmp(self::clave($a['nombre']), self::clave($b['nombre']))
        );
        return $visibles;
    }

    /**
     * Clave de ordenación: minúsculas, sin tildes y con la «ñ» entre la «n» y
     * la «o».
     */
    private static function clave(string $nombre): string
    {
        return strtr(
            mb_strtolower(trim($nombre), 'UTF-8'),
            [
                'á' => 'a', 'à' => 'a', 'ä' => 'a', 'â' => 'a',
                'é' => 'e', 'è' => 'e', 'ë' => 'e', 'ê' => 'e',
                'í' => 'i', 'ì' => 'i', 'ï' => 'i', 'î' => 'i',
                'ó' => 'o', 'ò' => 'o', 'ö' => 'o', 'ô' => 'o',
                'ú' => 'u', 'ù' => 'u', 'ü' => 'u', 'û' => 'u',
                'ç' => 'c',
                'ñ' => "n\u{7f}",
            ]
        );
    }
}

<?php

declare(strict_types=1);

namespace ClubEstoril2\Tests;

use ClubEstoril2\Categorias;
use PHPUnit\Framework\TestCase;

final class CategoriasTest extends TestCase
{
    /**
     * @return array{slug: string, nombre: string, total: int}
     */
    private static function categoria(string $slug, string $nombre): array
    {
        return ['slug' => $slug, 'nombre' => $nombre, 'total' => 1];
    }

    /**
     * @param list<array{slug: string, nombre: string, total: int}> $listado
     * @return list<string>
     */
    private static function slugs(array $listado): array
    {
        return array_map(static fn(array $categoria): string => $categoria['slug'], $listado);
    }

    public function testQuitaLasOcultas(): void
    {
        $listado = Categorias::listado(
            [
                self::categoria('facebook', 'Facebook'),
                self::categoria('deportes', 'Deportes'),
                self::categoria('destacado', 'Destacado'),
                self::categoria('sin-categoria', 'Sin categoría'),
            ],
            ['facebook', 'destacado', 'sin-categoria']
        );
        self::assertSame(['deportes'], self::slugs($listado));
    }

    public function testOrdenAlfabeticoSinTildesNiMayusculas(): void
    {
        $listado = Categorias::listado(
            [
                self::categoria('talleres', 'Talleres'),
                self::categoria('restauracion', 'Restauración'),
                self::categoria('actividades-culturales', 'Actividades culturales'),
                self::categoria('ofertas', 'ofertas'),
                self::categoria('actividades', 'Actividades'),
                self::categoria('menu-del-dia', 'Menú del día'),
                self::categoria('noticias', 'Noticias'),
            ],
            []
        );
        self::assertSame(
            [
                'actividades',
                'actividades-culturales',
                'menu-del-dia',
                'noticias',
                'ofertas',
                'restauracion',
                'talleres',
            ],
            self::slugs($listado)
        );
    }

    public function testLaEnieVaEntreLaEneYLaO(): void
    {
        $listado = Categorias::listado(
            [
                self::categoria('o', 'Ocio'),
                self::categoria('enie', 'Ñandú'),
                self::categoria('n', 'Nzz'),
            ],
            []
        );
        self::assertSame(['n', 'enie', 'o'], self::slugs($listado));
    }

    public function testListaVacia(): void
    {
        self::assertSame([], Categorias::listado([], ['facebook']));
    }
}

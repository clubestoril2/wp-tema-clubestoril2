<?php

declare(strict_types=1);

namespace ClubEstoril2\Tests;

use ClubEstoril2\Carrusel;
use PHPUnit\Framework\TestCase;

final class CarruselTest extends TestCase
{
    public function testIdsDelShortcode(): void
    {
        self::assertSame([12, 7, 30], Carrusel::ids('12,7, 30'));
    }

    public function testIdsSinRepetidosNiValoresRaros(): void
    {
        self::assertSame([5, 9], Carrusel::ids('5, x, 9, 5, -3, 0, '));
        self::assertSame([], Carrusel::ids(''));
    }

    public function testIdsDeContenidoDelEditorDeBloques(): void
    {
        $html = '<figure class="wp-block-gallery"><figure class="wp-block-image">'
            . '<img src="/a.jpg" class="wp-image-42"/></figure>'
            . '<figure class="wp-block-image"><img src="/b.jpg" class="alignnone wp-image-8 size-large"/></figure>'
            . '<img src="/c.jpg" class="wp-image-42"/></figure>';
        self::assertSame([42, 8], Carrusel::idsDeContenido($html));
    }

    public function testIdsDeContenidoSinImagenes(): void
    {
        self::assertSame([], Carrusel::idsDeContenido('<p>Sin imágenes</p>'));
        self::assertSame([], Carrusel::idsDeContenido('<img class="mi-wp-image-5" src="/x.jpg">'));
    }
}

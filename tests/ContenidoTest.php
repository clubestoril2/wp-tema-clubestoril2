<?php

declare(strict_types=1);

namespace ClubEstoril2\Tests;

use ClubEstoril2\Contenido;
use PHPUnit\Framework\TestCase;

final class ContenidoTest extends TestCase
{
    public function testSinTablasNoCambia(): void
    {
        $html = '<p>Texto sin tablas.</p>';
        self::assertSame($html, Contenido::envolverTablas($html));
    }

    public function testEnvuelveCadaTabla(): void
    {
        $html = '<table class="tabla" style="width: 100%;"><tr><td>1</td></tr></table>'
            . '<p>Entre</p><TABLE><tr><td>2</td></tr></TABLE >';
        self::assertSame(
            '<div class="ce-tabla"><table class="tabla" style="width: 100%;"><tr><td>1</td></tr></table></div>'
            . '<p>Entre</p><div class="ce-tabla"><table><tr><td>2</td></tr></table></div>',
            Contenido::envolverTablas($html)
        );
    }

    public function testNoVuelveAEnvolver(): void
    {
        $html = Contenido::envolverTablas('<table><tr><td>1</td></tr></table>');
        self::assertSame($html, Contenido::envolverTablas($html));
    }

    public function testNoConfundeOtrasEtiquetas(): void
    {
        $html = '<tablero>no es una tabla</tablero>';
        self::assertSame($html, Contenido::envolverTablas($html));
    }
}

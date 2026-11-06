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

    public function testMarcaElTipoDeLasListasNumeradas(): void
    {
        self::assertSame(
            '<ol data-ce-lista="upper-alpha" type="A"><li>Uno</li></ol>'
            . '<ol data-ce-lista="lower-alpha" start="2" type=\'a\'><li>Dos</li></ol>'
            . '<OL data-ce-lista="upper-roman" class="x" type=I><li>Tres</li></OL>',
            Contenido::marcarTiposDeLista(
                '<ol type="A"><li>Uno</li></ol>'
                . '<ol start="2" type=\'a\'><li>Dos</li></ol>'
                . '<OL class="x" type=I><li>Tres</li></OL>'
            )
        );
    }

    public function testNoMarcaListasSinTipoValido(): void
    {
        $html = '<ol><li>1</li></ol><ol data-type="A"><li>2</li></ol>'
            . '<ol type="disc"><li>3</li></ol><ol style="list-style-type: lower-alpha"><li>4</li></ol>'
            . '<ul type="A"><li>5</li></ul><old type="A">';
        self::assertSame($html, Contenido::marcarTiposDeLista($html));
    }

    public function testNoVuelveAMarcar(): void
    {
        $html = Contenido::marcarTiposDeLista('<ol type="i"><li>1</li></ol>');
        self::assertSame($html, Contenido::marcarTiposDeLista($html));
    }

    public function testNoConfundeOtrasEtiquetas(): void
    {
        $html = '<tablero>no es una tabla</tablero>';
        self::assertSame($html, Contenido::envolverTablas($html));
    }
}

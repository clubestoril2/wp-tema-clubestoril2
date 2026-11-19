<?php

declare(strict_types=1);

namespace ClubEstoril2\Tests;

use ClubEstoril2\Publicaciones;
use PHPUnit\Framework\TestCase;

final class PublicacionesTest extends TestCase
{
    public function testSinAtributosUsaLosValoresPorDefecto(): void
    {
        self::assertSame(
            [
                'categoria' => [],
                'etiqueta' => [],
                'cantidad' => Publicaciones::CANTIDAD_DEFECTO,
                'formato' => Publicaciones::FORMATO_TARJETA,
                'hijas' => true,
                'dias' => Publicaciones::DIAS_DEL_TEMA,
                'destacadas' => 0,
                'titulo_destacadas' => null,
                'titulo_anteriores' => null,
            ],
            Publicaciones::sanear([])
        );
    }

    public function testAtributosCompletos(): void
    {
        $resultado = Publicaciones::sanear(
            [
                'categoria' => 'restauracion, Menu-del-dia',
                'etiqueta' => 'padel',
                'cantidad' => '6',
                'formato' => 'Fila',
                'hijas' => 'no',
                'dias' => '7',
                'destacadas' => '2',
                'titulo_destacadas' => ' Lo mejor ',
                'titulo_anteriores' => '',
            ]
        );
        self::assertSame(['restauracion', 'menu-del-dia'], $resultado['categoria']);
        self::assertSame(['padel'], $resultado['etiqueta']);
        self::assertSame(6, $resultado['cantidad']);
        self::assertSame(Publicaciones::FORMATO_FILA, $resultado['formato']);
        self::assertFalse($resultado['hijas']);
        self::assertSame(7, $resultado['dias']);
        self::assertSame(2, $resultado['destacadas']);
        self::assertSame('Lo mejor', $resultado['titulo_destacadas']);
        self::assertSame('', $resultado['titulo_anteriores']);
    }

    public function testDestacadasSeLimitaAlRango(): void
    {
        self::assertSame(0, Publicaciones::destacadas(null));
        self::assertSame(0, Publicaciones::destacadas('-2'));
        self::assertSame(0, Publicaciones::destacadas('muchas'));
        self::assertSame(3, Publicaciones::destacadas('3'));
        self::assertSame(Publicaciones::DESTACADAS_MAXIMO, Publicaciones::destacadas(99));
    }

    public function testTituloNuloSiNoSeIndica(): void
    {
        self::assertNull(Publicaciones::titulo(null));
        self::assertNull(Publicaciones::titulo(3));
        self::assertSame('', Publicaciones::titulo('  '));
        self::assertSame('Novedades', Publicaciones::titulo(' Novedades '));
    }

    public function testSlugsDescartaLosNoValidosYLosRepetidos(): void
    {
        self::assertSame(
            ['noticias', 'deportes'],
            Publicaciones::slugs('noticias,, <script>, noticias, deportes, ñu, con espacio')
        );
        self::assertSame([], Publicaciones::slugs(null));
        self::assertSame([], Publicaciones::slugs(['noticias']));
    }

    public function testCantidadSeLimitaAlRango(): void
    {
        self::assertSame(1, Publicaciones::cantidad('0'));
        self::assertSame(1, Publicaciones::cantidad(-5));
        self::assertSame(Publicaciones::CANTIDAD_MAXIMA, Publicaciones::cantidad('999'));
        self::assertSame(Publicaciones::CANTIDAD_DEFECTO, Publicaciones::cantidad('muchas'));
        self::assertSame(Publicaciones::CANTIDAD_DEFECTO, Publicaciones::cantidad(null));
    }

    public function testFormatoDesconocidoEsTarjeta(): void
    {
        self::assertSame(Publicaciones::FORMATO_TARJETA, Publicaciones::formato('lista'));
        self::assertSame(Publicaciones::FORMATO_TARJETA, Publicaciones::formato(null));
        self::assertSame(Publicaciones::FORMATO_FILA, Publicaciones::formato(' fila '));
    }

    public function testBooleanoAdmiteVariasFormas(): void
    {
        self::assertTrue(Publicaciones::booleano('sí', false));
        self::assertTrue(Publicaciones::booleano('1', false));
        self::assertTrue(Publicaciones::booleano(true, false));
        self::assertFalse(Publicaciones::booleano('No', true));
        self::assertFalse(Publicaciones::booleano('0', true));
        self::assertFalse(Publicaciones::booleano(0, true));
        self::assertTrue(Publicaciones::booleano('quizás', true));
        self::assertFalse(Publicaciones::booleano(null, false));
    }

    public function testDiasNegativosOInvalidosUsanLaOpcionDelTema(): void
    {
        self::assertSame(0, Publicaciones::dias('0'));
        self::assertSame(30, Publicaciones::dias(30));
        self::assertSame(Publicaciones::DIAS_DEL_TEMA, Publicaciones::dias('-3'));
        self::assertSame(Publicaciones::DIAS_DEL_TEMA, Publicaciones::dias('ayer'));
        self::assertSame(Publicaciones::DIAS_DEL_TEMA, Publicaciones::dias(null));
    }
}

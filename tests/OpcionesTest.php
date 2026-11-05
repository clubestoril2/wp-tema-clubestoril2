<?php

declare(strict_types=1);

namespace ClubEstoril2\Tests;

use ClubEstoril2\Opciones;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class OpcionesTest extends TestCase
{
    public function testTodasLasOpcionesTienenUnDefectoQueYaEstaSaneado(): void
    {
        foreach (Opciones::claves() as $clave) {
            $defecto = Opciones::defecto($clave);
            self::assertSame($defecto, Opciones::sanear($clave, $defecto), $clave);
        }
    }

    public function testIncluyeLasOpcionesDePortadaYSecciones(): void
    {
        foreach (
            [
                'portada_destacadas',
                'portada_anteriores',
                'portada_dias_novedad',
                'seccion_destacadas',
                'seccion_anteriores',
                'seccion_dias_novedad',
            ] as $clave
        ) {
            self::assertTrue(Opciones::existe($clave), $clave);
            self::assertSame(Opciones::ENTERO, Opciones::tipo($clave), $clave);
        }
    }

    public function testUnaClaveDesconocidaLanzaExcepcion(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Opciones::defecto('no_existe');
    }

    /**
     * @return iterable<string, array{mixed, int}>
     */
    public static function enteros(): iterable
    {
        yield 'entero válido' => [5, 5];
        yield 'texto numérico' => [' 3 ', 3];
        yield 'por debajo del mínimo' => [0, 1];
        yield 'por encima del máximo' => [99, 6];
        yield 'texto no numérico' => ['muchas', 4];
        yield 'decimal' => ['2.5', 4];
        yield 'booleano' => [true, 4];
        yield 'nulo' => [null, 4];
    }

    #[DataProvider('enteros')]
    public function testSaneaEnterosConLimites(mixed $valor, int $esperado): void
    {
        self::assertSame($esperado, Opciones::sanear('portada_destacadas', $valor));
    }

    public function testSaneaBooleanos(): void
    {
        self::assertFalse(Opciones::sanear('portada_menu_dia', false));
        self::assertFalse(Opciones::sanear('portada_menu_dia', '0'));
        self::assertFalse(Opciones::sanear('portada_menu_dia', ''));
        self::assertTrue(Opciones::sanear('portada_menu_dia', '1'));
        self::assertTrue(Opciones::sanear('portada_menu_dia', 'texto raro'));
    }

    public function testSaneaOpcionesCerradas(): void
    {
        self::assertSame('dark', Opciones::sanear('modo_color', 'dark'));
        self::assertSame('auto', Opciones::sanear('modo_color', 'morado'));
        self::assertSame('post_tag', Opciones::sanear('secciones_taxonomia', 'post_tag'));
        self::assertSame('category', Opciones::sanear('secciones_taxonomia', ['post_tag']));
    }

    public function testSaneaTextoQuitandoEtiquetasYSaltos(): void
    {
        self::assertSame(
            'Abierto de 9 a 23 h',
            Opciones::sanear('cabecera_horario', "  <b>Abierto</b> de 9\na 23 h ")
        );
    }

    public function testSaneaUrls(): void
    {
        $instagram = 'https://www.instagram.com/club/';
        self::assertSame($instagram, Opciones::sanear('red_instagram', $instagram));
        $tarifas = '/informacion-general/tarifas/';
        self::assertSame($tarifas, Opciones::sanear('boton_socios_url', $tarifas));
        self::assertSame('', Opciones::sanear('red_instagram', ''));
        // Una URL no válida se sustituye por el valor por defecto de la opción.
        self::assertSame('', Opciones::sanear('red_youtube', 'javascript:alert(1)'));
        self::assertSame('', Opciones::sanear('red_youtube', '//evil.example/'));
        self::assertSame(
            'https://www.instagram.com/clubestoril2/',
            Opciones::sanear('red_instagram', 'javascript:alert(1)')
        );
        self::assertSame(
            '/informacion-general/cuota-de-socios/',
            Opciones::sanear('boton_socios_url', 'ftp://ejemplo.es/')
        );
    }

    public function testSaneaCorreo(): void
    {
        self::assertSame('secretaria@clubestoril2.es', Opciones::sanear('red_correo', ' secretaria@clubestoril2.es '));
        self::assertSame('', Opciones::sanear('red_correo', 'no es un correo'));
    }

    public function testSaneaListasDeSlugs(): void
    {
        self::assertSame(
            ['noticias', 'deportes'],
            Opciones::sanear('secciones_portada', 'Noticias, deportes, , noticias, <script>')
        );
        self::assertSame(['menu-del-dia'], Opciones::sanear('portada_excluir', ['menu-del-dia', 3]));
        self::assertSame([], Opciones::sanear('portada_excluir', []));
    }

    public function testLimitesDeEnteros(): void
    {
        self::assertSame([1, 365], Opciones::limites('portada_dias_novedad'));
        self::assertSame(['auto', 'light', 'dark'], Opciones::permitidos('modo_color'));
    }
}

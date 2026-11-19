<?php

declare(strict_types=1);

namespace ClubEstoril2\Tests;

use ClubEstoril2\IconosMenu;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class IconosMenuTest extends TestCase
{
    public function testElCatalogoNoEstaVacioYTieneEtiquetas(): void
    {
        self::assertNotEmpty(IconosMenu::todos());
        foreach (IconosMenu::todos() as $icono => $etiqueta) {
            self::assertMatchesRegularExpression('/^[a-z0-9]+(-[a-z0-9]+)*$/', $icono);
            self::assertNotSame('', trim($etiqueta), "El icono «{$icono}» no tiene etiqueta.");
        }
    }

    public function testElSpriteTieneUnSimboloPorCadaIcono(): void
    {
        $sprite = (string) file_get_contents(dirname(__DIR__) . '/assets/img/iconos-menu.svg');
        preg_match_all('/<symbol id="([^"]+)"/', $sprite, $coincidencias);
        self::assertSame(
            array_keys(IconosMenu::todos()),
            $coincidencias[1],
            'Vuelve a generar el sprite con «php bin/iconos-menu.php».'
        );
    }

    public function testSanearAdmiteIconosDelCatalogo(): void
    {
        self::assertSame('ball-football', IconosMenu::sanear('ball-football'));
        self::assertSame('ball-football', IconosMenu::sanear('  Ball-Football '));
    }

    /**
     * @return array<string, array{0: mixed}>
     */
    public static function valoresNoValidos(): array
    {
        return [
            'vacío' => [''],
            'desconocido' => ['no-existe'],
            'clase de Font Awesome' => ['fa fa-heartbeat'],
            'inyección' => ['home"><script>'],
            'nulo' => [null],
            'número' => [3],
            'lista' => [['home']],
        ];
    }

    #[DataProvider('valoresNoValidos')]
    public function testSanearDevuelveVacioSiNoEsValido(mixed $valor): void
    {
        self::assertSame('', IconosMenu::sanear($valor));
    }

    /**
     * Iconos que tenían las páginas del menú con el tema Vantage.
     *
     * @return array<string, array{0: string, 1: string}>
     */
    public static function iconosVantage(): array
    {
        return [
            'Himno' => ['fa fa-group', 'users-group'],
            'Historia del club' => ['fa fa-book', 'book'],
            'Escuelas y actividades' => ['fa fa-heart-o', 'heart'],
            'Ubicación' => ['fa fa-location-arrow', 'map-pin'],
            'Escuelas de fútbol' => ['fa fa-soccer-ball-o', 'ball-football'],
            'Horario' => ['fa fa-calendar', 'calendar'],
            'Actividades y gimnasio' => ['fa fa-heartbeat', 'heartbeat'],
            'Servicios' => ['fa fa-share-alt-square', 'share'],
            'El Club' => ['fa fa-address-book', 'address-book'],
            'Restauración' => ['fa fa-coffee', 'coffee'],
            'Organigrama' => ['fa fa-object-group', 'hierarchy'],
            'Información general' => ['fa fa-info-circle', 'info-circle'],
            'Instalaciones' => ['fa fa-building', 'building'],
            'Campamentos' => ['fa fa-space-shuttle', 'rocket'],
            'Cuota de socios' => ['fa fa-money', 'cash'],
            'Contacto' => ['fa fa-address-card', 'id'],
            'Tarifas' => ['fa fa-ticket', 'ticket'],
            'Imagen de marca' => ['fa fa-picture-o', 'photo'],
        ];
    }

    #[DataProvider('iconosVantage')]
    public function testConvierteLosIconosDeVantage(string $clase, string $esperado): void
    {
        self::assertSame($esperado, IconosMenu::desdeVantage($clase));
        self::assertTrue(IconosMenu::existe($esperado));
    }

    public function testConvierteClasesSinPrefijoOConEspacios(): void
    {
        self::assertSame('home', IconosMenu::desdeVantage('fa-home'));
        self::assertSame('ticket', IconosMenu::desdeVantage("  fa   fa-ticket \n"));
        self::assertSame('ticket', IconosMenu::desdeVantage('FA FA-TICKET'));
    }

    public function testSinEquivalenteDevuelveVacio(): void
    {
        self::assertSame('', IconosMenu::desdeVantage(''));
        self::assertSame('', IconosMenu::desdeVantage('fa fa-no-existe'));
        self::assertSame('', IconosMenu::desdeVantage('icon-heart'));
    }
}

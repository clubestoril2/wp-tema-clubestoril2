<?php

declare(strict_types=1);

namespace ClubEstoril2\Tests;

use ClubEstoril2\Novedad;
use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\TestCase;

final class NovedadTest extends TestCase
{
    private DateTimeImmutable $ahora;

    protected function setUp(): void
    {
        $this->ahora = new DateTimeImmutable('2026-11-05 12:00:00', new DateTimeZone('Europe/Madrid'));
    }

    public function testDentroDelPlazoEsNovedad(): void
    {
        $publicacion = $this->ahora->modify('-3 days');
        self::assertTrue(Novedad::esNovedad($publicacion, 14, $this->ahora));
    }

    public function testJustoEnElLimiteEsNovedad(): void
    {
        $publicacion = $this->ahora->modify('-14 days');
        self::assertTrue(Novedad::esNovedad($publicacion, 14, $this->ahora));
    }

    public function testFueraDelPlazoNoEsNovedad(): void
    {
        $publicacion = $this->ahora->modify('-14 days -1 second');
        self::assertFalse(Novedad::esNovedad($publicacion, 14, $this->ahora));
    }

    public function testConPlazoCeroNadaEsNovedad(): void
    {
        self::assertFalse(Novedad::esNovedad($this->ahora, 0, $this->ahora));
    }

    public function testCompararZonasHorariasDistintas(): void
    {
        $publicacion = new DateTimeImmutable('2026-10-22 11:30:00', new DateTimeZone('UTC'));
        self::assertTrue(Novedad::esNovedad($publicacion, 14, $this->ahora));
    }

    public function testRepartirEntreDestacadasYAnteriores(): void
    {
        $reparto = Novedad::repartir(['a', 'b', 'c', 'd', 'e', 'f'], 2, 3);
        self::assertSame(['a', 'b'], $reparto['destacadas']);
        self::assertSame(['c', 'd', 'e'], $reparto['anteriores']);
    }

    public function testRepartirConPocasPublicaciones(): void
    {
        $reparto = Novedad::repartir(['a'], 3, 5);
        self::assertSame(['a'], $reparto['destacadas']);
        self::assertSame([], $reparto['anteriores']);
    }

    public function testRepartirConCantidadesNegativas(): void
    {
        $reparto = Novedad::repartir(['a', 'b'], -1, -1);
        self::assertSame([], $reparto['destacadas']);
        self::assertSame([], $reparto['anteriores']);
    }
}

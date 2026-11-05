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
}

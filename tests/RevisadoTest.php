<?php

declare(strict_types=1);

namespace ClubEstoril2\Tests;

use ClubEstoril2\Revisado;
use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class RevisadoTest extends TestCase
{
    public function testFechaEnUtcSeConserva(): void
    {
        self::assertSame('2026-11-05T11:30:00Z', Revisado::sanear('2026-11-05T11:30:00Z'));
        self::assertSame('2026-11-05T11:30:00Z', Revisado::sanear(' 2026-11-05T11:30:00Z '));
    }

    public function testDesplazamientoHorarioSePasaAUtc(): void
    {
        self::assertSame('2026-11-05T11:30:00Z', Revisado::sanear('2026-11-05T12:30:00+01:00'));
        self::assertSame('2026-11-05T23:30:00Z', Revisado::sanear('2026-11-06T00:30:00+01:00'));
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function valoresSinRevisar(): array
    {
        return [
            'vacío' => [''],
            'null' => [null],
            'false' => [false],
            'cero' => [0],
            'cero en texto' => ['0'],
            'menos uno' => [-1],
            'texto' => ['revisado'],
            'solo fecha' => ['2026-11-05'],
            'sin zona' => ['2026-11-05T11:30:00'],
            'día imposible' => ['2026-02-30T10:00:00Z'],
            'hora imposible' => ['2026-11-05T25:00:00Z'],
        ];
    }

    #[DataProvider('valoresSinRevisar')]
    public function testValoresNoValidosSonSinRevisar(mixed $valor): void
    {
        self::assertSame(Revisado::SIN_REVISAR, Revisado::sanear($valor));
    }

    public function testFormatearConvierteAUtc(): void
    {
        $fecha = new DateTimeImmutable('2026-07-01 10:00:00', new DateTimeZone('Europe/Madrid'));
        self::assertSame('2026-07-01T08:00:00Z', Revisado::formatear($fecha));
    }
}

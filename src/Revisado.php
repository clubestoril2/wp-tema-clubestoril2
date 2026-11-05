<?php

declare(strict_types=1);

namespace ClubEstoril2;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;

/**
 * Valor del metadato «ce_revisado»: fecha de la última revisión de una
 * publicación en UTC (AAAA-MM-DDTHH:MM:SSZ) o cadena vacía si nunca se ha
 * revisado.
 */
final class Revisado
{
    public const META = 'ce_revisado';

    public const SIN_REVISAR = '';

    public const FORMATO = 'Y-m-d\TH:i:s\Z';

    /**
     * Normaliza un valor recibido a fecha UTC o a «sin revisar».
     *
     * Acepta la fecha en UTC («Z») o con desplazamiento horario («+01:00»),
     * que se convierte a UTC. Cualquier otro valor (vacío, null, false, 0,
     * "0", -1, fecha imposible...) significa «sin revisar».
     */
    public static function sanear(mixed $valor): string
    {
        if (!is_string($valor)) {
            return self::SIN_REVISAR;
        }
        $valor = trim($valor);
        foreach ([self::FORMATO, DateTimeInterface::ATOM] as $formato) {
            $zona = $formato === self::FORMATO ? new DateTimeZone('UTC') : null;
            $fecha = DateTimeImmutable::createFromFormat('!' . $formato, $valor, $zona);
            // Al volver a formatearla debe salir igual: así se descartan los
            // desbordamientos («2026-02-30» pasaría a marzo).
            if ($fecha === false || $fecha->format($formato) !== $valor) {
                continue;
            }
            return self::formatear($fecha);
        }
        return self::SIN_REVISAR;
    }

    /**
     * Fecha en el formato del metadato (UTC).
     */
    public static function formatear(DateTimeInterface $fecha): string
    {
        return DateTimeImmutable::createFromInterface($fecha)
            ->setTimezone(new DateTimeZone('UTC'))
            ->format(self::FORMATO);
    }
}

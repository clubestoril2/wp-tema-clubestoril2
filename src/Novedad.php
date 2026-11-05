<?php

declare(strict_types=1);

namespace ClubEstoril2;

use DateTimeImmutable;
use DateTimeInterface;

/**
 * Reglas para decidir si una publicación es una «novedad» que se destaca.
 */
final class Novedad
{
    /**
     * Una publicación es novedad si se publicó hace como mucho $dias días.
     *
     * @param DateTimeInterface $publicacion Fecha de publicación.
     * @param int $dias Antigüedad máxima en días. Con 0 o menos nada es novedad.
     * @param DateTimeInterface $ahora Momento de referencia.
     */
    public static function esNovedad(DateTimeInterface $publicacion, int $dias, DateTimeInterface $ahora): bool
    {
        if ($dias <= 0) {
            return false;
        }
        $limite = DateTimeImmutable::createFromInterface($ahora)->modify(sprintf('-%d days', $dias));
        return $publicacion >= $limite;
    }
}

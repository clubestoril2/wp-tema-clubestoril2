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

    /**
     * Reparte una lista ordenada de publicaciones entre destacadas y anteriores.
     *
     * Las primeras $destacadas van al bloque destacado; el resto, hasta
     * $anteriores, a la lista de anteriores. Se conserva el orden recibido.
     *
     * @param list<mixed> $publicaciones
     * @return array{destacadas: list<mixed>, anteriores: list<mixed>}
     */
    public static function repartir(array $publicaciones, int $destacadas, int $anteriores): array
    {
        $destacadas = max(0, $destacadas);
        $anteriores = max(0, $anteriores);
        return [
            'destacadas' => array_slice($publicaciones, 0, $destacadas),
            'anteriores' => array_slice($publicaciones, $destacadas, $anteriores),
        ];
    }
}

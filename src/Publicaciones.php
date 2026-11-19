<?php

declare(strict_types=1);

namespace ClubEstoril2;

/**
 * Atributos del shortcode [ce_publicaciones]: valores por defecto y saneamiento.
 *
 * No depende de WordPress. La consulta y el pintado se hacen en
 * inc/shortcodes.php.
 */
final class Publicaciones
{
    public const FORMATO_TARJETA = 'tarjeta';
    public const FORMATO_FILA = 'fila';

    public const CANTIDAD_DEFECTO = 4;
    public const CANTIDAD_MAXIMA = 24;

    /**
     * Máximo de publicaciones en la sección «Destacado»; 0 la desactiva.
     */
    public const DESTACADAS_MAXIMO = 6;

    /**
     * Valor de «dias» que indica «usar la opción del tema».
     */
    public const DIAS_DEL_TEMA = -1;

    // phpcs:disable Generic.Files.LineLength.TooLong -- la forma del array del docblock va en una línea.

    /**
     * Atributos saneados a partir de los escritos en el shortcode.
     *
     * - categoria: slugs de categorías separados por comas; vacío es «cualquiera».
     * - etiqueta: slugs de etiquetas separados por comas; vacío es «cualquiera».
     * - cantidad: 1 a CANTIDAD_MAXIMA; otros valores pasan al límite más cercano.
     * - formato: «tarjeta» (rejilla, como «Destacado» en la portada) o «fila»
     *   (lista, como «Últimas publicaciones»).
     * - hijas: si se incluyen las subcategorías («si»/«no»; por defecto, sí).
     * - dias: plazo del distintivo «Novedad»; 0 lo quita y sin indicarlo se
     *   usa la opción del tema (DIAS_DEL_TEMA).
     * - destacadas: con más de 0, como en la portada: una sección «Destacado»
     *   con hasta ese número de publicaciones de la categoría de destacados
     *   (en tarjetas) y otra con las demás, sin repetir (en filas).
     * - categoria_destacada: slugs de categorías, separados por comas, que
     *   forman la primera sección en vez de la categoría de destacados (p. ej.
     *   «menu-del-dia»); la segunda sección no incluye esas categorías.
     *   Vacío es «la categoría de destacados del tema».
     * - titulo_destacadas, titulo_anteriores: títulos de las dos secciones;
     *   vacío no imprime título. Sin indicarlos (null) se usan los del tema.
     *   También se admiten como texto_destacado y texto_publicaciones.
     *
     * @param array<string|int, mixed> $atributos Atributos tal como llegan del shortcode.
     * @return array{categoria: list<string>, etiqueta: list<string>, cantidad: int, formato: string, hijas: bool, dias: int, destacadas: int, categoria_destacada: list<string>, titulo_destacadas: ?string, titulo_anteriores: ?string}
     */
    public static function sanear(array $atributos): array
    {
        return [
            'categoria' => self::slugs($atributos['categoria'] ?? ''),
            'etiqueta' => self::slugs($atributos['etiqueta'] ?? ''),
            'cantidad' => self::cantidad($atributos['cantidad'] ?? null),
            'formato' => self::formato($atributos['formato'] ?? null),
            'hijas' => self::booleano($atributos['hijas'] ?? null, true),
            'dias' => self::dias($atributos['dias'] ?? null),
            'destacadas' => self::destacadas($atributos['destacadas'] ?? null),
            'categoria_destacada' => self::slugs($atributos['categoria_destacada'] ?? ''),
            'titulo_destacadas' => self::titulo(
                $atributos['titulo_destacadas'] ?? $atributos['texto_destacado'] ?? null
            ),
            'titulo_anteriores' => self::titulo(
                $atributos['titulo_anteriores'] ?? $atributos['texto_publicaciones'] ?? null
            ),
        ];
    }

    // phpcs:enable Generic.Files.LineLength.TooLong

    /**
     * Lista de slugs sin repetidos a partir de una cadena separada por comas.
     * Se admiten letras, números, guiones y guiones bajos; el resto se descarta.
     *
     * @return list<string>
     */
    public static function slugs(mixed $valor): array
    {
        if (!is_string($valor)) {
            return [];
        }
        $slugs = [];
        foreach (explode(',', $valor) as $slug) {
            $slug = strtolower(trim($slug));
            if ($slug !== '' && preg_match('/^[a-z0-9_-]+$/', $slug) === 1 && !in_array($slug, $slugs, true)) {
                $slugs[] = $slug;
            }
        }
        return $slugs;
    }

    public static function cantidad(mixed $valor): int
    {
        if (is_string($valor) && is_numeric($valor)) {
            $valor = (int) $valor;
        }
        if (!is_int($valor)) {
            return self::CANTIDAD_DEFECTO;
        }
        return max(1, min(self::CANTIDAD_MAXIMA, $valor));
    }

    public static function formato(mixed $valor): string
    {
        if (is_string($valor) && strtolower(trim($valor)) === self::FORMATO_FILA) {
            return self::FORMATO_FILA;
        }
        return self::FORMATO_TARJETA;
    }

    /**
     * «si», «sí», «yes», «true», «1» son verdadero; «no», «false», «0» son
     * falso; cualquier otra cosa devuelve $defecto.
     */
    public static function booleano(mixed $valor, bool $defecto): bool
    {
        if (is_bool($valor)) {
            return $valor;
        }
        if (is_int($valor)) {
            return $valor !== 0;
        }
        if (!is_string($valor)) {
            return $defecto;
        }
        $valor = strtolower(trim($valor));
        if (in_array($valor, ['si', 'sí', 'yes', 'true', '1'], true)) {
            return true;
        }
        if (in_array($valor, ['no', 'false', '0'], true)) {
            return false;
        }
        return $defecto;
    }

    /**
     * Publicaciones en «Destacado»: de 0 (sin sección, por defecto) a
     * DESTACADAS_MAXIMO.
     */
    public static function destacadas(mixed $valor): int
    {
        if (is_string($valor) && is_numeric($valor)) {
            $valor = (int) $valor;
        }
        if (!is_int($valor)) {
            return 0;
        }
        return max(0, min(self::DESTACADAS_MAXIMO, $valor));
    }

    /**
     * Título de una sección: la cadena sin espacios sobrantes, o null si no
     * se ha indicado (para usar el del tema).
     */
    public static function titulo(mixed $valor): ?string
    {
        if (!is_string($valor)) {
            return null;
        }
        return trim($valor);
    }

    /**
     * Días del distintivo «Novedad»: 0 o más, o DIAS_DEL_TEMA si no se indica
     * o no es un número.
     */
    public static function dias(mixed $valor): int
    {
        if (is_string($valor) && is_numeric($valor)) {
            $valor = (int) $valor;
        }
        if (!is_int($valor) || $valor < 0) {
            return self::DIAS_DEL_TEMA;
        }
        return $valor;
    }
}

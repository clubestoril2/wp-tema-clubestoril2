<?php

declare(strict_types=1);

namespace ClubEstoril2;

use InvalidArgumentException;

/**
 * Catálogo de opciones del tema: valores por defecto y saneamiento.
 *
 * No depende de WordPress, de modo que las reglas se pueden probar de forma
 * aislada. La lectura desde la base de datos (get_theme_mod) se hace en
 * inc/options.php.
 */
final class Opciones
{
    public const ENTERO = 'entero';
    public const BOOLEANO = 'booleano';
    public const OPCION = 'opcion';
    public const TEXTO = 'texto';
    public const URL = 'url';
    public const SLUGS = 'slugs';
    public const CORREO = 'correo';

    // phpcs:disable Generic.Files.LineLength.TooLong -- las formas de array de los docblocks van en una línea.

    /**
     * Definición de cada opción.
     *
     * Claves de cada definición:
     * - tipo: una de las constantes de tipo de esta clase.
     * - defecto: valor por defecto, ya saneado.
     * - min, max: límites para los enteros.
     * - opciones: valores permitidos para el tipo OPCION.
     *
     * @var array<string, array{tipo: string, defecto: int|bool|string|list<string>, min?: int, max?: int, opciones?: list<string>}>
     */
    private const DEFINICIONES = [
        // Portada.
        'portada_destacadas' => ['tipo' => self::ENTERO, 'defecto' => 4, 'min' => 1, 'max' => 6],
        'portada_anteriores' => ['tipo' => self::ENTERO, 'defecto' => 8, 'min' => 0, 'max' => 24],
        'portada_dias_novedad' => ['tipo' => self::ENTERO, 'defecto' => 14, 'min' => 1, 'max' => 365],
        'portada_excluir' => ['tipo' => self::SLUGS, 'defecto' => []],
        'portada_menu_dia' => ['tipo' => self::BOOLEANO, 'defecto' => true],
        'portada_facebook' => ['tipo' => self::BOOLEANO, 'defecto' => true],

        // Secciones.
        'secciones_taxonomia' => [
            'tipo' => self::OPCION,
            'defecto' => 'category',
            'opciones' => ['category', 'post_tag'],
        ],
        'secciones_portada' => [
            'tipo' => self::SLUGS,
            'defecto' => ['noticias', 'actividades', 'deportes', 'restauracion'],
        ],
        'secciones_portada_num' => ['tipo' => self::ENTERO, 'defecto' => 4, 'min' => 2, 'max' => 8],
        'seccion_destacadas' => ['tipo' => self::ENTERO, 'defecto' => 3, 'min' => 0, 'max' => 6],
        'seccion_anteriores' => ['tipo' => self::ENTERO, 'defecto' => 12, 'min' => 0, 'max' => 48],
        'seccion_dias_novedad' => ['tipo' => self::ENTERO, 'defecto' => 30, 'min' => 1, 'max' => 365],

        // Cabecera: información rápida, botones y redes sociales.
        'cabecera_eslogan' => ['tipo' => self::TEXTO, 'defecto' => 'Club deportivo y social en Móstoles'],
        'cabecera_telefono' => ['tipo' => self::TEXTO, 'defecto' => ''],
        'cabecera_horario' => ['tipo' => self::TEXTO, 'defecto' => ''],
        'cabecera_direccion' => ['tipo' => self::TEXTO, 'defecto' => ''],
        'cabecera_mapa_url' => ['tipo' => self::URL, 'defecto' => '/informacion-general/ubicacion/'],
        'boton_socios_texto' => ['tipo' => self::TEXTO, 'defecto' => 'Tarifas y socios'],
        'boton_socios_url' => ['tipo' => self::URL, 'defecto' => '/informacion-general/cuota-de-socios/'],
        'boton_contacto_texto' => ['tipo' => self::TEXTO, 'defecto' => 'Contacto'],
        'boton_contacto_url' => ['tipo' => self::URL, 'defecto' => '/informacion-general/contacto/'],
        'red_telegram' => ['tipo' => self::URL, 'defecto' => 'https://t.me/clubestoril2'],
        'red_facebook' => ['tipo' => self::URL, 'defecto' => 'https://www.facebook.com/clubestoril2/'],
        'red_instagram' => ['tipo' => self::URL, 'defecto' => 'https://www.instagram.com/clubestoril2/'],
        'red_maps' => [
            'tipo' => self::URL,
            'defecto' => 'https://www.google.com/maps/search/?api=1&query=Club+Estoril+II+M%C3%B3stoles',
        ],
        'red_x' => ['tipo' => self::URL, 'defecto' => 'https://x.com/clubestoril2'],
        'red_youtube' => ['tipo' => self::URL, 'defecto' => ''],
        'red_whatsapp' => ['tipo' => self::URL, 'defecto' => ''],
        'red_correo' => ['tipo' => self::CORREO, 'defecto' => ''],

        // Apariencia.
        'modo_color' => ['tipo' => self::OPCION, 'defecto' => 'auto', 'opciones' => ['auto', 'light', 'dark']],
        'mostrar_conmutador' => ['tipo' => self::BOOLEANO, 'defecto' => true],
        'menu_icono_inicio' => ['tipo' => self::BOOLEANO, 'defecto' => true],
        'menu_fijo' => ['tipo' => self::BOOLEANO, 'defecto' => true],

        // Listados.
        'extracto_palabras' => ['tipo' => self::ENTERO, 'defecto' => 40, 'min' => 10, 'max' => 120],
        'mostrar_autor' => ['tipo' => self::BOOLEANO, 'defecto' => false],
        'titulo_facebook' => [
            'tipo' => self::OPCION,
            'defecto' => 'fecha_corta',
            'opciones' => ['fecha_completa', 'fecha_corta', 'primera_frase'],
        ],
        'listados_excluir' => ['tipo' => self::SLUGS, 'defecto' => []],

        // Rendimiento.
        'cache_minutos' => ['tipo' => self::ENTERO, 'defecto' => 15, 'min' => 0, 'max' => 1440],

        // Pie.
        'pie_texto' => ['tipo' => self::TEXTO, 'defecto' => 'Club Deportivo Estoril II'],
    ];

    /**
     * Nombres de todas las opciones.
     *
     * @return list<string>
     */
    public static function claves(): array
    {
        return array_keys(self::DEFINICIONES);
    }

    public static function existe(string $clave): bool
    {
        return isset(self::DEFINICIONES[$clave]);
    }

    public static function tipo(string $clave): string
    {
        return self::definicion($clave)['tipo'];
    }

    /**
     * Valor por defecto de una opción.
     *
     * @return int|bool|string|list<string>
     */
    public static function defecto(string $clave): int|bool|string|array
    {
        return self::definicion($clave)['defecto'];
    }

    /**
     * Límites [mínimo, máximo] de una opción entera.
     *
     * @return array{0: int, 1: int}
     */
    public static function limites(string $clave): array
    {
        $definicion = self::definicion($clave);
        return [$definicion['min'] ?? PHP_INT_MIN, $definicion['max'] ?? PHP_INT_MAX];
    }

    /**
     * Valores permitidos de una opción de tipo OPCION.
     *
     * @return list<string>
     */
    public static function permitidos(string $clave): array
    {
        return self::definicion($clave)['opciones'] ?? [];
    }

    /**
     * Devuelve el valor saneado o, si no es válido, el valor por defecto.
     *
     * @return int|bool|string|list<string>
     */
    public static function sanear(string $clave, mixed $valor): int|bool|string|array
    {
        $definicion = self::definicion($clave);
        $defecto = $definicion['defecto'];

        return match ($definicion['tipo']) {
            self::ENTERO => self::sanearEntero(
                $valor,
                $defecto,
                $definicion['min'] ?? null,
                $definicion['max'] ?? null
            ),
            self::BOOLEANO => self::sanearBooleano($valor, $defecto),
            self::OPCION => self::sanearOpcion($valor, $defecto, $definicion['opciones'] ?? []),
            self::TEXTO => self::sanearTexto($valor, $defecto),
            self::URL => self::sanearUrl($valor, $defecto),
            self::SLUGS => self::sanearSlugs($valor, $defecto),
            self::CORREO => self::sanearCorreo($valor),
            default => $defecto,
        };
    }

    /**
     * @return array{tipo: string, defecto: int|bool|string|list<string>, min?: int, max?: int, opciones?: list<string>}
     */
    private static function definicion(string $clave): array
    {
        if (!isset(self::DEFINICIONES[$clave])) {
            throw new InvalidArgumentException(sprintf('Opción del tema desconocida: «%s».', $clave));
        }
        return self::DEFINICIONES[$clave];
    }

    // phpcs:enable Generic.Files.LineLength.TooLong

    /**
     * @param int|bool|string|list<string> $defecto
     */
    private static function sanearEntero(mixed $valor, int|bool|string|array $defecto, ?int $min, ?int $max): int
    {
        $defecto = is_int($defecto) ? $defecto : 0;
        if (is_string($valor)) {
            $valor = trim($valor);
        }
        $entero = filter_var($valor, FILTER_VALIDATE_INT);
        if ($entero === false || is_bool($valor)) {
            return $defecto;
        }
        if ($min !== null && $entero < $min) {
            return $min;
        }
        if ($max !== null && $entero > $max) {
            return $max;
        }
        return $entero;
    }

    /**
     * @param int|bool|string|list<string> $defecto
     */
    private static function sanearBooleano(mixed $valor, int|bool|string|array $defecto): bool
    {
        if (is_bool($valor)) {
            return $valor;
        }
        $booleano = filter_var($valor, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        return $booleano ?? (bool) $defecto;
    }

    /**
     * @param int|bool|string|list<string> $defecto
     * @param list<string> $permitidos
     */
    private static function sanearOpcion(mixed $valor, int|bool|string|array $defecto, array $permitidos): string
    {
        if (is_string($valor) && in_array($valor, $permitidos, true)) {
            return $valor;
        }
        return is_string($defecto) ? $defecto : '';
    }

    /**
     * Texto de una línea, sin etiquetas HTML ni caracteres de control.
     *
     * @param int|bool|string|list<string> $defecto
     */
    private static function sanearTexto(mixed $valor, int|bool|string|array $defecto): string
    {
        if (!is_string($valor) && !is_int($valor)) {
            return is_string($defecto) ? $defecto : '';
        }
        $texto = strip_tags((string) $valor);
        $texto = (string) preg_replace('/[\x00-\x1F\x7F]+/u', ' ', $texto);
        return trim((string) preg_replace('/\s+/u', ' ', $texto));
    }

    /**
     * Admite URL absolutas http(s), rutas del propio sitio («/ruta/») o vacío.
     *
     * @param int|bool|string|list<string> $defecto
     */
    private static function sanearUrl(mixed $valor, int|bool|string|array $defecto): string
    {
        $defecto = is_string($defecto) ? $defecto : '';
        if (!is_string($valor)) {
            return $defecto;
        }
        $url = trim($valor);
        if ($url === '') {
            return '';
        }
        if (str_starts_with($url, '/') && !str_starts_with($url, '//')) {
            return preg_match('#^/[^\s<>"\']*$#u', $url) === 1 ? $url : $defecto;
        }
        $esquema = strtolower((string) parse_url($url, PHP_URL_SCHEME));
        if (in_array($esquema, ['http', 'https'], true) && filter_var($url, FILTER_VALIDATE_URL) !== false) {
            return $url;
        }
        return $defecto;
    }

    /**
     * Dirección de correo válida o vacío.
     */
    private static function sanearCorreo(mixed $valor): string
    {
        if (!is_string($valor)) {
            return '';
        }
        $correo = trim($valor);
        return filter_var($correo, FILTER_VALIDATE_EMAIL) !== false ? $correo : '';
    }

    /**
     * Lista de slugs de términos. Acepta una lista o un texto separado por comas.
     *
     * @param int|bool|string|list<string> $defecto
     * @return list<string>
     */
    private static function sanearSlugs(mixed $valor, int|bool|string|array $defecto): array
    {
        if (is_string($valor)) {
            $valor = explode(',', $valor);
        }
        if (!is_array($valor)) {
            return is_array($defecto) ? $defecto : [];
        }
        $slugs = [];
        foreach ($valor as $slug) {
            if (!is_string($slug)) {
                continue;
            }
            $slug = strtolower(trim($slug));
            if (preg_match('/^[a-z0-9][a-z0-9_-]*$/', $slug) === 1 && !in_array($slug, $slugs, true)) {
                $slugs[] = $slug;
            }
        }
        return $slugs;
    }
}

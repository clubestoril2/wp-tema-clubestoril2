<?php

declare(strict_types=1);

namespace ClubEstoril2;

/**
 * Localiza la primera imagen del contenido HTML de una publicación.
 *
 * Se usa como respaldo cuando la publicación no tiene imagen destacada.
 */
final class PrimeraImagen
{
    /**
     * Devuelve los datos de la primera etiqueta <img> del contenido.
     *
     * - «id»: identificador del adjunto si la imagen viene de la biblioteca de
     *   medios (clase «wp-image-N» que añaden los editores de WordPress).
     * - «src»: URL de la imagen, con las entidades HTML decodificadas.
     *
     * @return array{id: ?int, src: ?string}|null Null si no hay ninguna imagen.
     */
    public static function buscar(string $html): ?array
    {
        if (preg_match('/<img\b[^>]*>/i', $html, $etiqueta) !== 1) {
            return null;
        }
        $img = $etiqueta[0];

        $id = null;
        if (
            preg_match('/\bclass\s*=\s*(["\'])(.*?)\1/is', $img, $clase) === 1
            && preg_match('/(?:^|\s)wp-image-(\d+)(?:\s|$)/', $clase[2], $numero) === 1
        ) {
            $id = (int) $numero[1];
        }

        $src = null;
        if (preg_match('/\bsrc\s*=\s*(["\'])(.*?)\1/is', $img, $atributo) === 1) {
            $src = trim(html_entity_decode($atributo[2], ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            if ($src === '') {
                $src = null;
            }
        }

        if ($id === null && $src === null) {
            return null;
        }
        return ['id' => $id, 'src' => $src];
    }

    /**
     * Indica si la URL de una imagen corresponde al fichero de un adjunto.
     *
     * La clase «wp-image-N» no basta: al copiar contenido de otra entrada (o al
     * importarlo con una plantilla) se conserva la clase aunque cambie la imagen.
     * Se comparan las rutas sin dominio ni consulta y sin los sufijos que añade
     * WordPress a los tamaños intermedios («-300x200») y a las imágenes grandes
     * («-scaled»).
     *
     * @param string $src URL de la imagen en el contenido.
     * @param string $urlAdjunto URL del fichero original del adjunto.
     */
    public static function esDelAdjunto(string $src, string $urlAdjunto): bool
    {
        $normalizar = static function (string $url): string {
            $ruta = (string) parse_url($url, PHP_URL_PATH);
            return (string) preg_replace('/(?:-\d+x\d+|-scaled)+(\.[a-z0-9]+)$/i', '$1', $ruta);
        };
        $ruta = $normalizar($src);
        return $ruta !== '' && $ruta === $normalizar($urlAdjunto);
    }
}

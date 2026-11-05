<?php

/**
 * Metadatos de las publicaciones.
 *
 * - ce_revisado: fecha de la última revisión en UTC (AAAA-MM-DDTHH:MM:SSZ) o
 *   cadena vacía si nunca se ha revisado. Se valida en ClubEstoril2\Revisado.
 *
 * @package ClubEstoril2
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

use ClubEstoril2\Revisado;

add_action('init', 'ce_registrar_metadatos');

function ce_registrar_metadatos(): void
{
    // Sin tipo concreto: vale para entradas, páginas y cualquier otro tipo.
    register_post_meta(
        '',
        Revisado::META,
        [
            'type' => 'string',
            'description' => __('Fecha de la última revisión (UTC) o vacío si no se ha revisado.', 'clubestoril2'),
            'single' => true,
            'default' => Revisado::SIN_REVISAR,
            'show_in_rest' => true,
            'sanitize_callback' => [Revisado::class, 'sanear'],
            'auth_callback' => static fn(bool $_permitido, string $_clave, int $id): bool
                => current_user_can('edit_post', $id),
        ]
    );
}

/**
 * Fecha de la última revisión de una publicación, o null si nunca se ha
 * revisado.
 */
function ce_revisado(?WP_Post $post = null): ?string
{
    $post = get_post($post);
    if (!$post instanceof WP_Post) {
        return null;
    }
    $valor = Revisado::sanear(get_post_meta($post->ID, Revisado::META, true));
    return $valor === Revisado::SIN_REVISAR ? null : $valor;
}

/**
 * Marca una publicación como revisada ahora.
 */
function ce_marcar_revisado(int $post_id): void
{
    update_post_meta($post_id, Revisado::META, Revisado::formatear(new DateTimeImmutable()));
}

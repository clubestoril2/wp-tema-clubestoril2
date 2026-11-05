<?php

/**
 * Control del Personalizador para elegir categorías: una casilla por
 * categoría. Guarda la lista de slugs separados por comas en un campo oculto
 * (assets/js/customizer-categorias.js lo mantiene al día), que
 * Opciones::sanear() convierte en lista.
 *
 * Se carga solo dentro de «customize_register», cuando ya existe
 * WP_Customize_Control.
 *
 * @package ClubEstoril2
 */

declare(strict_types=1);

namespace ClubEstoril2;

use WP_Customize_Control;
use WP_Term;

defined('ABSPATH') || exit;

final class ControlCategorias extends WP_Customize_Control
{
    /**
     * @var string
     */
    public $type = 'ce_categorias';

    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps -- nombre que impone WordPress.
    protected function render_content(): void
    {
        $valor = $this->value();
        $elegidas = array_map('trim', is_array($valor) ? $valor : explode(',', (string) $valor));
        $categorias = get_categories(['hide_empty' => false, 'orderby' => 'name']);
        ?>
        <div class="ce-control-categorias">
            <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
            <?php if ($this->description !== '') : ?>
                <span class="description customize-control-description">
                    <?php echo esc_html($this->description); ?>
                </span>
            <?php endif; ?>
            <input type="hidden" value="<?php echo esc_attr(implode(',', $elegidas)); ?>" <?php $this->link(); ?>>
            <ul>
                <?php foreach ($categorias as $categoria) : ?>
                    <?php if ($categoria instanceof WP_Term) : ?>
                        <li>
                            <label>
                                <input type="checkbox" value="<?php echo esc_attr($categoria->slug); ?>"
                                    <?php checked(in_array($categoria->slug, $elegidas, true)); ?>>
                                <?php echo esc_html($categoria->name); ?>
                                <span class="description">
                                    (<?php echo esc_html(number_format_i18n($categoria->count)); ?>)
                                </span>
                            </label>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php
    }
}

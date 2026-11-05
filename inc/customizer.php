<?php

/**
 * Panel «Tema Club Estoril II» del Personalizador (Apariencia › Personalizar).
 *
 * Cada control guarda una opción de src/Opciones.php como theme_mod y se
 * sanea con Opciones::sanear(). El tipo de control sale del tipo de la
 * opción: número, casilla, texto, dirección, correo, desplegable o, para las
 * listas de categorías, una casilla por categoría.
 *
 * @package ClubEstoril2
 */

declare(strict_types=1);

use ClubEstoril2\ControlCategorias;
use ClubEstoril2\Opciones;

defined('ABSPATH') || exit;

add_action('customize_register', 'ce_personalizador');
add_action('customize_controls_enqueue_scripts', 'ce_personalizador_recursos');

/**
 * Secciones del panel y sus opciones: clave => [etiqueta, descripción].
 *
 * @return array<string, array{titulo: string, opciones: array<string, array{0: string, 1?: string}>}>
 */
function ce_personalizador_secciones(): array
{
    return [
        'portada' => [
            'titulo' => __('Portada', 'clubestoril2'),
            'opciones' => [
                'portada_destacadas' => [
                    __('Publicaciones en «Destacado»', 'clubestoril2'),
                    __('Las más recientes de la categoría «Destacado».', 'clubestoril2'),
                ],
                'portada_anteriores' => [
                    __('Publicaciones en «Últimas publicaciones»', 'clubestoril2'),
                    __('Las más recientes que no salen en «Destacado». 0 oculta la sección.', 'clubestoril2'),
                ],
                'portada_dias_novedad' => [
                    __('Días con el distintivo «Novedad»', 'clubestoril2'),
                    __('Antigüedad máxima, en días, de las publicaciones marcadas como novedad.', 'clubestoril2'),
                ],
                'portada_excluir' => [
                    __('Categorías que no salen en la portada', 'clubestoril2'),
                    __('Ni en «Destacado» ni en «Últimas publicaciones».', 'clubestoril2'),
                ],
            ],
        ],
        'listados' => [
            'titulo' => __('Listados de publicaciones', 'clubestoril2'),
            'opciones' => [
                'seccion_dias_novedad' => [
                    __('Días con el distintivo «Novedad»', 'clubestoril2'),
                    __('En la página de publicaciones y en las de cada categoría.', 'clubestoril2'),
                ],
                'listados_excluir' => [
                    __('Categorías que no salen en la página de publicaciones', 'clubestoril2'),
                    __('La página de entradas elegida en Ajustes › Lectura.', 'clubestoril2'),
                ],
                'extracto_palabras' => [
                    __('Palabras del extracto', 'clubestoril2'),
                    __('Longitud del resumen de cada publicación en los listados.', 'clubestoril2'),
                ],
                'mostrar_autor' => [__('Mostrar el autor de cada publicación', 'clubestoril2')],
            ],
        ],
        'categorias' => [
            'titulo' => __('Página «Categorías»', 'clubestoril2'),
            'opciones' => [
                'categorias_ocultas' => [
                    __('Categorías ocultas', 'clubestoril2'),
                    __('No aparecen como tarjeta en la página «Categorías».', 'clubestoril2'),
                ],
            ],
        ],
        'cabecera' => [
            'titulo' => __('Cabecera', 'clubestoril2'),
            'opciones' => [
                'cabecera_telefono' => [__('Teléfono', 'clubestoril2')],
                'cabecera_horario' => [__('Horario', 'clubestoril2')],
                'cabecera_direccion' => [__('Dirección', 'clubestoril2')],
                'cabecera_mapa_url' => [
                    __('Enlace de la dirección', 'clubestoril2'),
                    __('Por ejemplo, la página de ubicación o un enlace de Google Maps.', 'clubestoril2'),
                ],
                'boton_socios_texto' => [__('Botón principal: texto', 'clubestoril2')],
                'boton_socios_url' => [__('Botón principal: enlace', 'clubestoril2')],
                'boton_contacto_texto' => [__('Botón secundario: texto', 'clubestoril2')],
                'boton_contacto_url' => [__('Botón secundario: enlace', 'clubestoril2')],
            ],
        ],
        'redes' => [
            'titulo' => __('Redes sociales', 'clubestoril2'),
            'opciones' => [
                'red_telegram' => ['Telegram'],
                'red_facebook' => ['Facebook'],
                'red_instagram' => ['Instagram'],
                'red_maps' => ['Google Maps'],
                'red_x' => ['X'],
                'red_youtube' => ['YouTube'],
                'red_whatsapp' => ['WhatsApp'],
                'red_correo' => [__('Correo electrónico', 'clubestoril2')],
            ],
        ],
        'apariencia' => [
            'titulo' => __('Apariencia', 'clubestoril2'),
            'opciones' => [
                'modo_color' => [
                    __('Modo de color', 'clubestoril2'),
                    __('El que se usa si el visitante no ha elegido otro con el conmutador.', 'clubestoril2'),
                ],
                'mostrar_conmutador' => [__('Mostrar el conmutador de modo claro / oscuro', 'clubestoril2')],
            ],
        ],
        'pie' => [
            'titulo' => __('Pie', 'clubestoril2'),
            'opciones' => [
                'pie_autor' => [
                    __('Autor', 'clubestoril2'),
                    __('Aparece entre el año y el nombre. Vacío, no se muestra.', 'clubestoril2'),
                ],
                'pie_texto' => [__('Nombre', 'clubestoril2')],
                'cabecera_eslogan' => [__('Eslogan', 'clubestoril2')],
            ],
        ],
    ];
}

function ce_personalizador(WP_Customize_Manager $personalizador): void
{
    require_once CE_DIR . '/inc/customizer-categorias.php';

    $personalizador->add_panel(
        'clubestoril2',
        [
            'title' => __('Tema Club Estoril II', 'clubestoril2'),
            'priority' => 30,
        ]
    );

    foreach (ce_personalizador_secciones() as $seccion => $datos) {
        $id_seccion = 'clubestoril2_' . $seccion;
        $personalizador->add_section($id_seccion, ['title' => $datos['titulo'], 'panel' => 'clubestoril2']);

        foreach ($datos['opciones'] as $clave => $textos) {
            $personalizador->add_setting(
                $clave,
                [
                    'type' => 'theme_mod',
                    'default' => Opciones::defecto($clave),
                    'sanitize_callback' => static fn(mixed $valor): int|bool|string|array => Opciones::sanear(
                        $clave,
                        $valor
                    ),
                ]
            );
            $control = [
                'label' => $textos[0],
                'description' => $textos[1] ?? '',
                'section' => $id_seccion,
            ];
            $tipo = Opciones::tipo($clave);
            if ($tipo === Opciones::SLUGS) {
                $personalizador->add_control(new ControlCategorias($personalizador, $clave, $control));
                continue;
            }
            $personalizador->add_control($clave, $control + ce_personalizador_control($clave, $tipo));
        }
    }
}

/**
 * Tipo de control (y sus atributos) para una opción según su tipo.
 *
 * @return array<string, mixed>
 */
function ce_personalizador_control(string $clave, string $tipo): array
{
    switch ($tipo) {
        case Opciones::ENTERO:
            [$minimo, $maximo] = Opciones::limites($clave);
            return ['type' => 'number', 'input_attrs' => ['min' => $minimo, 'max' => $maximo, 'step' => 1]];
        case Opciones::BOOLEANO:
            return ['type' => 'checkbox'];
        case Opciones::URL:
            return ['type' => 'url'];
        case Opciones::CORREO:
            return ['type' => 'email'];
        case Opciones::OPCION:
            $nombres = [
                'auto' => __('Automático (según el dispositivo)', 'clubestoril2'),
                'light' => __('Claro', 'clubestoril2'),
                'dark' => __('Oscuro', 'clubestoril2'),
            ];
            $opciones = [];
            foreach (Opciones::permitidos($clave) as $valor) {
                $opciones[$valor] = $nombres[$valor] ?? $valor;
            }
            return ['type' => 'select', 'choices' => $opciones];
        default:
            return ['type' => 'text'];
    }
}

function ce_personalizador_recursos(): void
{
    $relativa = 'assets/js/customizer-categorias.js';
    wp_enqueue_script(
        'clubestoril2-personalizador',
        CE_URI . '/' . $relativa,
        [],
        ce_version_recurso($relativa),
        ['in_footer' => true]
    );
}

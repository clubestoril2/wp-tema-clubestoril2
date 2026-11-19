<?php

/**
 * Iconos de los elementos del menú principal.
 *
 * El icono es uno del catálogo de src/IconosMenu.php (Tabler Icons) y se
 * puede elegir en dos sitios, ambos guardados en el metadato _ce_icono:
 *
 * - En el editor de cada página o entrada, en el recuadro «Icono del menú»
 *   de la barra lateral, como hacía el tema Vantage. Vale para todos los
 *   elementos del menú que enlazan a ella.
 * - En Apariencia › Menús, al desplegar cada elemento. Sirve también para
 *   categorías y enlaces, y si se elige tiene prioridad sobre el de la página.
 *
 * El enlace a la portada lleva el icono «Inicio» si no tiene otro y la opción
 * menu_icono_inicio está activada.
 *
 * Los iconos que se asignaban con Vantage (metadato vantage_menu_icon de cada
 * página) se copian una sola vez al metadato del tema de esas mismas páginas;
 * el metadato de Vantage no se modifica.
 *
 * @package ClubEstoril2
 */

declare(strict_types=1);

use ClubEstoril2\IconosMenu;

defined('ABSPATH') || exit;

/**
 * Metadato con el icono, tanto en páginas y entradas como en elementos del menú.
 */
const CE_META_ICONO_MENU = '_ce_icono';

/**
 * Opción que marca que ya se han copiado los iconos de Vantage.
 */
const CE_OPCION_ICONOS_MIGRADOS = 'ce_iconos_vantage_migrados';

add_action('init', 'ce_registrar_meta_icono_menu', 20);
add_filter('nav_menu_item_title', 'ce_titulo_menu_con_icono', 10, 3);
add_action('wp_nav_menu_item_custom_fields', 'ce_campo_icono_menu', 10, 2);
add_action('wp_update_nav_menu_item', 'ce_guardar_icono_menu', 10, 2);
add_action('add_meta_boxes', 'ce_recuadro_icono_menu');
add_action('save_post', 'ce_guardar_icono_contenido', 10, 2);
add_action('admin_enqueue_scripts', 'ce_recursos_icono_menu');
add_action('after_switch_theme', 'ce_migrar_iconos_vantage');
add_action('admin_init', 'ce_migrar_iconos_vantage');

/**
 * Tipos de contenido que se pueden enlazar desde el menú y llevan el recuadro.
 *
 * @return list<string>
 */
function ce_tipos_con_icono_menu(): array
{
    $tipos = get_post_types(['show_in_nav_menus' => true]);
    unset($tipos['attachment']);
    return array_values(array_map('strval', $tipos));
}

/**
 * Registra el metadato del icono para poder leerlo y escribirlo por la API REST.
 */
function ce_registrar_meta_icono_menu(): void
{
    $comun = [
        'type' => 'string',
        'single' => true,
        'default' => '',
        'show_in_rest' => true,
        'sanitize_callback' => static fn(mixed $valor): string => IconosMenu::sanear($valor),
    ];
    register_post_meta(
        'nav_menu_item',
        CE_META_ICONO_MENU,
        $comun + ['auth_callback' => static fn(): bool => current_user_can('edit_theme_options')]
    );
    foreach (ce_tipos_con_icono_menu() as $tipo) {
        register_post_meta(
            $tipo,
            CE_META_ICONO_MENU,
            // WordPress pasa (permitido, clave, id del contenido, …): basta con el id.
            $comun + [
                'auth_callback' => static fn(mixed ...$args): bool => current_user_can(
                    'edit_post',
                    (int) ($args[2] ?? 0)
                ),
            ]
        );
    }
}

/**
 * Icono guardado en un elemento del menú o en una página (vacío si no tiene).
 */
function ce_icono_guardado(int $id): string
{
    return IconosMenu::sanear(get_post_meta($id, CE_META_ICONO_MENU, true));
}

/**
 * Página o entrada a la que enlaza un elemento del menú (0 si es otra cosa).
 */
function ce_contenido_elemento_menu(WP_Post $elemento): int
{
    if (($elemento->type ?? '') !== 'post_type') {
        return 0;
    }
    return (int) ($elemento->object_id ?? 0);
}

/**
 * Icono de un elemento del menú: el suyo; si no, el de la página a la que
 * enlaza; si no, «Inicio» para el enlace a la portada.
 */
function ce_icono_elemento_menu(WP_Post $elemento): string
{
    $icono = ce_icono_guardado($elemento->ID);
    if ($icono !== '') {
        return $icono;
    }
    $contenido = ce_contenido_elemento_menu($elemento);
    if ($contenido > 0) {
        $icono = ce_icono_guardado($contenido);
        if ($icono !== '') {
            return $icono;
        }
    }
    $url = untrailingslashit((string) ($elemento->url ?? ''));
    if (ce_opcion_booleano('menu_icono_inicio') && $url !== '' && $url === untrailingslashit(home_url('/'))) {
        return 'home';
    }
    return '';
}

/**
 * Antepone el icono al texto de los elementos del menú principal.
 *
 * @param string  $titulo   Texto del elemento, ya escapado por WordPress.
 * @param WP_Post $elemento Elemento del menú.
 * @param mixed   $args     Argumentos de wp_nav_menu() (un objeto).
 */
function ce_titulo_menu_con_icono(string $titulo, WP_Post $elemento, mixed $args = null): string
{
    $ubicacion = is_object($args) && isset($args->theme_location) ? $args->theme_location : '';
    if ($ubicacion !== 'primary') {
        return $titulo;
    }
    $icono = ce_icono_elemento_menu($elemento);
    if ($icono === '') {
        return $titulo;
    }
    return ce_icono_html($icono, 'ce-icono--menu', 'iconos-menu') . $titulo;
}

/**
 * Desplegable con los iconos del catálogo y una muestra del elegido.
 *
 * @param string $vacio Texto de la opción sin icono.
 */
function ce_selector_icono_menu(string $id, string $nombre, string $actual, string $vacio): void
{
    ?>
    <span class="ce-icono-menu">
        <select
            id="<?php echo esc_attr($id); ?>"
            class="ce-icono-menu__selector"
            name="<?php echo esc_attr($nombre); ?>"
        >
            <option value=""><?php echo esc_html($vacio); ?></option>
            <?php foreach (IconosMenu::todos() as $icono => $etiqueta) : ?>
                <option value="<?php echo esc_attr($icono); ?>" <?php selected($actual, $icono); ?>>
                    <?php echo esc_html($etiqueta); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <svg
            class="ce-icono-menu__muestra"
            aria-hidden="true"
            focusable="false"
            <?php echo $actual === '' ? 'hidden' : ''; ?>
        >
            <use href="<?php echo esc_url(ce_url_sprite('iconos-menu') . '#' . $actual); ?>"></use>
        </svg>
    </span>
    <?php
}

/**
 * Selector del icono en cada elemento de Apariencia › Menús.
 */
function ce_campo_icono_menu(int|string $id_elemento, WP_Post $elemento): void
{
    $id_elemento = (int) $id_elemento;
    $campo = 'edit-menu-item-ce-icono-' . $id_elemento;
    $vacio = __('Sin icono', 'clubestoril2');
    $contenido = ce_contenido_elemento_menu($elemento);
    if ($contenido > 0) {
        $heredado = ce_icono_guardado($contenido);
        $vacio = $heredado === ''
            ? __('El de la página (no tiene)', 'clubestoril2')
            /* translators: %s: nombre del icono elegido en la página */
            : sprintf(__('El de la página: %s', 'clubestoril2'), IconosMenu::todos()[$heredado]);
    }
    ?>
    <p class="field-ce-icono description description-wide">
        <label for="<?php echo esc_attr($campo); ?>">
            <?php esc_html_e('Icono', 'clubestoril2'); ?><br>
            <?php
            ce_selector_icono_menu(
                $campo,
                'menu-item-ce-icono[' . $id_elemento . ']',
                ce_icono_guardado($elemento->ID),
                $vacio
            );
            ?>
        </label>
    </p>
    <?php
}

/**
 * Guarda el icono elegido al guardar el menú en Apariencia › Menús.
 *
 * WordPress pasa primero el identificador del menú, que no hace falta.
 *
 * @suppress PhanUnusedGlobalFunctionParameter
 */
function ce_guardar_icono_menu(int $id_menu, int $id_elemento): void
{
    if (!isset($_POST['update-nav-menu-nonce'], $_POST['menu-item-ce-icono'])) {
        return;
    }
    // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- se comprueba que es texto y pasa por sanitize_key().
    $nonce = wp_unslash($_POST['update-nav-menu-nonce']);
    if (
        !is_string($nonce)
        || !wp_verify_nonce(sanitize_key($nonce), 'update-nav_menu')
        || !current_user_can('edit_theme_options')
    ) {
        return;
    }
    // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- cada valor pasa por IconosMenu::sanear().
    $iconos = wp_unslash($_POST['menu-item-ce-icono']);
    if (!is_array($iconos) || !array_key_exists($id_elemento, $iconos)) {
        return;
    }
    ce_guardar_icono($id_elemento, $iconos[$id_elemento]);
}

/**
 * Guarda un icono saneado, o lo borra si está vacío o no es válido.
 */
function ce_guardar_icono(int $id, mixed $valor): void
{
    $icono = IconosMenu::sanear($valor);
    if ($icono === '') {
        delete_post_meta($id, CE_META_ICONO_MENU);
        return;
    }
    update_post_meta($id, CE_META_ICONO_MENU, $icono);
}

/**
 * Recuadro «Icono del menú» en la barra lateral del editor, como en Vantage.
 */
function ce_recuadro_icono_menu(): void
{
    add_meta_box(
        'ce-icono-menu',
        __('Icono del menú', 'clubestoril2'),
        'ce_pintar_recuadro_icono_menu',
        ce_tipos_con_icono_menu(),
        'side',
        'high'
    );
}

function ce_pintar_recuadro_icono_menu(WP_Post $contenido): void
{
    wp_nonce_field('ce_icono_menu', 'ce_icono_menu_nonce');
    ?>
    <p>
        <label class="screen-reader-text" for="ce-icono-menu-selector">
            <?php esc_html_e('Icono del menú', 'clubestoril2'); ?>
        </label>
        <?php
        ce_selector_icono_menu(
            'ce-icono-menu-selector',
            'ce_icono_menu',
            ce_icono_guardado($contenido->ID),
            __('Sin icono', 'clubestoril2')
        );
        ?>
    </p>
    <p class="description">
        <?php
        esc_html_e(
            // phpcs:ignore Generic.Files.LineLength.TooLong -- cadena traducible en una sola pieza.
            'Se muestra junto a su nombre en el menú principal. En Apariencia › Menús se puede elegir otro para un elemento concreto.',
            'clubestoril2'
        );
        ?>
    </p>
    <?php
}

/**
 * Guarda el icono del recuadro al guardar la página o entrada.
 */
function ce_guardar_icono_contenido(int $id, WP_Post $contenido): void
{
    if (
        !isset($_POST['ce_icono_menu_nonce'], $_POST['ce_icono_menu'])
        || wp_is_post_revision($id) !== false
        || wp_is_post_autosave($id) !== false
        || !in_array($contenido->post_type, ce_tipos_con_icono_menu(), true)
    ) {
        return;
    }
    // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- se comprueba que es texto y pasa por sanitize_key().
    $nonce = wp_unslash($_POST['ce_icono_menu_nonce']);
    if (
        !is_string($nonce)
        || !wp_verify_nonce(sanitize_key($nonce), 'ce_icono_menu')
        || !current_user_can('edit_post', $id)
    ) {
        return;
    }
    // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- pasa por IconosMenu::sanear().
    ce_guardar_icono($id, wp_unslash($_POST['ce_icono_menu']));
}

/**
 * Muestra del icono junto al selector, en Apariencia › Menús y en el editor.
 */
function ce_recursos_icono_menu(string $pantalla): void
{
    if (!in_array($pantalla, ['nav-menus.php', 'post.php', 'post-new.php'], true)) {
        return;
    }
    wp_register_style('clubestoril2-iconos-menu', false, [], CE_VERSION);
    wp_enqueue_style('clubestoril2-iconos-menu');
    wp_add_inline_style('clubestoril2-iconos-menu', CE_CSS_SELECTOR_ICONOS);

    wp_enqueue_script(
        'clubestoril2-iconos-menu',
        CE_URI . '/' . CE_JS_SELECTOR_ICONOS,
        [],
        ce_version_recurso(CE_JS_SELECTOR_ICONOS),
        ['in_footer' => true]
    );
    wp_add_inline_script(
        'clubestoril2-iconos-menu',
        'window.ceSelectorIconos = ' . wp_json_encode(
            [
                'sprite' => ce_url_sprite('iconos-menu'),
                'textos' => [
                    'buscar' => __('Escribe para buscar…', 'clubestoril2'),
                    'sinResultados' => __('Ningún icono con ese nombre', 'clubestoril2'),
                    'rejilla' => __('Todos los iconos', 'clubestoril2'),
                ],
            ]
        ) . ';',
        'before'
    );
}

/**
 * Script que convierte el selector de iconos en un buscador.
 */
const CE_JS_SELECTOR_ICONOS = 'assets/js/selector-iconos.js';

/**
 * Estilos del selector de iconos y de su buscador en el escritorio.
 */
const CE_CSS_SELECTOR_ICONOS = <<<'CSS'
.ce-icono-menu{display:flex;align-items:flex-start;gap:8px}
.ce-icono-menu__selector,.ce-selector-iconos{flex:1 1 auto;min-width:0}
.ce-icono-menu__muestra{width:24px;height:24px;flex:none;margin-top:4px}
.ce-selector-iconos{position:relative;display:block}
.ce-selector-iconos__campo{width:100%;margin:0}
/* La lista va en el flujo, no flotando, para no tapar la cuadrícula de debajo. */
.ce-selector-iconos__lista{max-height:10rem;overflow-y:auto;margin:2px 0 0;
padding:4px 0;list-style:none;background:#fff;border:1px solid #8c8f94;border-radius:4px;
box-shadow:0 4px 12px rgb(0 0 0 / .15)}
.ce-selector-iconos__opcion,.ce-selector-iconos__vacio{display:flex;align-items:center;gap:8px;margin:0;
padding:6px 10px;line-height:1.3;cursor:pointer}
.ce-selector-iconos__vacio{color:#646970;cursor:default}
.ce-selector-iconos__opcion--elegida{font-weight:600}
.ce-selector-iconos__opcion[aria-selected=true]{background:#2271b1;color:#fff}
.ce-selector-iconos__dibujo{width:20px;height:20px;flex:none}
.ce-selector-iconos__rejilla{display:grid;grid-template-columns:repeat(auto-fill,minmax(34px,1fr));gap:4px;
max-height:9.5rem;overflow-y:auto;margin-top:6px;padding:4px;background:#fff;border:1px solid #dcdcde;border-radius:4px}
.ce-selector-iconos__casilla{display:grid;place-items:center;height:34px;margin:0;padding:0;color:#1e1e1e;
background:none;border:1px solid transparent;border-radius:4px;cursor:pointer}
.ce-selector-iconos__casilla[hidden],.ce-selector-iconos__rejilla-vacia[hidden]{display:none}
.ce-selector-iconos__casilla:hover{background:#f0f0f1;border-color:#c3c4c7}
.ce-selector-iconos__casilla:focus-visible{outline:2px solid #2271b1;outline-offset:1px}
.ce-selector-iconos__casilla[aria-pressed=true]{background:#2271b1;border-color:#2271b1;color:#fff}
.ce-selector-iconos__casilla .ce-selector-iconos__dibujo{width:22px;height:22px}
.ce-selector-iconos__casilla--ninguno .ce-selector-iconos__dibujo{width:16px;height:16px;border:2px dashed currentColor;
border-radius:50%;opacity:.6}
.ce-selector-iconos__rejilla-vacia{grid-column:1/-1;padding:6px;color:#646970}
CSS;

/**
 * Copia una sola vez los iconos de Vantage al metadato del tema.
 *
 * Vantage guardaba el icono en cada página (metadato vantage_menu_icon, con
 * clases de Font Awesome 4). Cada página con icono, y que aún no tiene uno
 * del tema, recibe el equivalente del catálogo. Los iconos sin equivalente
 * se dejan sin asignar.
 */
function ce_migrar_iconos_vantage(): void
{
    if (get_option(CE_OPCION_ICONOS_MIGRADOS)) {
        return;
    }
    $paginas = get_posts(
        [
            'post_type' => 'any',
            'post_status' => 'any',
            'numberposts' => -1,
            'fields' => 'ids',
            // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- se ejecuta una sola vez.
            'meta_key' => 'vantage_menu_icon',
        ]
    );
    foreach ($paginas as $id) {
        $id = (int) $id;
        if (ce_icono_guardado($id) !== '') {
            continue;
        }
        $icono = IconosMenu::desdeVantage((string) get_post_meta($id, 'vantage_menu_icon', true));
        if ($icono !== '') {
            update_post_meta($id, CE_META_ICONO_MENU, $icono);
        }
    }
    update_option(CE_OPCION_ICONOS_MIGRADOS, CE_VERSION, false);
}

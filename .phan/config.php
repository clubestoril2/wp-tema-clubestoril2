<?php

/**
 * Configuración de Phan para el tema.
 *
 * - Analiza src/, inc/, template-parts/ y las plantillas de la raíz.
 * - Usa los stubs de WordPress (php-stubs/wordpress-stubs) solo para conocer
 *   las funciones y clases del núcleo, sin analizarlos.
 * - Excluye siempre vendor/ del análisis.
 */

$raiz = dirname(__DIR__);

$plantillas = array_map(
    static fn(string $fichero): string => basename($fichero),
    glob($raiz . '/*.php') ?: []
);

return [
    'target_php_version' => null,
    'minimum_target_php_version' => '8.1',
    'directory_list' => [
        'src/',
        'inc/',
        'template-parts/',
        'page-templates/',
        'vendor/php-stubs/wordpress-stubs/',
    ],
    // Con PHP < 8.2 falta el atributo AllowDynamicProperties que usan los
    // stubs de WordPress: se declara para Phan, sin analizarlo.
    'file_list' => PHP_VERSION_ID < 80200
        ? array_merge($plantillas, ['.phan/stubs/AllowDynamicProperties.php'])
        : $plantillas,
    'exclude_analysis_directory_list' => [
        'vendor/',
        '.phan/stubs/',
    ],
    // Variables globales que WordPress pone a disposición de las plantillas.
    'globals_type_map' => [
        'args' => 'array<string,mixed>',
        'post' => '?\WP_Post',
        'content_width' => 'int',
    ],
];

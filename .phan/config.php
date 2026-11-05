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
        'vendor/php-stubs/wordpress-stubs/',
    ],
    'file_list' => $plantillas,
    'exclude_analysis_directory_list' => [
        'vendor/',
    ],
    // Variables globales que WordPress pone a disposición de las plantillas.
    'globals_type_map' => [
        'args' => 'array<string,mixed>',
        'post' => '?\WP_Post',
        'content_width' => 'int',
    ],
];

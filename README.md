# Tema WordPress «Club Estoril II»

Tema del sitio https://www.clubestoril2.es/. Solo depende del núcleo de WordPress (≥ 6.6, PHP ≥ 8.1).

## Estructura

| Ruta | Contenido |
|---|---|
| `style.css` | Cabecera del tema (sin estilos). |
| `functions.php` | Arranque: constantes y carga de `inc/`. |
| `inc/` | Integración con WordPress: soportes, menús, recursos, opciones y funciones de plantilla. |
| `src/` | Lógica pura sin WordPress (espacio de nombres `ClubEstoril2`), probada con PHPUnit. |
| `template-parts/` | Piezas de plantilla: cabecera, tarjetas, contenido y pie. |
| `assets/css/` | Hoja de estilos común: `tokens.css` (colores del escudo, modo claro y oscuro), `base`, `layout`, `header`, `components`, `content` y `editor`. |
| `assets/js/` | Conmutador de modo de color. |
| `assets/img/` | Escudo y sprite de iconos. |
| `theme.json` | Paleta y tipografía para el editor de bloques. |

Convenciones: funciones y variables de plantilla con prefijo `ce_`, clases CSS `.ce-*`, dominio de traducción `clubestoril2`. Estilo PHP PSR-12 más las reglas de seguridad de WordPress (escapado, nonces, entrada saneada).

## Opciones

Las opciones del tema se guardan como `theme_mod`. Sus valores por defecto y su saneamiento están en `src/Opciones.php`; se leen con `ce_opcion_entero()`, `ce_opcion_texto()` y similares. El panel del Personalizador se añadirá en una fase posterior.

## Desarrollo

Requisitos: Docker con Compose. Opcionalmente PHP 8.1+ y Composer en local.

```bash
cp .env.example .env
bin/preparar-wp.sh
```

Abre http://localhost:8080 con el tema activo y contenido de prueba. Las credenciales de administración locales están en `.env`. Para empezar de cero usa `bin/preparar-wp.sh --reiniciar`.

Calidad (PHPUnit, PHPCS y Phan):

```bash
composer install
composer test
```

Sin PHP local:

```bash
docker compose run -e UID=$UID --rm instalacion
docker compose run -e UID=$UID --rm pruebas
```

## Empaquetado

`.gitattributes` excluye los ficheros de desarrollo, así que el tema instalable sale de:

```bash
git archive --format=zip --prefix=clubestoril2/ HEAD > clubestoril2.zip
```

## Licencia

GPL-3.0 o posterior (ver `LICENSE`).

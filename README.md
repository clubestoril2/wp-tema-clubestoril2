# Tema WordPress «Club Deportivo Estoril II»

Tema del sitio https://www.clubestoril2.es/. Solo depende del núcleo de WordPress (≥ 6.6, PHP ≥ 8.1).

## Estructura

| Ruta | Contenido |
|---|---|
| `style.css` | Cabecera del tema (sin estilos). |
| `screenshot.png` | Vista previa en Apariencia › Temas (1200 × 900, captura de la portada). |
| `functions.php` | Arranque: constantes y carga de `inc/`. |
| `inc/` | Integración con WordPress: soportes, menús, recursos, opciones y funciones de plantilla. |
| `src/` | Lógica pura sin WordPress (espacio de nombres `ClubEstoril2`), probada con PHPUnit. |
| `page-templates/` | Plantillas que se eligen en el editor: «Página estrecha» (920 px en lugar de los 1120 px por defecto), para páginas y entradas. |
| `template-parts/` | Piezas de plantilla: cabecera, tarjetas, contenido y pie. |
| `assets/css/` | Hoja de estilos común: `tokens.css` (colores del escudo, modo claro y oscuro), `base`, `layout`, `header`, `components`, `content` y `editor`. `print.css` se carga solo al imprimir; cualquier elemento con la clase `ce-no-imprimir` (en el editor, «Clase(s) CSS adicional(es)» del bloque) no sale en papel. |
| `assets/js/` | Conmutador de modo de color. |
| `assets/img/` | Escudo y sprite de iconos. |
| `theme.json` | Paleta y tipografía para el editor de bloques. |

Convenciones: funciones y variables de plantilla con prefijo `ce_`, clases CSS `.ce-*`, dominio de traducción `clubestoril2`. Estilo PHP PSR-12 más las reglas de seguridad de WordPress (escapado, nonces, entrada saneada).

## Opciones

Las opciones del tema se guardan como `theme_mod`. Sus valores por defecto y su saneamiento están en `src/Opciones.php`; se leen con `ce_opcion_entero()`, `ce_opcion_texto()` y similares. Las más importantes se editan en Apariencia › Personalizar › «Tema Club Estoril II» (`inc/customizer.php`).

## Metadatos

| Clave | Valor | Por defecto |
|---|---|---|
| `ce_revisado` | Fecha de la última revisión en UTC, `AAAA-MM-DDTHH:MM:SSZ` (p. ej. `2026-11-05T11:30:00Z`). | `""`: nunca revisado. |

Está registrado para todos los tipos de contenido y se puede leer y escribir por la API REST (campo `meta.ce_revisado`, con permiso de edición de la publicación). Al guardarlo, las fechas con desplazamiento horario (`2026-11-05T12:30:00+01:00`) se pasan a UTC, y cualquier otro valor (`0`, `"0"`, `-1`, `false`, una fecha imposible) se guarda como `""`. Desde PHP: `ce_revisado()` devuelve la fecha o `null`, y `ce_marcar_revisado($id)` guarda la fecha actual.

## Shortcodes

`[ce_publicaciones]` muestra las últimas publicaciones de una o varias categorías o etiquetas con las mismas tarjetas y filas que la portada. Sirve para que una página como «Restauración» enseñe sus últimas entradas: se escribe en un bloque «Shortcode» del editor. Si no hay publicaciones que mostrar, no imprime nada.

| Atributo | Valores | Por defecto |
|---|---|---|
| `categoria` | Slugs de categorías separados por comas. | Cualquiera |
| `etiqueta` | Slugs de etiquetas separados por comas. Con `categoria`, la publicación debe cumplir ambos. | Cualquiera |
| `cantidad` | De 1 a 24. | 4 |
| `formato` | `tarjeta` (rejilla, como «Destacado») o `fila` (lista, como «Últimas publicaciones»). | `tarjeta` |
| `hijas` | `si` o `no`: si se incluyen las subcategorías. | `si` |
| `dias` | Días con el distintivo «Novedad»; `0` lo quita. | La opción «Días con el distintivo “Novedad”» de la portada |
| `destacadas` | De 0 a 6. Con más de 0 se comporta como la portada: una sección «Destacado» con hasta ese número de publicaciones que además estén en la categoría `destacado`, en tarjetas, y otra «Últimas publicaciones» con `cantidad` de las demás, sin repetir, en filas. `formato` no se aplica. | 0 (una sola lista) |
| `categoria_destacada` | Slugs de categorías, separados por comas, que forman la primera sección en vez de `destacado` (p. ej. `menu-del-dia`). La segunda sección no incluye esas categorías. | `destacado` |
| `titulo_destacadas`, `titulo_anteriores` | Títulos de las dos secciones; vacío (`""`) no imprime título. También valen `texto_destacado` y `texto_publicaciones`. | «Destacado» y «Últimas publicaciones» |

Ejemplos:

```
[ce_publicaciones categoria="restauracion"]
[ce_publicaciones categoria="restauracion" cantidad="6" formato="fila"]
[ce_publicaciones etiqueta="padel" hijas="no" dias="0"]
[ce_publicaciones categoria="restauracion" destacadas="3" cantidad="6"]
[ce_publicaciones categoria="restauracion" categoria_destacada="menu-del-dia" texto_destacado="Menús del día" destacadas="4" cantidad="6"]
```

El último ejemplo abre «Restauración» con los cuatro últimos menús del día y sigue con «Últimas publicaciones» de restauración que no sean menús.

Los títulos de las secciones llevan la clase `ce-titulo-seccion`, la misma que los de la portada (texto en mayúsculas y línea roja hasta el margen derecho). Para escribir un título así en cualquier página, en un bloque «Título» (h2) se pone `ce-titulo-seccion` en «Clase(s) CSS adicional(es)», en el panel Avanzado del bloque.

La publicación donde se escribe el shortcode nunca se lista a sí misma. Los atributos se sanean en `src/Publicaciones.php` y la consulta está en `inc/shortcodes.php`.

## Imágenes de las entradas

La imagen destacada (o, si no hay, la primera del contenido) se recorta en los listados a **1,91:1**. Es la proporción de las vistas previas de enlaces en Facebook, X/Twitter, WhatsApp, Telegram y LinkedIn, así que la misma imagen sirve para la web y para compartir.

| | Recomendación |
|---|---|
| Proporción | 1,91:1 |
| Resolución | **1200 × 630 px** (mínimo 600 × 315) |
| Formato | JPG o WebP, menos de 1 MB |
| Contenido importante | Centrado, con margen: algunos sitios recortan los bordes |

Tamaños que genera el tema: `ce-apertura` (1200 × 630), `ce-tarjeta` (600 × 315) y `ce-fila` (400 × 210). Las imágenes subidas antes de instalar el tema no los tienen: se pueden generar con un plugin como «Regenerate Thumbnails». Mientras tanto se usa el tamaño más cercano recortado por CSS.

Instagram no muestra vistas previas de enlaces; para publicaciones en su feed conviene una versión aparte en 4:5 (1080 × 1350) o 1:1 (1080 × 1080).

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

## Empaquetado e instalación

El ZIP se instala desde Apariencia › Temas › Añadir › Subir tema. Dentro lleva la carpeta `clubestoril2`, así que cada subida reemplaza la versión instalada.

GitHub Actions lo genera automáticamente:

- **Cada commit:** tras pasar las pruebas, la ejecución del workflow «Tema» guarda el artefacto `wp-tema-clubestoril2.<commit>` durante 30 días. Se descarga desde la pestaña Actions y ya es el ZIP instalable.
- **Cada etiqueta:** además se crea una release con `wp-tema-clubestoril2.<etiqueta>.zip`. La versión del tema pasa a ser la de la etiqueta sin la «v» inicial.

```bash
git tag v0.2.0
git push origin v0.2.0
```

Para generarlo en local con lo que hay en el último commit:

```bash
bin/empaquetar.sh dist/clubestoril2.zip            # versión de style.css
bin/empaquetar.sh dist/clubestoril2.zip 0.2.0      # con otra versión
```

Solo entran los ficheros versionados; `.gitattributes` deja fuera las herramientas de desarrollo.

## Licencia

GPL-3.0 o posterior (ver `LICENSE`).

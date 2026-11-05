#!/usr/bin/env bash
# Genera el ZIP instalable del tema (Apariencia › Temas › Añadir › Subir tema).
#
# Uso: bin/empaquetar.sh <salida.zip> [versión] [revisión]
#
#   salida.zip  Ruta del ZIP que se genera.
#   versión     Opcional. Sustituye la versión de style.css y CE_VERSION
#               (por ejemplo 1.2.0; una «v» inicial se elimina).
#   revisión    Opcional. Commit o etiqueta que se empaqueta (por defecto HEAD).
#
# Solo incluye ficheros versionados en git y respeta export-ignore de
# .gitattributes, así que quedan fuera las herramientas de desarrollo.
# Dentro del ZIP el tema va en la carpeta «clubestoril2», que es el nombre
# con el que WordPress lo instala: así cada subida sustituye a la anterior.
set -euo pipefail

CARPETA_TEMA=clubestoril2

salida=${1:?Uso: bin/empaquetar.sh <salida.zip> [versión] [revisión]}
version=${2:-}
revision=${3:-HEAD}

raiz=$(cd "$(dirname "$0")/.." && pwd)
case "$salida" in
    /*) ;;
    *) salida="$PWD/$salida" ;;
esac

version=${version#v}
if [[ -n "$version" && ! "$version" =~ ^[0-9A-Za-z][0-9A-Za-z.+-]*$ ]]; then
    echo "Versión no válida: «$version»" >&2
    exit 1
fi

temporal=$(mktemp -d)
trap 'rm -rf "$temporal"' EXIT

mkdir -p "$temporal/$CARPETA_TEMA"
git -C "$raiz" archive --format=tar "$revision" | tar -x -C "$temporal/$CARPETA_TEMA"

tema="$temporal/$CARPETA_TEMA"
for obligatorio in style.css functions.php index.php; do
    if [[ ! -f "$tema/$obligatorio" ]]; then
        echo "Falta $obligatorio en la revisión $revision: no es un tema válido." >&2
        exit 1
    fi
done

if [[ -n "$version" ]]; then
    sed -i -E "s/^Version: .*/Version: $version/" "$tema/style.css"
    sed -i -E "s/define\('CE_VERSION', '[^']*'\);/define('CE_VERSION', '$version');/" "$tema/functions.php"
    grep -q "^Version: $version\$" "$tema/style.css"
    grep -q "define('CE_VERSION', '$version');" "$tema/functions.php"
fi

mkdir -p "$(dirname "$salida")"
rm -f "$salida"
(cd "$temporal" && zip -qrX "$salida" "$CARPETA_TEMA")

echo "Generado $salida ($(grep -m1 '^Version:' "$tema/style.css"))"

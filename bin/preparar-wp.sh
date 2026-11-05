#!/usr/bin/env bash
# Prepara el WordPress local de desarrollo: lo instala, activa el tema y crea
# contenido de prueba con la estructura del sitio del club (categorías,
# jerarquía de páginas, menú principal y entradas con distintas fechas).
#
# Uso: bin/preparar-wp.sh            (idempotente: si ya está instalado, solo activa el tema)
#      bin/preparar-wp.sh --reiniciar (borra la base de datos local y empieza de cero)
set -euo pipefail

cd "$(dirname "$0")/.."

if [[ ! -f .env ]]; then
    cp .env.example .env
fi
# shellcheck disable=SC1091
set -a; source .env; set +a

if [[ "${1:-}" == "--reiniciar" ]]; then
    docker compose down -v
fi

docker compose up -d --wait wordpress

# Espera a que el contenedor de WordPress haya copiado sus ficheros.
for _ in $(seq 1 30); do
    if docker compose exec -T wordpress test -f /var/www/html/wp-config.php; then
        break
    fi
    sleep 1
done

docker compose run --rm -T \
    -e WP_URL="$WP_URL" \
    -e WP_ADMIN_USUARIO="$WP_ADMIN_USUARIO" \
    -e WP_ADMIN_CLAVE="$WP_ADMIN_CLAVE" \
    -e WP_ADMIN_CORREO="$WP_ADMIN_CORREO" \
    wpcli sh -s <<'GUION'
set -e

if wp core is-installed 2>/dev/null; then
    echo "WordPress ya está instalado: solo se activa el tema."
    wp theme activate clubestoril2
    exit 0
fi

wp core install --url="$WP_URL" --title="Club Deportivo Estoril II" \
    --admin_user="$WP_ADMIN_USUARIO" --admin_password="$WP_ADMIN_CLAVE" \
    --admin_email="$WP_ADMIN_CORREO" --skip-email
wp language core install es_ES --activate || echo "Sin conexión para el idioma: se sigue en inglés."
wp option update blogdescription "Página oficial del Club Deportivo Estoril II de Móstoles"
wp option update timezone_string "Europe/Madrid"
wp option update permalink_structure "/%year%/%monthnum%/%day%/%postname%/"
wp option update posts_per_page 15
wp theme activate clubestoril2
wp post delete 1 2 --force >/dev/null 2>&1 || true

# Categorías como en el sitio real.
wp term create category Novedades --slug=novedades
NOVEDADES=$(wp term get category novedades --by=slug --field=term_id)
wp term create category Facebook --slug=facebook --parent="$NOVEDADES"
wp term create category Noticias --slug=noticias
wp term create category Actividades --slug=actividades
wp term create category Deportes --slug=deportes
wp term create category Restauración --slug=restauracion
RESTAURACION=$(wp term get category restauracion --by=slug --field=term_id)
wp term create category "Menú del día" --slug=menu-del-dia --parent="$RESTAURACION"

pagina() { # título, padre (ID o 0), contenido
    wp post create --post_type=page --post_status=publish --post_title="$1" \
        --post_parent="$2" --post_content="$3" --porcelain
}

INICIO=$(pagina "Club Deportivo Estoril II" 0 "")
wp option update show_on_front page
wp option update page_on_front "$INICIO"

INFO=$(pagina "Información general" 0 "<p>Toda la información práctica del club.</p>")
HISTORIA=$(pagina "Historia del club" "$INFO" "<p>El club nació como club de tenis en el barrio de Estoril II de Móstoles.</p>")
pagina "Himno" "$HISTORIA" "<p>Letra del himno del club.</p>" >/dev/null
pagina "Imagen de marca" "$HISTORIA" "<p>Descripción del escudo y zona de descarga.</p>" >/dev/null
pagina "Tarifas" "$INFO" '<p>Las invitaciones y abonos para no socios solo pueden adquirirse acompañados de un socio.</p>
<div class="ce-aviso"><h2>Nota muy importante</h2><p>Las invitaciones y abonos al club o a la piscina para no socios <strong>únicamente pueden ser adquiridas acompañados de un socio</strong>.</p></div>
<table class="tabla"><thead><tr><th>Invitación al club para no socios</th><th>Euros (€)</th></tr></thead><tbody>
<tr><td>Invitación de paseo y bar acompañados de un socio</td><td>1</td></tr>
<tr><td>Invitación para la práctica deportiva</td><td>3</td></tr>
<tr><td>Bono 10 invitaciones para práctica deportiva</td><td>25</td></tr>
</tbody></table>' >/dev/null
pagina "Ubicación" "$INFO" "<p>Calle de ejemplo, Móstoles.</p>" >/dev/null
pagina "Cuota de socios" "$INFO" "<p>Cuotas y ventajas de pago.</p>" >/dev/null
INSTALACIONES=$(pagina "Instalaciones" "$INFO" "<p>Instalaciones del club.</p>")
PISCINA=$(pagina "Piscina" "$INSTALACIONES" "<p>Piscina de verano.</p>")
pagina "Normativa de uso" "$PISCINA" "<p>Normas de la piscina.</p>" >/dev/null
pagina "Horario de apertura" "$PISCINA" "<p>Horario de la piscina.</p>" >/dev/null
pagina "Pistas de tenis" "$INSTALACIONES" "<p>Pistas de tenis.</p>" >/dev/null
pagina "Minigolf" "$INSTALACIONES" "<p>Minigolf.</p>" >/dev/null
pagina "Contacto" "$INFO" "<p>Teléfono y correo de la secretaría.</p>" >/dev/null
ESCUELAS=$(pagina "Escuelas y actividades" 0 "<p>Escuelas deportivas y actividades.</p>")
for t in "Escuela de pádel" "Escuela de tenis" "Escuela de fútbol 7" "Actividad de yoga"; do
    pagina "$t" "$ESCUELAS" "<p>$t.</p>" >/dev/null
done
SERVICIOS=$(pagina "Servicios" 0 "<p>Servicios para socios.</p>")
pagina "Gimnasio" "$SERVICIOS" "<p>Gimnasio.</p>" >/dev/null
RESTAURANTE=$(pagina "Restauración" "$SERVICIOS" "<p>Restaurante y terraza.</p>")
pagina "Carta" "$RESTAURANTE" "<p>Carta del restaurante.</p>" >/dev/null
CAMPAMENTOS=$(pagina "Campamentos" "$SERVICIOS" "<p>Campamentos.</p>")
pagina "Campamento de verano" "$CAMPAMENTOS" "<p>Campamento de verano.</p>" >/dev/null
CLUB=$(pagina "El Club" 0 "<p>El club.</p>")
pagina "Colecciones de fotos" "$CLUB" "<p>Fotos.</p>" >/dev/null

# Menú principal a partir de la jerarquía de páginas.
wp menu create "Menú principal"
wp menu location assign "Menú principal" principal
wp menu item add-custom "Menú principal" "Portada" "$(wp option get home)/" >/dev/null
anadir_rama() { # ID de página, ID de elemento padre del menú (0 = raíz)
    local item hija
    item=$(wp menu item add-post "Menú principal" "$1" --parent-id="$2" --porcelain)
    for hija in $(wp post list --post_type=page --post_parent="$1" --orderby=ID --order=ASC --field=ID); do
        anadir_rama "$hija" "$item"
    done
}
for raiz in "$INFO" "$ESCUELAS" "$SERVICIOS" "$CLUB"; do
    anadir_rama "$raiz" 0
done
CLUB_ITEM=$(wp menu item list "Menú principal" --fields=db_id,object_id --format=csv | awk -F, -v id="$CLUB" '$2==id{print $1}')
wp menu item add-term "Menú principal" category "$NOVEDADES" --parent-id="$CLUB_ITEM" >/dev/null
wp menu item add-custom "Menú principal" "Facebook" "https://www.facebook.com/clubestoril2/" --parent-id="$CLUB_ITEM" >/dev/null

# Entradas de prueba con fechas repartidas en las últimas semanas.
entrada() { # días atrás, categoría, título, contenido
    fecha=$(php -r "echo date('Y-m-d H:i:s', time() - $1 * 86400);")
    wp post create --post_status=publish --post_date="$fecha" --post_category="$2" \
        --post_title="$3" --post_content="$4" --porcelain >/dev/null
}
LOREM="<p>Texto de prueba para comprobar cómo se ven los listados y las entradas del club con varias líneas de contenido.</p>"
entrada 1 noticias "Abierta la inscripción de las escuelas deportivas" "$LOREM$LOREM"
entrada 2 facebook "Martes 03 de noviembre de 2026 a las 20:11h" "<p>Dicen que a mal tiempo buena cara. Nuestro restaurante ha preparado unos deliciosos menús.</p><p>Aconsejamos hacer reserva.</p>"
entrada 3 actividades "Pasaje del terror: gracias a todos" "$LOREM"
entrada 3 menu-del-dia "Menú del día: miércoles 4 de noviembre" "<p>Primeros, segundos y postres.</p>"
entrada 5 deportes "Torneo social de pádel" "$LOREM"
entrada 8 facebook "Viernes 30 de octubre de 2026 a las 16:27h" "<p>Termina octubre y empieza noviembre.</p>"
entrada 12 noticias "Nuevo horario de invierno" "$LOREM"
entrada 16 actividades "Cuentacuentos infantil" "$LOREM"
entrada 20 deportes "Resultados de la liga de fútbol 7" "$LOREM"
entrada 25 noticias "Asamblea general de socios" "$LOREM"
entrada 33 deportes "Clausura de la temporada de tenis" "$LOREM"
entrada 40 actividades "Campamento de verano: balance" "$LOREM"
entrada 47 noticias "Mejoras en las pistas polideportivas" "$LOREM"
entrada 60 facebook "Domingo 06 de septiembre de 2026 a las 12:00h" "<p>Último fin de semana de piscina.</p>"

# Datos de la cabecera para ver la barra de información rápida.
wp theme mod set cabecera_horario "Hoy de 9:00 a 23:00"
wp theme mod set cabecera_telefono "641 165 023"
wp theme mod set cabecera_direccion "Móstoles (Madrid)"

wp rewrite flush
echo "Listo: $WP_URL (usuario $WP_ADMIN_USUARIO, clave en .env)"
GUION

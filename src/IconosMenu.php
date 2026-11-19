<?php

declare(strict_types=1);

namespace ClubEstoril2;

/**
 * Catálogo de iconos que se pueden poner a los elementos del menú.
 *
 * Los dibujos son de Tabler Icons (https://tabler.io/icons, licencia MIT) y
 * están en assets/img/iconos-menu.svg, que genera bin/iconos-menu.php a partir
 * de este catálogo. Cada icono se identifica por su nombre en Tabler.
 *
 * No depende de WordPress, de modo que las reglas se pueden probar de forma
 * aislada. Los ganchos que lo usan están en inc/menu-iconos.php.
 */
final class IconosMenu
{
    /**
     * Versión de Tabler Icons de la que sale el sprite.
     */
    public const VERSION_TABLER = '3.19.0';

    /**
     * Icono de Tabler => nombre que se muestra al elegirlo.
     *
     * @var array<string, string>
     */
    private const ICONOS = [
        // Club e información.
        'home' => 'Inicio',
        'info-circle' => 'Información',
        'book' => 'Libro (historia)',
        'music' => 'Música (himno)',
        'photo' => 'Imagen (fotos)',
        'hierarchy' => 'Organigrama',
        'users-group' => 'Grupo',
        'users' => 'Personas',
        'news' => 'Noticias',
        'file-text' => 'Documento (normativa)',
        'brand-facebook' => 'Facebook',

        // Socios y precios.
        'ticket' => 'Entrada (tarifas)',
        'cash' => 'Dinero (cuotas)',
        'currency-euro' => 'Euro',
        'id' => 'Tarjeta (contacto)',
        'address-book' => 'Agenda',
        'mail' => 'Correo',
        'phone' => 'Teléfono',

        // Lugar y horario.
        'map-pin' => 'Ubicación',
        'building' => 'Edificio (instalaciones)',
        'calendar' => 'Calendario (horario)',
        'clock' => 'Reloj',

        // Instalaciones y servicios.
        'swimming' => 'Natación (piscina)',
        'coffee' => 'Café (restauración)',
        'tools-kitchen-2' => 'Cubiertos (carta)',
        'golf' => 'Golf (minigolf)',
        'horse-toy' => 'Caballito (parque infantil)',
        'barbell' => 'Pesas (gimnasio)',
        'share' => 'Compartir (servicios)',
        'tent' => 'Tienda (campamentos)',
        'rocket' => 'Cohete',
        'sun' => 'Sol (verano)',
        'cake' => 'Tarta (cumpleaños)',
        'balloon' => 'Globo',
        'confetti' => 'Fiesta',
        'shirt-sport' => 'Camiseta (material deportivo)',

        // Deportes y actividades.
        'heart' => 'Corazón',
        'heartbeat' => 'Pulso (actividad física)',
        'ball-tennis' => 'Tenis y pádel',
        'ball-football' => 'Fútbol',
        'ball-basketball' => 'Baloncesto',
        'ball-volleyball' => 'Voleibol',
        'stretching' => 'Estiramientos (gimnasia)',
        'yoga' => 'Yoga',
        'run' => 'Carrera',
        'bike' => 'Bicicleta',
        'trophy' => 'Trofeo',
        'school' => 'Escuela',
        'mood-kid' => 'Niños',
    ];

    /**
     * Iconos de Font Awesome 4 que usaba el tema Vantage => icono equivalente.
     *
     * @var array<string, string>
     */
    private const DESDE_VANTAGE = [
        'fa-address-book' => 'address-book',
        'fa-address-book-o' => 'address-book',
        'fa-address-card' => 'id',
        'fa-address-card-o' => 'id',
        'fa-bicycle' => 'bike',
        'fa-birthday-cake' => 'cake',
        'fa-book' => 'book',
        'fa-building' => 'building',
        'fa-building-o' => 'building',
        'fa-calendar' => 'calendar',
        'fa-calendar-o' => 'calendar',
        'fa-clock-o' => 'clock',
        'fa-coffee' => 'coffee',
        'fa-cutlery' => 'tools-kitchen-2',
        'fa-envelope' => 'mail',
        'fa-envelope-o' => 'mail',
        'fa-eur' => 'currency-euro',
        'fa-euro' => 'currency-euro',
        'fa-facebook' => 'brand-facebook',
        'fa-facebook-official' => 'brand-facebook',
        'fa-facebook-square' => 'brand-facebook',
        'fa-file-text' => 'file-text',
        'fa-file-text-o' => 'file-text',
        'fa-futbol-o' => 'ball-football',
        'fa-graduation-cap' => 'school',
        'fa-group' => 'users-group',
        'fa-heart' => 'heart',
        'fa-heart-o' => 'heart',
        'fa-heartbeat' => 'heartbeat',
        'fa-home' => 'home',
        'fa-image' => 'photo',
        'fa-info' => 'info-circle',
        'fa-info-circle' => 'info-circle',
        'fa-location-arrow' => 'map-pin',
        'fa-map-marker' => 'map-pin',
        'fa-money' => 'cash',
        'fa-music' => 'music',
        'fa-newspaper-o' => 'news',
        'fa-object-group' => 'hierarchy',
        'fa-phone' => 'phone',
        'fa-photo' => 'photo',
        'fa-picture-o' => 'photo',
        'fa-rocket' => 'rocket',
        'fa-share-alt' => 'share',
        'fa-share-alt-square' => 'share',
        'fa-sitemap' => 'hierarchy',
        'fa-soccer-ball-o' => 'ball-football',
        'fa-space-shuttle' => 'rocket',
        'fa-sun-o' => 'sun',
        'fa-ticket' => 'ticket',
        'fa-trophy' => 'trophy',
        'fa-users' => 'users',
    ];

    /**
     * Todos los iconos, en el orden en que se ofrecen: nombre => etiqueta.
     *
     * @return array<string, string>
     */
    public static function todos(): array
    {
        return self::ICONOS;
    }

    public static function existe(string $icono): bool
    {
        return isset(self::ICONOS[$icono]);
    }

    /**
     * El icono si está en el catálogo; si no, vacío (sin icono).
     */
    public static function sanear(mixed $valor): string
    {
        if (!is_string($valor)) {
            return '';
        }
        $icono = strtolower(trim($valor));
        return self::existe($icono) ? $icono : '';
    }

    /**
     * Icono equivalente a una clase de Font Awesome guardada por Vantage
     * («fa fa-heartbeat»), o vacío si no tiene equivalente.
     */
    public static function desdeVantage(string $clase): string
    {
        foreach (preg_split('/\s+/', strtolower(trim($clase))) ?: [] as $parte) {
            if (isset(self::DESDE_VANTAGE[$parte])) {
                return self::DESDE_VANTAGE[$parte];
            }
        }
        return '';
    }
}

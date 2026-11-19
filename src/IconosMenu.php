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
        'books' => 'Libros (biblioteca)',
        'music' => 'Música (himno)',
        'photo' => 'Imagen (fotos)',
        'album' => 'Álbum de fotos',
        'camera' => 'Cámara',
        'video' => 'Vídeo',
        'hierarchy' => 'Organigrama',
        'building-community' => 'Comunidad (club social)',
        'shield' => 'Escudo',
        'flag' => 'Bandera',
        'crown' => 'Corona',
        'star' => 'Estrella',
        'heart-handshake' => 'Apretón de manos (voluntariado)',
        'thumb-up' => 'Me gusta',
        'news' => 'Noticias',
        'speakerphone' => 'Megáfono (anuncios)',
        'bell' => 'Campana (avisos)',
        'alert-triangle' => 'Aviso',
        'help' => 'Ayuda (preguntas)',
        'rss' => 'RSS',
        'brand-facebook' => 'Facebook',
        'brand-instagram' => 'Instagram',
        'brand-x' => 'X (Twitter)',
        'brand-youtube' => 'YouTube',
        'brand-telegram' => 'Telegram',
        'brand-whatsapp' => 'WhatsApp',

        // Documentos y trámites.
        'file-text' => 'Documento (normativa)',
        'file-description' => 'Documento con texto',
        'files' => 'Documentos',
        'file' => 'Archivo',
        'download' => 'Descarga',
        'clipboard-list' => 'Lista (inscripciones)',
        'checklist' => 'Lista de comprobación',
        'list-details' => 'Listado',
        'notes' => 'Notas',
        'pencil' => 'Lápiz',
        'certificate' => 'Certificado',
        'license' => 'Licencia',
        'gavel' => 'Mazo (asamblea)',
        'scale' => 'Balanza (estatutos)',
        'shield-check' => 'Escudo con marca (seguridad)',
        'link' => 'Enlace',

        // Personas.
        'users-group' => 'Grupo',
        'users' => 'Personas',
        'user' => 'Persona',
        'user-plus' => 'Persona con más (alta de socio)',
        'user-check' => 'Persona con marca (socio)',
        'friends' => 'Amigos',
        'mood-kid' => 'Niños',
        'baby-carriage' => 'Carrito de bebé',
        'mood-happy' => 'Cara alegre',
        'wheelchair' => 'Silla de ruedas (accesibilidad)',

        // Socios, precios y contacto.
        'ticket' => 'Entrada (tarifas)',
        'cash' => 'Dinero (cuotas)',
        'cash-banknote' => 'Billete',
        'coins' => 'Monedas',
        'currency-euro' => 'Euro',
        'wallet' => 'Cartera',
        'pig-money' => 'Hucha',
        'credit-card' => 'Tarjeta de crédito',
        'receipt' => 'Recibo',
        'tag' => 'Etiqueta (precio)',
        'discount' => 'Descuento',
        'gift' => 'Regalo',
        'id' => 'Tarjeta (contacto)',
        'id-badge' => 'Carné',
        'key' => 'Llave',
        'lock' => 'Candado',
        'address-book' => 'Agenda',
        'mail' => 'Correo',
        'at' => 'Arroba',
        'phone' => 'Teléfono',
        'device-mobile' => 'Móvil',
        'message' => 'Mensaje',
        'messages' => 'Conversación',
        'shopping-bag' => 'Bolsa (tienda)',
        'shopping-cart' => 'Carrito de la compra',
        'building-store' => 'Tienda',

        // Lugar y horario.
        'map-pin' => 'Ubicación',
        'map' => 'Mapa',
        'compass' => 'Brújula',
        'route' => 'Ruta',
        'world' => 'Mundo',
        'building' => 'Edificio (instalaciones)',
        'building-stadium' => 'Estadio',
        'building-castle' => 'Castillo',
        'door' => 'Puerta (acceso)',
        'parking' => 'Aparcamiento',
        'car' => 'Coche',
        'bus' => 'Autobús',
        'motorbike' => 'Moto',
        'calendar' => 'Calendario (horario)',
        'calendar-event' => 'Calendario con evento',
        'calendar-time' => 'Calendario con reloj',
        'clock' => 'Reloj',
        'alarm' => 'Despertador',
        'hourglass' => 'Reloj de arena',
        'stopwatch' => 'Cronómetro',

        // Instalaciones y naturaleza.
        'swimming' => 'Natación (piscina)',
        'pool' => 'Piscina',
        'lifebuoy' => 'Salvavidas (socorrismo)',
        'beach' => 'Playa',
        'umbrella' => 'Sombrilla',
        'bath' => 'Bañera (vestuarios)',
        'massage' => 'Masaje (spa)',
        'first-aid-kit' => 'Botiquín',
        'golf' => 'Golf (minigolf)',
        'horse-toy' => 'Caballito (parque infantil)',
        'barbell' => 'Pesas (gimnasio)',
        'share' => 'Compartir (servicios)',
        'tent' => 'Tienda (campamentos)',
        'campfire' => 'Hoguera',
        'mountain' => 'Montaña',
        'tree' => 'Árbol',
        'trees' => 'Árboles (jardín)',
        'plant' => 'Planta',
        'leaf' => 'Hoja',
        'flower' => 'Flor',
        'paw' => 'Huella (mascotas)',
        'dog' => 'Perro',
        'rocket' => 'Cohete',
        'sun' => 'Sol (verano)',
        'sunset' => 'Atardecer',
        'moon' => 'Luna (noche)',
        'snowflake' => 'Copo de nieve (invierno)',
        'cloud-rain' => 'Lluvia',
        'temperature' => 'Termómetro',
        'wifi' => 'Wifi',

        // Restauración.
        'coffee' => 'Café (restauración)',
        'cup' => 'Taza',
        'tools-kitchen-2' => 'Cubiertos (carta)',
        'tools-kitchen' => 'Cuchillo y tenedor',
        'chef-hat' => 'Gorro de cocinero',
        'glass-full' => 'Copa llena',
        'glass' => 'Copa vacía',
        'beer' => 'Cerveza',
        'bottle' => 'Botella',
        'pizza' => 'Pizza',
        'burger' => 'Hamburguesa',
        'soup' => 'Sopa',
        'salad' => 'Ensalada',
        'bread' => 'Pan',
        'fish' => 'Pescado',
        'meat' => 'Carne',
        'ice-cream' => 'Helado',

        // Fiestas, ocio y cultura.
        'cake' => 'Tarta (cumpleaños)',
        'balloon' => 'Globo',
        'confetti' => 'Fiesta',
        'masks-theater' => 'Teatro',
        'movie' => 'Cine',
        'microphone' => 'Micrófono',
        'guitar-pick' => 'Púa (guitarra)',
        'palette' => 'Paleta (pintura)',
        'brush' => 'Pincel',
        'backpack' => 'Mochila',
        'school' => 'Escuela',
        'chess' => 'Ajedrez',
        'chess-knight' => 'Caballo de ajedrez',
        'dice' => 'Dado',
        'cards' => 'Cartas',
        'play-card' => 'Naipe',
        'puzzle' => 'Puzle',
        'sport-billard' => 'Billar',

        // Deportes y actividades.
        'heart' => 'Corazón',
        'heartbeat' => 'Pulso (actividad física)',
        'ball-tennis' => 'Tenis y pádel',
        'ping-pong' => 'Tenis de mesa',
        'ball-football' => 'Fútbol',
        'play-football' => 'Jugador de fútbol',
        'soccer-field' => 'Campo de fútbol',
        'ball-basketball' => 'Baloncesto',
        'play-basketball' => 'Jugador de baloncesto',
        'ball-volleyball' => 'Voleibol',
        'play-volleyball' => 'Jugador de voleibol',
        'play-handball' => 'Balonmano',
        'ball-baseball' => 'Béisbol',
        'ball-american-football' => 'Fútbol americano',
        'ball-bowling' => 'Bolos',
        'stretching' => 'Estiramientos (gimnasia)',
        'stretching-2' => 'Estiramientos de pie',
        'yoga' => 'Yoga',
        'weight' => 'Pesa rusa',
        'treadmill' => 'Cinta de correr',
        'jump-rope' => 'Comba',
        'karate' => 'Artes marciales',
        'swords' => 'Esgrima',
        'target' => 'Diana (tiro)',
        'run' => 'Carrera',
        'walk' => 'Caminar',
        'trekking' => 'Senderismo',
        'bike' => 'Bicicleta',
        'skateboarding' => 'Monopatín',
        'roller-skating' => 'Patinaje',
        'kayak' => 'Piragüismo',
        'sailboat' => 'Vela',
        'scuba-diving' => 'Buceo',
        'fish-hook' => 'Pesca',
        'trophy' => 'Trofeo',
        'medal' => 'Medalla',
        'medal-2' => 'Medalla con cinta',
        'award' => 'Premio',
        'olympics' => 'Aros olímpicos',
        'shirt-sport' => 'Camiseta (material deportivo)',
        'shirt' => 'Camiseta',
        'shoe' => 'Zapatilla',
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

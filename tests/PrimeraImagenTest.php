<?php

declare(strict_types=1);

namespace ClubEstoril2\Tests;

use ClubEstoril2\PrimeraImagen;
use PHPUnit\Framework\TestCase;

final class PrimeraImagenTest extends TestCase
{
    public function testSinImagenesDevuelveNull(): void
    {
        self::assertNull(PrimeraImagen::buscar('<p>Crónica del partido.</p>'));
        self::assertNull(PrimeraImagen::buscar(''));
    }

    public function testImagenDeLaBibliotecaDeMedios(): void
    {
        $html = '<!-- wp:image {"id":42} --><figure class="wp-block-image size-large">'
            . '<img src="https://ejemplo.es/wp-content/uploads/foto.jpg" alt="" class="wp-image-42"/>'
            . '</figure><!-- /wp:image -->';
        self::assertSame(
            ['id' => 42, 'src' => 'https://ejemplo.es/wp-content/uploads/foto.jpg', 'video' => false],
            PrimeraImagen::buscar($html)
        );
    }

    public function testEditorClasicoConVariasClases(): void
    {
        $html = "<p><img class='alignnone size-medium wp-image-7' src='/foto.jpg' width='300'></p>";
        self::assertSame(['id' => 7, 'src' => '/foto.jpg', 'video' => false], PrimeraImagen::buscar($html));
    }

    public function testImagenExternaSinIdentificador(): void
    {
        $html = '<p>Texto</p><IMG SRC="https://cdn.ejemplo.es/a.png?x=1&amp;y=2" alt="Equipo">';
        self::assertSame(
            ['id' => null, 'src' => 'https://cdn.ejemplo.es/a.png?x=1&y=2', 'video' => false],
            PrimeraImagen::buscar($html)
        );
    }

    public function testDevuelveSoloLaPrimera(): void
    {
        $html = '<img src="/primera.jpg" class="wp-image-1"><img src="/segunda.jpg" class="wp-image-2">';
        self::assertSame(['id' => 1, 'src' => '/primera.jpg', 'video' => false], PrimeraImagen::buscar($html));
    }

    public function testClaseParecidaNoEsIdentificador(): void
    {
        $html = '<img src="/foto.jpg" class="mi-wp-image-5">';
        self::assertSame(['id' => null, 'src' => '/foto.jpg', 'video' => false], PrimeraImagen::buscar($html));
    }

    public function testImagenSinSrcNiIdentificador(): void
    {
        self::assertNull(PrimeraImagen::buscar('<img alt="vacía" src="">'));
    }

    public function testFotogramaDeVideoDelSincronizador(): void
    {
        $html = '<p><a href="https://www.facebook.com/v/1" target="_blank" class="video">'
            . '<img src="/fotograma.jpg" alt="Cartel" class="alignright wp-image-3156"></a><br>Texto</p>';
        self::assertSame(['id' => 3156, 'src' => '/fotograma.jpg', 'video' => true], PrimeraImagen::buscar($html));
    }

    public function testEnlaceDeVideoYaCerradoNoMarcaLaImagen(): void
    {
        $html = '<a class="video" href="/v">ver vídeo</a> <a href="/foto"><img src="/foto.jpg"></a>';
        self::assertSame(['id' => null, 'src' => '/foto.jpg', 'video' => false], PrimeraImagen::buscar($html));
        $html = '<a class="video" href="/v">ver vídeo</a><p><img src="/foto.jpg"></p>';
        self::assertFalse(PrimeraImagen::buscar($html)['video'] ?? true);
    }

    public function testClaseParecidaAVideoNoEsVideo(): void
    {
        $html = '<a class="videoteca" href="/v"><img src="/foto.jpg"></a>';
        self::assertFalse(PrimeraImagen::buscar($html)['video'] ?? true);
    }

    public function testEsDelAdjuntoConTamanoIntermedioYOtroDominio(): void
    {
        self::assertTrue(PrimeraImagen::esDelAdjunto(
            'http://www.ejemplo.es/wp-content/uploads/2023/09/foto-300x300.png?v=2',
            'https://www.ejemplo.es/wp-content/uploads/2023/09/foto.png'
        ));
        self::assertTrue(PrimeraImagen::esDelAdjunto(
            '/wp-content/uploads/2023/09/foto-scaled-1024x768.jpg',
            'https://www.ejemplo.es/wp-content/uploads/2023/09/foto-scaled.jpg'
        ));
    }

    public function testClaseCopiadaConOtraImagenNoEsDelAdjunto(): void
    {
        self::assertFalse(PrimeraImagen::esDelAdjunto(
            'https://estaticos.ejemplo.es/imagenes/facebook/album.720x1018.jpg',
            'https://www.ejemplo.es/wp-content/uploads/2023/09/Recordatorio.png'
        ));
        self::assertFalse(PrimeraImagen::esDelAdjunto('', 'https://www.ejemplo.es/wp-content/uploads/a.png'));
    }
}

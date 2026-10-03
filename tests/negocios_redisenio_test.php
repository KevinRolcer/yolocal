<?php

function renderizarVista($archivo, $variables)
{
    extract($variables);
    ob_start();
    include __DIR__ . '/../vistas/' . $archivo;
    return ob_get_clean();
}

function obtenerDocumento($html)
{
    $documento = new DOMDocument();
    @$documento->loadHTML($html);
    return new DOMXPath($documento);
}

function obtenerReglasCss($css, $selector)
{
    preg_match_all('/' . preg_quote($selector, '/') . '\s*\{([^}]*)\}/', $css, $coincidencias);
    if (empty($coincidencias[1])) {
        throw new RuntimeException('No se encontró la regla CSS "' . $selector . '".');
    }
    return $coincidencias[1];
}

function obtenerUltimaReglaCss($css, $selector)
{
    $reglas = obtenerReglasCss($css, $selector);
    return end($reglas);
}

try {
    $catalogo = renderizarVista('negociosL.php', [
        'categorias' => [],
        'negocios' => [[
            'ID_Negocio' => 7,
            'nombre_negocio' => 'Café del barrio',
            'nombre_categoria' => 'Cafeterías',
            'Rutaicono' => '',
            'DescripcionN' => 'Café local',
            'Direccion' => 'Centro',
            'GoogleMaps' => '',
        ]],
        'pagina_actual' => 1,
        'total_paginas' => 1,
    ]);
    $catalogoXpath = obtenerDocumento($catalogo);
    $tituloCatalogo = $catalogoXpath->query('//h1[normalize-space()="Negocios locales"]');
    if ($tituloCatalogo->length !== 1) {
        throw new RuntimeException('El catálogo debe presentar un título principal claro.');
    }
    $logoHero = $catalogoXpath->query('//section[contains(concat(" ", normalize-space(@class), " "), " catalogo-hero ")]/img[contains(concat(" ", normalize-space(@class), " "), " catalogo-hero-logo ")]');
    if ($logoHero->length !== 1
        || $logoHero->item(0)->getAttribute('src') !== '../assets/img/LogoYolocal.png'
        || $logoHero->item(0)->getAttribute('alt') !== 'Logo de Yolocal') {
        throw new RuntimeException('El hero debe mostrar el logo de Yolocal en el lado amarillo.');
    }
    if ($catalogoXpath->query('//*[@id="busqueda"]')->length !== 1
        || $catalogoXpath->query('//*[@id="filtroCategoria"]')->length !== 1
        || $catalogoXpath->query('//*[contains(concat(" ", normalize-space(@class), " "), " negocio-card ")]')->length !== 1) {
        throw new RuntimeException('El rediseño debe conservar filtros y tarjetas del catálogo.');
    }
    $fraseCatalogo = $catalogoXpath->query('//*[@id="seccion-filtros"]/following-sibling::p[contains(concat(" ", normalize-space(@class), " "), " catalogo-frase ") and following-sibling::*[contains(concat(" ", normalize-space(@class), " "), " negocios-container ")]]');
    $fraseAccesible = $catalogoXpath->query('./span[contains(concat(" ", normalize-space(@class), " "), " catalogo-frase-lectores ") and normalize-space(.)="Conoce lo mejor de Texmelucan"]', $fraseCatalogo->item(0));
    if ($fraseCatalogo->length !== 1
        || $fraseAccesible->length !== 1
        || $catalogoXpath->query('.//span[contains(concat(" ", normalize-space(@class), " "), " texmelucan-letra ")]', $fraseCatalogo->item(0))->length !== 10) {
        throw new RuntimeException('La búsqueda debe incluir debajo la frase centrada con Texmelucan preparado para la secuencia de color.');
    }
    if ($catalogoXpath->query('//main[@class="principal"]//button[contains(concat(" ", normalize-space(@class), " "), " btn-conocer-mas ")]')->length !== 1) {
        throw new RuntimeException('Cada tarjeta debe mantener su botón dentro del contenido del negocio.');
    }
    if ($catalogoXpath->query('//*[contains(concat(" ", normalize-space(@class), " "), " negocio-icono-fallback ")]')->length !== 1) {
        throw new RuntimeException('Cada negocio debe tener un estado visual para su icono no disponible.');
    }
    if (!is_file(__DIR__ . '/../vistas/partials/tarjeta_negocio.php')) {
        throw new RuntimeException('La búsqueda debe poder reutilizar el render compartido de tarjetas.');
    }
    $tarjetaAjax = renderizarVista('partials/tarjeta_negocio.php', [
        'negocio' => [
            'ID_Negocio' => 7,
            'nombre_negocio' => 'Café del barrio',
            'nombre_categoria' => 'Cafeterías',
            'Rutaicono' => '../assets/uploads/cafe.webp',
            'DescripcionN' => 'Café local',
            'Direccion' => 'Centro',
            'GoogleMaps' => '',
        ],
    ]);
    $tarjetaAjaxXpath = obtenerDocumento($tarjetaAjax);
    $iconoAjax = $tarjetaAjaxXpath->query('//span[contains(concat(" ", normalize-space(@class), " "), " negocio-icono-marco ")]/img[contains(concat(" ", normalize-space(@class), " "), " negocio-icono ")]');
    if ($iconoAjax->length !== 1
        || $tarjetaAjaxXpath->query('//span[contains(concat(" ", normalize-space(@class), " "), " negocio-icono-marco ")]/span[contains(concat(" ", normalize-space(@class), " "), " negocio-icono-fallback ")]')->length !== 1) {
        throw new RuntimeException('La tarjeta devuelta para la búsqueda debe conservar el marco fijo y el fallback de su icono.');
    }

    $detalle = renderizarVista('detalle_negocio_vista.php', [
        'negocio' => [
            'nombre_negocio' => 'Café del barrio',
            'nombre_categoria' => 'Cafeterías',
            'DescripcionN' => 'Café local',
            'Rutaicono' => '../assets/uploads/iconos/cafe.webp',
            'SitioWeb' => 'https://cafedelbarrio.example',
            'Facebook' => '',
            'Instagram' => '',
            'TikTok' => '',
            'Direccion' => 'Centro',
            'GoogleMaps' => '',
            'Telefono' => '2221234567',
        ],
        'horarios' => [],
        'imagenes' => [['ruta_imagen' => '../assets/uploads/no-disponible.jpg']],
    ]);
    $detalleXpath = obtenerDocumento($detalle);
    if ($detalleXpath->query('//h1[normalize-space()="Café del barrio"]')->length !== 1) {
        throw new RuntimeException('El detalle debe conservar el nombre del negocio como título principal.');
    }
    $logoNegocio = $detalleXpath->query('//*[contains(concat(" ", normalize-space(@class), " "), " detalle-logo ")]/img');
    if ($logoNegocio->length !== 1
        || $logoNegocio->item(0)->getAttribute('src') !== '../assets/uploads/iconos/cafe.webp'
        || $logoNegocio->item(0)->getAttribute('alt') !== 'Logo de Café del barrio') {
        throw new RuntimeException('El encabezado del detalle debe mostrar el logo del negocio con texto alternativo.');
    }
    if ($detalleXpath->query('//*[contains(concat(" ", normalize-space(@class), " "), " imagen-fallback ") and @role="status"]')->length !== 1) {
        throw new RuntimeException('El carrusel debe incluir un estado accesible para imágenes no disponibles.');
    }

    $detalleSinLogo = renderizarVista('detalle_negocio_vista.php', [
        'negocio' => [
            'nombre_negocio' => 'Café del barrio',
            'nombre_categoria' => 'Cafeterías',
            'DescripcionN' => 'Café local',
            'Rutaicono' => '',
            'SitioWeb' => '',
            'Facebook' => '',
            'Instagram' => '',
            'TikTok' => '',
            'Direccion' => '',
            'GoogleMaps' => '',
            'Telefono' => '',
        ],
        'horarios' => [],
        'imagenes' => [],
    ]);
    $detalleSinLogoXpath = obtenerDocumento($detalleSinLogo);
    $logoFallback = $detalleSinLogoXpath->query('//*[contains(concat(" ", normalize-space(@class), " "), " detalle-logo ")]/img');
    if ($logoFallback->length !== 1
        || $logoFallback->item(0)->getAttribute('src') !== '../assets/img/LogoYolocal.png') {
        throw new RuntimeException('El encabezado debe mostrar el logo de Yolocal cuando el negocio no tiene logo.');
    }
    foreach (['Redes Sociales', 'Horarios', 'Contacto y Ubicación'] as $seccion) {
        if ($detalleXpath->query('//h2[normalize-space()="' . $seccion . '"]')->length !== 1) {
            throw new RuntimeException('El detalle debe conservar la sección "' . $seccion . '".');
        }
    }

    $catalogoCss = file_get_contents(__DIR__ . '/../assets/css/negocioL.css');
    $reglasLogoHero = implode("\n", obtenerReglasCss($catalogoCss, '.catalogo-negocios .catalogo-hero-logo'));
    $animacionLogoHero = preg_match(
        '/@keyframes\s+catalogo-logo-fade\s*\{\s*from\s*\{\s*opacity:\s*0;\s*\}\s*to\s*\{\s*opacity:\s*1;\s*\}\s*\}/',
        $catalogoCss
    );
    $logoOcultoEnMovil = preg_match(
        '/@media\s*\(max-width:\s*600px\)\s*\{[\s\S]*?\.catalogo-negocios \.catalogo-hero-logo\s*\{[^}]*display:\s*none/s',
        $catalogoCss
    );
    $logoRespetaMovimientoReducido = preg_match(
        '/@media\s*\(prefers-reduced-motion:\s*reduce\)\s*\{[\s\S]*?\.catalogo-negocios \.catalogo-hero-logo[\s\S]*?\{[^}]*animation:\s*none[^}]*opacity:\s*1/s',
        $catalogoCss
    );
    if (!str_contains($reglasLogoHero, 'position: absolute')
        || !str_contains($reglasLogoHero, 'animation: catalogo-logo-fade')
        || !$animacionLogoHero
        || !$logoOcultoEnMovil
        || !$logoRespetaMovimientoReducido) {
        throw new RuntimeException('El logo del hero debe aparecer con fade, ocultarse en móvil y respetar movimiento reducido.');
    }

    $fraseCatalogoCss = obtenerUltimaReglaCss($catalogoCss, '.catalogo-negocios .catalogo-frase');
    $secuenciaColores = preg_match(
        '/@keyframes\s+texmelucan-secuencia\s*\{[\s\S]*?color:\s*#d69a00[\s\S]*?color:\s*#4c0682/s',
        $catalogoCss
    );
    $secuenciaAccesible = preg_match(
        '/@media\s*\(prefers-reduced-motion:\s*reduce\)\s*\{[^}]*\.catalogo-negocios \.texmelucan-letra[^}]*animation:\s*none/s',
        $catalogoCss
    );
    if (!str_contains($fraseCatalogoCss, 'text-align: center')
        || !str_contains($fraseCatalogoCss, 'font-size: 0.9rem')
        || !str_contains($catalogoCss, 'animation: texmelucan-secuencia 3.6s steps(1, end) infinite')
        || !$secuenciaColores
        || !$secuenciaAccesible) {
        throw new RuntimeException('La frase debe ser pequeña, centrada, animar ambos colores y respetar la reducción de movimiento.');
    }

    $fondoCatalogo = obtenerUltimaReglaCss($catalogoCss, 'body.catalogo-negocios');
    if (!str_contains($fondoCatalogo, 'radial-gradient')
        || !str_contains($fondoCatalogo, 'rgba(255, 202, 5')
        || !str_contains($fondoCatalogo, 'rgba(76, 6, 130')) {
        throw new RuntimeException('El difuminado amarillo y morado debe pertenecer al fondo general del catálogo.');
    }

    $tarjeta = obtenerUltimaReglaCss($catalogoCss, '.catalogo-negocios .negocio-card');
    $tarjetaHover = obtenerUltimaReglaCss($catalogoCss, '.catalogo-negocios .negocio-card:hover');
    if (!preg_match('/background:\s*#fff\b/i', $tarjeta)
        || !preg_match('/border-color:\s*#(?:e8e2eb|ece8ef)\b/i', $tarjeta)
        || preg_match('/border-color:\s*#(?:d99f12|ffca05)\b/i', $tarjetaHover)) {
        throw new RuntimeException('Las tarjetas deben conservar fondo blanco y bordes sin amarillo.');
    }

    $detalleCss = file_get_contents(__DIR__ . '/../assets/css/negocioD.css');
    $reglasTituloDetalle = implode("\n", obtenerReglasCss($detalleCss, '.detalle-negocio-page .detalle-presentacion h1'));
    if (!str_contains($reglasTituloDetalle, 'animation: detalle-title-enter')) {
        throw new RuntimeException('El título del detalle debe tener una animación de entrada.');
    }
    $animacionTitulo = preg_match(
        '/@keyframes\s+detalle-title-enter\s*\{[\s\S]*?from\s*\{[^}]*transform:\s*translateY\([^)]*\)[^}]*\}[\s\S]*?to\s*\{[^}]*transform:\s*translateY\(0\)/',
        $detalleCss
    );
    if (!$animacionTitulo) {
        throw new RuntimeException('El título debe entrar desplazándose suavemente de abajo hacia arriba.');
    }
    $movimientoReducido = preg_match(
        '/@media\s*\(prefers-reduced-motion:\s*reduce\)\s*\{[\s\S]*?\.detalle-negocio-page \.detalle-presentacion h1[\s\S]*?animation:\s*none/s',
        $detalleCss
    );
    if (!$movimientoReducido) {
        throw new RuntimeException('El fade-in del título debe respetar el movimiento reducido.');
    }

    echo "PASS: catálogo y detalle conservan su contenido principal y controles.\n";
} catch (Throwable $error) {
    fwrite(STDERR, 'FAIL: ' . $error->getMessage() . "\n");
    exit(1);
}

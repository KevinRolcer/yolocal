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
    if ($catalogoXpath->query('//*[@id="busqueda"]')->length !== 1
        || $catalogoXpath->query('//*[@id="filtroCategoria"]')->length !== 1
        || $catalogoXpath->query('//*[contains(concat(" ", normalize-space(@class), " "), " negocio-card ")]')->length !== 1) {
        throw new RuntimeException('El rediseño debe conservar filtros y tarjetas del catálogo.');
    }
    if ($catalogoXpath->query('//main[@class="principal"]//button[contains(concat(" ", normalize-space(@class), " "), " btn-conocer-mas ")]')->length !== 1) {
        throw new RuntimeException('Cada tarjeta debe mantener su botón dentro del contenido del negocio.');
    }
    if ($catalogoXpath->query('//*[contains(concat(" ", normalize-space(@class), " "), " negocio-icono-fallback ")]')->length !== 1) {
        throw new RuntimeException('Cada negocio debe tener un estado visual para su icono no disponible.');
    }

    $detalle = renderizarVista('detalle_negocio_vista.php', [
        'negocio' => [
            'nombre_negocio' => 'Café del barrio',
            'nombre_categoria' => 'Cafeterías',
            'DescripcionN' => 'Café local',
            'Rutaicono' => '',
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
    if ($detalleXpath->query('//*[contains(concat(" ", normalize-space(@class), " "), " imagen-fallback ") and @role="status"]')->length !== 1) {
        throw new RuntimeException('El carrusel debe incluir un estado accesible para imágenes no disponibles.');
    }
    foreach (['Redes Sociales', 'Horarios', 'Contacto y Ubicación'] as $seccion) {
        if ($detalleXpath->query('//h2[normalize-space()="' . $seccion . '"]')->length !== 1) {
            throw new RuntimeException('El detalle debe conservar la sección "' . $seccion . '".');
        }
    }

    $catalogoCss = file_get_contents(__DIR__ . '/../assets/css/negocioL.css');
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

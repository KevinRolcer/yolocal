<?php

function renderizarCatalogo($pagina, $totalPaginas)
{
    $pagina_actual = $pagina;
    $total_paginas = $totalPaginas;
    $categorias = [];
    $negocios = [[
        'ID_Negocio' => 1, 'nombre_negocio' => 'Café del barrio',
        'nombre_categoria' => 'Cafeterías', 'Rutaicono' => '',
    ]];
    $_GET = ['categoria' => '3', 'busqueda' => 'café & pan'];
    ob_start();
    include __DIR__ . '/../vistas/negociosL.php';
    return ob_get_clean();
}

if (($argv[1] ?? '') === '--render') {
    echo renderizarCatalogo((int) ($argv[2] ?? 21), (int) ($argv[3] ?? 42));
    exit();
}

try {
    foreach ([[1, 42], [21, 42], [42, 42], [2, 3]] as [$pagina, $total]) {
        $documento = new DOMDocument();
        @$documento->loadHTML(renderizarCatalogo($pagina, $total));
        $xpath = new DOMXPath($documento);
        $navegacion = $xpath->query('//nav[contains(@class, "paginacion")]')->item(0);
        if (!$navegacion) throw new RuntimeException('La paginación debe ser una navegación identificable.');
        $actual = $xpath->query('.//*[@aria-current="page"]', $navegacion);
        if ($actual->length !== 1 || trim($actual->item(0)->textContent) !== (string) $pagina) {
            throw new RuntimeException('Debe identificar la página actual.');
        }
        $enlaces = $xpath->query('.//a', $navegacion);
        if ($enlaces->length > 8) throw new RuntimeException('Debe limitar los enlaces cuando hay muchas páginas.');
        foreach ($enlaces as $enlace) {
            parse_str(parse_url($enlace->getAttribute('href'), PHP_URL_QUERY), $parametros);
            if (($parametros['categoria'] ?? '') !== '3' || ($parametros['busqueda'] ?? '') !== 'café & pan') {
                throw new RuntimeException('Cambiar de página debe conservar los filtros.');
            }
            if ((int) $parametros['pagina'] < 1 || (int) $parametros['pagina'] > $total) {
                throw new RuntimeException('Los enlaces deben apuntar a páginas válidas.');
            }
        }
    }
    if (str_contains(renderizarCatalogo(1, 1), 'aria-label="Páginas de negocios"')) {
        throw new RuntimeException('Una sola página no necesita barra de paginación.');
    }
    echo "PASS: paginación compacta, página actual, límites y conservación de filtros.\n";
} catch (Throwable $error) {
    fwrite(STDERR, 'FAIL: ' . $error->getMessage() . "\n");
    exit(1);
}

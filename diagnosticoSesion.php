<?php
require_once __DIR__ . '/config.php';

ini_set('display_errors', '0');
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, private, max-age=0');
header('X-Robots-Tag: noindex, nofollow');

$nombreCookie = session_name();
$cookieRecibida = isset($_COOKIE[$nombreCookie]);
$cookiesSesion = preg_match_all('/(?:^|;\s*)' . preg_quote($nombreCookie, '/') . '=/', $_SERVER['HTTP_COOKIE'] ?? '');
$errorSesion = false;
set_error_handler(function ($nivel, $mensaje) use (&$errorSesion) {
    $errorSesion = true;
    error_log('[YoLocal diagnóstico de sesión] ' . $mensaje);
    return true;
});
try {
    iniciarSesionYoLocal();
    $sesionActiva = session_status() === PHP_SESSION_ACTIVE;
    $persistencia = $sesionActiva && isset($_SESSION['__yl_prueba_persistencia']);
    $loginPresente = $sesionActiva && ($_SESSION['sistema'] ?? '') === 'YoLocal';
    if ($sesionActiva) {
        $_SESSION['__yl_prueba_persistencia'] = true;
        $escritura = session_write_close();
    } else {
        $escritura = false;
    }
} finally {
    restore_error_handler();
}
$parametros = session_get_cookie_params();
echo json_encode([
    'instruccion' => 'Recarga esta página una vez y comparte el resultado. Después prueba abrirla tras iniciar sesión.',
    'ruta_aplicacion' => RUTA,
    'cookie_recibida' => $cookieRecibida,
    'cookies_sesion_recibidas' => $cookiesSesion,
    'ruta_cookie' => $parametros['path'],
    'cookie_solo_https' => $parametros['secure'],
    'sesion_iniciada' => $sesionActiva,
    'sesion_conservada_entre_peticion' => $persistencia,
    'escritura_reportada_correcta' => $escritura,
    'login_presente' => $loginPresente,
    'errores_de_sesion' => $errorSesion,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

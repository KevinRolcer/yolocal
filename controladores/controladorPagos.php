<?php
if (!function_exists('dbConectar')) {
    require_once __DIR__ . '/../config.php';
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function obtenerDatosPagoUsuario(int $usuarioId): array
{
    $datos = [
        'nombre_dueno' => '',
        'nombre_local' => '',
        'negocios' => [],
        'dia_pago' => date('Y-m-d'),
        'email' => '',
        'referencia_pago' => '',
    ];

    if ($usuarioId <= 0) {
        return $datos;
    }

    $conexion = dbConectar();
    if (!($conexion instanceof mysqli)) {
        return $datos;
    }

    $stmt = $conexion->prepare("SELECT u.Nombre, u.ApellidoP, u.ApellidoM, u.Correo, n.nombre_negocio, n.ID_Negocio FROM usuarios u LEFT JOIN negocios n ON n.ID_Usuario = u.ID_Usuario WHERE u.ID_Usuario = ? ORDER BY n.ID_Negocio ASC");
    if (!$stmt) {
        return $datos;
    }

    $stmt->bind_param('i', $usuarioId);
    $stmt->execute();
    $resultado = $stmt->get_result();

    $negocios = [];
    while ($fila = $resultado->fetch_assoc()) {
        $nombreCompleto = trim(($fila['Nombre'] ?? '') . ' ' . ($fila['ApellidoP'] ?? '') . ' ' . ($fila['ApellidoM'] ?? ''));
        $datos['nombre_dueno'] = $nombreCompleto;
        if (!empty($fila['Correo'])) {
            $datos['email'] = $fila['Correo'];
        }
        if (!empty($fila['nombre_negocio'])) {
            $negocios[] = [
                'nombre_negocio' => $fila['nombre_negocio'],
                'ID_Negocio' => $fila['ID_Negocio'] ?? '',
            ];
            if ($datos['nombre_local'] === '') {
                $datos['nombre_local'] = $fila['nombre_negocio'];
            }
        }
    }

    $datos['negocios'] = $negocios;
    if ($datos['referencia_pago'] === '') {
        $datos['referencia_pago'] = generarReferenciaPago($usuarioId, $datos['dia_pago']);
    }
    $stmt->close();
    return $datos;
}

function obtenerConfigMercadoPago(): array
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }

    // configS.ini (ignorado por git) tiene prioridad sobre config.ini para no
    // exponer credenciales reales en el repositorio.
    $ini = [];
    foreach (['/../configS.ini', '/../config.ini'] as $rel) {
        $rutaIni = __DIR__ . $rel;
        if (!is_file($rutaIni)) {
            continue;
        }
        $parsed = @parse_ini_file($rutaIni, true);
        if (is_array($parsed) && isset($parsed['mercadopago']) && is_array($parsed['mercadopago'])) {
            $ini = $parsed['mercadopago'];
            break;
        }
    }

    // Token: primero los .ini, si no la variable de entorno MP_ACCESS_TOKEN.
    $token = trim((string)($ini['access_token'] ?? ''));
    if ($token === '') {
        $token = trim((string)(getenv('MP_ACCESS_TOKEN') ?: ''));
    }

    $baseUrl = trim((string)($ini['base_url'] ?? ''));
    if ($baseUrl === '') {
        $baseUrl = 'https://api.mercadopago.com';
    }

    $publicBaseUrl = rtrim(trim((string)($ini['public_base_url'] ?? '')), '/');

    $notificationUrl = trim((string)($ini['notification_url'] ?? ''));
    if ($notificationUrl === '' && $publicBaseUrl !== '') {
        $notificationUrl = $publicBaseUrl . '/controladores/mp_webhook.php';
    }

    $cache = [
        'access_token' => $token,
        'public_key' => trim((string)($ini['public_key'] ?? '')),
        'base_url' => $baseUrl,
        'public_base_url' => $publicBaseUrl,
        'integrator_id' => trim((string)($ini['integrator_id'] ?? '')),
        'notification_url' => $notificationUrl,
        'es_sandbox' => stripos($token, 'TEST-') === 0,
    ];

    return $cache;
}

function mercadoPagoApiDisponible(): bool
{
    $config = obtenerConfigMercadoPago();
    $token = trim((string)($config['access_token'] ?? ''));
    return $token !== '';
}

function obtenerLinkPagoNormalManual(): string
{
    return 'https://mpago.la/1MLH1oE';
}

function obtenerLinkAliadosImpulsoManual(): string
{
    return 'https://mpago.la/1QW4dBw';
}

function generarReferenciaPago(int $usuarioId, string $diaPago = ''): string
{
    $fecha = $diaPago !== '' ? $diaPago : date('Ymd');
    return 'YP-' . $usuarioId . '-' . str_replace('-', '', $fecha);
}

function obtenerUrlRetornoPago(string $ruta = 'index.php?pag=home'): string
{
    // 1) URL publica configurada explicitamente (tunel local o dominio real).
    $configMp = obtenerConfigMercadoPago();
    $publicBaseUrl = trim((string)($configMp['public_base_url'] ?? ''));
    if ($publicBaseUrl !== '') {
        return rtrim($publicBaseUrl, '/') . '/' . ltrim($ruta, '/');
    }

    $baseUrl = $GLOBALS['baseUrl'] ?? '';
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

    // 2) Autodeteccion a partir de la peticion actual (incluye subcarpeta si aplica).
    if ($baseUrl === '' && defined('RUTA')) {
        $rutaApp = trim((string) RUTA, '/');
        $baseUrl = $rutaApp === '' ? '' : '/' . $rutaApp;
    }

    if ($baseUrl !== '' && (strpos($baseUrl, 'http://') === 0 || strpos($baseUrl, 'https://') === 0)) {
        return rtrim($baseUrl, '/') . '/' . ltrim($ruta, '/');
    }

    if ($baseUrl !== '') {
        $baseLimpia = '/' . ltrim($baseUrl, '/');
        $baseConRuta = rtrim($baseLimpia, '/') . '/' . ltrim($ruta, '/');
        return $scheme . '://' . $host . $baseConRuta;
    }

    return $scheme . '://' . $host . '/' . ltrim($ruta, '/');
}

function guardarPago(array $datos): bool
{
    $conexion = dbConectar();
    if (!($conexion instanceof mysqli)) {
        return false;
    }

    $stmt = $conexion->prepare("INSERT INTO pagos_yolocal (nombre_dueno, nombre_local, referencia_pago, dia_pago, monto, email, estado, ID_Usuario, ID_Negocio, mp_preference_id, mp_init_point, fecha_creacion) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    if (!$stmt) {
        return false;
    }

    $idUsuario = isset($datos['ID_Usuario']) ? (int)$datos['ID_Usuario'] : null;
    $idNegocio = isset($datos['ID_Negocio']) ? (int)$datos['ID_Negocio'] : null;

    $stmt->bind_param('ssssdssiiss', $datos['nombre_dueno'], $datos['nombre_local'], $datos['referencia_pago'], $datos['dia_pago'], $datos['monto'], $datos['email'], $datos['estado'], $idUsuario, $idNegocio, $datos['mp_preference_id'], $datos['mp_init_point']);
    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}

function mercadoPagoRequest(string $metodo, string $endpoint, array $payload = [], array $headersExtra = []): array
{
    $config = obtenerConfigMercadoPago();
    $token = trim((string)($config['access_token'] ?? ''));
    $baseUrl = rtrim((string)($config['base_url'] ?? 'https://api.mercadopago.com'), '/');

    if ($token === '') {
        return [
            'ok' => false,
            'status' => 0,
            'mensaje' => 'Mercado Pago sin access token.',
            'data' => null,
        ];
    }

    if (!function_exists('curl_init')) {
        return [
            'ok' => false,
            'status' => 0,
            'mensaje' => 'La extension cURL no esta habilitada en PHP.',
            'data' => null,
        ];
    }

    $url = $baseUrl . '/' . ltrim($endpoint, '/');
    $curl = curl_init($url);

    $headers = [
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json',
        'Accept: application/json',
    ];

    $integratorId = trim((string)($config['integrator_id'] ?? ''));
    if ($integratorId !== '') {
        $headers[] = 'x-integrator-id: ' . $integratorId;
    }

    foreach ($headersExtra as $headerLinea) {
        $headerLinea = trim((string)$headerLinea);
        if ($headerLinea !== '') {
            $headers[] = $headerLinea;
        }
    }

    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => strtoupper($metodo),
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_TIMEOUT => 25,
    ]);

    if (!empty($payload)) {
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload, JSON_UNESCAPED_UNICODE));
    }

    $respuesta = curl_exec($curl);
    $errorCurl = curl_error($curl);
    $statusHttp = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    if ($respuesta === false) {
        return [
            'ok' => false,
            'status' => 0,
            'mensaje' => 'Error de conexion con Mercado Pago: ' . $errorCurl,
            'data' => null,
        ];
    }

    $json = json_decode($respuesta, true);
    $ok = $statusHttp >= 200 && $statusHttp < 300;

    return [
        'ok' => $ok,
        'status' => $statusHttp,
        'mensaje' => $ok ? 'OK' : 'Mercado Pago respondio con estado ' . $statusHttp,
        'data' => is_array($json) ? $json : null,
        'raw' => $respuesta,
    ];
}

function obtenerPaymentIdRetornoMercadoPago(array $query): string
{
    $candidatos = [
        $query['payment_id'] ?? '',
        $query['collection_id'] ?? '',
        $query['mp_payment_id'] ?? '',
    ];

    foreach ($candidatos as $valor) {
        $valor = trim((string)$valor);
        if ($valor !== '') {
            return $valor;
        }
    }

    return '';
}

function obtenerPagoMercadoPago(string $paymentId): array
{
    $paymentId = trim($paymentId);
    if ($paymentId === '') {
        return [
            'ok' => false,
            'status' => 0,
            'mensaje' => 'payment_id vacio.',
            'data' => null,
        ];
    }

    return mercadoPagoRequest('GET', 'v1/payments/' . rawurlencode($paymentId));
}

function crearPreferenciaMercadoPago(float $monto, string $titulo, string $descripcion, string $email, array $opciones = []): array
{
    if (!mercadoPagoApiDisponible()) {
        return [
            'ok' => false,
            'mensaje' => 'Mercado Pago no esta configurado en este ambiente.',
        ];
    }

    $externalReference = trim((string)($opciones['external_reference'] ?? ''));
    $urlSuccess = trim((string)($opciones['url_success'] ?? ''));
    $urlPending = trim((string)($opciones['url_pending'] ?? $urlSuccess));
    $urlFailure = trim((string)($opciones['url_failure'] ?? $urlSuccess));
    $notificationUrl = trim((string)($opciones['notification_url'] ?? ''));

    if ($urlSuccess === '') {
        $urlSuccess = obtenerUrlRetornoPago('index.php?pag=home');
    }
    if ($urlPending === '') {
        $urlPending = $urlSuccess;
    }
    if ($urlFailure === '') {
        $urlFailure = $urlSuccess;
    }

    $item = [
        'title' => $titulo,
        'description' => $descripcion,
        'quantity' => 1,
        'currency_id' => 'MXN',
        'unit_price' => round($monto, 2),
    ];

    $payload = [
        'items' => [$item],
        'payer' => [
            'email' => $email,
        ],
        'back_urls' => [
            'success' => $urlSuccess,
            'pending' => $urlPending,
            'failure' => $urlFailure,
        ],
        'auto_return' => 'approved',
    ];

    if ($externalReference !== '') {
        $payload['external_reference'] = $externalReference;
    }

    if ($notificationUrl !== '') {
        $payload['notification_url'] = $notificationUrl;
    } else {
        $config = obtenerConfigMercadoPago();
        $notiConfig = trim((string)($config['notification_url'] ?? ''));
        if ($notiConfig !== '') {
            $payload['notification_url'] = $notiConfig;
        }
    }

    $respuesta = mercadoPagoRequest('POST', 'checkout/preferences', $payload);
    if (!$respuesta['ok']) {
        return [
            'ok' => false,
            'mensaje' => $respuesta['mensaje'] ?? 'No se pudo crear la preferencia.',
            'error' => $respuesta['data'] ?? null,
        ];
    }

    $data = is_array($respuesta['data'] ?? null) ? $respuesta['data'] : [];
    $initPoint = (string)($data['init_point'] ?? '');
    $prefId = (string)($data['id'] ?? '');

    if ($initPoint === '' || $prefId === '') {
        return [
            'ok' => false,
            'mensaje' => 'Mercado Pago no devolvio init_point o id de preferencia.',
            'error' => $data,
        ];
    }

    return [
        'ok' => true,
        'id' => $prefId,
        'init_point' => $initPoint,
        'sandbox_init_point' => (string)($data['sandbox_init_point'] ?? ''),
        'external_reference' => (string)($data['external_reference'] ?? $externalReference),
        'raw' => $data,
    ];
}

function inicializarTablaPagos(): void
{
    $conexion = dbConectar();
    if (!($conexion instanceof mysqli)) {
        return;
    }

    $sql = "CREATE TABLE IF NOT EXISTS pagos_yolocal (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre_dueno VARCHAR(100) NOT NULL,
        nombre_local VARCHAR(150) NOT NULL,
        referencia_pago VARCHAR(100) NOT NULL,
        dia_pago DATE NOT NULL,
        monto DECIMAL(10,2) NOT NULL DEFAULT 199.00,
        email VARCHAR(150) DEFAULT NULL,
        estado VARCHAR(20) NOT NULL DEFAULT 'pendiente',
        ID_Usuario INT DEFAULT NULL,
        ID_Negocio INT DEFAULT NULL,
        mp_preference_id VARCHAR(100) DEFAULT NULL,
        mp_init_point TEXT,
        fecha_creacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        fecha_actualizacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

    $conexion->query($sql);

    $columnsToCheck = [
        'ID_Usuario' => 'INT DEFAULT NULL',
        'ID_Negocio' => 'INT DEFAULT NULL',
    ];

    foreach ($columnsToCheck as $columnName => $columnDefinition) {
        $escapedColumn = $conexion->real_escape_string($columnName);
        $result = $conexion->query("SHOW COLUMNS FROM pagos_yolocal LIKE '$escapedColumn'");
        if ($result && $result->num_rows === 0) {
            $conexion->query("ALTER TABLE pagos_yolocal ADD COLUMN `$columnName` $columnDefinition");
        }
    }
}


function agruparPagosAdministracion(): array
{
    inicializarTablaPagos();

    $resultado = [
        'al_corriente' => [],
        'pendientes' => [],
        'con_atraso' => [],
        'sin_pagos' => [],
        'error' => '',
    ];

    $conexion = dbConectar();
    if (!($conexion instanceof mysqli)) {
        $resultado['error'] = 'No se pudo conectar a la base de datos.';
        return $resultado;
    }

    $hoy = date('Y-m-d');

    // Pagos al corriente: solo pagos marcados como pagado.
    $stmtCorriente = $conexion->prepare(
        "SELECT p.*
         FROM pagos_yolocal p
         WHERE LOWER(TRIM(COALESCE(p.estado, 'pendiente'))) = 'pagado'
         ORDER BY p.dia_pago ASC, p.id DESC"
    );

    if ($stmtCorriente) {
        if ($stmtCorriente->execute()) {
            $resCorriente = $stmtCorriente->get_result();
            while ($fila = $resCorriente->fetch_assoc()) {
                $resultado['al_corriente'][] = $fila;
            }
        }
        $stmtCorriente->close();
    }

    // Pagos pendientes: pendientes con fecha vigente o sin fecha de pago.
    $stmtPendientes = $conexion->prepare(
        "SELECT p.*
         FROM pagos_yolocal p
         WHERE LOWER(TRIM(COALESCE(p.estado, 'pendiente'))) = 'pendiente'
           AND (p.dia_pago IS NULL OR p.dia_pago >= ?)
         ORDER BY p.dia_pago ASC, p.id DESC"
    );

    if ($stmtPendientes) {
        $stmtPendientes->bind_param('s', $hoy);
        if ($stmtPendientes->execute()) {
            $resPendientes = $stmtPendientes->get_result();
            while ($fila = $resPendientes->fetch_assoc()) {
                $resultado['pendientes'][] = $fila;
            }
        }
        $stmtPendientes->close();
    }

    // Pagos con atraso: pendientes con fecha vencida.
    $stmtAtraso = $conexion->prepare(
        "SELECT p.*
         FROM pagos_yolocal p
         WHERE LOWER(TRIM(COALESCE(p.estado, 'pendiente'))) = 'pendiente'
           AND p.dia_pago < ?
         ORDER BY p.dia_pago ASC, p.id DESC"
    );

    if ($stmtAtraso) {
        $stmtAtraso->bind_param('s', $hoy);
        if ($stmtAtraso->execute()) {
            $resAtraso = $stmtAtraso->get_result();
            while ($fila = $resAtraso->fetch_assoc()) {
                $resultado['con_atraso'][] = $fila;
            }
        }
        $stmtAtraso->close();
    }

    // Negocios sin pagos: negocios que no tienen registro en pagos_yolocal.
    $sqlSinPagos = "
        SELECT
            n.ID_Negocio,
            n.nombre_negocio,
            u.Nombre,
            u.ApellidoP,
            u.ApellidoM
        FROM negocios n
        INNER JOIN usuarios u ON u.ID_Usuario = n.ID_Usuario
        WHERE NOT EXISTS (
            SELECT 1
            FROM pagos_yolocal p
            WHERE p.ID_Negocio = n.ID_Negocio
        )
        ORDER BY n.nombre_negocio ASC
    ";

    $resSinPagos = $conexion->query($sqlSinPagos);
    if ($resSinPagos instanceof mysqli_result) {
        while ($fila = $resSinPagos->fetch_assoc()) {
            $resultado['sin_pagos'][] = $fila;
        }
    }

    if (
        empty($resultado['al_corriente'])
        && empty($resultado['pendientes'])
        && empty($resultado['con_atraso'])
        && empty($resultado['sin_pagos'])
        && $conexion->error
    ) {
        $resultado['error'] = 'No se pudieron cargar los datos de pagos: ' . $conexion->error;
    }

    return $resultado;
}

function cambiarEstadoPago(int $id, string $estado): bool
{
    $conexion = dbConectar();
    if (!($conexion instanceof mysqli)) {
        return false;
    }

    $stmt = $conexion->prepare("UPDATE pagos_yolocal SET estado = ? WHERE id = ?");
    if (!$stmt) {
        return false;
    }

    $stmt->bind_param('si', $estado, $id);
    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}

/**
 * Devuelve un registro completo de pagos_yolocal por su id (para el recibo).
 * @return array<string,mixed>|null
 */
function obtenerPagoPorId(int $id): ?array
{
    if ($id <= 0) {
        return null;
    }

    $conexion = dbConectar();
    if (!($conexion instanceof mysqli)) {
        return null;
    }

    $stmt = $conexion->prepare("SELECT * FROM pagos_yolocal WHERE id = ? LIMIT 1");
    if (!$stmt) {
        return null;
    }

    $stmt->bind_param('i', $id);
    $stmt->execute();
    $fila = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    return $fila ?: null;
}

function pagoEstaVigentePorFecha(array $pago): bool
{
    $fechaBase = trim((string)($pago['dia_pago'] ?? ''));
    if ($fechaBase === '') {
        $fechaBase = substr((string)($pago['fecha_creacion'] ?? ''), 0, 10);
    }

    if ($fechaBase === '') {
        return false;
    }

    try {
        $fechaPago = new DateTime($fechaBase);
        $hoy = new DateTime('today');
        $dias = (int)$fechaPago->diff($hoy)->days;

        if ($fechaPago > $hoy) {
            return true;
        }

        return $dias <= 30;
    } catch (Exception $e) {
        return false;
    }
}

function obtenerUltimoPagoPagadoPorTipo(int $usuarioId, string $tipoPago): ?array
{
    if ($usuarioId <= 0) {
        return null;
    }

    inicializarTablaPagos();
    $conexion = dbConectar();
    if (!($conexion instanceof mysqli)) {
        return null;
    }

    $sql = "SELECT referencia_pago, dia_pago, fecha_creacion
            FROM pagos_yolocal
            WHERE ID_Usuario = ?
              AND LOWER(TRIM(COALESCE(estado, ''))) = 'pagado'";

    if ($tipoPago === 'normal') {
        $sql .= " AND (referencia_pago IS NULL OR referencia_pago = '' OR referencia_pago LIKE '%-PN' OR referencia_pago NOT LIKE '%-AI')";
    } elseif ($tipoPago === 'aportacion') {
        $sql .= " AND referencia_pago LIKE '%-AI'";
    }

    $sql .= " ORDER BY id DESC LIMIT 1";

    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        return null;
    }

    $stmt->bind_param('i', $usuarioId);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $pago = $resultado ? $resultado->fetch_assoc() : null;
    $stmt->close();

    return is_array($pago) ? $pago : null;
}

function usuarioTienePagoNormalActivo(int $usuarioId): bool
{
    $pago = obtenerUltimoPagoPagadoPorTipo($usuarioId, 'normal');
    if (!$pago) {
        return false;
    }

    return pagoEstaVigentePorFecha($pago);
}

function usuarioTieneAportacionActiva(int $usuarioId): bool
{
    $pago = obtenerUltimoPagoPagadoPorTipo($usuarioId, 'aportacion');
    if (!$pago) {
        return false;
    }

    return pagoEstaVigentePorFecha($pago);
}

function usuarioTienePagoActivo(int $usuarioId): bool
{
    return usuarioTienePagoNormalActivo($usuarioId);
}

function normalizarEstadoPagoRetorno(string $estadoRetorno): string
{
    $estadoNormalizado = strtolower(trim($estadoRetorno));
    if ($estadoNormalizado === 'approved' || $estadoNormalizado === 'accredited' || $estadoNormalizado === 'success') {
        return 'pagado';
    }

    if ($estadoNormalizado === 'pending' || $estadoNormalizado === 'in_process' || $estadoNormalizado === 'inreview') {
        return 'pendiente';
    }

    return 'rechazado';
}

function actualizarPagoPorRetornoMercadoPago(int $usuarioId, string $estadoRetorno, string $referenciaPago = '', array $opciones = []): bool
{
    // Si no hay API, no hay forma segura de confirmar automaticamente.
    if (!mercadoPagoApiDisponible()) {
        return false;
    }

    $estadoFinal = normalizarEstadoPagoRetorno($estadoRetorno);
    if ($estadoFinal !== 'pagado') {
        return false;
    }

    $filtroInitPoint = trim((string)($opciones['filtro_init_point'] ?? ''));
    $paymentId = trim((string)($opciones['payment_id'] ?? ''));
    $maxMinutosRetorno = (int)($opciones['max_minutos_retorno'] ?? 180);
    if ($maxMinutosRetorno <= 0) {
        $maxMinutosRetorno = 180;
    }

    // Validacion real del pago en API de Mercado Pago.
    if ($paymentId === '') {
        return false;
    }

    $pagoApi = obtenerPagoMercadoPago($paymentId);
    if (!$pagoApi['ok']) {
        return false;
    }

    $pagoData = is_array($pagoApi['data'] ?? null) ? $pagoApi['data'] : [];
    $estadoApi = strtolower(trim((string)($pagoData['status'] ?? '')));
    if ($estadoApi !== 'approved') {
        return false;
    }

    $referenciaApi = trim((string)($pagoData['external_reference'] ?? ''));
    if ($referenciaPago !== '' && $referenciaApi !== '' && $referenciaApi !== $referenciaPago) {
        return false;
    }

    if ($referenciaPago === '' && $referenciaApi !== '') {
        $referenciaPago = $referenciaApi;
    }

    $fechaMinima = new DateTime('now');
    $fechaMinima->modify('-' . $maxMinutosRetorno . ' minutes');
    $fechaMinimaSql = $fechaMinima->format('Y-m-d H:i:s');

    $conexion = dbConectar();
    if (!($conexion instanceof mysqli)) {
        return false;
    }

    $referenciaPago = trim($referenciaPago);

    $sqlBase = "SELECT id
                FROM pagos_yolocal
                WHERE estado = 'pendiente'
                  AND ID_Usuario = ?
                  AND fecha_creacion >= ?";

    if ($referenciaPago !== '' && $filtroInitPoint !== '') {
        $stmt = $conexion->prepare($sqlBase . " AND referencia_pago = ? AND mp_init_point = ? ORDER BY id DESC LIMIT 1");
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param('isss', $usuarioId, $fechaMinimaSql, $referenciaPago, $filtroInitPoint);
    } elseif ($referenciaPago !== '') {
        $stmt = $conexion->prepare($sqlBase . " AND referencia_pago = ? ORDER BY id DESC LIMIT 1");
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param('iss', $usuarioId, $fechaMinimaSql, $referenciaPago);
    } elseif ($filtroInitPoint !== '') {
        $stmt = $conexion->prepare($sqlBase . " AND mp_init_point = ? ORDER BY id DESC LIMIT 1");
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param('iss', $usuarioId, $fechaMinimaSql, $filtroInitPoint);
    } else {
        $stmt = $conexion->prepare($sqlBase . " ORDER BY id DESC LIMIT 1");
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param('is', $usuarioId, $fechaMinimaSql);
    }

    $stmt->execute();
    $resultado = $stmt->get_result();
    $pago = $resultado->fetch_assoc();
    $stmt->close();

    if (!$pago) {
        return false;
    }

    $pagoId = (int)($pago['id'] ?? 0);
    if ($pagoId <= 0) {
        return false;
    }

    $stmtUpd = $conexion->prepare("UPDATE pagos_yolocal SET estado = ? WHERE id = ?");
    if (!$stmtUpd) {
        return false;
    }

    $stmtUpd->bind_param('si', $estadoFinal, $pagoId);
    $ok = $stmtUpd->execute();
    $stmtUpd->close();
    return $ok;
}

/**
 * Confirma un pago consultando su estado real en la API de Mercado Pago.
 * Pensado para el webhook (no depende de la sesion). Busca la fila pendiente
 * por external_reference (= referencia_pago) y la marca como 'pagado'.
 *
 * @return array{ok:bool, mensaje:string, id_pago:int, estado_mp:string}
 */
function confirmarPagoMercadoPagoPorPaymentId(string $paymentId): array
{
    $resultado = ['ok' => false, 'mensaje' => '', 'id_pago' => 0, 'estado_mp' => ''];

    $paymentId = trim($paymentId);
    if ($paymentId === '') {
        $resultado['mensaje'] = 'payment_id vacio';
        return $resultado;
    }

    if (!mercadoPagoApiDisponible()) {
        $resultado['mensaje'] = 'Mercado Pago sin access token';
        return $resultado;
    }

    $pagoApi = obtenerPagoMercadoPago($paymentId);
    if (!$pagoApi['ok']) {
        $resultado['mensaje'] = $pagoApi['mensaje'] ?? 'No se pudo consultar el pago en Mercado Pago';
        return $resultado;
    }

    $pagoData = is_array($pagoApi['data'] ?? null) ? $pagoApi['data'] : [];
    $estadoApi = strtolower(trim((string)($pagoData['status'] ?? '')));
    $resultado['estado_mp'] = $estadoApi;

    $referenciaApi = trim((string)($pagoData['external_reference'] ?? ''));
    $mpPaymentId = trim((string)($pagoData['id'] ?? $paymentId));

    $conexion = dbConectar();
    if (!($conexion instanceof mysqli)) {
        $resultado['mensaje'] = 'Sin conexion a la base de datos';
        return $resultado;
    }

    // Localizar el registro local por referencia.
    $pago = null;
    if ($referenciaApi !== '') {
        $stmt = $conexion->prepare(
            "SELECT id, estado FROM pagos_yolocal WHERE referencia_pago = ? ORDER BY id DESC LIMIT 1"
        );
        if ($stmt) {
            $stmt->bind_param('s', $referenciaApi);
            $stmt->execute();
            $pago = $stmt->get_result()->fetch_assoc();
            $stmt->close();
        }
    }

    if (!$pago) {
        $resultado['mensaje'] = 'No se encontro un pago local para la referencia ' . $referenciaApi;
        return $resultado;
    }

    $pagoId = (int)$pago['id'];
    $resultado['id_pago'] = $pagoId;

    $estadoFinal = normalizarEstadoPagoRetorno($estadoApi);

    // Guardar el id de pago de MP para trazabilidad (columna opcional).
    $tieneColMpPayment = false;
    $colCheck = $conexion->query("SHOW COLUMNS FROM pagos_yolocal LIKE 'mp_payment_id'");
    if ($colCheck instanceof mysqli_result) {
        $tieneColMpPayment = $colCheck->num_rows > 0;
    }
    if (!$tieneColMpPayment) {
        $conexion->query("ALTER TABLE pagos_yolocal ADD COLUMN mp_payment_id VARCHAR(50) DEFAULT NULL");
    }

    $stmtUpd = $conexion->prepare("UPDATE pagos_yolocal SET estado = ?, mp_payment_id = ? WHERE id = ?");
    if (!$stmtUpd) {
        $resultado['mensaje'] = 'No se pudo preparar la actualizacion';
        return $resultado;
    }
    $stmtUpd->bind_param('ssi', $estadoFinal, $mpPaymentId, $pagoId);
    $ok = $stmtUpd->execute();
    $stmtUpd->close();

    $resultado['ok'] = $ok;
    $resultado['mensaje'] = $ok
        ? 'Pago ' . $pagoId . ' actualizado a "' . $estadoFinal . '"'
        : 'Fallo al actualizar el pago ' . $pagoId;
    return $resultado;
}

/**
 * Revisa todos los usuarios con pagos y envía notificaciones automáticas
 * cuando su aportación vence en exactamente 7 días o en 1 día.
 * Se ejecuta una sola vez por día por usuario (evita duplicados).
 */
function verificarVencimientoAportaciones(): void
{
    if (!function_exists('dbConectar')) {
        return;
    }
    if (!function_exists('crearNotificacion')) {
        require_once __DIR__ . '/controladorNotificaciones.php';
    }

    $conexion = dbConectar();
    if (!($conexion instanceof mysqli)) {
        return;
    }

    // Obtener el último pago 'pagado' por usuario
    $sql = "
        SELECT ID_Usuario, MAX(dia_pago) AS ultimo_pago
        FROM pagos_yolocal
        WHERE estado = 'pagado'
          AND ID_Usuario IS NOT NULL
        GROUP BY ID_Usuario
    ";

    $resultado = $conexion->query($sql);
    if (!($resultado instanceof mysqli_result)) {
        return;
    }

    $hoy = new DateTime('today');

    while ($fila = $resultado->fetch_assoc()) {
        $usuarioId  = (int)$fila['ID_Usuario'];
        $ultimoPago = new DateTime($fila['ultimo_pago']);
        $vencimiento = (clone $ultimoPago)->modify('+30 days');
        $diasRestantes = (int)$hoy->diff($vencimiento)->days;

        // diff puede ser negativo si ya venció — ignorar vencidos
        if ($vencimiento < $hoy) {
            continue;
        }

        if ($diasRestantes === 7) {
            $titulo  = 'Tu aportación vence en 1 semana';
            $mensaje = '¡Que no se te pase! En una semana toca renovar tu aportación simbólica. Sigamos haciendo crecer lo local 💜';
        } elseif ($diasRestantes === 1) {
            $titulo  = 'Tu aportación vence mañana';
            $mensaje = '¡Mañana vence tu aportación simbólica! Renueva a tiempo para seguir disfrutando de todos los beneficios de Yo Local 💛';
        } else {
            continue;
        }

        // Evitar duplicados: no enviar si ya existe hoy una notificación con el mismo título
        $stmtCheck = $conexion->prepare(
            "SELECT COUNT(*) AS total
             FROM notificaciones
             WHERE usuario_id = ?
               AND titulo = ?
               AND DATE(fecha_creacion) = CURDATE()"
        );
        if (!$stmtCheck) {
            continue;
        }
        $stmtCheck->bind_param('is', $usuarioId, $titulo);
        $stmtCheck->execute();
        $resCheck = $stmtCheck->get_result()->fetch_assoc();
        $stmtCheck->close();

        if ((int)($resCheck['total'] ?? 0) > 0) {
            continue; // ya fue enviada hoy
        }

        crearNotificacion(
            $usuarioId,
            $titulo,
            $mensaje,
            'advertencia',
            'index.php?pag=aportacion'
        );
    }
}

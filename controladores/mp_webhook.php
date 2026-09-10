<?php
/**
 * Webhook / IPN de Mercado Pago.
 *
 * Mercado Pago llama a esta URL cuando cambia el estado de un pago.
 * Configura esta direccion en config.ini (notification_url) o en el panel de MP:
 *   https://TU-DOMINIO/controladores/mp_webhook.php
 *
 * Responde siempre 200 lo antes posible; MP reintenta si recibe otro codigo.
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/controladorPagos.php';

// --- Registro simple para depuracion (assets/uploads/mp_webhook.log) ---
function mpWebhookLog(string $linea): void
{
    $dir = __DIR__ . '/../assets/uploads';
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
    @file_put_contents(
        $dir . '/mp_webhook.log',
        '[' . date('Y-m-d H:i:s') . '] ' . $linea . PHP_EOL,
        FILE_APPEND
    );
}

$rawBody = file_get_contents('php://input') ?: '';
$body = json_decode($rawBody, true);
if (!is_array($body)) {
    $body = [];
}

// El id del pago puede venir de varias formas segun la version de la notificacion.
$tipo = strtolower(trim((string)(
    $_GET['type']
    ?? $_GET['topic']
    ?? $body['type']
    ?? ($body['action'] ?? '')
)));

// PHP convierte "data.id" de la query en "data_id". Contemplamos ambos y la query cruda.
$paymentId = trim((string)(
    $_GET['data_id']
    ?? $_GET['data.id']
    ?? $_GET['id']
    ?? ($body['data']['id'] ?? '')
    ?? ($body['resource'] ?? '')
    ?? ''
));

if ($paymentId === '' && !empty($_SERVER['QUERY_STRING'])) {
    parse_str(str_replace('data.id', 'data_id', $_SERVER['QUERY_STRING']), $qsCruda);
    $paymentId = trim((string)($qsCruda['data_id'] ?? $qsCruda['id'] ?? ''));
}

// A veces "resource" es una URL .../payments/123 -> quedarnos con el numero final.
if ($paymentId !== '' && strpos($paymentId, '/') !== false) {
    $paymentId = trim((string)substr($paymentId, strrpos($paymentId, '/') + 1));
}

mpWebhookLog('IN tipo=' . $tipo . ' payment_id=' . $paymentId . ' query=' . json_encode($_GET) . ' body=' . $rawBody);

// Solo nos interesan las notificaciones de pagos.
$esPago = $tipo === 'payment'
    || strpos($tipo, 'payment') !== false;

if (!$esPago || $paymentId === '') {
    http_response_code(200);
    echo json_encode(['ok' => true, 'ignored' => true, 'motivo' => 'no es notificacion de pago o sin id']);
    exit;
}

$resultado = confirmarPagoMercadoPagoPorPaymentId($paymentId);
mpWebhookLog('OUT ' . json_encode($resultado));

// Aunque no encontremos el pago local, respondemos 200 para que MP no reintente
// indefinidamente. El detalle queda en el log.
http_response_code(200);
header('Content-Type: application/json');
echo json_encode([
    'ok' => (bool)($resultado['ok'] ?? false),
    'mensaje' => $resultado['mensaje'] ?? '',
    'estado_mp' => $resultado['estado_mp'] ?? '',
]);

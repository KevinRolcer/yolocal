<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../controladores/controladorPagos.php';
require_once __DIR__ . '/../../controladores/controladorNotificaciones.php';
require_once __DIR__ . '/../../lib/PHPMailer-master/src/Exception.php';
require_once __DIR__ . '/../../lib/PHPMailer-master/src/PHPMailer.php';
require_once __DIR__ . '/../../lib/PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$baseUrl = $baseUrl ?? '';
$estadoRaw = $_GET['estado'] ?? $_GET['status'] ?? $_GET['collection_status'] ?? 'approved';
$estadoNormalizado = strtolower(trim((string)$estadoRaw));
$usuarioId = (int)($_SESSION['ID_Usuario'] ?? $_SESSION['id'] ?? 0);
$referenciaRetorno = trim((string)($_GET['external_reference'] ?? $_GET['reference'] ?? ''));
$paymentIdRetorno = obtenerPaymentIdRetornoMercadoPago($_GET);
$retornoAprobado = $estadoNormalizado === 'approved' || $estadoNormalizado === 'accredited' || $estadoNormalizado === 'success';
$actualizacionOk = false;

if ($retornoAprobado && $usuarioId > 0) {
    $actualizacionOk = actualizarPagoPorRetornoMercadoPago(
        $usuarioId,
        $estadoNormalizado,
        $referenciaRetorno,
        [
            'payment_id' => $paymentIdRetorno,
            'max_minutos_retorno' => 1440,
        ]
    );
}

if ($retornoAprobado && !$actualizacionOk) {
    $estado = 'pending';
    $titulo = 'Pago en validacion';
    $mensajePag = 'Recibimos tu regreso desde Mercado Pago. Como este flujo usa enlace manual, tu pago queda en revision hasta su validacion.';
} elseif ($retornoAprobado) {
    $estado = 'approved';
    $titulo = 'Pago confirmado';
    $mensajePag = '¡Felicitaciones! Tu pago fue completado con éxito. Tu negocio sigue disfrutando de todos los beneficios de YoLocal.';
} elseif ($estadoNormalizado === 'pending' || $estadoNormalizado === 'in_process' || $estadoNormalizado === 'inreview') {
    $estado = 'pending';
    $titulo = 'Pago pendiente';
    $mensajePag = 'Tu pago quedó pendiente de confirmación. Te notificaremos cuando se acredite.';
} else {
    $estado = 'rejected';
    $titulo = 'Pago no completado';
    $mensajePag = 'No se pudo completar el pago. Puedes intentarlo nuevamente desde la sección de pagos.';
}

// Marcar el último pago pendiente como pagado y enviar comprobante
if ($estado === 'approved' && $actualizacionOk) {
    if ($usuarioId > 0) {
        $datosDueno = obtenerDatosPagoUsuario($usuarioId);
        $conexion = dbConectar();

        if ($conexion instanceof mysqli) {
            // Obtener el último pago pendiente para tomar el email y datos
            $stmtGet = $conexion->prepare(
                "SELECT * FROM pagos_yolocal WHERE ID_Usuario = ? AND estado = 'pagado' ORDER BY id DESC LIMIT 1"
            );
            $pagoRegistro = null;
            $pagoId = null;
            if ($stmtGet) {
                $stmtGet->bind_param('i', $usuarioId);
                $stmtGet->execute();
                $res = $stmtGet->get_result();
                $pagoRegistro = $res->fetch_assoc();
                if ($pagoRegistro) {
                    $pagoId = (int)($pagoRegistro['id'] ?? 0);
                }
                $stmtGet->close();
            }

            if ($pagoId > 0) {
                // Crear notificación de pago exitoso
                crearNotificacion(
                    $usuarioId,
                    'Pago Completado',
                    'Tu pago de $199.00 MXN ha sido procesado exitosamente. Tu negocio "' . ($pagoRegistro['nombre_local'] ?? 'N/A') . '" sigue activo en YoLocal.',
                    'exito',
                    'index.php?pag=pagos_usuario'
                );
            }

            // Enviar comprobante si hay correo registrado
            if ($pagoRegistro && !empty($pagoRegistro['email'])) {
                try {
                    $mail = new PHPMailer(true);
                    $mail->isSMTP();
                    $mail->CharSet    = 'UTF-8';
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'lcldmnstrcn@gmail.com';
                    $mail->Password   = 'blul eqsq ifnk fgxn';
                    $mail->SMTPSecure = 'ssl';
                    $mail->Port       = 465;

                    $mail->setFrom('lcldmnstrcn@gmail.com', 'YoLocal');
                    $mail->addAddress($pagoRegistro['email'], $pagoRegistro['nombre_dueno']);
                    $mail->Subject = 'Comprobante de pago - YoLocal';
                    $mail->isHTML(true);

                    $fechaPago  = htmlspecialchars($pagoRegistro['dia_pago'] ?? date('Y-m-d'));
                    $referencia = htmlspecialchars($pagoRegistro['referencia_pago'] ?? '');
                    $local      = htmlspecialchars($pagoRegistro['nombre_local'] ?? '');
                    $dueno      = htmlspecialchars($pagoRegistro['nombre_dueno'] ?? '');
                    $monto      = '$' . number_format((float)($pagoRegistro['monto'] ?? 199), 2) . ' MXN';

                    $mail->Body = "
                    <div style='font-family:sans-serif;max-width:560px;margin:auto;border-radius:16px;overflow:hidden;border:1px solid #e5e7eb'>
                        <div style='background:#4c0682;padding:28px 32px;text-align:center'>
                            <h1 style='color:#fff;margin:0;font-size:22px'>Comprobante de Pago</h1>
                            <p style='color:#dfc3f7;margin:6px 0 0;font-size:14px'>YoLocal</p>
                        </div>
                        <div style='padding:28px 32px;background:#fff'>
                            <p style='font-size:15px;color:#333'>Hola <strong>{$dueno}</strong>,</p>
                            <p style='font-size:14px;color:#555;margin-bottom:20px'>Tu pago ha sido procesado exitosamente. Aquí tienes el resumen:</p>
                            <table style='width:100%;border-collapse:collapse;font-size:14px'>
                                <tr style='background:#f9f5ff'><td style='padding:10px 14px;font-weight:600;color:#4c0682'>Negocio</td><td style='padding:10px 14px;color:#333'>{$local}</td></tr>
                                <tr><td style='padding:10px 14px;font-weight:600;color:#4c0682'>Referencia</td><td style='padding:10px 14px;color:#333'>{$referencia}</td></tr>
                                <tr style='background:#f9f5ff'><td style='padding:10px 14px;font-weight:600;color:#4c0682'>Fecha</td><td style='padding:10px 14px;color:#333'>{$fechaPago}</td></tr>
                                <tr><td style='padding:10px 14px;font-weight:600;color:#4c0682'>Monto</td><td style='padding:10px 14px;color:#333'>{$monto}</td></tr>
                                <tr style='background:#f9f5ff'><td style='padding:10px 14px;font-weight:600;color:#4c0682'>Estado</td><td style='padding:10px 14px'><span style='background:#e8f6ee;color:#1f8f4b;padding:4px 10px;border-radius:999px;font-weight:700'>Pagado</span></td></tr>
                            </table>
                            <p style='font-size:13px;color:#888;margin-top:22px'>Gracias por tu aportación. Tu negocio sigue activo y visible en la plataforma YoLocal.</p>
                        </div>
                        <div style='background:#f3f4f6;padding:14px 32px;text-align:center'>
                            <p style='font-size:12px;color:#aaa;margin:0'>YoLocal · Este correo es un comprobante automático</p>
                        </div>
                    </div>";

                    $mail->send();
                } catch (Exception $e) {
                    // Si falla el correo no se interrumpe la página
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo) ?> - Yo Local</title>
    <?php
    $adminCssFiles = ["assets/css/principal.css", "assets/css/pagos-responsive.css"];
    include_once(__DIR__ . '/head.php');
    ?>
    <style>
        .gracias-wrap { min-height: calc(100vh - 80px); display: flex; align-items: center; justify-content: center; padding: 32px 16px; }
        .gracias-card { max-width: 760px; width: 100%; background: #fff; border-radius: 22px; box-shadow: 0 18px 40px rgba(0,0,0,.12); padding: 32px; }
        .gracias-badge { display: inline-block; padding: 8px 14px; border-radius: 999px; background: #e8f6ee; color: #1f8f4b; font-weight: 700; margin-bottom: 16px; }
        .beneficios { display: grid; gap: 12px; margin-top: 18px; }
        .beneficio { padding: 14px; border-radius: 14px; background: #f8f9fa; }
        .acciones { display: flex; gap: 12px; flex-wrap: wrap; margin-top: 24px; }
        .btn-primario { background: #009ee3; color: #fff; border: none; padding: 11px 18px; border-radius: 10px; font-weight: 700; text-decoration: none; }
        .btn-secundario { background: #1f2937; color: #fff; border: none; padding: 11px 18px; border-radius: 10px; font-weight: 700; text-decoration: none; }
        .felicitacion-modal {
            position: fixed;
            inset: 0;
            background: rgba(17, 24, 39, .55);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1200;
            padding: 16px;
        }
        .felicitacion-modal.activo { display: flex; }
        .felicitacion-contenido {
            width: 100%;
            max-width: 480px;
            background: #fff;
            border-radius: 18px;
            padding: 28px 24px;
            text-align: center;
            box-shadow: 0 18px 45px rgba(0, 0, 0, .25);
            animation: modalEntrada .35s ease-out;
        }
        .felicitacion-icono {
            width: 68px;
            height: 68px;
            border-radius: 50%;
            background: #e8f6ee;
            color: #1f8f4b;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 34px;
            margin-bottom: 12px;
        }
        .felicitacion-titulo { font-size: 1.45rem; font-weight: 800; color: #0f172a; margin-bottom: 8px; }
        .felicitacion-texto { color: #4b5563; margin-bottom: 16px; }
        .btn-cerrar-modal {
            border: none;
            border-radius: 10px;
            background: #009ee3;
            color: #fff;
            font-weight: 700;
            padding: 10px 16px;
            cursor: pointer;
        }
        @keyframes modalEntrada {
            from { opacity: 0; transform: translateY(16px) scale(.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
    </style>
</head>
<body>
    <?php if ($estado === 'approved'): ?>
    <div id="felicitacionModal" class="felicitacion-modal" role="dialog" aria-modal="true" aria-labelledby="felicitacionTitulo">
        <div class="felicitacion-contenido">
            <div class="felicitacion-icono"><i class="bi bi-heart-fill" style="color:#4c0682;font-size:28px;"></i></div>
            <h2 id="felicitacionTitulo" class="felicitacion-titulo">¡Gracias por seguir creyendo en lo local! 💜💛</h2>
            <p class="felicitacion-texto">Gracias por tu confianza. Sigamos impulsando juntos a los negocios locales.</p>
            <button type="button" id="cerrarModalPago" class="btn-cerrar-modal">Continuar</button>
        </div>
    </div>
    <?php endif; ?>

    <div class="gracias-wrap">
        <div class="gracias-card">
            <span class="gracias-badge"><?= htmlspecialchars($titulo) ?></span>
            <h1 class="mb-3">Gracias por tu pago</h1>
            <p class="text-muted fs-5"><?= htmlspecialchars($mensajePag) ?></p>

            <div class="beneficios">
                <div class="beneficio">Tu negocio sigue activo dentro de YoLocal.</div>
                <div class="beneficio">Mantienes acceso a promociones, cupones y visibilidad dentro de la plataforma.</div>
                <div class="beneficio">Tu apoyo ayuda a mejorar la experiencia y las herramientas disponibles para tu local.</div>
            </div>

            <div class="acciones">
                <a href="<?= $baseUrl ?>index.php?pag=home" class="btn-primario">Volver al inicio</a>
                <a href="<?= $baseUrl ?>index.php?pag=pagos_usuario" class="btn-secundario">Ver mis pagos</a>
            </div>
        </div>
    </div>

    <?php if ($estado === 'approved'): ?>
    <script>
        (function () {
            var modal = document.getElementById('felicitacionModal');
            var cerrar = document.getElementById('cerrarModalPago');
            if (!modal || !cerrar) {
                return;
            }

            modal.classList.add('activo');

            cerrar.addEventListener('click', function () {
                modal.classList.remove('activo');
            });

            modal.addEventListener('click', function (event) {
                if (event.target === modal) {
                    modal.classList.remove('activo');
                }
            });
        })();
    </script>
    <?php endif; ?>
</body>
</html>

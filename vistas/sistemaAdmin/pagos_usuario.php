<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../controladores/controladorPagos.php';

$baseUrl = $baseUrl ?? '';
$usuarioId = (int)($_SESSION['ID_Usuario'] ?? $_SESSION['id'] ?? 0);
$mostrarConfirmacion = false;
$esAliadoSesion = strtolower((string)($_SESSION['es_aliado'] ?? '')) === 'si';

if ($usuarioId > 0) {
    $conexionAliado = dbConectar();
    if ($conexionAliado instanceof mysqli) {
        $stmtAliado = $conexionAliado->prepare("SELECT es_aliado FROM usuarios WHERE ID_Usuario = ? LIMIT 1");
        if ($stmtAliado) {
            $stmtAliado->bind_param('i', $usuarioId);
            $stmtAliado->execute();
            $resAliado = $stmtAliado->get_result();
            if ($filaAliado = $resAliado->fetch_assoc()) {
                $esAliadoSesion = strtolower((string)($filaAliado['es_aliado'] ?? 'no')) === 'si';
                $_SESSION['es_aliado'] = $esAliadoSesion ? 'si' : 'no';
            }
            $stmtAliado->close();
        }
    }
}

$datosPago = obtenerDatosPagoUsuario($usuarioId);
$pagos = [];

if ($usuarioId > 0) {
    inicializarTablaPagos();
    $conexion = dbConectar();
    $idNegocio = $datosPago['negocios'][0]['ID_Negocio'] ?? 0;

    if ($idNegocio > 0) {
        $stmt = $conexion->prepare("SELECT * FROM pagos_yolocal WHERE ID_Negocio = ? OR nombre_dueno = ? OR nombre_local = ? ORDER BY fecha_creacion DESC");
        $stmt->bind_param("iss", $idNegocio, $datosPago['nombre_dueno'], $datosPago['nombre_local']);
    } else {
        $stmt = $conexion->prepare("SELECT * FROM pagos_yolocal WHERE nombre_dueno = ? OR nombre_local = ? ORDER BY fecha_creacion DESC");
        $stmt->bind_param("ss", $datosPago['nombre_dueno'], $datosPago['nombre_local']);
    }

    $stmt->execute();
    $resultado = $stmt->get_result();
    while ($fila = $resultado->fetch_assoc()) {
        $pagos[] = $fila;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis pagos</title>
    <?php include_once(__DIR__ . '/head.php'); ?>
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/principal.css">
    <style>
        .card-resumen { background: #fff; border-radius: 16px; padding: 20px; box-shadow: 0 8px 20px rgba(0,0,0,.08); }
        .badge-pagado { background: #28a745; color: #fff; padding: 6px 10px; border-radius: 999px; font-size: 12px; }
        .badge-pendiente { background: #dc3545; color: #fff; padding: 6px 10px; border-radius: 999px; font-size: 12px; }
        
        .pago-aviso {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            background: #f0f7ff;
            border-left: 4px solid #009ee3;
            border-radius: 10px;
            padding: 12px 14px;
            font-size: 13px;
            color: #1a5a80;
            margin-bottom: 18px;
        }
        .pago-aviso i { font-size: 17px; margin-top: 1px; flex-shrink: 0; }
    </style>
</head>
<body>
<div class="navigation admin-sidebar">
    <?php include_once(__DIR__ . '/encabezado.php'); ?>
</div>
<div class="main">
    <div class="topbar">
        <div class="toggle">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
        </div>
        <div class="contenedor">
                <?php include_once(__DIR__ . "/calendario_btn.php"); ?>
            <?php include_once(__DIR__ . '/prueba_pago_btn.php'); ?>
            <?php include_once(__DIR__ . '/prueba_notif_btn.php'); ?>
            <?php include_once(__DIR__ . '/notificaciones_dueno_btn.php'); ?>
            <div class="usuario"><img src="<?= $baseUrl ?>assets/img/descarga.gif" alt=""></div>
        </div>
    </div>

    <div class="content p-4">
        <div id="contenido-pagos" style="<?= $mostrarConfirmacion ? 'display:none;' : '' ?>">
            <div class="card-resumen mb-4">
                <h3 class="mb-2">Resumen de pagos</h3>
                <p class="text-muted mb-0">Aqui puedes ver tu estado de pago, referencia y fecha registrada.</p>
            </div>

            <div class="card-resumen">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Local</th>
                            <th>Referencia</th>
                            <th>Dia</th>
                            <th>Estado</th>
                            <th>Accion</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pagos)): ?>
                            <tr><td colspan="5" class="text-center py-4">No hay pagos registrados para este usuario.</td></tr>
                        <?php else: foreach ($pagos as $pago): ?>
                            <tr>
                                <td><?= htmlspecialchars($pago['nombre_local']) ?></td>
                                <td><?= htmlspecialchars($pago['referencia_pago']) ?></td>
                                <td><?= htmlspecialchars($pago['dia_pago']) ?></td>
                                <td>
                                    <?php if ($pago['estado'] === 'pagado'): ?>
                                        <span class="badge-pagado">Pagado</span>
                                    <?php else: ?>
                                        <span class="badge-pendiente">Pendiente</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($pago['estado'] !== 'pagado'): ?>
                                        <?php
                                            $enlacePago = $baseUrl . 'index.php?pag=pagos';
                                            $initPoint = trim((string)($pago['mp_init_point'] ?? ''));
                                            if ($initPoint === obtenerLinkAliadosImpulsoManual()) {
                                                $enlacePago = $baseUrl . 'index.php?pag=aportacion';
                                            }
                                        ?>
                                        <a href="<?= htmlspecialchars($enlacePago) ?>" style="display:inline-block;background:var(--principal,#4c0682);color:#fff;border:none;padding:6px 12px;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;transition:background .2s;text-decoration:none;">
                                            <i class="bi bi-credit-card"></i> Pagar
                                        </a>
                                    <?php else: ?>
                                        <span style="color:#999;font-size:12px;">Completado</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="pago-aviso" style="margin-top: 20px;">
                <i class="bi bi-info-circle-fill"></i>
                Tus pagos se gestionan de forma segura. Al confirmar, tu aportacion quedara registrada.
            </div>
        </div>

        <div id="contenido-confirmacion" style="display:<?= $mostrarConfirmacion ? 'block' : 'none' ?>;">
            <div style="min-height:calc(100vh - 180px);display:flex;align-items:center;justify-content:center;padding:32px 16px;">
                <div style="max-width:480px;width:100%;background:#fff;border-radius:22px;box-shadow:0 20px 60px rgba(76,6,130,0.25);padding:48px 32px;text-align:center;">
                    <div style="width:68px;height:68px;border-radius:50%;background:linear-gradient(135deg,#4c0682,#f8c300);display:inline-flex;align-items:center;justify-content:center;margin-bottom:20px;">
                        <i class="bi bi-heart-fill" style="color:#fff;font-size:32px;"></i>
                    </div>
                    <h2 style="font-size:1.45rem;font-weight:800;color:#1f2937;margin-bottom:12px;">Gracias por seguir creyendo en lo local</h2>
                    <p style="color:#4b5563;font-size:15px;margin-bottom:28px;line-height:1.6;">Gracias por tu confianza. Sigamos impulsando juntos a los negocios locales.</p>
                    <p style="color:#6b7280;font-size:13px;margin-bottom:16px;"><i class="bi bi-check-circle-fill" style="color:#22c55e;margin-right:6px;"></i>Tu pago ha sido procesado correctamente.</p>
                    <p style="color:#6b7280;font-size:13px;margin-bottom:32px;"><i class="bi bi-hourglass-split" style="color:#3b82f6;margin-right:6px;"></i>En modo enlace manual, la confirmacion final depende de validacion administrativa.</p>
                    <div style="display:flex;gap:12px;flex-direction:column;">
                        <a href="<?= $baseUrl ?>index.php?pag=home" style="display:inline-block;background:var(--principal,#4c0682);color:#fff;border:none;padding:12px 28px;border-radius:12px;font-size:15px;font-weight:700;cursor:pointer;text-decoration:none;transition:background .2s;text-align:center;">
                            <i class="bi bi-house"></i> Ir al inicio
                        </a>
                        <button type="button" onclick="regresarAlListado()" style="background:#f3f4f6;color:#374151;border:none;padding:12px 28px;border-radius:12px;font-size:15px;font-weight:700;cursor:pointer;transition:background .2s;text-align:center;">
                            <i class="bi bi-wallet2"></i> Ver mis pagos
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    </div>

</div>
<script src="<?= $baseUrl ?>assets/js/main.js"></script>

<script>
function regresarAlListado() {
    const contenidoPagos = document.getElementById('contenido-pagos');
    const contenidoConfirmacion = document.getElementById('contenido-confirmacion');
    
    if (contenidoPagos && contenidoConfirmacion) {
        contenidoConfirmacion.style.display = 'none';
        contenidoPagos.style.display = 'block';
    }
}
</script>
</body>
</html>


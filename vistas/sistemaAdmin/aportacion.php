<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../controladores/controladorPagos.php';

$baseUrl = $baseUrl ?? '';
$mensaje = '';
$preferencia = null;
$linkAportacionManual = 'https://mpago.la/1QW4dBw';
$modoApiMercadoPago = mercadoPagoApiDisponible();
$usuarioId = (int)($_SESSION['ID_Usuario'] ?? $_SESSION['id'] ?? 0);
$aportacionVigente = $usuarioId > 0 && usuarioTieneAportacionActiva($usuarioId);
$soloExtra = isset($_GET['solo_extra']) && $_GET['solo_extra'] == '1';
$referenciaRetorno = trim((string)($_GET['external_reference'] ?? $_GET['reference'] ?? ''));
$paymentIdRetorno = obtenerPaymentIdRetornoMercadoPago($_GET);
$estadoPago = $_GET['estado'] ?? $_GET['status'] ?? $_GET['collection_status'] ?? null;
$estadoPagoNormalizado = strtolower(trim((string)$estadoPago));
$forzarConfirmacion = isset($_GET['confirmacion']) && (string)$_GET['confirmacion'] === '1';
$debugHost = isset($_GET['debug_host']) && (string)$_GET['debug_host'] === '1';
$debugVersionTag = 'YOLOCAL-DEPLOY-2026-07-31-APORTACION';
$mostrarConfirmacion = (int)($_SESSION['mostrar_confirmacion_aportacion'] ?? 0) === 1 || $forzarConfirmacion || $aportacionVigente;
$confirmacionAportacionValidada = (int)($_SESSION['confirmacion_aportacion_validada'] ?? 0) === 1 || $aportacionVigente;
$estadoAprobado = $estadoPagoNormalizado === 'approved' || $estadoPagoNormalizado === 'accredited' || $estadoPagoNormalizado === 'success';

if ($estadoAprobado) {
    $actualizacionOk = false;
    if ($usuarioId > 0) {
        $actualizacionOk = actualizarPagoPorRetornoMercadoPago(
            $usuarioId,
            $estadoPagoNormalizado,
            $referenciaRetorno,
            [
                'filtro_init_point' => $modoApiMercadoPago ? '' : $linkAportacionManual,
                'payment_id' => $paymentIdRetorno,
                'max_minutos_retorno' => 1440,
            ]
        );
    }

    $_SESSION['mostrar_confirmacion_aportacion'] = 1;
    $_SESSION['confirmacion_aportacion_validada'] = $actualizacionOk ? 1 : 0;
    $mostrarConfirmacion = true;
    $confirmacionAportacionValidada = $actualizacionOk;

    if (!isset($_GET['confirmacion'])) {
        header('Location: ' . $baseUrl . 'index.php?pag=aportacion&confirmacion=1');
        exit();
    }
}

if ($mostrarConfirmacion) {
    unset($_SESSION['mostrar_confirmacion_aportacion']);
    unset($_SESSION['confirmacion_aportacion_validada']);
}

$datosPago = obtenerDatosPagoUsuario($usuarioId);
$datosPago = array_merge([
    'nombre_dueno' => '',
    'nombre_local' => '',
    'referencia_pago' => '',
    'dia_pago' => date('Y-m-d'),
    'email' => '',
    'negocios' => [],
], $datosPago);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_aportacion'])) {
    $nombreDueno = trim($_POST['nombre_dueno'] ?? '');
    $nombreLocal = trim($_POST['nombre_local'] ?? '');
    $referenciaPago = trim($_POST['referencia_pago'] ?? '');
    $diaPago = trim($_POST['dia_pago'] ?? '');
    $emailAportacion = trim($_POST['email_aportacion'] ?? '');

    if ($nombreDueno === '') {
        $nombreDueno = $datosPago['nombre_dueno'];
    }
    if ($nombreLocal === '') {
        $nombreLocal = $datosPago['nombre_local'];
    }
    if ($referenciaPago === '') {
        $referenciaPago = generarReferenciaPago($usuarioId, $diaPago) . '-AI';
    } elseif (strpos($referenciaPago, '-AI') === false) {
        $referenciaPago .= '-AI';
    }
    if ($diaPago === '') {
        $diaPago = $datosPago['dia_pago'];
    }

    $montoPlan = 65;
    $titulo = 'Aliados Impulso YoLocal - ' . $nombreDueno;
    $descripcion = 'Local: ' . $nombreLocal . ' | Referencia: ' . $referenciaPago . ' | Fecha: ' . $diaPago;

    if ($emailAportacion !== '' && !filter_var($emailAportacion, FILTER_VALIDATE_EMAIL)) {
        $mensaje = 'Ingresa un correo valido para continuar con tu aportacion.';
    }

    $idNegocioSeleccionado = null;
    if (!empty($datosPago['negocios'])) {
        foreach ($datosPago['negocios'] as $negocio) {
            if ((string)($negocio['nombre_negocio'] ?? '') === $nombreLocal) {
                $idNegocioSeleccionado = (int)($negocio['ID_Negocio'] ?? 0);
                break;
            }
        }

        if (!$idNegocioSeleccionado) {
            $idNegocioSeleccionado = (int)($datosPago['negocios'][0]['ID_Negocio'] ?? 0);
            $nombreLocal = (string)($datosPago['negocios'][0]['nombre_negocio'] ?? $nombreLocal);
        }
    }

    if ($mensaje === '') {
        $preferenciaId = '';
        $linkCheckout = $linkAportacionManual;

        if ($modoApiMercadoPago) {
            $retorno = obtenerUrlRetornoPago('index.php?pag=aportacion');
            $preferencia = crearPreferenciaMercadoPago(
                $montoPlan,
                $titulo,
                $descripcion,
                $emailAportacion,
                [
                    'external_reference' => $referenciaPago,
                    'url_success' => $retorno,
                    'url_pending' => $retorno,
                    'url_failure' => $retorno,
                ]
            );

            if (!($preferencia['ok'] ?? false)) {
                $mensaje = 'No se pudo iniciar Mercado Pago con API. Revisa token y configuracion.';
            } else {
                $preferenciaId = (string)($preferencia['id'] ?? '');
                $linkCheckout = trim((string)($preferencia['init_point'] ?? ''));
            }
        }

        if ($linkCheckout === '') {
            $mensaje = 'No se pudo obtener el enlace de pago para la aportacion.';
        }

        $datosSave = [
            'nombre_dueno'    => $nombreDueno,
            'nombre_local'    => $nombreLocal,
            'referencia_pago' => $referenciaPago,
            'dia_pago'        => $diaPago !== '' ? $diaPago : date('Y-m-d'),
            'monto'           => $montoPlan,
            'email'           => $emailAportacion,
            'estado'          => 'pendiente',
            'ID_Usuario'      => $usuarioId,
            'ID_Negocio'      => $idNegocioSeleccionado,
            'mp_preference_id' => $preferenciaId,
            'mp_init_point'   => $linkCheckout,
        ];

        if ($mensaje === '' && guardarPago($datosSave)) {
            $mensaje = 'Listo, veci. Te estamos mandando a Mercado Pago.';
            $preferencia = [
                'ok' => true,
                'init_point' => $linkCheckout
            ];
            header('Location: ' . $preferencia['init_point']);
            exit;
        }

        if ($mensaje === '') {
            $mensaje = 'No se pudo registrar tu aportacion pendiente. Intenta nuevamente.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aliados Impulso - Yo Local</title>
    <?php
    $adminCssFiles = ["assets/css/principal.css", "assets/css/pagos-responsive.css", "assets/css/usuarios.css"];
    include_once(__DIR__ . '/head.php');
    ?>
    <style>
        .pago-header {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 28px;
        }
        .pago-header .icono-mp {
            width: 54px;
            height: 54px;
            border-radius: 14px;
            background: #009ee3;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .pago-header .icono-mp i {
            font-size: 26px;
            color: #fff;
        }
        .pago-header h2 { margin: 0; font-size: 22px; font-weight: 700; color: var(--texto2); }
        .pago-header p  { margin: 0; font-size: 13px; color: var(--black2); }

        .pago-layout {
            display: grid;
            grid-template-columns: minmax(0, 640px) minmax(0, 1fr);
            gap: 24px;
            align-items: stretch;
        }

        .pago-card {
            background: #fff;
            border-radius: 20px;
            padding: 32px;
            box-shadow: 0 6px 24px rgba(0,0,0,.08);
            max-width: 640px;
            width: 100%;
        }

        .pago-side-image {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 6px 24px rgba(0,0,0,.08);
            border: 1px solid #f1f1f1;
            overflow: hidden;
            position: sticky;
            top: 90px;
            width: 100%;
            margin: 0;
            height: 100%;
            display: flex;
        }

        .pago-side-image img {
            width: 100%;
            height: 100%;
            display: block;
            object-fit: cover;
        }

        .pago-card .form-label {
            font-weight: 600;
            font-size: 13px;
            color: var(--texto);
            margin-bottom: 4px;
        }
        .pago-card .form-control,
        .pago-card .form-select {
            border-radius: 10px;
            border: 1.5px solid var(--lineas);
            font-size: 14px;
            padding: 9px 12px;
            transition: border-color .2s;
        }
        .pago-card .form-control:focus,
        .pago-card .form-select:focus {
            border-color: var(--principal);
            box-shadow: 0 0 0 3px rgba(76,6,130,.12);
        }
        .pago-card .form-control[readonly] {
            background: #f8f9fa;
            color: var(--black2);
        }

        .pago-divider {
            border: none;
            border-top: 1.5px solid #f0f0f0;
            margin: 22px 0;
        }

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

        .btn-pagar {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--principal);
            color: #fff;
            border: none;
            padding: 12px 28px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: background .2s, transform .1s;
        }
        .btn-pagar:hover  { background: var(--principal2); transform: translateY(-1px); color: #fff; }
        .btn-pagar:active { transform: translateY(0); }

        .pago-status {
            background: #f8f9fa;
            border: 1px solid #ececec;
            border-radius: 12px;
            padding: 14px;
        }

        .confirmacion-beneficios {
            margin: 14px auto 0;
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid #ece7ff;
            box-shadow: 0 8px 24px rgba(76, 6, 130, 0.12);
            width: 100%;
            max-width: 820px;
            background: #fff;
        }

        .confirmacion-beneficios img {
            width: 100%;
            display: block;
            height: auto;
            cursor: zoom-in;
            touch-action: manipulation;
        }

        .image-lightbox {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.82);
            backdrop-filter: blur(3px);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 18px;
            z-index: 5000;
        }

        .image-lightbox.activo {
            display: flex;
        }

        .image-lightbox img {
            max-width: min(1200px, 96vw);
            max-height: 92vh;
            width: auto;
            height: auto;
            border-radius: 14px;
            box-shadow: 0 26px 70px rgba(0, 0, 0, 0.45);
            border: 1px solid rgba(255, 255, 255, 0.18);
            cursor: grab;
            transform-origin: center center;
            transition: transform .18s ease;
            user-select: none;
            -webkit-user-drag: none;
            touch-action: none;
        }

        .image-lightbox img.arrastrando {
            cursor: grabbing;
        }

        .image-lightbox-controls {
            position: fixed;
            top: 16px;
            right: 16px;
            z-index: 5100;
            display: flex;
            gap: 8px;
            align-items: center;
            background: rgba(17, 24, 39, 0.72);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 999px;
            padding: 6px 8px;
            color: #fff;
        }

        .image-lightbox-btn {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            border: none;
            background: rgba(255, 255, 255, 0.18);
            color: #fff;
            font-weight: 800;
            font-size: 18px;
            line-height: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .image-lightbox-zoom {
            min-width: 52px;
            text-align: center;
            font-weight: 700;
            font-size: 13px;
        }

        @media (max-width: 768px) {
            .content.p-4 {
                padding: 16px !important;
            }

            .confirmacion-beneficios {
                margin-top: 12px;
                max-width: 100%;
                border-radius: 12px;
            }

            .pago-header {
                flex-direction: column;
                align-items: flex-start;
                margin-bottom: 20px;
            }
            .pago-layout {
                grid-template-columns: 1fr;
                gap: 16px;
            }
            .pago-card {
                width: 100%;
                max-width: 100%;
                padding: 20px;
            }
            .pago-side-image {
                position: static;
                min-height: auto;
                height: auto;
            }
            .btn-pagar {
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .content.p-4 {
                padding: 12px !important;
            }
            .pago-header .icono-mp {
                width: 48px;
                height: 48px;
            }
            .pago-header h2 {
                font-size: 20px;
            }
            .pago-card {
                padding: 16px;
                border-radius: 16px;
            }
        }
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
            <?php if ($debugHost): ?>
                <div style="margin: 0 0 16px; padding: 12px 14px; border-radius: 10px; border: 1px solid #f59e0b; background: #fffbeb; color: #92400e; font-size: 13px; line-height: 1.45;">
                    <strong>DEBUG HOST:</strong> <?= htmlspecialchars($debugVersionTag) ?><br>
                    <strong>archivo:</strong> <?= htmlspecialchars(str_replace('\\', '/', __FILE__)) ?><br>
                    <strong>modificado:</strong> <?= date('Y-m-d H:i:s', filemtime(__FILE__)) ?><br>
                    <strong>usuario_id:</strong> <?= (int)$usuarioId ?> |
                    <strong>api_mp:</strong> <?= $modoApiMercadoPago ? '1' : '0' ?> |
                    <strong>aportacion_vigente:</strong> <?= $aportacionVigente ? '1' : '0' ?> |
                    <strong>confirmacion:</strong> <?= $forzarConfirmacion ? '1' : '0' ?> |
                    <strong>mostrar_confirmacion:</strong> <?= $mostrarConfirmacion ? '1' : '0' ?> |
                    <strong>validada:</strong> <?= $confirmacionAportacionValidada ? '1' : '0' ?> |
                    <strong>payment_id:</strong> <?= htmlspecialchars((string)$paymentIdRetorno) ?> |
                    <strong>estado:</strong> <?= htmlspecialchars((string)$estadoPagoNormalizado) ?>
                </div>
            <?php endif; ?>
            <?php if ($mostrarConfirmacion): ?>
                <!-- Pantalla de confirmacion despues del pago -->
                <div style="min-height:calc(100vh - 180px);display:flex;align-items:center;justify-content:center;padding:32px 16px;">
                    <div style="max-width:480px;width:100%;background:#fff;border-radius:22px;box-shadow:0 20px 60px rgba(76,6,130,0.25);padding:48px 32px;text-align:center;">
                        <div style="width:68px;height:68px;border-radius:50%;background:linear-gradient(135deg,#4c0682,#f8c300);display:inline-flex;align-items:center;justify-content:center;margin-bottom:20px;">
                            <i class="bi bi-heart-fill" style="color:#fff;font-size:32px;"></i>
                        </div>
                        <h2 style="font-size:1.45rem;font-weight:800;color:#1f2937;margin-bottom:12px;">&iexcl;Gracias por seguir creyendo en lo local!</h2>
                        <p style="color:#4b5563;font-size:15px;margin-bottom:28px;line-height:1.6;">Gracias por tu confianza. Sigamos impulsando juntos a los negocios locales.</p>
                        <p style="color:#6b7280;font-size:13px;margin-bottom:16px;"><i class="bi <?= $confirmacionAportacionValidada ? 'bi-check-circle-fill' : 'bi-hourglass-split' ?>" style="color:<?= $confirmacionAportacionValidada ? '#22c55e' : '#f59e0b' ?>;margin-right:6px;"></i><?= $confirmacionAportacionValidada ? 'Tu pago ha sido procesado correctamente.' : 'Recibimos tu aportacion. Quedo en revision para validacion final.' ?></p>
                        <p style="color:#6b7280;font-size:13px;margin-bottom:32px;"><i class="bi <?= $confirmacionAportacionValidada ? 'bi-repeat' : 'bi-shield-check' ?>" style="color:#3b82f6;margin-right:6px;"></i><?= $confirmacionAportacionValidada ? 'Tu aportacion fue validada automaticamente con Mercado Pago.' : 'En modo enlace manual o retorno incompleto, se valida desde administracion.' ?></p>
                        <div style="display:flex;gap:12px;flex-direction:column;">
                            <a href="<?= $baseUrl ?>index.php?pag=home" style="display:inline-block;background:var(--principal,#4c0682);color:#fff;border:none;padding:12px 28px;border-radius:12px;font-size:15px;font-weight:700;cursor:pointer;text-decoration:none;transition:background .2s;text-align:center;">
                                <i class="bi bi-house"></i> Ir al inicio
                            </a>
                            <a href="<?= $baseUrl ?>index.php?pag=pagos_usuario" style="display:inline-block;background:#f3f4f6;color:#374151;border:none;padding:12px 28px;border-radius:12px;font-size:15px;font-weight:700;cursor:pointer;text-decoration:none;transition:background .2s;text-align:center;">
                                <i class="bi bi-wallet2"></i> Ver mis pagos
                            </a>
                        </div>
                    </div>
                </div>
                <figure class="confirmacion-beneficios" aria-label="Beneficios para negocios">
                    <img src="<?= $baseUrl ?>assets/img/banners/bannerimoulsoaliado.png" alt="Beneficios de tu aportacion Aliados Impulso" class="js-expandible-beneficios">
                </figure>
            <?php else: ?>
                <!-- Formulario de aportacion -->
                <div class="pago-header">
                    <div class="icono-mp"><i class="bi bi-cash-coin"></i></div>
                    <div>
                        <h2><b>Impulsa la visibilidad de tu negocio</b></h2>
                        <p>Dale un mayor alcance a tu negocio dentro de Yo Local</p>
                    </div>
                </div>

                <div class="pago-layout">
                    <div class="pago-card">
                        <?php if (!empty($mensaje)): ?>
                            <div class="alert alert-warning mb-3"><?= htmlspecialchars($mensaje) ?></div>
                        <?php endif; ?>

                        <form method="post">
                            <input type="hidden" name="crear_aportacion" value="1">

                            <p class="fw-semibold mb-3" style="color:var(--principal); font-size:13px; text-transform:uppercase; letter-spacing:.6px;">
                                <i class="bi bi-person-fill me-1"></i> Tus datos
                            </p>
                            <div class="row g-3 mb-2">
                                <div class="col-md-6">
                                    <label class="form-label">Nombre del propietario</label>
                                    <input type="text" name="nombre_dueno" class="form-control" value="<?= htmlspecialchars($datosPago['nombre_dueno']) ?>" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Nombre del negocio</label>
                                    <?php if (!empty($datosPago['negocios'])): ?>
                                        <select name="nombre_local" class="form-select" required>
                                            <?php foreach ($datosPago['negocios'] as $negocio): ?>
                                                <option value="<?= htmlspecialchars($negocio['nombre_negocio']) ?>" <?= $negocio['nombre_negocio'] === $datosPago['nombre_local'] ? 'selected' : '' ?>><?= htmlspecialchars($negocio['nombre_negocio']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    <?php else: ?>
                                        <input type="text" name="nombre_local" class="form-control" value="<?= htmlspecialchars($datosPago['nombre_local']) ?>" readonly>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <hr class="pago-divider">

                            <p class="fw-semibold mb-3" style="color:var(--principal); font-size:13px; text-transform:uppercase; letter-spacing:.6px;">
                                <i class="bi bi-receipt me-1"></i> Datos de tu aporte
                            </p>
                            <div class="row g-3 mb-2">
                                <div class="col-md-6">
                                    <label class="form-label">Referencia de aportacion</label>
                                    <input type="text" name="referencia_pago" class="form-control" value="<?= htmlspecialchars($datosPago['referencia_pago'] ?: generarReferenciaPago($usuarioId, $datosPago['dia_pago'])) ?>" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Fecha de aportacion</label>
                                    <input type="date" name="dia_pago" class="form-control" value="<?= htmlspecialchars($datosPago['dia_pago']) ?>" readonly>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Correo electronico <span class="text-danger">*</span></label>
                                    <input type="email" name="email_aportacion" class="form-control" value="<?= htmlspecialchars($datosPago['email']) ?>" placeholder="usuario@correo.com" required>
                                </div>
                            </div>

                            <hr class="pago-divider">

                            <div class="pago-aviso">
                                <i class="bi bi-info-circle-fill"></i>
                                Tus pagos se gestionan de forma segura. Al confirmar, tu aportacion quedara registrada.
                            </div>

                            <button type="submit" class="btn-pagar">
                                <i class="bi bi-credit-card"></i>
                                Seguir con mi aportacion
                            </button>
                        </form>

                        <?php if ($preferencia && $preferencia['ok']): ?>
                            <hr class="pago-divider">
                            <div class="pago-status">
                                <h5 class="mb-2">Listo, ya quedo</h5>
                                <p class="mb-3">Te estamos enviando a Mercado Pago para completar tu aporte.</p>
                                <a href="<?= htmlspecialchars($preferencia['init_point']) ?>" class="btn-pagar" target="_blank">
                                    <i class="bi bi-box-arrow-up-right"></i> Ir a pagar
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <figure class="pago-side-image">
                        <img src="<?= $baseUrl ?>assets/img/banners/bannerimoulsoaliado.png" alt="Banner lateral de aportacion">
                    </figure>
                </div>
            <?php endif; ?>
    </div>

    <div id="lightboxBeneficiosAportacion" class="image-lightbox" role="dialog" aria-modal="true" aria-label="Imagen ampliada de beneficios">
        <div class="image-lightbox-controls" aria-label="Controles de zoom">
            <button type="button" id="zoomOutAportacion" class="image-lightbox-btn" aria-label="Reducir imagen">-</button>
            <button type="button" id="zoomResetAportacion" class="image-lightbox-btn" aria-label="Restablecer zoom">�6�1</button>
            <button type="button" id="zoomInAportacion" class="image-lightbox-btn" aria-label="Aumentar imagen">+</button>
            <span id="zoomNivelAportacion" class="image-lightbox-zoom">100%</span>
            <button type="button" id="zoomCloseAportacion" class="image-lightbox-btn" aria-label="Cerrar imagen">��</button>
        </div>
        <img id="lightboxBeneficiosAportacionImg" src="" alt="Imagen ampliada de beneficios">
    </div>

    <script src="<?= $baseUrl ?>assets/js/main.js"></script>
    <script>
    (function () {
        var imagenes = document.querySelectorAll('.js-expandible-beneficios');
        var lightbox = document.getElementById('lightboxBeneficiosAportacion');
        var lightboxImg = document.getElementById('lightboxBeneficiosAportacionImg');
        var zoomOut = document.getElementById('zoomOutAportacion');
        var zoomIn = document.getElementById('zoomInAportacion');
        var zoomReset = document.getElementById('zoomResetAportacion');
        var zoomClose = document.getElementById('zoomCloseAportacion');
        var zoomNivel = document.getElementById('zoomNivelAportacion');
        var escala = 1;
        var minZoom = 0.6;
        var maxZoom = 3;
        var posX = 0;
        var posY = 0;
        var arrastrando = false;
        var inicioX = 0;
        var inicioY = 0;
        var inicioPosX = 0;
        var inicioPosY = 0;

        function aplicarZoom() {
            lightboxImg.style.transform = 'translate(' + posX + 'px,' + posY + 'px) scale(' + escala + ')';
            if (zoomNivel) {
                zoomNivel.textContent = Math.round(escala * 100) + '%';
            }
        }

        function cambiarZoom(delta) {
            escala = Math.max(minZoom, Math.min(maxZoom, escala + delta));
            aplicarZoom();
        }

        function resetZoom() {
            escala = 1;
            posX = 0;
            posY = 0;
            aplicarZoom();
        }

        function abrirImagen(img) {
            lightboxImg.src = img.src;
            lightboxImg.alt = img.alt || 'Imagen ampliada';
            resetZoom();
            lightbox.classList.add('activo');
        }

        function cerrarLightbox() {
            lightbox.classList.remove('activo');
            arrastrando = false;
            lightboxImg.classList.remove('arrastrando');
        }

        if (!imagenes.length || !lightbox || !lightboxImg) {
            return;
        }

        imagenes.forEach(function (img) {
            img.addEventListener('pointerup', function (event) {
                if (event.pointerType === 'mouse' && event.button !== 0) {
                    return;
                }
                abrirImagen(img);
            });
        });

        lightbox.addEventListener('click', function (event) {
            if (event.target === lightbox) {
                cerrarLightbox();
            }
        });

        lightboxImg.addEventListener('wheel', function (event) {
            event.preventDefault();
            cambiarZoom(event.deltaY < 0 ? 0.12 : -0.12);
        }, { passive: false });

        lightboxImg.addEventListener('pointerdown', function (event) {
            arrastrando = true;
            inicioX = event.clientX;
            inicioY = event.clientY;
            inicioPosX = posX;
            inicioPosY = posY;
            lightboxImg.classList.add('arrastrando');
            lightboxImg.setPointerCapture(event.pointerId);
        });

        lightboxImg.addEventListener('pointermove', function (event) {
            if (!arrastrando) {
                return;
            }

            posX = inicioPosX + (event.clientX - inicioX);
            posY = inicioPosY + (event.clientY - inicioY);
            aplicarZoom();
        });

        lightboxImg.addEventListener('pointerup', function (event) {
            if (!arrastrando) {
                return;
            }

            arrastrando = false;
            lightboxImg.classList.remove('arrastrando');
            try {
                lightboxImg.releasePointerCapture(event.pointerId);
            } catch (e) {
            }
        });

        lightboxImg.addEventListener('pointercancel', function () {
            arrastrando = false;
            lightboxImg.classList.remove('arrastrando');
        });

        if (zoomOut) {
            zoomOut.addEventListener('click', function () { cambiarZoom(-0.2); });
        }
        if (zoomIn) {
            zoomIn.addEventListener('click', function () { cambiarZoom(0.2); });
        }
        if (zoomReset) {
            zoomReset.addEventListener('click', resetZoom);
        }
        if (zoomClose) {
            zoomClose.addEventListener('click', cerrarLightbox);
        }

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                cerrarLightbox();
            }
        });
    })();
    </script>
</body>
</html>


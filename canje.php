<?php
require_once "config.php";
require_once __DIR__ . "/modelos/cupones.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$id_promocion = isset($_GET["id"]) ? (int) $_GET["id"] : 0;
$codigo_precargado = isset($_GET["c"]) ? trim((string) $_GET["c"]) : "";
$error = null;
$success = null;
$modalCanje = null;

if (!empty($_SESSION["canje_ok"])) {
    $success = $_SESSION["canje_ok"];
    unset($_SESSION["canje_ok"]);
}
if (!empty($_SESSION["canje_err"])) {
    $error = $_SESSION["canje_err"];
    unset($_SESSION["canje_err"]);
}
if (!empty($_SESSION["canje_modal"])) {
    $modalCanje = (string) $_SESSION["canje_modal"];
    unset($_SESSION["canje_modal"]);
}

if ($id_promocion <= 0) {
    die("Cupón no válido.");
}

$enlace = dbConectar();

$sql = "SELECT p.*, n.nombre_negocio
        FROM promociones p
        INNER JOIN negocios n ON p.ID_Negocio = n.ID_Negocio
        WHERE p.ID_Promocion = ?";
$stmt = $enlace->prepare($sql);
$stmt->bind_param("i", $id_promocion);
$stmt->execute();
$promo = $stmt->get_result()->fetch_assoc();

if (!$promo) {
    die("Promoción no encontrada.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if ((int) $promo["cantidad"] <= 0) {
        $_SESSION["canje_err"] = "Este cupón ya no tiene unidades disponibles o fue reclamado en su totalidad.";
        header("Location: canje.php?id=" . $id_promocion, true, 303);
        exit;
    }

    $cupMod = new Cupones();
    $res = $cupMod->canjearConCodigoCupon($id_promocion, (string) ($_POST["codigo_cupon"] ?? ""));
    if (!empty($res["ok"])) {
        $_SESSION["canje_ok"] = $res["msg"] ?? "¡Cupón canjeado con éxito!";
    } else {
        $_SESSION["canje_err"] = $res["msg"] ?? "No se pudo canjear el cupón.";
        if (($res["reason"] ?? null) === "already_redeemed") {
            $_SESSION["canje_modal"] = "already_redeemed";
        }
    }

    header("Location: canje.php?id=" . $id_promocion, true, 303);
    exit;
}

$st2 = $enlace->prepare("SELECT cantidad, Canjeados FROM promociones WHERE ID_Promocion = ?");
$st2->bind_param("i", $id_promocion);
$st2->execute();
$row2 = $st2->get_result()->fetch_assoc();
if ($row2) {
    $promo["cantidad"] = $row2["cantidad"];
    $promo["Canjeados"] = $row2["Canjeados"];
}

$sinStock = (int) $promo["cantidad"] <= 0;

$rutaBase = defined("RUTA") ? RUTA : "/";
$logoSrc = rtrim($rutaBase, "/") . "/assets/img/LogoYolocal.png";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Canje de cupón — Yo Local</title>
    <link rel="icon" href="<?= htmlspecialchars($logoSrc) ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --yl-purple: #4c0682;
            --yl-purple-2: #613f9b;
            --yl-yellow: #ffcd00;
            --yl-bg: #f5f0fb;
            --yl-card: #ffffff;
            --yl-muted: #64748b;
        }
        * { box-sizing: border-box; }
        html {
            height: 100%;
        }
        body {
            margin: 0;
            min-height: 100vh;
            min-height: 100dvh;
            font-family: "Poppins", system-ui, sans-serif;
            background: linear-gradient(155deg, #ede8f7 0%, #f8f5fc 38%, #fff9e6 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 22px 18px;
        }
        .canje-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 18px;
            text-decoration: none;
            color: var(--yl-purple);
        }
        .canje-brand img {
            width: 48px;
            height: 48px;
            object-fit: contain;
            border-radius: 10px;
            box-shadow: 0 6px 16px rgba(76, 6, 130, 0.15);
        }
        .canje-brand span {
            font-weight: 800;
            font-size: 1.32rem;
            letter-spacing: -0.02em;
        }
        .canje-brand span em {
            font-style: normal;
            color: var(--yl-yellow);
            text-shadow: 0 0 0 1px rgba(76, 6, 130, 0.25);
        }
        .canje-card {
            width: 100%;
            max-width: 440px;
            background: var(--yl-card);
            border-radius: 22px;
            padding: 28px 28px 32px;
            min-height: 420px;
            box-shadow: 0 18px 48px rgba(76, 6, 130, 0.12), 0 2px 0 rgba(255, 255, 255, 0.8) inset;
            border: 1px solid rgba(76, 6, 130, 0.08);
            display: flex;
            flex-direction: column;
        }
        .canje-icon-wrap {
            width: 76px;
            height: 76px;
            margin: 0 auto 18px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--yl-purple), var(--yl-purple-2));
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.85rem;
            box-shadow: 0 10px 28px rgba(76, 6, 130, 0.28);
        }
        .promo-title {
            font-weight: 700;
            font-size: 1.22rem;
            color: #1e1b2e;
            text-align: center;
            margin: 0 0 8px;
            padding-top: 0.12em;
            line-height: 1.35;
        }
        /* Cabecera de la tarjeta: no debe encogerse cuando el panel de cámara pide mucha altura (flex). */
        .canje-card > .promo-title,
        .canje-card > .business-name,
        .canje-card > .stock-pill {
            flex-shrink: 0;
        }
        .canje-card--cam > .promo-title {
            display: block;
            -webkit-line-clamp: unset;
            line-clamp: unset;
            overflow: visible;
            word-break: break-word;
        }
        .business-name {
            text-align: center;
            color: var(--yl-muted);
            font-size: 0.92rem;
            font-weight: 500;
            margin-bottom: 20px;
        }
        .stock-pill {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 12px 16px;
            border-radius: 14px;
            background: linear-gradient(90deg, rgba(76, 6, 130, 0.08), rgba(255, 205, 0, 0.15));
            border: 1px solid rgba(76, 6, 130, 0.1);
            font-size: 0.88rem;
            color: var(--yl-purple);
            font-weight: 600;
            margin-bottom: 22px;
        }
        .stock-pill strong {
            font-size: 1.08rem;
        }
        .form-label-canje {
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            color: var(--yl-purple);
            text-transform: uppercase;
        }
        .form-control-canje {
            border-radius: 14px;
            padding: 14px 16px;
            text-align: center;
            font-size: 1.12rem;
            letter-spacing: 0.12em;
            border: 2px solid #e8e0f2;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-control-canje:focus {
            border-color: var(--yl-purple);
            box-shadow: 0 0 0 3px rgba(76, 6, 130, 0.12);
        }
        .btn-canje-primary {
            width: 100%;
            border: none;
            border-radius: 14px;
            padding: 14px 16px;
            font-weight: 700;
            font-size: 0.98rem;
            margin-top: 18px;
            background: linear-gradient(135deg, var(--yl-purple), var(--yl-purple-2));
            color: #fff;
            box-shadow: 0 10px 24px rgba(76, 6, 130, 0.25);
        }
        .btn-canje-primary:hover {
            filter: brightness(1.06);
            color: #fff;
        }
        .alert-canje {
            border-radius: 14px;
            border: none;
            font-size: 0.88rem;
        }
        .alert-canje.alert-success {
            background: #ecfdf5;
            color: #065f46;
        }
        .alert-canje.alert-danger {
            background: #fef2f2;
            color: #991b1b;
        }
        .alert-canje.alert-warning {
            background: #fffbeb;
            color: #92400e;
        }
        .canje-mode-tabs {
            display: flex;
            gap: 4px;
            margin-bottom: 18px;
            padding: 4px;
            border-radius: 14px;
            background: rgba(76, 6, 130, 0.08);
            border: 1px solid rgba(76, 6, 130, 0.06);
        }
        .canje-mode-tabs .canje-tab {
            flex: 1;
            border: none;
            background: transparent;
            padding: 10px 12px;
            border-radius: 11px;
            font-weight: 600;
            font-size: 0.88rem;
            color: var(--yl-muted);
            transition: background 0.2s, color 0.2s, box-shadow 0.2s;
        }
        .canje-mode-tabs .canje-tab:hover {
            color: var(--yl-purple);
        }
        .canje-mode-tabs .canje-tab[aria-selected="true"] {
            background: #fff;
            color: var(--yl-purple);
            box-shadow: 0 2px 10px rgba(76, 6, 130, 0.12);
        }
        .canje-panel-camara {
            display: none;
            flex-direction: column;
            flex: 1;
            min-height: 0;
        }
        .canje-card--cam .canje-panel-codigo {
            display: none !important;
        }
        .canje-card--cam .canje-panel-camara {
            display: flex;
        }
        .canje-card--cam .canje-mode-tabs {
            margin-bottom: 14px;
            flex-shrink: 0;
        }
        .canje-card--cam .canje-cam-hint {
            flex-shrink: 0;
        }
        #qrReaderWrap {
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid rgba(76, 6, 130, 0.12);
            background: #0f172a;
            flex: 1;
            min-height: min(52vh, 420px);
            display: flex;
            flex-direction: column;
        }
        #qrReader {
            flex: 1;
            min-height: min(48vh, 380px);
        }
        .canje-cam-hint {
            font-size: 0.82rem;
            color: var(--yl-muted);
            text-align: center;
            margin-bottom: 10px;
            line-height: 1.35;
        }
        .btn-outline-yolocal {
            border-radius: 12px;
            border: 2px solid #e8e0f2;
            color: var(--yl-purple);
            font-weight: 600;
        }
        .btn-outline-yolocal:hover {
            background: rgba(76, 6, 130, 0.06);
            color: var(--yl-purple);
        }
        .canje-dialog {
            width: min(430px, calc(100vw - 32px));
            padding: 0;
            overflow: hidden;
            border: 0;
            border-radius: 16px;
            background: #fff;
            color: #1e1b2e;
            box-shadow: 0 24px 70px rgba(35, 18, 57, 0.3);
        }
        .canje-dialog::backdrop {
            background: rgba(24, 18, 31, 0.62);
            backdrop-filter: blur(3px);
        }
        .canje-dialog-inner {
            padding: 30px 28px 26px;
            text-align: center;
        }
        .canje-dialog-icon {
            width: 68px;
            height: 68px;
            display: grid;
            place-items: center;
            margin: 0 auto 18px;
            border-radius: 50%;
            background: #fff4d6;
            color: #9a6700;
            font-size: 1.8rem;
        }
        .canje-dialog h2 {
            margin: 0 0 10px;
            color: #261c30;
            font-size: clamp(1.3rem, 4vw, 1.65rem);
            font-weight: 700;
            letter-spacing: -0.02em;
        }
        .canje-dialog p {
            margin: 0;
            color: #62596c;
            font-size: 0.94rem;
            line-height: 1.55;
        }
        .canje-dialog-note {
            display: block;
            margin-top: 10px;
            color: #766d80;
            font-size: 0.8rem;
        }
        .canje-dialog-button {
            width: 100%;
            min-height: 46px;
            margin-top: 24px;
            border: 0;
            border-radius: 12px;
            background: var(--yl-purple);
            color: #fff;
            font-weight: 700;
            cursor: pointer;
            transition: background-color 160ms ease-out, transform 160ms ease-out;
        }
        .canje-dialog-button:hover { background: #3d0569; }
        .canje-dialog-button:active { transform: scale(0.985); }
        .canje-dialog-button:focus-visible {
            outline: 3px solid rgba(76, 6, 130, 0.28);
            outline-offset: 3px;
        }

        @media (min-width: 769px) {
            html,
            body {
                height: 100%;
                max-height: 100%;
                overflow-x: hidden;
                overflow-y: auto;
            }
            body {
                padding: 16px 20px;
            }
            .canje-brand {
                margin-bottom: 14px;
            }
            .canje-brand img {
                width: 44px;
                height: 44px;
            }
            .canje-brand span {
                font-size: 1.28rem;
            }
            .canje-card {
                min-height: min(540px, calc(100dvh - 100px));
                max-height: calc(100dvh - 72px);
                overflow: hidden;
                padding: 32px 32px 36px;
            }
            .canje-card.canje-card--cam {
                overflow-y: auto;
                overflow-x: hidden;
            }
            .canje-card.canje-card--cam > .promo-title {
                display: block;
                -webkit-line-clamp: unset;
                line-clamp: unset;
                overflow: visible;
            }
            .canje-card.canje-card--cam #qrReaderWrap {
                min-height: min(420px, calc(100dvh - 260px));
            }
            .canje-card.canje-card--cam #qrReader {
                min-height: min(380px, calc(100dvh - 300px));
            }
            .canje-icon-wrap {
                width: 72px;
                height: 72px;
                margin-bottom: 16px;
                font-size: 1.75rem;
            }
            .promo-title {
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
                font-size: 1.18rem;
            }
            .business-name {
                margin-bottom: 16px;
                font-size: 0.94rem;
            }
            .stock-pill {
                margin-bottom: 20px;
                padding: 11px 15px;
            }
            .form-control-canje {
                padding: 15px 16px;
            }
            .btn-canje-primary {
                padding: 15px 18px;
                margin-top: 20px;
            }
        }
    </style>
</head>
<body>

<?php if ($modalCanje === "already_redeemed"): ?>
<dialog class="canje-dialog" id="modalCuponCanjeado" aria-labelledby="tituloCuponCanjeado" aria-describedby="mensajeCuponCanjeado">
    <div class="canje-dialog-inner">
        <div class="canje-dialog-icon" aria-hidden="true">
            <i class="bi bi-exclamation-circle-fill"></i>
        </div>
        <h2 id="tituloCuponCanjeado">Este cupón ya fue canjeado</h2>
        <p id="mensajeCuponCanjeado">El código ingresado ya se utilizó anteriormente y no puede volver a canjearse.</p>
        <span class="canje-dialog-note">No se descontaron unidades adicionales.</span>
        <button type="button" class="canje-dialog-button" id="cerrarModalCuponCanjeado">Entendido</button>
    </div>
</dialog>
<?php endif; ?>

<a class="canje-brand" href="<?= htmlspecialchars(rtrim($rutaBase, "/") . "/vistas/cuponesPagina.php") ?>" title="Ir a la cuponera">
    <img src="<?= htmlspecialchars($logoSrc) ?>" alt="Yo Local">
    <span>Y<em>o</em> Local</span>
</a>

<div class="canje-card" id="canjeCard">


    <h1 class="promo-title"><?= htmlspecialchars($promo["titulo"]) ?></h1>
    <p class="business-name"><?= htmlspecialchars($promo["nombre_negocio"]) ?></p>

    <div class="stock-pill">
        <?php if ($success): ?>
            <i class="bi bi-check2-circle"></i>
            Canje registrado. Quedan <strong><?= (int) $promo["cantidad"] ?></strong> por canjear · Total canjeados: <strong><?= (int) $promo["Canjeados"] ?></strong>
        <?php elseif ($sinStock): ?>
            <i class="bi bi-x-octagon"></i>
            <span>Sin cupones disponibles</span>
        <?php else: ?>
            <i class="bi bi-stack"></i>
            Disponibles: <strong><?= (int) $promo["cantidad"] ?></strong>
            · Canjeados: <strong><?= (int) $promo["Canjeados"] ?></strong>
        <?php endif; ?>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-canje alert-success py-3 px-3 mb-0" role="status">
            <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($success) ?>
        </div>
        <p class="text-center small text-muted mt-2 mb-0">Ese código de cupón ya no puede usarse de nuevo.</p>
        <button type="button" class="btn btn-outline-yolocal w-100 mt-3" onclick="window.close()">Cerrar ventana</button>
    <?php elseif ($sinStock): ?>
        <div class="alert alert-canje alert-danger py-3 px-3 mb-0" role="alert">
            <i class="bi bi-x-octagon me-2"></i>Este cupón ya no tiene unidades disponibles o fue reclamado en su totalidad.
        </div>
        <?php if ($error): ?>
            <div class="alert alert-canje alert-danger py-2 px-3 mt-2 mb-0" role="alert">
                <i class="bi bi-exclamation-circle me-1"></i><?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <form method="post" action="canje.php?id=<?= (int) $id_promocion ?>" id="formCanje" class="d-flex flex-column flex-grow-1" style="min-height:0;">
            <div class="canje-mode-tabs" role="tablist" aria-label="Forma de canje">
                <button type="button" class="canje-tab" role="tab" id="tabCodigo" aria-controls="panelCodigo" aria-selected="true">
                    <i class="bi bi-keyboard me-1"></i>Código
                </button>
                <button type="button" class="canje-tab" role="tab" id="tabCamara" aria-controls="panelCamara" aria-selected="false">
                    <i class="bi bi-camera-fill me-1"></i>Escanear QR
                </button>
            </div>

            <div id="panelCodigo" class="canje-panel-codigo" role="tabpanel" aria-labelledby="tabCodigo">
                <div class="mb-2">
                    <label for="codigo_cupon" class="form-label form-label-canje d-block text-center mb-2">Código de tu cupón</label>
                    <input type="text" name="codigo_cupon" id="codigo_cupon" class="form-control form-control-canje font-monospace" placeholder="000000-0-0000" value="<?= htmlspecialchars($codigo_precargado) ?>" required autocomplete="off" spellcheck="false" inputmode="text" style="letter-spacing:0.04em;">
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-canje alert-danger py-2 px-3 mt-2 mb-0" role="alert">
                        <i class="bi bi-exclamation-circle me-1"></i><?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <button type="submit" class="btn btn-canje-primary">Validar canje</button>
            </div>

            <div id="panelCamara" class="canje-panel-camara" role="tabpanel" aria-labelledby="tabCamara">
                <p class="canje-cam-hint mb-0">Apunte la cámara al QR (debe verse <strong>oscuro sobre fondo claro</strong>). Si su PDF es antiguo (QR blanco sobre morado), use la viñeta <strong>Código</strong> o vuelva a descargar el cupón. Puede abrir el QR del panel de administración.</p>
                <div id="qrReaderWrap" class="mt-3">
                    <div id="qrReader"></div>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
(function () {
    var modal = document.getElementById("modalCuponCanjeado");
    var cerrar = document.getElementById("cerrarModalCuponCanjeado");
    if (!modal) return;

    if (typeof modal.showModal === "function") {
        modal.showModal();
    } else {
        modal.setAttribute("open", "open");
    }

    if (cerrar) {
        cerrar.addEventListener("click", function () {
            if (typeof modal.close === "function") modal.close();
            else modal.removeAttribute("open");
        });
    }
})();

(function () {
    var card = document.getElementById("canjeCard");
    var wrap = document.getElementById("qrReaderWrap");
    var tabCodigo = document.getElementById("tabCodigo");
    var tabCamara = document.getElementById("tabCamara");
    var panelCodigo = document.getElementById("panelCodigo");
    var panelCamara = document.getElementById("panelCamara");
    var inputCodigo = document.getElementById("codigo_cupon");
    var readerEl = document.getElementById("qrReader");
    if (!card || !wrap || !tabCodigo || !tabCamara || !panelCodigo || !panelCamara || !readerEl || typeof Html5Qrcode === "undefined") return;

    var scanner = null;
    var started = false;
    var starting = false;
    var navigating = false;

    function canjePathFromText(text) {
        if (!text || typeof text !== "string") return null;
        var t = text.trim();
        try {
            var u = new URL(t, window.location.href);
            var id = u.searchParams.get("id");
            var path = (u.pathname || "").replace(/\\/g, "/");
            if (path.indexOf("canje.php") !== -1 && id && /^\d+$/.test(id)) {
                var c = u.searchParams.get("c");
                return "canje.php?id=" + id + (c ? "&c=" + encodeURIComponent(c) : "");
            }
        } catch (e) { /* ignore */ }
        var m = t.match(/canje\.php\?([^#]+)/i);
        if (m) return "canje.php?" + m[1].replace(/^&+/, "");
        return null;
    }

    function setTabsCodigo() {
        tabCodigo.setAttribute("aria-selected", "true");
        tabCamara.setAttribute("aria-selected", "false");
        tabCodigo.tabIndex = 0;
        tabCamara.tabIndex = -1;
    }

    function setTabsCamara() {
        tabCodigo.setAttribute("aria-selected", "false");
        tabCamara.setAttribute("aria-selected", "true");
        tabCodigo.tabIndex = -1;
        tabCamara.tabIndex = 0;
    }

    function stopCam() {
        starting = false;
        if (!scanner) {
            started = false;
            return Promise.resolve();
        }
        var s = scanner;
        scanner = null;
        started = false;
        return s.stop().then(function () {
            try { s.clear(); } catch (e) {}
        }).catch(function () {
            try { s.clear(); } catch (e2) {}
        });
    }

    function showCodigo() {
        card.classList.remove("canje-card--cam");
        setTabsCodigo();
        panelCodigo.setAttribute("aria-hidden", "false");
        panelCamara.setAttribute("aria-hidden", "true");
        if (inputCodigo) inputCodigo.setAttribute("required", "required");
    }

    function showCamara() {
        card.classList.add("canje-card--cam");
        setTabsCamara();
        panelCodigo.setAttribute("aria-hidden", "true");
        panelCamara.setAttribute("aria-hidden", "false");
        if (inputCodigo) inputCodigo.removeAttribute("required");
    }

    function startCamIfNeeded() {
        if (started || starting) return;
        if (scanner) return;
        starting = true;
        scanner = new Html5Qrcode("qrReader", false);
        var cfg = {
            fps: 8,
            rememberLastUsedCamera: true,
            experimentalFeatures: {
                useBarCodeDetectorIfSupported: true
            },
            qrbox: function (vw, vh) {
                var n = Math.floor(Math.min(vw, vh) * 0.88);
                return { width: Math.min(vw - 16, Math.max(160, n)), height: Math.min(vh - 16, Math.max(160, n)) };
            }
        };
        if (typeof Html5QrcodeSupportedFormats !== "undefined") {
            cfg.formatsToSupport = [Html5QrcodeSupportedFormats.QR_CODE];
        }
        var activeScanner = scanner;
        activeScanner.start(
            { facingMode: "environment" },
            cfg,
            function (decoded) {
                if (navigating) return;
                var path = canjePathFromText(decoded);
                if (!path) return;
                navigating = true;
                var sc = activeScanner;
                sc.stop().then(function () {
                    started = false;
                    starting = false;
                    try { sc.clear(); } catch (e) {}
                    if (scanner === sc) scanner = null;
                    window.location.href = path;
                }).catch(function () {
                    started = false;
                    starting = false;
                    if (scanner === sc) scanner = null;
                    window.location.href = path;
                });
            },
            function () {}
        ).then(function () {
            started = true;
            starting = false;
        }).catch(function () {
            started = false;
            starting = false;
            scanner = null;
            alert("No se pudo usar la cámara. Revise permisos o pruebe con otro navegador.");
            showCodigo();
        });
    }

    tabCodigo.addEventListener("click", function () {
        stopCam().then(function () {
            showCodigo();
        });
    });

    tabCamara.addEventListener("click", function () {
        showCamara();
        startCamIfNeeded();
    });

    tabCodigo.addEventListener("keydown", function (e) {
        if (e.key === "ArrowRight") {
            e.preventDefault();
            tabCamara.click();
            tabCamara.focus();
        }
    });
    tabCamara.addEventListener("keydown", function (e) {
        if (e.key === "ArrowLeft") {
            e.preventDefault();
            tabCodigo.click();
            tabCodigo.focus();
        }
    });

    showCodigo();
})();
</script>

</body>
</html>
<?php $enlace->close(); ?>

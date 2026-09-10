<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../controladores/controladorPagos.php';

$baseUrl = $baseUrl ?? '';
$idPago = (int)($_GET['id'] ?? 0);
$pago = $idPago > 0 ? obtenerPagoPorId($idPago) : null;

if (!$pago) {
    http_response_code(404);
}

$estadoPago = strtolower(trim((string)($pago['estado'] ?? 'pendiente')));
$estadoLabel = 'Pendiente';
$estadoClass = 'estado-pendiente';

if ($estadoPago === 'pagado') {
    $estadoLabel = 'Pagado';
    $estadoClass = 'estado-pagado';
} elseif ($estadoPago === 'atrasado' || $estadoPago === 'vencido') {
    $estadoLabel = 'Atraso';
    $estadoClass = 'estado-atraso';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo de pago - YoLocal</title>
    <?php include_once(__DIR__ . '/head.php'); ?>
    <style>
        body {
            background: #f4f6fb;
        }

        .recibo-wrap {
            max-width: 820px;
            margin: 24px auto;
            padding: 0 16px;
        }

        .recibo-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 14px 32px rgba(0, 0, 0, 0.12);
            overflow: hidden;
        }

        .recibo-header {
            background: linear-gradient(135deg, #0f172a, #1e293b);
            color: #fff;
            padding: 24px;
        }

        .recibo-header h1 {
            margin: 0;
            font-size: 1.6rem;
            font-weight: 800;
        }

        .recibo-header p {
            margin: 6px 0 0;
            opacity: 0.9;
        }

        .recibo-body {
            padding: 24px;
        }

        .recibo-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .dato {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 14px;
            background: #f8fafc;
        }

        .dato .label {
            display: block;
            font-size: 0.85rem;
            color: #64748b;
            margin-bottom: 4px;
            font-weight: 600;
        }

        .dato .value {
            color: #0f172a;
            font-size: 1rem;
            font-weight: 700;
            word-break: break-word;
        }

        .estado {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 6px 12px;
            border-radius: 999px;
            color: #fff;
            font-size: 0.82rem;
            font-weight: 700;
        }

        .estado-pagado { background: #16a34a; }
        .estado-pendiente { background: #f59e0b; }
        .estado-atraso { background: #dc2626; }

        .acciones {
            margin-top: 20px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-recibo {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            border-radius: 10px;
            padding: 10px 14px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
        }

        .btn-regresar {
            background: #111827;
            color: #fff;
        }

        .btn-imprimir {
            background: #009ee3;
            color: #fff;
        }

        .nota {
            margin-top: 18px;
            color: #64748b;
            font-size: 0.9rem;
        }

        .alerta {
            background: #fff1f2;
            color: #9f1239;
            border: 1px solid #fecdd3;
            border-radius: 12px;
            padding: 12px 14px;
            margin-top: 14px;
            font-weight: 600;
        }

        @media print {
            .acciones {
                display: none;
            }

            body {
                background: #fff;
            }

            .recibo-card {
                box-shadow: none;
                border: 1px solid #e5e7eb;
            }
        }

        @media (max-width: 680px) {
            .recibo-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
<div class="recibo-wrap">
    <div class="recibo-card">
        <div class="recibo-header">
            <h1>Recibo de pago</h1>
            <p>Administracion YoLocal</p>
        </div>

        <div class="recibo-body">
            <?php if (!$pago): ?>
                <div class="alerta">No se encontro el pago solicitado.</div>
                <div class="acciones">
                    <a class="btn-recibo btn-regresar" href="<?= htmlspecialchars($baseUrl) ?>index.php?pag=pagos_admin">Volver a pagos</a>
                </div>
            <?php else: ?>
                <div class="recibo-grid">
                    <div class="dato">
                        <span class="label">ID de pago</span>
                        <span class="value">#<?= (int)($pago['id'] ?? 0) ?></span>
                    </div>
                    <div class="dato">
                        <span class="label">Estado</span>
                        <span class="value"><span class="estado <?= htmlspecialchars($estadoClass) ?>"><?= htmlspecialchars($estadoLabel) ?></span></span>
                    </div>

                    <div class="dato">
                        <span class="label">Dueño</span>
                        <span class="value"><?= htmlspecialchars((string)($pago['nombre_dueno'] ?? '')) ?></span>
                    </div>
                    <div class="dato">
                        <span class="label">Local</span>
                        <span class="value"><?= htmlspecialchars((string)($pago['nombre_local'] ?? '')) ?></span>
                    </div>

                    <div class="dato">
                        <span class="label">ID local</span>
                        <span class="value"><?= htmlspecialchars((string)($pago['ID_Negocio'] ?? '')) ?></span>
                    </div>
                    <div class="dato">
                        <span class="label">Referencia</span>
                        <span class="value"><?= htmlspecialchars((string)($pago['referencia_pago'] ?? '')) ?></span>
                    </div>

                    <div class="dato">
                        <span class="label">Fecha de pago</span>
                        <span class="value"><?= htmlspecialchars((string)($pago['dia_pago'] ?? '')) ?></span>
                    </div>
                    <div class="dato">
                        <span class="label">Monto</span>
                        <span class="value">$<?= number_format((float)($pago['monto'] ?? 0), 2) ?> MXN</span>
                    </div>

                    <div class="dato">
                        <span class="label">Email</span>
                        <span class="value"><?= htmlspecialchars((string)($pago['email'] ?? '')) ?></span>
                    </div>
                    <div class="dato">
                        <span class="label">Creado</span>
                        <span class="value"><?= htmlspecialchars((string)($pago['fecha_creacion'] ?? '')) ?></span>
                    </div>
                </div>

                <p class="nota">Este comprobante muestra la informacion registrada en el sistema para el pago seleccionado.</p>

                <div class="acciones">
                    <a class="btn-recibo btn-regresar" href="<?= htmlspecialchars($baseUrl) ?>index.php?pag=pagos_admin">Volver a pagos</a>
                    <button type="button" class="btn-recibo btn-imprimir" onclick="window.print()">Imprimir recibo</button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>

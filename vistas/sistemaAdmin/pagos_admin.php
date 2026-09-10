<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../controladores/controladorPagos.php';

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cambiar_estado'])) {
    $id = (int)($_POST['id'] ?? 0);
    $estado = trim($_POST['estado'] ?? 'pendiente');
    if ($id > 0 && cambiarEstadoPago($id, $estado)) {
        $mensaje = 'Estado actualizado correctamente.';
    } else {
        $mensaje = 'No se pudo actualizar el estado.';
    }
}

$pagosAgrupados = agruparPagosAdministracion();
$pagosAlCorriente = $pagosAgrupados['al_corriente'];
$pagosPendientes = $pagosAgrupados['pendientes'] ?? [];
$pagosConAtraso = $pagosAgrupados['con_atraso'];
$sinPagos = $pagosAgrupados['sin_pagos'];
$errorConexion = $pagosAgrupados['error'] ?? '';

function renderFilaPagoAdmin(array $pago): void
{
    global $baseUrl;
    $estado = strtolower(trim((string) ($pago['estado'] ?? 'pendiente')));
    $diaPago = trim((string)($pago['dia_pago'] ?? ''));
    $hoy = date('Y-m-d');
    $badgeClass = 'badge-pendiente';
    $badgeText = 'Pendiente';
    if ($estado === 'pagado') {
        $badgeClass = 'badge-pagado';
        $badgeText = 'Pagado';
    } elseif (
        $estado === 'atrasado'
        || $estado === 'vencido'
        || ($estado === 'pendiente' && $diaPago !== '' && $diaPago < $hoy)
    ) {
        $badgeClass = 'badge-atraso';
        $badgeText = 'Atraso';
    }
    ?>
    <tr>
        <td><?= htmlspecialchars($pago['nombre_dueno'] ?? '') ?></td>
        <td><?= htmlspecialchars($pago['nombre_local'] ?? '') ?></td>
        <td><?= htmlspecialchars((string)($pago['ID_Negocio'] ?? '')) ?></td>
        <td><?= htmlspecialchars($pago['referencia_pago'] ?? '') ?></td>
        <td><?= htmlspecialchars($pago['dia_pago'] ?? '') ?></td>
        <td>$<?= number_format((float)($pago['monto'] ?? 0), 2) ?></td>
        <td><span class="<?= $badgeClass ?>"><?= $badgeText ?></span></td>
        <td>
            <?php if ($estado === 'pagado' && !empty($pago['id'])): ?>
                <a href="<?= htmlspecialchars($baseUrl) ?>index.php?pag=recibo_pago_admin&id=<?= (int)$pago['id'] ?>" target="_blank" class="btn btn-sm btn-outline-primary">Abrir</a>
            <?php else: ?>
                <span class="text-muted">No disponible</span>
            <?php endif; ?>
        </td>
        <td>
            <form method="post" class="d-flex gap-2">
                <input type="hidden" name="cambiar_estado" value="1">
                <input type="hidden" name="id" value="<?= (int)($pago['id'] ?? 0) ?>">
                <select name="estado" class="form-select form-select-sm">
                    <option value="pendiente" <?= $estado === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                    <option value="pagado" <?= $estado === 'pagado' ? 'selected' : '' ?>>Pagado</option>
                </select>
                <button class="btn btn-sm btn-dark">Guardar</button>
            </form>
        </td>
    </tr>
    <?php
}

function renderFilaSinPagoAdmin(array $fila): void
{
    $dueno = trim((string) (($fila['Nombre'] ?? '') . ' ' . ($fila['ApellidoP'] ?? '') . ' ' . ($fila['ApellidoM'] ?? '')));
    ?>
    <tr>
        <td><?= htmlspecialchars($dueno !== '' ? $dueno : 'Sin nombre registrado') ?></td>
        <td><?= htmlspecialchars($fila['nombre_negocio'] ?? '') ?></td>
        <td><?= htmlspecialchars($fila['ID_Negocio'] ?? '') ?></td>
        <td><span class="badge-sin-pago">Sin pagos</span></td>
    </tr>
    <?php
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de pagos</title>
    <?php $baseUrl = $baseUrl ?? ''; include_once(__DIR__ . '/head.php'); ?>
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/principal.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/pagos-responsive.css">
    <style>
        .pagos-table { background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 8px 20px rgba(0,0,0,.08); }
        .badge-pagado { background: #28a745; color: #fff; padding: 6px 10px; border-radius: 999px; font-size: 12px; }
        .badge-pendiente { background: #f59e0b; color: #fff; padding: 6px 10px; border-radius: 999px; font-size: 12px; }
        .badge-atraso { background: #dc3545; color: #fff; padding: 6px 10px; border-radius: 999px; font-size: 12px; }
        .badge-sin-pago { background: #6c757d; color: #fff; padding: 6px 10px; border-radius: 999px; font-size: 12px; }
        .seccion-pagos { background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 8px 20px rgba(0,0,0,.08); margin-bottom: 24px; }
        .buscador-pagos { padding: 0 1.5rem 1rem; }
        @media (max-width: 768px) {
            .content.p-4 {
                padding: 16px !important;
            }
            .buscador-pagos {
                padding: 0 1rem 1rem;
            }
            .seccion-pagos .p-4 {
                padding: 1rem !important;
            }
            .seccion-pagos form.d-flex {
                flex-direction: column;
            }
        }

        @media (max-width: 480px) {
            .content.p-4 {
                padding: 12px !important;
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
                <?php include_once(__DIR__ . "/notificaciones_dueno_btn.php"); ?>
            <div class="usuario"><img src="<?= $baseUrl ?>assets/img/descarga.gif" alt=""></div>
        </div>
    </div>

    <div class="content p-4">
        <?php if (!empty($errorConexion)): ?>
            <div class="alert alert-danger mb-3">
                <?= htmlspecialchars($errorConexion) ?> Revisa que MySQL esté iniciado en XAMPP y que la base de datos <strong>yolocal</strong> exista.
            </div>
        <?php endif; ?>

        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-success mb-3"><?= htmlspecialchars($mensaje) ?></div>
        <?php endif; ?>

        <div class="seccion-pagos">
            <div class="p-4 border-bottom">
                <h3 class="mb-1">Al corriente</h3>
                <p class="text-muted mb-0">Negocios con pago marcado como pagado.</p>
            </div>
            <div class="buscador-pagos">
                <input type="text" id="buscarAlCorriente" class="form-control" placeholder="Buscar por dueño, local o referencia" oninput="filtrarTablaPagos('buscarAlCorriente', 'tablaAlCorriente')">
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="tablaAlCorriente">
                    <thead>
                        <tr>
                            <th>Dueño</th>
                            <th>Local</th>
                            <th>ID Local</th>
                            <th>Referencia</th>
                            <th>Día</th>
                            <th>Monto</th>
                            <th>Estado</th>
                            <th>Recibo</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pagosAlCorriente)): ?>
                            <tr><td colspan="9" class="text-center py-4">No hay negocios al corriente.</td></tr>
                        <?php else: foreach ($pagosAlCorriente as $pago): renderFilaPagoAdmin($pago); endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="seccion-pagos">
            <div class="p-4 border-bottom">
                <h3 class="mb-1">Pagos pendientes</h3>
                <p class="text-muted mb-0">Pagos pendientes con fecha vigente o sin fecha asignada.</p>
            </div>
            <div class="buscador-pagos">
                <input type="text" id="buscarPendientes" class="form-control" placeholder="Buscar por dueño, local o referencia" oninput="filtrarTablaPagos('buscarPendientes', 'tablaPendientes')">
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="tablaPendientes">
                    <thead>
                        <tr>
                            <th>Dueño</th>
                            <th>Local</th>
                            <th>ID Local</th>
                            <th>Referencia</th>
                            <th>Día</th>
                            <th>Monto</th>
                            <th>Estado</th>
                            <th>Recibo</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pagosPendientes)): ?>
                            <tr><td colspan="9" class="text-center py-4">No hay pagos pendientes vigentes.</td></tr>
                        <?php else: foreach ($pagosPendientes as $pago): renderFilaPagoAdmin($pago); endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="seccion-pagos">
            <div class="p-4 border-bottom">
                <h3 class="mb-1">Con atraso</h3>
                <p class="text-muted mb-0">Negocios con pago pendiente y fecha vencida.</p>
            </div>
            <div class="buscador-pagos">
                <input type="text" id="buscarAtraso" class="form-control" placeholder="Buscar por dueño, local o referencia" oninput="filtrarTablaPagos('buscarAtraso', 'tablaAtraso')">
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="tablaAtraso">
                    <thead>
                        <tr>
                            <th>Dueño</th>
                            <th>Local</th>
                            <th>ID Local</th>
                            <th>Referencia</th>
                            <th>Fecha</th>
                            <th>Monto</th>
                            <th>Estado</th>
                            <th>Recibo</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pagosConAtraso)): ?>
                            <tr><td colspan="9" class="text-center py-4">No hay negocios con atraso.</td></tr>
                        <?php else: foreach ($pagosConAtraso as $pago): renderFilaPagoAdmin($pago); endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="seccion-pagos">
            <div class="p-4 border-bottom">
                <h3 class="mb-1">Sin pagos</h3>
                <p class="text-muted mb-0">Negocios que no tienen ningún registro de pago.</p>
            </div>
            <div class="buscador-pagos">
                <input type="text" id="buscarSinPagos" class="form-control" placeholder="Buscar por dueño o local" oninput="filtrarTablaPagos('buscarSinPagos', 'tablaSinPagos')">
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="tablaSinPagos">
                    <thead>
                        <tr>
                            <th>Dueño</th>
                            <th>Local</th>
                            <th>ID Negocio</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($sinPagos)): ?>
                            <tr><td colspan="4" class="text-center py-4">Todos los negocios tienen al menos un pago registrado.</td></tr>
                        <?php else: foreach ($sinPagos as $fila): renderFilaSinPagoAdmin($fila); endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    function filtrarTablaPagos(inputId, tableId) {
        const input = document.getElementById(inputId);
        const table = document.getElementById(tableId);
        if (!input || !table) return;

        const filtro = input.value.toLowerCase().trim();
        const filas = table.querySelectorAll('tbody tr');

        filas.forEach((fila) => {
            const texto = fila.textContent.toLowerCase();
            fila.style.display = texto.includes(filtro) ? '' : 'none';
        });
    }
</script>
<script src="<?= $baseUrl ?>assets/js/main.js"></script>
</body>
</html>


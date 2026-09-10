<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../controladores/controladorNotificaciones.php';

$baseUrl = $baseUrl ?? '';
$usuarioId = (int)($_SESSION['ID_Usuario'] ?? $_SESSION['id'] ?? 0);

if ($usuarioId <= 0) {
    header('Location: ' . $baseUrl . 'vistas/sistemaAdmin/login.php');
    exit;
}

$notificaciones = obtenerNotificacionesUsuario($usuarioId, 100);
$noLeidasCount = contarNotificacionesNoLeidas($usuarioId);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificaciones - Yo Local</title>
    <?php include_once(__DIR__ . '/head.php'); ?>
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/principal.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/usuarios.css">
    <style>
        .notif-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .notif-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .notif-header h2 {
            font-size: 28px;
            font-weight: 700;
            color: var(--texto2);
            margin: 0;
        }

        .btn-marcar-todo {
            background: var(--principal);
            color: #fff;
            border: none;
            padding: 10px 18px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-marcar-todo:hover {
            background: var(--principal2);
        }

        .notif-item {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 18px;
            margin-bottom: 15px;
            transition: all 0.2s;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 15px;
        }

        .notif-item:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border-color: var(--principal);
        }

        .notif-item.no-leida {
            background: #f0f7ff;
            border-left: 4px solid var(--principal);
        }

        .notif-content {
            flex: 1;
        }

        .notif-titulo {
            font-size: 15px;
            font-weight: 700;
            color: var(--texto2);
            margin: 0 0 6px 0;
        }

        .notif-mensaje {
            font-size: 14px;
            color: var(--texto);
            margin: 0 0 10px 0;
            line-height: 1.4;
        }

        .notif-fecha {
            font-size: 12px;
            color: var(--black2);
        }

        .notif-tipo {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            margin-top: 8px;
        }

        .notif-tipo.info {
            background: #dbeafe;
            color: #1e40af;
        }

        .notif-tipo.exito {
            background: #dcfce7;
            color: #166534;
        }

        .notif-tipo.advertencia {
            background: #fef3c7;
            color: #92400e;
        }

        .notif-tipo.error {
            background: #fee2e2;
            color: #991b1b;
        }

        .notif-acciones {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .btn-accion {
            background: none;
            border: 1px solid var(--lineas);
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.2s;
            color: var(--texto);
            font-weight: 500;
        }

        .btn-accion:hover {
            border-color: var(--principal);
            color: var(--principal);
        }

        .notif-empty {
            text-align: center;
            padding: 60px 20px;
            color: var(--black2);
        }

        .notif-empty svg {
            width: 80px;
            height: 80px;
            margin-bottom: 20px;
            opacity: 0.3;
        }

        .badge-no-leidas {
            display: inline-block;
            background: #ef4444;
            color: #fff;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            margin-left: 10px;
        }

        .btn-regresar-inicio {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #fff;
            color: var(--principal);
            border: 1px solid var(--lineas);
            border-radius: 8px;
            padding: 8px 14px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            margin-bottom: 16px;
            transition: all 0.2s;
        }

        .btn-regresar-inicio:hover {
            border-color: var(--principal);
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            color: var(--principal2);
        }
    </style>
</head>
<body class="bg-light">
    <div class="navigation admin-sidebar">
        <?php include_once(__DIR__ . '/encabezado.php'); ?>
    </div>
    <div class="main">
        <div class="topbar">
            <div class="toggle">
                <svg class="svg" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </div>
            <div class="contenedor">
                <?php include_once(__DIR__ . "/calendario_btn.php"); ?>
                <?php include_once(__DIR__ . '/notificaciones_dueno_btn.php'); ?>
                <div class="usuario">
                    <img src="<?= $baseUrl ?>assets/img/descarga.gif" alt="">
                </div>
            </div>
        </div>

        <div class="content p-4">
            <div class="notif-container">
                <a href="#" class="btn-regresar-inicio" onclick="regresarPaginaAnterior(event)">
                    <i class="bi bi-arrow-left"></i>
                    Regresar
                </a>
                <div class="notif-header">
                    <div>
                        <h2>Mis Notificaciones
                            <?php if ($noLeidasCount > 0): ?>
                                <span class="badge-no-leidas"><?= $noLeidasCount ?></span>
                            <?php endif; ?>
                        </h2>
                        <p style="margin: 8px 0 0 0; color: var(--black2); font-size: 14px;">
                            Aquí puedes ver todas tus notificaciones en un solo lugar
                        </p>
                    </div>
                    <?php if ($noLeidasCount > 0): ?>
                        <button class="btn-marcar-todo" onclick="marcarTodoLeido()">
                            Marcar todo como leído
                        </button>
                    <?php endif; ?>
                </div>

                <div id="notificaciones-list">
                    <?php if (empty($notificaciones)): ?>
                        <div class="notif-empty">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0M3.124 7.5A8.969 8.969 0 0 1 5.292 3m13.416 0a8.969 8.969 0 0 1 2.168 4.5" />
                            </svg>
                            <h3>No hay notificaciones</h3>
                            <p>Cuando tengas nuevas notificaciones, aparecerán aquí</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($notificaciones as $notif): ?>
                            <div class="notif-item <?= $notif['leida'] ? '' : 'no-leida' ?>" data-id="<?= $notif['id'] ?>">
                                <div class="notif-content">
                                    <h3 class="notif-titulo"><?= htmlspecialchars($notif['titulo']) ?></h3>
                                    <p class="notif-mensaje"><?= htmlspecialchars($notif['mensaje']) ?></p>
                                    <span class="notif-tipo <?= htmlspecialchars($notif['tipo']) ?>">
                                        <?= ucfirst($notif['tipo']) ?>
                                    </span>
                                    <div class="notif-fecha">
                                        <?= date('d/m/Y H:i', strtotime($notif['fecha_creacion'])) ?>
                                    </div>
                                </div>
                                <div class="notif-acciones">
                                    <?php if (!$notif['leida']): ?>
                                        <button class="btn-accion" onclick="marcarLeida(<?= $notif['id'] ?>)">
                                            Marcar leída
                                        </button>
                                    <?php endif; ?>
                                    <?php if ($notif['url_accion']): ?>
                                        <a href="<?= htmlspecialchars($notif['url_accion']) ?>" class="btn-accion" style="text-decoration: none; text-align: center;">
                                            Ver más
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="<?= $baseUrl ?>assets/js/main.js"></script>
    <script>
        function marcarLeida(idNotificacion) {
            fetch('<?= $baseUrl ?>controladores/controladorNotificaciones.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'action=marcar_leida&id=' + idNotificacion
            })
            .then(res => res.json())
            .then(data => {
                if (data.ok) {
                    const item = document.querySelector(`[data-id="${idNotificacion}"]`);
                    if (item) {
                        item.classList.remove('no-leida');
                        item.querySelector('.btn-accion:first-child')?.remove();
                    }
                    location.reload();
                }
            });
        }

        function marcarTodoLeido() {
            if (!confirm('Â¿Marcar todas como leídas?')) return;
            
            fetch('<?= $baseUrl ?>controladores/controladorNotificaciones.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'action=marcar_todo_leido'
            })
            .then(res => res.json())
            .then(data => {
                if (data.ok) {
                    location.reload();
                }
            });
        }

        function regresarPaginaAnterior(event) {
            event.preventDefault();

            if (window.history.length > 1) {
                window.history.back();
                return;
            }

            window.location.href = '<?= $baseUrl ?>index.php?pag=home';
        }
    </script>
</body>
</html>


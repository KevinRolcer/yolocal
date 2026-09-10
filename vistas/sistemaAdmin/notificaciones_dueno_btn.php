<?php
$mostrarBotonNotificacionesDueno = (($_SESSION['tipo'] ?? '') === 'negocio') || (($_SESSION['tipo'] ?? '') === 'admin');
$notificacionesNoLeidasDueno = 0;
$notificacionesListaDueno = [];

if ($mostrarBotonNotificacionesDueno) {
    if (!function_exists('contarNotificacionesNoLeidas')) {
        require_once __DIR__ . '/../../controladores/controladorNotificaciones.php';
    }
    $usuarioNotificacionId = (int)($_SESSION['ID_Usuario'] ?? $_SESSION['id'] ?? 0);

    if ($usuarioNotificacionId > 0) {
        $notificacionesNoLeidasDueno = contarNotificacionesNoLeidas($usuarioNotificacionId);
        $notificacionesListaDueno    = obtenerNotificacionesUsuario($usuarioNotificacionId, 30);
    }
}
?>
<?php if ($mostrarBotonNotificacionesDueno): ?>

<!-- Estilos del panel emergente -->
<style>
#notif-backdrop {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.25);
    z-index: 1040;
}
#notif-panel {
    display: none;
    position: fixed;
    top: 0;
    right: 0;
    width: 380px;
    max-width: 95vw;
    height: 100vh;
    background: #fff;
    box-shadow: -4px 0 24px rgba(0,0,0,0.15);
    z-index: 1050;
    flex-direction: column;
    border-radius: 16px 0 0 16px;
    overflow: hidden;
    transform: translateX(100%);
    transition: transform 0.3s cubic-bezier(0.4,0,0.2,1);
}
#notif-panel.abierto {
    display: flex;
    transform: translateX(0);
}
#notif-backdrop.abierto {
    display: block;
}
.notif-panel-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px 20px 16px;
    border-bottom: 1px solid #f0f0f0;
    background: #f8f4ff;
    flex-shrink: 0;
}
.notif-panel-titulo {
    font-size: 17px;
    font-weight: 700;
    color: var(--principal, #4c0682);
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0;
}
.notif-panel-badge {
    background: #ef4444;
    color: #fff;
    font-size: 11px;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 999px;
}
.notif-panel-cerrar {
    background: none;
    border: none;
    cursor: pointer;
    color: #6b7280;
    padding: 4px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    transition: background 0.2s;
}
.notif-panel-cerrar:hover { background: #f3f4f6; }
.notif-panel-acciones {
    display: flex;
    gap: 8px;
    padding: 10px 20px;
    border-bottom: 1px solid #f0f0f0;
    flex-shrink: 0;
}
.notif-panel-btn-todo {
    background: var(--principal, #4c0682);
    color: #fff;
    border: none;
    padding: 7px 14px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
}
.notif-panel-btn-todo:hover { background: var(--principal2, #6a0dad); }
.notif-panel-body {
    flex: 1;
    overflow-y: auto;
    padding: 12px 16px;
}
.notif-panel-item {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: 14px;
    margin-bottom: 10px;
    transition: all 0.2s;
    position: relative;
}
.notif-panel-item:hover { box-shadow: 0 3px 10px rgba(0,0,0,0.08); }
.notif-panel-item.no-leida {
    background: #f5f0ff;
    border-left: 3px solid var(--principal, #4c0682);
}
.notif-panel-item-titulo {
    font-size: 14px;
    font-weight: 700;
    color: #1f2937;
    margin: 0 0 5px 0;
}
.notif-panel-item-msg {
    font-size: 13px;
    color: #4b5563;
    margin: 0 0 8px 0;
    line-height: 1.4;
}
.notif-panel-item-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 6px;
}
.notif-panel-tipo {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 5px;
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
}
.notif-panel-tipo.info       { background: #dbeafe; color: #1e40af; }
.notif-panel-tipo.exito      { background: #dcfce7; color: #166534; }
.notif-panel-tipo.advertencia{ background: #fef3c7; color: #92400e; }
.notif-panel-tipo.error      { background: #fee2e2; color: #991b1b; }
.notif-panel-fecha {
    font-size: 11px;
    color: #9ca3af;
}
.notif-panel-item-btns {
    display: flex;
    gap: 6px;
    margin-top: 8px;
}
.notif-panel-item-btn {
    background: none;
    border: 1px solid #d1d5db;
    padding: 4px 10px;
    border-radius: 5px;
    font-size: 11px;
    cursor: pointer;
    color: #374151;
    font-weight: 500;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    transition: all 0.2s;
}
.notif-panel-item-btn:hover { border-color: var(--principal, #4c0682); color: var(--principal, #4c0682); }
.notif-panel-vacio {
    text-align: center;
    padding: 50px 20px;
    color: #9ca3af;
}
.notif-panel-vacio svg { width: 60px; height: 60px; margin-bottom: 12px; opacity: 0.3; }
</style>

<!-- Botón campana -->
<button id="notif-btn-abrir"
        title="Ver notificaciones"
        onclick="abrirPanelNotif()"
        style="position: relative; width: 42px; height: 42px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; background: #f6f1ff; color: var(--principal, #4c0682); border: 1px solid rgba(76,6,130,0.2); cursor: pointer; transition: transform 0.2s, box-shadow 0.2s; box-shadow: 0 3px 10px rgba(76,6,130,0.15);"
        onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 6px 14px rgba(76,6,130,0.2)'"
        onmouseout="this.style.transform='';this.style.boxShadow='0 3px 10px rgba(76,6,130,0.15)'">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width:22px;height:22px;">
        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9a6 6 0 1 0-12 0v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
    </svg>
    <?php if ($notificacionesNoLeidasDueno > 0): ?>
        <span id="notif-badge" style="position:absolute;top:-4px;right:-2px;min-width:18px;height:18px;border-radius:999px;background:#ef4444;color:#fff;font-size:11px;font-weight:700;display:inline-flex;align-items:center;justify-content:center;padding:0 5px;line-height:1;border:2px solid #fff;">
            <?= $notificacionesNoLeidasDueno > 9 ? '9+' : $notificacionesNoLeidasDueno ?>
        </span>
    <?php else: ?>
        <span id="notif-badge" style="display:none;position:absolute;top:-4px;right:-2px;min-width:18px;height:18px;border-radius:999px;background:#ef4444;color:#fff;font-size:11px;font-weight:700;display:none;align-items:center;justify-content:center;padding:0 5px;line-height:1;border:2px solid #fff;"></span>
    <?php endif; ?>
</button>

<!-- Fondo oscuro -->
<div id="notif-backdrop" onclick="cerrarPanelNotif()"></div>

<!-- Panel lateral emergente -->
<div id="notif-panel">
    <div class="notif-panel-header">
        <h3 class="notif-panel-titulo">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width:20px;height:20px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9a6 6 0 1 0-12 0v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
            </svg>
            Notificaciones
            <?php if ($notificacionesNoLeidasDueno > 0): ?>
                <span class="notif-panel-badge" id="notif-panel-badge-count"><?= $notificacionesNoLeidasDueno ?></span>
            <?php endif; ?>
        </h3>
        <button class="notif-panel-cerrar" onclick="cerrarPanelNotif()" title="Cerrar">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:20px;height:20px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <?php if ($notificacionesNoLeidasDueno > 0): ?>
    <div class="notif-panel-acciones">
        <button class="notif-panel-btn-todo" onclick="notifMarcarTodo()">
            <i class="bi bi-check2-all"></i> Marcar todo como leído
        </button>
    </div>
    <?php endif; ?>

    <div class="notif-panel-body" id="notif-panel-lista">
        <?php if (empty($notificacionesListaDueno)): ?>
            <div class="notif-panel-vacio">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                </svg>
                <p style="margin:0;font-size:14px;">No hay notificaciones</p>
            </div>
        <?php else: ?>
            <?php foreach ($notificacionesListaDueno as $nItem): ?>
                <div class="notif-panel-item <?= $nItem['leida'] ? '' : 'no-leida' ?>" id="notif-item-<?= $nItem['id'] ?>">
                    <p class="notif-panel-item-titulo"><?= htmlspecialchars($nItem['titulo']) ?></p>
                    <p class="notif-panel-item-msg"><?= htmlspecialchars($nItem['mensaje']) ?></p>
                    <div class="notif-panel-item-footer">
                        <span class="notif-panel-tipo <?= htmlspecialchars($nItem['tipo']) ?>"><?= ucfirst($nItem['tipo']) ?></span>
                        <span class="notif-panel-fecha"><?= date('d/m/Y H:i', strtotime($nItem['fecha_creacion'])) ?></span>
                    </div>
                    <div class="notif-panel-item-btns">
                        <?php if (!$nItem['leida']): ?>
                            <button class="notif-panel-item-btn" onclick="notifMarcarLeida(<?= $nItem['id'] ?>)">
                                <i class="bi bi-check2" style="margin-right:3px;"></i> Marcar leída
                            </button>
                        <?php endif; ?>
                        <?php if (!empty($nItem['url_accion'])): ?>
                            <a href="<?= htmlspecialchars($nItem['url_accion']) ?>" class="notif-panel-item-btn">
                                <i class="bi bi-arrow-right" style="margin-right:3px;"></i> Ver más
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
function abrirPanelNotif() {
    document.getElementById('notif-panel').classList.add('abierto');
    document.getElementById('notif-backdrop').classList.add('abierto');
    document.body.style.overflow = 'hidden';
}
function cerrarPanelNotif() {
    document.getElementById('notif-panel').classList.remove('abierto');
    document.getElementById('notif-backdrop').classList.remove('abierto');
    document.body.style.overflow = '';
}
function notifMarcarLeida(id) {
    fetch('<?= $baseUrl ?>controladores/controladorNotificaciones.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=marcar_leida&id=' + id
    })
    .then(r => r.json())
    .then(data => {
        if (data.ok) {
            const item = document.getElementById('notif-item-' + id);
            if (item) {
                item.classList.remove('no-leida');
                const btn = item.querySelector('.notif-panel-item-btn');
                if (btn && btn.textContent.trim().includes('leída')) btn.remove();
            }
            actualizarBadgeNotif();
        }
    });
}
function notifMarcarTodo() {
    fetch('<?= $baseUrl ?>controladores/controladorNotificaciones.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=marcar_todo_leido'
    })
    .then(r => r.json())
    .then(data => {
        if (data.ok) {
            document.querySelectorAll('.notif-panel-item.no-leida').forEach(el => el.classList.remove('no-leida'));
            document.querySelectorAll('.notif-panel-item-btn').forEach(btn => {
                if (btn.textContent.trim().includes('leída')) btn.remove();
            });
            actualizarBadgeNotif(0);
            const accionesBar = document.querySelector('.notif-panel-acciones');
            if (accionesBar) accionesBar.remove();
        }
    });
}
function actualizarBadgeNotif(forzar) {
    const noLeidas = (forzar !== undefined) ? forzar : document.querySelectorAll('.notif-panel-item.no-leida').length;
    const badge = document.getElementById('notif-badge');
    const badgePanel = document.getElementById('notif-panel-badge-count');
    if (badge) {
        if (noLeidas > 0) {
            badge.style.display = 'inline-flex';
            badge.textContent = noLeidas > 9 ? '9+' : noLeidas;
        } else {
            badge.style.display = 'none';
        }
    }
    if (badgePanel) {
        if (noLeidas > 0) {
            badgePanel.textContent = noLeidas;
        } else {
            badgePanel.remove();
        }
    }
}
// Cerrar con tecla Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') cerrarPanelNotif();
});
</script>

<?php endif; ?>

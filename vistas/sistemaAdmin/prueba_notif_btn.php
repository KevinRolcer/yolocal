<?php
// Solo visible para el beta tester (ID_Usuario = 235)
$_pnUsuarioId = (int)($_SESSION['ID_Usuario'] ?? $_SESSION['id'] ?? 0);
if ($_pnUsuarioId !== 235) return;
$baseUrl = $baseUrl ?? '';
?>

<button id="btn-prueba-notif"
        title="Generar notificación de prueba"
        onclick="generarNotifPrueba()"
        style="position: relative; width: 42px; height: 42px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; background: #fff8e1; color: #b45309; border: 1px solid rgba(180,83,9,0.3); cursor: pointer; transition: transform 0.2s, box-shadow 0.2s; box-shadow: 0 3px 10px rgba(180,83,9,0.15);"
        onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 6px 14px rgba(180,83,9,0.25)'"
        onmouseout="this.style.transform='';this.style.boxShadow='0 3px 10px rgba(180,83,9,0.15)'">
    <!-- Ícono matraz de prueba -->
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width:20px;height:20px;">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 0 1-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 0 1 4.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15M14.25 3.104c.251.023.501.05.75.082M19.8 15a2.25 2.25 0 0 1 .75 1.901v.102a2.25 2.25 0 0 1-2.25 2.25H5.7a2.25 2.25 0 0 1-2.25-2.25v-.102c0-.73.31-1.428.75-1.9M19.8 15H4.2" />
    </svg>
</button>

<script>
function generarNotifPrueba() {
    const btn = document.getElementById('btn-prueba-notif');
    btn.disabled = true;
    btn.style.opacity = '0.6';

    fetch('<?= $baseUrl ?>controladores/controladorNotificaciones.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=crear_prueba'
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        btn.style.opacity = '1';
        if (data.ok) {
            // Textos por tipo para el toast
            const textos = {
                info:        { t: '🔔 Notificación de prueba',        m: 'Esta es una notificación informativa de prueba. Así se verá en el panel.' },
                exito:       { t: '✅ ¡Pago registrado con éxito!',    m: 'Tu aportación simbólica fue recibida correctamente. ¡Gracias por apoyar lo local! 💜' },
                advertencia: { t: '⚠️ Tu aportación vence en 1 semana', m: '¡Que no se te pase! En una semana toca renovar tu aportación simbólica. Sigamos haciendo crecer lo local 💜' },
                error:       { t: '❌ Tu aportación vence mañana',     m: '¡Mañana vence tu aportación simbólica! Renueva a tiempo para seguir disfrutando de todos los beneficios de Yo Local 💛' },
            };
            const tipo = data.tipo || 'info';
            const txt  = textos[tipo] || textos.info;

            // Mostrar toast en pantalla
            if (typeof mostrarToast === 'function') {
                mostrarToast(txt.t, txt.m, tipo, 5000);
            }

            // Actualizar badge de la campana
            const badge = document.getElementById('notif-badge');
            if (badge) {
                const actual = parseInt(badge.textContent) || 0;
                badge.textContent = actual + 1 > 9 ? '9+' : actual + 1;
                badge.style.display = 'inline-flex';
            }
            // Feedback visual en el botón
            btn.style.background = '#dcfce7';
            btn.style.color = '#166534';
            btn.style.borderColor = '#86efac';
            setTimeout(() => {
                btn.style.background = '#fff8e1';
                btn.style.color = '#b45309';
                btn.style.borderColor = 'rgba(180,83,9,0.3)';
            }, 1200);
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.style.opacity = '1';
    });
}
</script>

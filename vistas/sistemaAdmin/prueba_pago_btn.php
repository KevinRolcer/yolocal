<?php
// Solo visible para el beta tester (ID_Usuario = 235)
$_ppUsuarioId = (int)($_SESSION['ID_Usuario'] ?? $_SESSION['id'] ?? 0);
if ($_ppUsuarioId !== 235) return;
$baseUrl = $baseUrl ?? '';
?>

<!-- Botón para simular pago en Aliados Impulso -->
<button id="btn-prueba-pago-aportacion"
        title="Simular pago completado en Aliados Impulso"
        onclick="simularPago('aportacion')"
        style="position: relative; width: 42px; height: 42px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; background: #e0f7ff; color: #0369a1; border: 1px solid rgba(3,105,161,0.3); cursor: pointer; transition: transform 0.2s, box-shadow 0.2s; box-shadow: 0 3px 10px rgba(3,105,161,0.15);"
        onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 6px 14px rgba(3,105,161,0.25)'"
        onmouseout="this.style.transform='';this.style.boxShadow='0 3px 10px rgba(3,105,161,0.15)'">
    <i class="bi bi-cash-coin" style="font-size:20px;"></i>
</button>

<!-- Botón para simular pago en Pagos normales -->
<button id="btn-prueba-pago-normal"
        title="Simular pago completado en Pagos"
        onclick="simularPago('pagos')"
        style="position: relative; width: 42px; height: 42px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; background: #dcfce7; color: #16a34a; border: 1px solid rgba(22,163,74,0.3); cursor: pointer; transition: transform 0.2s, box-shadow 0.2s; box-shadow: 0 3px 10px rgba(22,163,74,0.15); margin-left: 8px;"
        onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 6px 14px rgba(22,163,74,0.25)'"
        onmouseout="this.style.transform='';this.style.boxShadow='0 3px 10px rgba(22,163,74,0.15)'">
    <i class="bi bi-credit-card" style="font-size:20px;"></i>
</button>

<!-- Botón para simular pago en Mis pagos -->
<button id="btn-prueba-pago-pagos"
        title="Simular pago completado en Mis pagos"
        onclick="simularPago('pagos_usuario')"
        style="position: relative; width: 42px; height: 42px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; background: #fce7f3; color: #ec4899; border: 1px solid rgba(236,72,153,0.3); cursor: pointer; transition: transform 0.2s, box-shadow 0.2s; box-shadow: 0 3px 10px rgba(236,72,153,0.15); margin-left: 8px;"
        onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 6px 14px rgba(236,72,153,0.25)'"
        onmouseout="this.style.transform='';this.style.boxShadow='0 3px 10px rgba(236,72,153,0.15)'">
    <i class="bi bi-wallet2" style="font-size:20px;"></i>
</button>

<script>
function simularPago(pagina) {
    const url = '<?= $baseUrl ?>index.php?pag=' + pagina + '&estado=approved';
    window.location.href = url;
}
</script>


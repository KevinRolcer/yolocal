<?php $baseUrl = $baseUrl ?? ''; ?>

<!-- Estilos del panel de calendario -->
<style>
#cal-backdrop {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.25);
    z-index: 1040;
}
#cal-panel {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) scale(0.92);
    width: 340px;
    max-width: 95vw;
    background: #fff;
    box-shadow: 0 20px 60px rgba(76,6,130,0.2);
    z-index: 1050;
    border-radius: 20px;
    overflow: hidden;
    opacity: 0;
    transition: opacity 0.25s ease, transform 0.25s cubic-bezier(0.4,0,0.2,1);
}
#cal-panel.abierto {
    display: block;
    opacity: 1;
    transform: translate(-50%, -50%) scale(1);
}
#cal-backdrop.abierto {
    display: block;
}

/* Encabezado del panel */
.cal-panel-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 20px 12px;
    background: var(--principal, #4c0682);
    color: #fff;
}
.cal-panel-titulo {
    font-size: 15px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0;
}
.cal-panel-cerrar {
    background: rgba(255,255,255,0.15);
    border: none;
    cursor: pointer;
    color: #fff;
    padding: 5px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    transition: background 0.2s;
}
.cal-panel-cerrar:hover { background: rgba(255,255,255,0.3); }

/* Calendario */
.cal-box {
    padding: 20px;
    user-select: none;
}
.cal-nav {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
}
.cal-nav-btn {
    background: #f6f1ff;
    border: none;
    width: 34px;
    height: 34px;
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--principal, #4c0682);
    font-size: 16px;
    transition: background 0.2s;
}
.cal-nav-btn:hover { background: #ede7ff; }
.cal-mes-anio {
    font-size: 15px;
    font-weight: 700;
    color: #1f2937;
}
.cal-dias-semana {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    margin-bottom: 8px;
}
.cal-dias-semana span {
    text-align: center;
    font-size: 11px;
    font-weight: 700;
    color: #9ca3af;
    padding: 4px 0;
    text-transform: uppercase;
}
.cal-fechas {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 2px;
    list-style: none;
    margin: 0;
    padding: 0;
}
.cal-fechas li {
    text-align: center;
    padding: 7px 2px;
    font-size: 13px;
    border-radius: 8px;
    cursor: default;
    color: #374151;
    font-weight: 500;
    transition: background 0.15s;
}
.cal-fechas li.inactivo {
    color: #d1d5db;
    font-weight: 400;
}
.cal-fechas li.hoy {
    background: var(--principal, #4c0682);
    color: #fff;
    font-weight: 700;
    border-radius: 50%;
}
.cal-pie {
    padding: 12px 20px 16px;
    border-top: 1px solid #f0f0f0;
    text-align: center;
    font-size: 12px;
    color: #9ca3af;
}
.cal-pie span {
    font-weight: 600;
    color: var(--principal, #4c0682);
}
</style>

<!-- Botón calendario -->
<button id="cal-btn-abrir"
        title="Ver calendario"
        onclick="abrirPanelCal()"
        style="position: relative; width: 42px; height: 42px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; background: #f6f1ff; color: var(--principal, #4c0682); border: 1px solid rgba(76,6,130,0.2); cursor: pointer; transition: transform 0.2s, box-shadow 0.2s; box-shadow: 0 3px 10px rgba(76,6,130,0.15);"
        onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 6px 14px rgba(76,6,130,0.2)'"
        onmouseout="this.style.transform='';this.style.boxShadow='0 3px 10px rgba(76,6,130,0.15)'">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width:22px;height:22px;">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25m10.5-2.25v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25M3 18.75A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75M3 10.5h18" />
    </svg>
</button>

<!-- Fondo oscuro -->
<div id="cal-backdrop" onclick="cerrarPanelCal()"></div>

<!-- Panel emergente calendario -->
<div id="cal-panel">
    <div class="cal-panel-header">
        <h3 class="cal-panel-titulo">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width:18px;height:18px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25m10.5-2.25v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25M3 18.75A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75M3 10.5h18" />
            </svg>
            Calendario
        </h3>
        <button class="cal-panel-cerrar" onclick="cerrarPanelCal()" title="Cerrar">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:18px;height:18px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <div class="cal-box">
        <div class="cal-nav">
            <button class="cal-nav-btn" id="cal-prev" onclick="calNavegar(-1)">&#8249;</button>
            <span class="cal-mes-anio" id="cal-mes-anio">...</span>
            <button class="cal-nav-btn" id="cal-next" onclick="calNavegar(1)">&#8250;</button>
        </div>
        <div class="cal-dias-semana">
            <span>Do</span><span>Lu</span><span>Ma</span><span>Mi</span>
            <span>Ju</span><span>Vi</span><span>Sa</span>
        </div>
        <ul class="cal-fechas" id="cal-fechas"></ul>
    </div>

    <div class="cal-pie">
        Hoy: <span id="cal-hoy-texto"></span>
    </div>
</div>

<script>
(function () {
    const meses = [
        'Enero','Febrero','Marzo','Abril','Mayo','Junio',
        'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'
    ];
    const diasSemana = ['Do','Lu','Ma','Mi','Ju','Vi','Sa'];

    let _hoy   = new Date();
    let _mes   = _hoy.getMonth();
    let _anio  = _hoy.getFullYear();

    function renderCal() {
        const primero   = new Date(_anio, _mes, 1).getDay();
        const totalDias = new Date(_anio, _mes + 1, 0).getDate();
        const ultDiaMes = new Date(_anio, _mes, totalDias).getDay();
        const diasMesAnterior = new Date(_anio, _mes, 0).getDate();

        let html = '';

        // Días del mes anterior (relleno)
        for (let i = primero; i > 0; i--) {
            html += `<li class="inactivo">${diasMesAnterior - i + 1}</li>`;
        }

        // Días del mes actual
        for (let d = 1; d <= totalDias; d++) {
            const esHoy = d === _hoy.getDate()
                       && _mes  === new Date().getMonth()
                       && _anio === new Date().getFullYear();
            html += `<li${esHoy ? ' class="hoy"' : ''}>${d}</li>`;
        }

        // Días del mes siguiente (relleno)
        for (let i = ultDiaMes; i < 6; i++) {
            html += `<li class="inactivo">${i - ultDiaMes + 1}</li>`;
        }

        document.getElementById('cal-fechas').innerHTML = html;
        document.getElementById('cal-mes-anio').textContent = `${meses[_mes]} ${_anio}`;

        // Pie: fecha de hoy
        const hoyReal = new Date();
        const opciones = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        document.getElementById('cal-hoy-texto').textContent =
            hoyReal.toLocaleDateString('es-MX', opciones);
    }

    window.calNavegar = function (dir) {
        _mes += dir;
        if (_mes < 0)  { _mes = 11; _anio--; }
        if (_mes > 11) { _mes = 0;  _anio++; }
        renderCal();
    };

    window.abrirPanelCal = function () {
        renderCal();
        document.getElementById('cal-panel').classList.add('abierto');
        document.getElementById('cal-backdrop').classList.add('abierto');
        document.body.style.overflow = 'hidden';
    };

    window.cerrarPanelCal = function () {
        document.getElementById('cal-panel').classList.remove('abierto');
        document.getElementById('cal-backdrop').classList.remove('abierto');
        document.body.style.overflow = '';
    };

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') cerrarPanelCal();
    });
})();
</script>

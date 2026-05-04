<!DOCTYPE html>
<html lang="es">

<head>
    <title>Trabajos y Eventos - Yo Local</title>
    <?php
    $adminCssFiles = ["assets/css/bolsaTrabajoAdmon.css", "assets/css/eventosModern.css", "assets/css/paginacion.css", "assets/css/pildora.css", "assets/css/modal-detalles-evento.css", "assets/css/adminFormal.css"];
    include_once("head.php");
    ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css">
    <script>
        if (typeof usuarioId === 'undefined') {
            var usuarioId = <?= json_encode($_SESSION["ID_Usuario"]) ?>;
        }
        if (typeof usuarioTipo === 'undefined') {
            var usuarioTipo = <?= json_encode($_SESSION["tipo"]) ?>;
        }

        // SPA Navigation Logic
        function switchView(view) {
            const jobsView = document.getElementById('jobs-content-area');
            const eventsView = document.getElementById('events-content-area');
            const pillTrabajos = document.getElementById('opcion1');
            const pillEventos = document.getElementById('opcion2');
            const jobsActions = document.getElementById('jobs-actions');
            const eventsActions = document.getElementById('events-actions');

            if (view === 'jobs') {
                if(jobsView) jobsView.classList.remove('d-none');
                if(eventsView) eventsView.classList.add('d-none');
                if(pillTrabajos) pillTrabajos.classList.add('active');
                if(pillEventos) pillEventos.classList.remove('active');
                if(jobsActions) jobsActions.classList.remove('d-none');
                if(eventsActions) eventsActions.classList.add('d-none');
                
                if (typeof window.listarPromociones === 'function') window.listarPromociones();
                window.history.pushState({view: 'jobs'}, '', 'index.php?pag=bolsa_trabajo&vista=trabajos');
            } else {
                if(jobsView) jobsView.classList.add('d-none');
                if(eventsView) eventsView.classList.remove('d-none');
                if(pillTrabajos) pillTrabajos.classList.remove('active');
                if(pillEventos) pillEventos.classList.add('active');
                if(jobsActions) jobsActions.classList.add('d-none');
                if(eventsActions) eventsActions.classList.remove('d-none');
                
                // Ensure categories and list are loaded
                if (typeof window.cargarCategorias === 'function') window.cargarCategorias();
                if (typeof window.listarEventosEnTarjetas === 'function') window.listarEventosEnTarjetas();
                
                window.history.pushState({view: 'events'}, '', 'index.php?pag=bolsa_trabajo&vista=eventos');
            }
        }

        window.onpopstate = function(event) {
            if (event.state && event.state.view) {
                switchView(event.state.view === 'jobs' ? 'jobs' : 'events');
            }
        };

        function handleGlobalSearch(val) {
            const isJobs = !document.getElementById('jobs-content-area').classList.contains('d-none');
            if (isJobs) {
                if (typeof window.buscarTrabajos === 'function') window.buscarTrabajos(val);
            } else {
                if (typeof window.filtrarEventos === 'function') window.filtrarEventos(val);
            }
        }
    </script>
</head>

<body class="bolsa-trabajo-page">
    <div class="navigation admin-sidebar">
        <?php
        include_once("encabezado.php")
        ?>
    </div>
    <div class="main">
        <div class="topbar">
            <div class="toggle">
                <i class="ri-menu-2-line admin-menu-icon" aria-hidden="true"></i>
            </div>

            <div class="contenedor">
                <div class="notificacion" onclick="toggleNotifi()">
                    <i class="ri-notification-3-line"></i>
                </div>
                <div class="usuario">
                    <img src="assets/img/descarga.gif"  alt="">
                </div>
            </div>
        </div>

        <div id="spa-content-container">
            <!-- COMMON HEADER AREA -->
            <div class="container-fluid job-admin-shell">
                <div class="job-board-header">
                    <div class="job-search-row">
                        <i class="ri-search-line"></i>
                        <input type="text" id="globalSearchInput" placeholder="Buscar..." onkeyup="handleGlobalSearch(this.value)">
                    </div>

                    <div class="job-header-actions">
                        <div class="pill-selector">
                            <button onclick="switchView('jobs')" class="pill-option active" id="opcion1">
                                Trabajos
                            </button>
                            <?php if ($_SESSION["tipo"] === "admin"): ?>
                            <button onclick="switchView('events')" class="pill-option" id="opcion2">
                                Eventos
                            </button>
                            <?php endif; ?>
                        </div>

                        <div id="jobs-actions" class="job-primary-action">
                            <?php if ($_SESSION["tipo"] === "admin" || $_SESSION["tipo"] === "negocio"): ?>
                                <button type="button" class="btn btn-primary px-4 rounded-pill" data-bs-toggle="modal" data-bs-target="#modalPromocion">
                                    <i class="ri-add-line me-1"></i> Publicar
                                </button>
                            <?php endif; ?>
                        </div>
                        <div id="events-actions" class="job-primary-action d-none">
                            <?php if ($_SESSION["tipo"] === "admin"): ?>
                                <button type="button" class="btn btn-primary px-4 rounded-pill" data-bs-toggle="modal" data-bs-target="#modalEvento">
                                    <i class="ri-add-line me-1"></i> Cargar
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- JOBS CONTENT -->
            <div id="jobs-content-area">
                <div class="container-fluid job-admin-shell">
                    <div class="job-toolbar-minimal">
                        <div class="job-pills-container" id="jobFilterPills">
                            <button class="job-pill active" data-filter="all">Todos</button>
                            <button class="job-pill" data-filter="Turno Completo">Tiempo completo</button>
                            <button class="job-pill" data-filter="Matutino">Matutino</button>
                            <button class="job-pill" data-filter="Vespertino">Vespertino</button>
                            <button class="job-pill" data-filter="Horas">Por horas</button>
                            <button class="job-pill" data-filter="__SIN_TURNO__">Sin turno</button>
                            <button class="job-pill" id="limpiarFiltros">Limpiar filtros</button>
                        </div>
                    </div>

                    <div class="job-split-container">
                        <div class="job-list-panel">
                            <div class="job-list-heading">
                                <div>
                                    <h2>Vacantes publicadas</h2>
                                </div>
                                <span class="job-results-count" id="jobsContador">Cargando...</span>
                            </div>
                            <div class="job-cards-list" id="contenedor"></div>
                        </div>
                        <div class="job-detail-panel" id="jobDetailPanel">
                            <div class="text-center p-5 text-muted">
                                <i class="ri-information-line ri-3x mb-3 d-block"></i>
                                <p>Selecciona un trabajo para ver los detalles aquí.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- EVENTS CONTENT -->
            <div id="events-content-area" class="d-none">
                <div class="container-fluid job-admin-shell">
                    <div class="job-toolbar-minimal">
                        <div class="job-pills-container" id="eventFilterPills">
                            <button class="job-pill active" data-filter="all">Todos</button>
                            <button class="job-pill" id="limpiarFiltrosEventos">Limpiar filtros</button>
                        </div>
                    </div>
                    <div class="eventos-grid" id="contenedorEventos"></div>
                </div>
            </div>
        </div>

        <!-- MODALS -->
        <?php include_once("modales_trabajos_eventos.php"); ?>
    </div>

    <script src="assets/js/main.js"></script>
    <script type="module" src="assets/js/funcionesTrabajos.js"></script>
    <script type="module" src="assets/js/funcionesEventos.js"></script>
    <script src="assets/js/modal-detalles-evento.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            const pag = urlParams.get('pag');
            const vista = urlParams.get('vista');
            if (pag === 'eventos' || vista === 'eventos') {
                switchView('events');
            } else if (vista === 'trabajos') {
                switchView('jobs');
            }
        });
    </script>
</body>
</html>

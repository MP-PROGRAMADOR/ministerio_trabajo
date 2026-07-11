<?php
session_start();

// ===== VERIFICAR SESIÓN =====
if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../login_desempleados.php');
    exit();
}

$titulo = 'Bolsa de Trabajo - Portal de Empleo';
include '../componentes/header_desempleado.php';
include '../conexion/conexion.php';

$id_usuario = $_SESSION['id_usuario'];
$nombre_completo = $_SESSION['nombre_completo'] ?? '';

// ===== VERIFICAR PERFIL EN BBDD =====
try {
    $perfil_completo = false;
    $buscador = null;

    // Verificar buscadores_empleo
    $stmt = $pdo->prepare("SELECT * FROM buscadores_empleo WHERE usuario_id = ?");
    $stmt->execute([$id_usuario]);
    $buscador = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar documentos
    $stmt_doc = $pdo->prepare("SELECT id FROM documentos WHERE usuario_id = ?");
    $stmt_doc->execute([$id_usuario]);
    $documentos = $stmt_doc->fetch();
    
    $perfil_completo = ($buscador && $documentos);

    // ===== OBTENER OFERTAS DE EMPLEO DESDE BBDD =====
    $stmt = $pdo->prepare("
        SELECT 
            o.*, 
            e.nombre_empresa,
            e.sector_industrial,
            u.nombre,
            u.apellidos
        FROM ofertas_empleo o
        JOIN empleadores e ON o.empleador_id = e.id
        JOIN usuarios u ON e.usuario_id = u.id
        WHERE o.estado = 'abierta'
        ORDER BY o.fecha_publicacion DESC
    ");
    $stmt->execute();
    $ofertas_bd = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ===== OBTENER SECTORES ÚNICOS PARA FILTROS =====
    $stmt = $pdo->prepare("SELECT DISTINCT sector_industrial FROM empleadores ORDER BY sector_industrial");
    $stmt->execute();
    $sectores = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // ===== OBTENER CIUDADES ÚNICAS PARA FILTROS =====
    $stmt = $pdo->prepare("SELECT DISTINCT ciudad_municipio FROM buscadores_empleo ORDER BY ciudad_municipio");
    $stmt->execute();
    $ciudades = $stmt->fetchAll(PDO::FETCH_COLUMN);

} catch (PDOException $e) {
    error_log("Error en bolsa de trabajo: " . $e->getMessage());
    $perfil_completo = false;
    $buscador = null;
    $ofertas_bd = [];
    $sectores = [];
    $ciudades = [];
}

// ===== INCLUIR MENÚ =====
include '../componentes/menu_desempleado.php';
?>

<style>
    /* ===== PALETA INSTITUCIONAL UNIFICADA ===== */
    :root {
        --gov-blue: #0B3A60;
        --gov-blue-light: #165285;
        --gov-gold: #C9A84C;
        --gov-gold-light: #E8D5A3;
        --gov-green: #1E7E34;
        --gov-green-light: #2E9B4A;
        --gov-dark: #0A192F;
        --gov-bg: #F8FAFC;
        --gov-border: #E2E8F0;
        --gov-radius: 8px;
        --gov-radius-sm: 4px;
    }

    body, html {
        min-height: 100vh;
        margin: 0;
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        background-color: var(--gov-bg);
        color: var(--gov-dark);
        position: relative;
    }
    #canvas-background {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 0;
        pointer-events: none;
        opacity: 0.3;
    }
    .main-wrapper {
        position: relative;
        z-index: 1;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    /* ===== NAVBAR ===== */
    .navbar-portal {
        background: rgba(255, 255, 255, 0.96) !important;
        backdrop-filter: blur(12px);
        border-bottom: 3px solid var(--gov-blue);
        box-shadow: 0 4px 20px rgba(11, 58, 96, 0.04);
        position: relative;
        z-index: 1050;
    }
    .navbar-nav .nav-link {
        font-weight: 500;
        color: var(--gov-dark) !important;
        padding: 0.5rem 1rem;
        border-radius: var(--gov-radius-sm);
        transition: all 0.2s;
    }
    .navbar-nav .nav-link:hover,
    .navbar-nav .nav-link.active {
        background: rgba(11, 58, 96, 0.06);
        color: var(--gov-blue) !important;
    }
    .navbar-nav .nav-link.active {
        background: rgba(11, 58, 96, 0.10);
        font-weight: 600;
    }

    /* ===== DROPDOWN PERFIL ===== */
    .profile-dropdown { position: relative !important; }
    .profile-menu-img {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        border: 2.5px solid var(--gov-gold);
        object-fit: cover;
        cursor: pointer;
        transition: border-color 0.3s, transform 0.2s;
    }
    .profile-menu-img:hover {
        border-color: var(--gov-blue);
        transform: scale(1.04);
    }
    .custom-profile-menu {
        background: rgba(255, 255, 255, 0.98) !important;
        backdrop-filter: blur(12px);
        border: 1px solid rgba(201, 168, 76, 0.25) !important;
        border-radius: var(--gov-radius) !important;
        box-shadow: 0 12px 40px rgba(11, 58, 96, 0.12) !important;
        padding: 8px !important;
        z-index: 1060 !important;
        min-width: 240px;
    }
    .custom-profile-menu .dropdown-item {
        border-radius: var(--gov-radius-sm);
        padding: 0.6rem 1rem;
        font-weight: 500;
        color: var(--gov-dark);
        transition: all 0.15s;
    }
    .custom-profile-menu .dropdown-item:hover {
        background: rgba(11, 58, 96, 0.05);
        color: var(--gov-blue);
    }
    .custom-profile-menu .dropdown-item i { color: var(--gov-gold); }
    @media (min-width: 992px) {
        .custom-profile-menu {
            position: absolute !important;
            right: 0 !important;
            left: auto !important;
            transform: translateY(8px) !important;
        }
        .profile-dropdown:hover .custom-profile-menu {
            display: block !important;
            opacity: 1 !important;
            visibility: visible !important;
        }
    }
    @media (max-width: 991.98px) {
        .custom-profile-menu {
            position: absolute !important;
            left: 50% !important;
            right: auto !important;
            transform: translate(-50%, 10px) !important;
            width: 260px !important;
            max-width: calc(100vw - 30px) !important;
        }
        .profile-dropdown:hover .custom-profile-menu {
            display: block !important;
            opacity: 1 !important;
            visibility: visible !important;
        }
    }

    /* ===== TARJETAS ===== */
    .dashboard-card {
        background: #ffffff;
        border: 1px solid var(--gov-border);
        border-radius: var(--gov-radius);
        box-shadow: 0 4px 12px rgba(11, 58, 96, 0.015);
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .dashboard-card:hover {
        border-color: var(--gov-gold);
        box-shadow: 0 10px 25px rgba(11, 58, 96, 0.04);
        transform: translateY(-1px);
    }

    /* ===== TARJETAS DE OFERTA ===== */
    .job-card {
        background: #ffffff;
        border: 1px solid var(--gov-border);
        border-radius: var(--gov-radius);
        padding: 1.25rem 1.5rem;
        transition: all 0.25s;
        position: relative;
        overflow: hidden;
        min-height: 180px;
    }
    .job-card:hover {
        border-color: var(--gov-blue);
        box-shadow: 0 6px 20px rgba(11, 58, 96, 0.08);
        transform: translateY(-2px);
    }
    .job-card .card-accent {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
    }
    .job-card .card-accent.blue { background: var(--gov-blue); }
    .job-card .card-accent.gold { background: var(--gov-gold); }
    .job-card .card-accent.green { background: var(--gov-green); }

    .job-card .job-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--gov-dark);
    }
    .job-card .company-name {
        font-weight: 600;
        color: var(--gov-blue);
    }
    .job-card .job-meta {
        font-size: 0.85rem;
        color: #6b7a8a;
    }
    .job-card .job-meta i {
        width: 1.2rem;
        color: var(--gov-gold);
    }

    /* Badges */
    .badge-blue {
        background: var(--gov-blue);
        color: white;
        font-weight: 600;
        padding: 0.3rem 0.9rem;
        border-radius: 20px;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .badge-gold {
        background: var(--gov-gold);
        color: white;
        font-weight: 600;
        padding: 0.3rem 0.9rem;
        border-radius: 20px;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .badge-green {
        background: var(--gov-green);
        color: white;
        font-weight: 600;
        padding: 0.3rem 0.9rem;
        border-radius: 20px;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .badge-urgent {
        background: #DC3545;
        color: white;
        font-weight: 600;
        padding: 0.3rem 0.9rem;
        border-radius: 20px;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .badge-soft-blue {
        background: rgba(11, 58, 96, 0.08);
        color: var(--gov-blue);
        font-weight: 500;
        padding: 0.3rem 0.9rem;
        border-radius: 20px;
        font-size: 0.75rem;
    }

    /* ===== BOTONES ===== */
    .btn-blue {
        background: var(--gov-blue);
        border: none;
        color: white;
        border-radius: var(--gov-radius-sm);
        padding: 0.6rem 1.8rem;
        font-weight: 600;
        transition: all 0.25s;
        box-shadow: 0 4px 12px rgba(11, 58, 96, 0.15);
    }
    .btn-blue:hover {
        background: var(--gov-blue-light);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(11, 58, 96, 0.20);
        color: white;
    }
    .btn-gold {
        background: var(--gov-gold);
        border: none;
        color: white;
        border-radius: var(--gov-radius-sm);
        padding: 0.6rem 1.8rem;
        font-weight: 600;
        transition: all 0.25s;
        box-shadow: 0 4px 12px rgba(201, 168, 76, 0.15);
    }
    .btn-gold:hover {
        background: #B8953A;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(201, 168, 76, 0.20);
        color: white;
    }
    .btn-green {
        background: var(--gov-green);
        border: none;
        color: white;
        border-radius: var(--gov-radius-sm);
        padding: 0.6rem 1.8rem;
        font-weight: 600;
        transition: all 0.25s;
        box-shadow: 0 4px 12px rgba(30, 126, 52, 0.15);
    }
    .btn-green:hover {
        background: var(--gov-green-light);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(30, 126, 52, 0.20);
        color: white;
    }
    .btn-outline-secondary {
        border: 2px solid var(--gov-border);
        color: var(--gov-blue);
        background: transparent;
        border-radius: var(--gov-radius-sm);
        font-weight: 500;
        transition: all 0.25s;
    }
    .btn-outline-secondary:hover {
        background: var(--gov-blue);
        color: white;
        border-color: var(--gov-blue);
    }

    /* ===== FILTROS ===== */
    .form-control-search {
        border-radius: var(--gov-radius-sm) !important;
        border: 1.5px solid var(--gov-border);
        padding: 0.7rem 1.2rem;
        font-size: 0.95rem;
        transition: border-color 0.3s, box-shadow 0.3s;
        width: 100%;
    }
    .form-control-search:focus {
        border-color: var(--gov-blue);
        box-shadow: 0 0 0 4px rgba(11, 58, 96, 0.12);
    }
    .form-select-custom {
        border-radius: var(--gov-radius-sm);
        border: 1.5px solid var(--gov-border);
        padding: 0.7rem 1.2rem;
        font-size: 0.95rem;
        transition: border-color 0.3s;
    }
    .form-select-custom:focus {
        border-color: var(--gov-gold);
        box-shadow: 0 0 0 4px rgba(201, 168, 76, 0.12);
    }

    /* ===== PAGINACIÓN ===== */
    .pagination-custom .page-link {
        border-radius: var(--gov-radius-sm) !important;
        margin: 0 4px;
        border: 1px solid var(--gov-border);
        color: var(--gov-blue);
        transition: all 0.2s;
    }
    .pagination-custom .page-link:hover {
        background: var(--gov-gold);
        color: white;
        border-color: var(--gov-gold);
    }
    .pagination-custom .page-item.active .page-link {
        background: var(--gov-blue);
        border-color: var(--gov-blue);
        color: white;
    }

    /* ===== MODALES ===== */
    .modal-content {
        border-radius: var(--gov-radius);
        border: none;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        overflow: hidden;
    }
    .modal-header {
        border-bottom: 2px solid var(--gov-gold-light);
        background: rgba(255, 255, 255, 0.95);
        padding: 1.5rem 2rem;
    }
    .modal-header .modal-title {
        font-weight: 700;
        color: var(--gov-blue);
    }
    .modal-body {
        padding: 2rem;
        background: #FAFBFC;
    }
    .modal-footer {
        border-top: 1px solid rgba(0, 0, 0, 0.05);
        background: white;
        padding: 1.2rem 2rem;
    }
    .form-control-custom {
        border-radius: var(--gov-radius-sm);
        border: 1.5px solid var(--gov-border);
        padding: 0.6rem 1rem;
        transition: border-color 0.3s, box-shadow 0.3s;
    }
    .form-control-custom:focus {
        border-color: var(--gov-blue);
        box-shadow: 0 0 0 4px rgba(11, 58, 96, 0.10);
    }

    /* ALERTA */
    .alert-profile-incomplete {
        background-color: #FFF5F5;
        border: 1px solid #FEE2E2;
        border-left: 5px solid #DC3545;
        border-radius: var(--gov-radius);
        color: #7F1D1D;
    }
    .registro-pendiente {
        background: #f0f7ff;
        border: 1px solid #cfe2ff;
        border-radius: var(--gov-radius);
        padding: 1.2rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
        margin-bottom: 1.5rem;
    }
    .registro-pendiente .icono {
        font-size: 2rem;
        color: var(--gov-blue);
    }
    .registro-pendiente .texto {
        flex: 1;
        font-size: 0.95rem;
        color: var(--gov-dark);
    }
    .registro-pendiente .texto strong {
        color: var(--gov-blue);
    }
    .registro-pendiente .btn-link {
        color: var(--gov-blue);
        font-weight: 600;
        text-decoration: none;
        border-bottom: 2px solid transparent;
        transition: all 0.2s;
    }
    .registro-pendiente .btn-link:hover {
        border-bottom-color: var(--gov-blue);
    }

    footer {
        background: rgba(255, 255, 255, 0.9) !important;
        backdrop-filter: blur(8px);
        border-top: 1px solid rgba(201, 168, 76, 0.15);
    }

    @media (max-width: 576px) {
        .dashboard-card { padding: 1.5rem !important; }
        .job-card { padding: 1rem !important; }
        .modal-body { padding: 1.2rem; }
        .modal-header { padding: 1.2rem; }
    }
</style>

<body>
    <canvas id="canvas-background"></canvas>
    <div class="main-wrapper">

        <main class="container py-5 flex-grow-1">

            <!-- ALERTA EXPEDIENTE INCOMPLETO -->
            <?php if (!$perfil_completo): ?>
                <div id="incompleteProfileBanner" class="alert alert-profile-incomplete p-4 mb-4">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                        <div class="d-flex align-items-start gap-3">
                            <i class="bi bi-exclamation-octagon-fill text-danger fs-3 mt-1"></i>
                            <div>
                                <h4 class="fw-bold m-0 h5 text-dark">Expediente Digital Incompleto</h4>
                                <p class="m-0 text-muted small mt-1">Conforme a la normativa del Sistema Nacional de Empleo, debe completar su perfil adjuntando su Documento de Identidad Personal (DIP) y su Currículum Vitae para acceder a la visualización de ofertas laborales vigentes.</p>
                            </div>
                        </div>
                        <a href="completar_perfil.php" class="btn btn-danger btn-sm text-nowrap px-4 py-2 rounded-pill fw-bold shadow-sm">
                            <i class="bi bi-pencil-square me-1"></i> Completar Perfil Ahora
                        </a>
                    </div>
                </div>

                <div class="registro-pendiente mb-4">
                    <div class="icono"><i class="bi bi-info-circle-fill"></i></div>
                    <div class="texto">
                        <strong>¡Atención!</strong> Para acceder a todas las funcionalidades del portal (postulaciones, ofertas personalizadas, cursos, etc.) debe completar su registro en el <a href="completar_perfil.php" class="btn-link">Sistema Nacional de Empleo</a>.
                    </div>
                    <a href="completar_perfil.php" class="btn btn-gov btn-sm rounded-pill px-4">Completar registro</a>
                </div>
            <?php endif; ?>

            <!-- ===== CONTENIDO PRINCIPAL ===== -->
            <?php if ($perfil_completo): ?>
                <div class="row g-4">
                    <!-- FILTROS DE BÚSQUEDA -->
                    <div class="col-12">
                        <div class="card dashboard-card p-4">
                            <h5 class="fw-bold mb-3 h6 text-uppercase tracking-wider text-muted"><i class="bi bi-funnel me-2"></i>Filtros de Búsqueda</h5>
                            <form id="filterForm">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <input type="text" id="searchInput" class="form-control form-control-search" placeholder="Buscar por título o empresa..." value="">
                                    </div>
                                    <div class="col-md-3">
                                        <select id="sectorSelect" class="form-select form-select-custom">
                                            <option value="">Todos los sectores</option>
                                            <?php foreach ($sectores as $sector): ?>
                                                <option value="<?php echo htmlspecialchars($sector); ?>"><?php echo htmlspecialchars($sector); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select id="citySelect" class="form-select form-select-custom">
                                            <option value="">Todas las ciudades</option>
                                            <?php foreach ($ciudades as $ciudad): ?>
                                                <option value="<?php echo htmlspecialchars($ciudad); ?>"><?php echo ucfirst(str_replace('_', ' ', htmlspecialchars($ciudad))); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-outline-secondary w-100" id="clearFiltersBtn">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i> Limpiar
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- SECCIÓN DE OFERTAS DE EMPLEO -->
                    <div class="col-12">
                        <div class="card dashboard-card p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="fw-bold m-0 h6 text-uppercase tracking-wider text-muted"><i class="bi bi-briefcase me-2"></i>Ofertas de Empleo (<span id="jobCount"><?php echo count($ofertas_bd); ?></span>)</h5>
                                <span class="badge-soft-blue">Últimos 30 días</span>
                            </div>
                            <div id="jobList" class="row g-3">
                                <!-- Las tarjetas se generan con JavaScript -->
                            </div>
                            <nav class="mt-4">
                                <ul class="pagination justify-content-center pagination-custom" id="paginationControls"></ul>
                            </nav>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </main>

        <!-- MODAL POSTULACIÓN -->
        <div class="modal fade" id="postulacionModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-pencil-square me-2" style="color: var(--gov-green);"></i>Postularse a: <span id="postulacionTitulo">Oferta</span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted small mb-3"><i class="bi bi-building me-1"></i>Empresa: <strong id="postulacionEmpresa">-</strong></p>
                        <form id="postulacionForm">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nombre completo *</label>
                                <input type="text" id="postulacionNombre" class="form-control form-control-custom" placeholder="Ej: Juan Carlos Nsue" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Documento de Identidad (DIP) *</label>
                                <input type="text" id="postulacionDIP" class="form-control form-control-custom" placeholder="Ej: 123456789" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Teléfono *</label>
                                <input type="tel" id="postulacionTelefono" class="form-control form-control-custom" placeholder="Ej: 555-123456" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Correo electrónico *</label>
                                <input type="email" id="postulacionEmail" class="form-control form-control-custom" placeholder="ejemplo@correo.gq" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Mensaje / Carta de presentación</label>
                                <textarea id="postulacionMensaje" class="form-control form-control-custom" rows="3" placeholder="Cuéntenos por qué es el candidato ideal..."></textarea>
                            </div>
                            <p class="text-muted small">* Campos obligatorios</p>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-green" id="enviarPostulacionBtn"><i class="bi bi-send me-2"></i>Enviar postulación</button>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL CONFIRMACIÓN -->
        <div class="modal fade" id="confirmacionPostulacionModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header" style="border-bottom-color: var(--gov-green);">
                        <h5 class="modal-title"><i class="bi bi-check-circle-fill me-2" style="color: var(--gov-green);"></i>Postulación enviada</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center py-4">
                        <i class="bi bi-hourglass-split" style="font-size: 4rem; color: var(--gov-gold);"></i>
                        <h5 class="mt-3 fw-bold">¡Su postulación ha sido recibida!</h5>
                        <p class="text-muted">La empresa recibirá sus datos y se pondrá en contacto en caso de ser seleccionado. Recibirá notificaciones sobre el estado de su candidatura.</p>
                        <div class="alert alert-info mt-3" role="alert">
                            <i class="bi bi-info-circle me-2"></i> Puede hacer seguimiento de sus postulaciones desde el <strong>Panel General</strong>.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-blue" data-bs-dismiss="modal">Entendido</button>
                    </div>
                </div>
            </div>
        </div>

        <?php include '../componentes/footer_desempleado.php'; ?>

        <script>
            // ===== DATOS DE OFERTAS DESDE PHP =====
            const jobs = <?php 
                $jobs_array = [];
                foreach ($ofertas_bd as $oferta) {
                    // Determinar color de acento según sector
                    $colors = ['blue', 'gold', 'green'];
                    $color = $colors[array_rand($colors)];
                    
                    // Badge según antigüedad
                    $badge = null;
                    $badgeColor = null;
                    $fecha = new DateTime($oferta['fecha_publicacion']);
                    $hoy = new DateTime();
                    $diff = $hoy->diff($fecha)->days;
                    
                    if ($diff <= 3) {
                        $badge = 'Nuevo';
                        $badgeColor = 'gold';
                    } elseif ($diff <= 7) {
                        $badge = 'Reciente';
                        $badgeColor = 'blue';
                    }
                    
                    $jobs_array[] = [
                        'id' => $oferta['id'],
                        'title' => $oferta['titulo_puesto'],
                        'company' => $oferta['nombre_empresa'],
                        'city' => ucfirst(str_replace('_', ' ', $oferta['provincia'] ?? 'No especificada')),
                        'sector' => $oferta['sector_industrial'] ?? 'Otros',
                        'date' => date('d/m/Y', strtotime($oferta['fecha_publicacion'])),
                        'contract' => 'Indefinido',
                        'jornada' => 'Completa',
                        'badge' => $badge,
                        'badgeColor' => $badgeColor,
                        'accent' => $color,
                        'descripcion' => $oferta['descripcion'] ?? '',
                        'requisitos' => $oferta['requisitos'] ?? '',
                        'salario' => $oferta['salario_ofrecido'] ?? null
                    ];
                }
                echo json_encode($jobs_array);
            ?>;

            let currentPage = 1;
            const itemsPerPage = 6;
            let filteredJobs = [...jobs];

            // ===== FUNCIÓN POSTULAR =====
            function abrirPostulacion(titulo, empresa) {
                document.getElementById('postulacionTitulo').textContent = titulo;
                document.getElementById('postulacionEmpresa').textContent = empresa;
                document.getElementById('postulacionNombre').value = '';
                document.getElementById('postulacionDIP').value = '';
                document.getElementById('postulacionTelefono').value = '';
                document.getElementById('postulacionEmail').value = '';
                document.getElementById('postulacionMensaje').value = '';
                const modal = new bootstrap.Modal(document.getElementById('postulacionModal'));
                modal.show();
            }

            // ===== ENVIAR POSTULACIÓN =====
            document.getElementById('enviarPostulacionBtn').addEventListener('click', function() {
                const nombre = document.getElementById('postulacionNombre').value.trim();
                const dip = document.getElementById('postulacionDIP').value.trim();
                const telefono = document.getElementById('postulacionTelefono').value.trim();
                const email = document.getElementById('postulacionEmail').value.trim();

                if (!nombre || !dip || !telefono || !email) {
                    alert('Por favor, complete todos los campos obligatorios (*).');
                    return;
                }

                const postulacionModal = bootstrap.Modal.getInstance(document.getElementById('postulacionModal'));
                postulacionModal.hide();

                const confirmacionModal = new bootstrap.Modal(document.getElementById('confirmacionPostulacionModal'));
                confirmacionModal.show();

                console.log('Postulación enviada:', {
                    nombre, dip, telefono, email,
                    mensaje: document.getElementById('postulacionMensaje').value.trim(),
                    titulo: document.getElementById('postulacionTitulo').textContent,
                    empresa: document.getElementById('postulacionEmpresa').textContent
                });
            });

            // ===== RENDERIZAR TARJETAS =====
            function renderJobs() {
                const start = (currentPage - 1) * itemsPerPage;
                const end = start + itemsPerPage;
                const pageItems = filteredJobs.slice(start, end);

                const container = document.getElementById('jobList');
                document.getElementById('jobCount').textContent = filteredJobs.length;

                if (filteredJobs.length === 0) {
                    container.innerHTML = `<div class="col-12 text-center py-5 text-muted"><i class="bi bi-inbox fs-1 d-block mb-2"></i>No se encontraron ofertas que coincidan con los filtros.</div>`;
                    document.getElementById('paginationControls').innerHTML = '';
                    return;
                }

                let html = '';
                pageItems.forEach((job) => {
                    const accentClass = job.accent || 'blue';
                    let badgeHtml = '';
                    if (job.badge) {
                        const badgeClass = job.badgeColor === 'gold' ? 'badge-gold' :
                            job.badgeColor === 'green' ? 'badge-green' :
                            job.badgeColor === 'blue' ? 'badge-blue' :
                            job.badgeColor === 'urgent' ? 'badge-urgent' : 'badge-blue';
                        badgeHtml = `<span class="${badgeClass} small me-2">${job.badge}</span>`;
                    }

                    let salarioHtml = '';
                    if (job.salario) {
                        salarioHtml = `<span class="badge-soft-blue"><i class="bi bi-coin me-1"></i>${new Intl.NumberFormat('es-ES').format(job.salario)} FCFA</span>`;
                    }

                    html += `
                        <div class="col-md-6 col-lg-4">
                            <div class="job-card h-100">
                                <div class="card-accent ${accentClass}"></div>
                                <div class="pt-2">
                                    ${badgeHtml}
                                    <h6 class="fw-bold m-0 job-title">${job.title}</h6>
                                    <p class="m-0 job-meta mt-1"><i class="bi bi-building"></i><span class="company-name">${job.company}</span></p>
                                    <p class="m-0 job-meta"><i class="bi bi-geo-alt"></i>${job.city}</p>
                                    <p class="m-0 job-meta"><i class="bi bi-calendar-event"></i>${job.date} · ${job.jornada}</p>
                                    <div class="mt-3 d-flex flex-wrap gap-2 align-items-center">
                                        <button class="btn btn-blue btn-sm px-3" onclick="abrirPostulacion('${job.title.replace(/'/g, "\\'")}', '${job.company.replace(/'/g, "\\'")}')">Postular</button>
                                        ${salarioHtml}
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });
                container.innerHTML = html;
                renderPagination();
            }

            // ===== PAGINACIÓN =====
            function renderPagination() {
                const totalPages = Math.ceil(filteredJobs.length / itemsPerPage);
                const controls = document.getElementById('paginationControls');
                if (totalPages <= 1) {
                    controls.innerHTML = '';
                    return;
                }

                let html = '';
                html += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${currentPage - 1}">Anterior</a></li>`;
                for (let i = 1; i <= totalPages; i++) {
                    html += `<li class="page-item ${i === currentPage ? 'active' : ''}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
                }
                html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${currentPage + 1}">Siguiente</a></li>`;

                controls.innerHTML = html;

                controls.querySelectorAll('a.page-link').forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        const page = parseInt(this.dataset.page);
                        if (page >= 1 && page <= totalPages) {
                            currentPage = page;
                            renderJobs();
                        }
                    });
                });
            }

            // ===== FILTRADO EN TIEMPO REAL =====
            function filterJobs() {
                const searchTerm = document.getElementById('searchInput').value.toLowerCase().trim();
                const sector = document.getElementById('sectorSelect').value;
                const city = document.getElementById('citySelect').value.toLowerCase();

                filteredJobs = jobs.filter(job => {
                    const matchSearch = job.title.toLowerCase().includes(searchTerm) || 
                                       job.company.toLowerCase().includes(searchTerm) ||
                                       job.sector.toLowerCase().includes(searchTerm);
                    const matchSector = sector === '' || job.sector === sector;
                    const matchCity = city === '' || job.city.toLowerCase() === city;
                    return matchSearch && matchSector && matchCity;
                });

                currentPage = 1;
                renderJobs();
            }

            // ===== LIMPIAR FILTROS =====
            document.getElementById('clearFiltersBtn').addEventListener('click', function() {
                document.getElementById('searchInput').value = '';
                document.getElementById('sectorSelect').value = '';
                document.getElementById('citySelect').value = '';
                filterJobs();
            });

            // ===== EVENTOS EN TIEMPO REAL =====
            document.getElementById('searchInput').addEventListener('input', filterJobs);
            document.getElementById('sectorSelect').addEventListener('change', filterJobs);
            document.getElementById('citySelect').addEventListener('change', filterJobs);

            // ===== INICIALIZAR =====
            renderJobs();
        </script>

    </div> <!-- .main-wrapper -->
</body>
</html>
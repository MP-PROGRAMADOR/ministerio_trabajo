<?php
session_start();

// ===== VERIFICAR SESIÓN =====
if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../login_desempleados.php');
    exit();
}

$titulo = 'Cursos Públicos - Portal de Empleo';
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

    // ===== OBTENER CURSOS DESDE BBDD =====
    $stmt = $pdo->prepare("
        SELECT 
            c.*,
            ef.nombre_entidad,
            ef.siglas,
            ef.tipo_entidad,
            ef.provincia as entidad_provincia
        FROM cursos c
        JOIN entidades_formadoras ef ON c.entidad_id = ef.id
        WHERE c.estado != 'finalizado'
        ORDER BY 
            FIELD(c.estado, 'activo', 'proximamente'),
            c.fecha_creacion DESC
    ");
    $stmt->execute();
    $cursos_bd = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener inscripciones del usuario
    $cursos_solicitados = [];
    if ($buscador) {
        $stmt = $pdo->prepare("SELECT curso_id, estado FROM inscripciones_cursos WHERE buscador_id = ?");
        $stmt->execute([$buscador['id']]);
        $inscripciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($inscripciones as $ins) {
            $cursos_solicitados[$ins['curso_id']] = $ins['estado'];
        }
    }

    // ===== OBTENER MODALIDADES ÚNICAS PARA FILTROS =====
    $stmt = $pdo->prepare("SELECT DISTINCT modalidad FROM cursos ORDER BY modalidad");
    $stmt->execute();
    $modalidades = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // ===== OBTENER ENTIDADES ÚNICAS =====
    $entidades_unicas = array_unique(array_column($cursos_bd, 'nombre_entidad'));

} catch (PDOException $e) {
    error_log("Error en cursos públicos: " . $e->getMessage());
    $perfil_completo = false;
    $buscador = null;
    $cursos_bd = [];
    $modalidades = [];
    $cursos_solicitados = [];
    $entidades_unicas = [];
}

// ===== INCLUIR MENÚ =====
include '../componentes/menu_desempleado.php';
?>

<style>
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

    /* ===== LISTA DE CURSOS ===== */
    .list-item-custom {
        background: #F8FAFC;
        border: 1px solid #F1F5F9;
        border-radius: var(--gov-radius);
        transition: all 0.2s;
        padding: 1rem 1.25rem;
        position: relative;
        overflow: hidden;
    }
    .list-item-custom:hover {
        background: #ffffff;
        border-color: var(--gov-gold);
        transform: translateX(4px);
        box-shadow: 0 4px 12px rgba(11, 58, 96, 0.04);
    }
    .list-item-custom .accent-bar {
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
    }
    .list-item-custom .accent-bar.blue { background: var(--gov-blue); }
    .list-item-custom .accent-bar.gold { background: var(--gov-gold); }
    .list-item-custom .accent-bar.green { background: var(--gov-green); }

    .list-item-custom .course-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--gov-dark);
    }
    .list-item-custom .institution-name {
        font-weight: 600;
        color: var(--gov-blue);
    }
    .list-item-custom .course-meta {
        font-size: 0.85rem;
        color: #6b7a8a;
    }
    .list-item-custom .course-meta i {
        width: 1.2rem;
        color: var(--gov-gold);
    }
    .list-item-custom .spots-badge {
        background: rgba(11, 58, 96, 0.08);
        color: var(--gov-blue);
        font-weight: 600;
        padding: 0.25rem 0.9rem;
        border-radius: 20px;
        font-size: 0.8rem;
        border: 1px solid rgba(11, 58, 96, 0.1);
    }

    /* Badges */
    .badge-blue {
        background: var(--gov-blue);
        color: white;
        font-weight: 600;
        padding: 0.2rem 0.8rem;
        border-radius: 20px;
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .badge-gold {
        background: var(--gov-gold);
        color: white;
        font-weight: 600;
        padding: 0.2rem 0.8rem;
        border-radius: 20px;
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .badge-green {
        background: var(--gov-green);
        color: white;
        font-weight: 600;
        padding: 0.2rem 0.8rem;
        border-radius: 20px;
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .badge-urgent {
        background: #DC3545;
        color: white;
        font-weight: 600;
        padding: 0.2rem 0.8rem;
        border-radius: 20px;
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .badge-soft-blue {
        background: rgba(11, 58, 96, 0.08);
        color: var(--gov-blue);
        font-weight: 500;
        padding: 0.2rem 0.8rem;
        border-radius: 20px;
        font-size: 0.7rem;
    }

    /* Badges de estado de solicitud */
    .badge-soft-warning {
        background-color: #fff3cd;
        color: #856404;
        border: 1px solid #ffc107;
        font-weight: 500;
        padding: 0.3rem 0.8rem;
        border-radius: 20px;
        font-size: 0.75rem;
    }
    .badge-soft-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #28a745;
        font-weight: 500;
        padding: 0.3rem 0.8rem;
        border-radius: 20px;
        font-size: 0.75rem;
    }
    .badge-soft-danger {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #dc3545;
        font-weight: 500;
        padding: 0.3rem 0.8rem;
        border-radius: 20px;
        font-size: 0.75rem;
    }
    .badge-soft-info {
        background-color: #cce5ff;
        color: #004085;
        border: 1px solid #0dcaf0;
        font-weight: 500;
        padding: 0.3rem 0.8rem;
        border-radius: 20px;
        font-size: 0.75rem;
    }

    /* ===== BOTONES ===== */
    .btn-blue {
        background: var(--gov-blue);
        border: none;
        color: white;
        border-radius: var(--gov-radius-sm);
        padding: 0.5rem 1.2rem;
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
        padding: 0.5rem 1.2rem;
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
        padding: 0.5rem 1.2rem;
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
    .btn-outline-gold {
        border: 2px solid var(--gov-gold);
        color: var(--gov-gold);
        background: transparent;
        border-radius: var(--gov-radius-sm);
        font-weight: 600;
        transition: all 0.25s;
        padding: 0.4rem 1rem;
        font-size: 0.85rem;
    }
    .btn-outline-gold:hover {
        background: var(--gov-gold);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(201, 168, 76, 0.30);
    }
    .btn-outline-secondary {
        border: 2px solid var(--gov-border);
        color: var(--gov-dark);
        background: transparent;
        border-radius: var(--gov-radius-sm);
        font-weight: 500;
        transition: all 0.25s;
    }
    .btn-outline-secondary:hover {
        background: #e9ecef;
        border-color: #cbd5e1;
    }
    .btn-secondary-custom {
        background: #e9ecef;
        color: #6c757d;
        border: none;
        border-radius: var(--gov-radius-sm);
        padding: 0.5rem 1.2rem;
        font-weight: 500;
        cursor: default;
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
    .modal-body .detail-row {
        display: flex;
        align-items: baseline;
        padding: 0.6rem 0;
        border-bottom: 1px solid rgba(0, 0, 0, 0.04);
    }
    .modal-body .detail-row:last-child { border-bottom: none; }
    .modal-body .detail-label {
        font-weight: 600;
        color: var(--gov-dark);
        width: 130px;
        flex-shrink: 0;
        font-size: 0.9rem;
    }
    .modal-body .detail-value {
        color: #2D3A4A;
        font-size: 0.95rem;
    }
    .modal-body .detail-value i {
        color: var(--gov-gold);
        margin-right: 6px;
        width: 1.2rem;
    }
    .modal-body .requirements-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .modal-body .requirements-list li {
        padding: 0.3rem 0;
        padding-left: 1.6rem;
        position: relative;
    }
    .modal-body .requirements-list li::before {
        content: "▸";
        color: var(--gov-gold);
        font-weight: 700;
        position: absolute;
        left: 0;
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

    /* ===== FOOTER ===== */
    footer {
        background: rgba(255, 255, 255, 0.9) !important;
        backdrop-filter: blur(8px);
        border-top: 1px solid rgba(201, 168, 76, 0.15);
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

    @media (max-width: 576px) {
        .dashboard-card { padding: 1.5rem !important; }
        .list-item-custom { padding: 1rem !important; }
        .modal-body { padding: 1.2rem; }
        .modal-header { padding: 1.2rem; }
        .modal-body .detail-label {
            width: 100%;
            margin-bottom: 2px;
        }
        .modal-body .detail-row { flex-wrap: wrap; }
    }
</style>

<body>
    <canvas id="canvas-background"></canvas>
    <div class="main-wrapper">

        <main class="container py-5 flex-grow-1">

            <!-- MENSAJES FLASH -->
            <?php if (isset($_SESSION['mensaje'])): ?>
                <div id="flashMessage" class="alert alert-<?php echo $_SESSION['mensaje_tipo']; ?> alert-dismissible fade show" role="alert">
                    <i class="bi bi-<?php echo $_SESSION['mensaje_tipo'] == 'success' ? 'check-circle-fill' : ($_SESSION['mensaje_tipo'] == 'danger' ? 'x-circle-fill' : 'info-circle-fill'); ?> me-2"></i>
                    <?php echo htmlspecialchars($_SESSION['mensaje']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['mensaje']); unset($_SESSION['mensaje_tipo']); ?>
            <?php endif; ?>

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
                    
                    <!-- FILTROS -->
                    <div class="col-12">
                        <div class="card dashboard-card p-4">
                            <h5 class="fw-bold mb-3 h6 text-uppercase tracking-wider text-muted"><i class="bi bi-funnel me-2"></i>Filtrar Cursos</h5>
                            <form id="filterForm">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <input type="text" id="searchInput" class="form-control form-control-search" placeholder="Buscar curso o entidad..." value="">
                                    </div>
                                    <div class="col-md-3">
                                        <select id="modalidadSelect" class="form-select form-select-custom">
                                            <option value="">Todas las modalidades</option>
                                            <?php foreach ($modalidades as $modalidad): ?>
                                                <option value="<?php echo htmlspecialchars($modalidad); ?>">
                                                    <?php echo ucfirst(htmlspecialchars($modalidad)); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <select id="estadoSelect" class="form-select form-select-custom">
                                            <option value="">Estado</option>
                                            <option value="activo">Activos</option>
                                            <option value="proximamente">Próximamente</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <select id="entidadSelect" class="form-select form-select-custom">
                                            <option value="">Todas las entidades</option>
                                            <?php foreach ($entidades_unicas as $entidad): ?>
                                                <option value="<?php echo htmlspecialchars($entidad); ?>">
                                                    <?php echo htmlspecialchars($entidad); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-outline-secondary w-100" id="clearFiltersBtn" title="Limpiar todos los filtros">
                                            <i class="bi bi-arrow-counterclockwise"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- LISTA DE CURSOS -->
                    <div class="col-12">
                        <div class="card dashboard-card p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="fw-bold m-0 h6 text-uppercase tracking-wider text-muted">
                                    <i class="bi bi-journal-bookmark me-2"></i>
                                    Planes Estatales de Capacitación 
                                    (<span id="courseCount"><?php echo count($cursos_bd); ?></span>)
                                </h5>
                                <span class="badge-gold">Cupos disponibles</span>
                            </div>

                            <div id="courseList" class="d-flex flex-column gap-3">
                                <!-- Generado por JavaScript -->
                            </div>

                            <nav class="mt-4">
                                <ul class="pagination justify-content-center pagination-custom" id="paginationControls">
                                </ul>
                            </nav>
                        </div>
                    </div>
                    
                </div>
            <?php endif; ?>

        </main>

        <!-- ===== MODAL DETALLE CURSO (sin modales de solicitud) ===== -->
        <div class="modal fade" id="courseModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-journal-bookmark-fill me-2" style="color: var(--gov-gold);"></i><span id="modalCourseTitle">Curso</span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" id="modalCourseBody"></div>
                    <div class="modal-footer">
                        <button class="btn btn-green" id="modalSolicitarBtn"><i class="bi bi-send me-2"></i>Solicitar plaza</button>
                        <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        <?php include '../componentes/footer_desempleado.php'; ?>

        <script>
            // ===== DATOS DE CURSOS DESDE PHP (con estado de solicitud) =====
            const courses = <?php 
                $courses_array = [];
                foreach ($cursos_bd as $curso) {
                    $color = $curso['estado'] == 'activo' ? 'green' : 'gold';
                    $badge = $curso['estado'] == 'activo' ? 'Cupos disponibles' : 'Próximamente';
                    $badgeColor = $curso['estado'] == 'activo' ? 'green' : 'gold';
                    $estado = $curso['estado'];
                    
                    $solicitado = isset($cursos_solicitados[$curso['id']]) ? $cursos_solicitados[$curso['id']] : null;
                    
                    $courses_array[] = [
                        'id' => $curso['id'],
                        'title' => $curso['titulo_curso'],
                        'institution' => $curso['nombre_entidad'],
                        'siglas' => $curso['siglas'] ?? '',
                        'modalidad' => $curso['modalidad'] ?? 'presencial',
                        'estado' => $estado,
                        'spots' => $curso['cupos_maximos'] ?? 30,
                        'start_date' => $curso['fecha_inicio'] ? date('d/m/Y', strtotime($curso['fecha_inicio'])) : 'Por definir',
                        'duration' => $curso['duracion_horas'] . ' horas',
                        'badge' => $badge,
                        'badgeColor' => $badgeColor,
                        'accent' => $color,
                        'description' => $curso['descripcion_curso'] ?? '',
                        'imagen' => $curso['imagen_portada'] ?? '',
                        'solicitado' => $solicitado // 'pendiente', 'confirmado', 'rechazado' o null
                    ];
                }
                echo json_encode($courses_array);
            ?>;

            let currentPage = 1;
            const itemsPerPage = 4;
            let filteredCourses = [...courses];
            let selectedCourseId = null;

            // ===== FUNCIONES =====

            function renderCourses() {
                const start = (currentPage - 1) * itemsPerPage;
                const end = start + itemsPerPage;
                const pageItems = filteredCourses.slice(start, end);

                const container = document.getElementById('courseList');
                document.getElementById('courseCount').textContent = filteredCourses.length;

                if (filteredCourses.length === 0) {
                    container.innerHTML = `
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            No se encontraron cursos que coincidan con los filtros.
                        </div>
                    `;
                    document.getElementById('paginationControls').innerHTML = '';
                    return;
                }

                let html = '';
                pageItems.forEach(course => {
                    const accentClass = course.accent || 'blue';
                    
                    let badgeHtml = '';
                    if (course.badge) {
                        const badgeClass = course.badgeColor === 'gold' ? 'badge-gold' :
                            course.badgeColor === 'green' ? 'badge-green' :
                            course.badgeColor === 'blue' ? 'badge-blue' :
                            course.badgeColor === 'urgent' ? 'badge-urgent' : 'badge-blue';
                        badgeHtml = `<span class="${badgeClass} me-2">${course.badge}</span>`;
                    }

                    const spotsHtml = course.spots > 0 ?
                        `<span class="spots-badge">${course.spots} cupos</span>` :
                        `<span class="spots-badge" style="background:#dc3545; color:white; border:none;">Completo</span>`;

                    const siglasHtml = course.siglas ? `(${course.siglas})` : '';

                    // Botón o estado según solicitud
                    let btnHtml = '';
                    const solicitado = course.solicitado;
                    if (solicitado) {
                        let estadoTexto = '';
                        let estadoClase = '';
                        if (solicitado === 'pendiente') {
                            estadoTexto = '⏳ Pendiente de confirmación';
                            estadoClase = 'badge-soft-warning';
                        } else if (solicitado === 'confirmado') {
                            estadoTexto = '✅ Confirmado';
                            estadoClase = 'badge-soft-success';
                        } else if (solicitado === 'rechazado') {
                            estadoTexto = '❌ Rechazado';
                            estadoClase = 'badge-soft-danger';
                        } else {
                            estadoTexto = 'Solicitado';
                            estadoClase = 'badge-soft-info';
                        }
                        btnHtml = `<span class="badge ${estadoClase}" style="font-size:0.75rem; padding:0.4rem 1rem;">${estadoTexto}</span>`;
                    } else if (course.estado === 'activo') {
                        btnHtml = `
                            <a href="solicitar_plaza.php?curso_id=${course.id}" class="btn btn-green btn-sm px-3">
                                <i class="bi bi-send me-1"></i> Solicitar plaza
                            </a>
                        `;
                    } else if (course.estado === 'proximamente') {
                        btnHtml = `
                            <button class="btn btn-secondary-custom btn-sm px-3" disabled>
                                <i class="bi bi-clock me-1"></i> Próximamente
                            </button>
                        `;
                    } else {
                        btnHtml = `
                            <button class="btn btn-secondary-custom btn-sm px-3" disabled>
                                <i class="bi bi-x-circle me-1"></i> Cerrado
                            </button>
                        `;
                    }

                    // Imagen del curso (si existe)
                    let imgHtml = '';
                    if (course.imagen && course.imagen !== 'img/cursos/default.jpg') {
                        imgHtml = `<img src="../${course.imagen}" alt="Curso" style="width:80px; height:60px; object-fit:cover; border-radius:4px;">`;
                    }

                    html += `
                        <div class="list-item-custom">
                            <div class="accent-bar ${accentClass}"></div>
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-3 ps-3">
                                <div class="flex-grow-1">
                                    ${badgeHtml}
                                    <h6 class="fw-bold m-0 course-title">${course.title}</h6>
                                    <p class="m-0 course-meta">
                                        <i class="bi bi-building"></i>
                                        <span class="institution-name">${course.institution}</span>
                                        ${siglasHtml}
                                    </p>
                                    <p class="m-0 course-meta">
                                        <i class="bi bi-grid"></i>
                                        ${course.modalidad.charAt(0).toUpperCase() + course.modalidad.slice(1)} · 
                                        <i class="bi bi-calendar-event"></i> Inicio: ${course.start_date} · 
                                        <i class="bi bi-clock"></i> ${course.duration}
                                    </p>
                                    <div class="mt-1 d-flex flex-wrap gap-2 align-items-center">
                                        ${spotsHtml}
                                        <span class="badge-soft-blue">
                                            <i class="bi bi-tag me-1"></i> ${course.estado === 'activo' ? 'Abierto' : 'Próximamente'}
                                        </span>
                                    </div>
                                </div>
                                ${imgHtml}
                                <div class="d-flex flex-wrap gap-2 align-items-center">
                                    <button class="btn btn-outline-gold btn-sm px-3" onclick="openCourseModal(${course.id})">
                                        <i class="bi bi-eye me-1"></i> Ver más
                                    </button>
                                    ${btnHtml}
                                </div>
                            </div>
                        </div>
                    `;
                });
                container.innerHTML = html;
                renderPagination();
            }

            function renderPagination() {
                const totalPages = Math.ceil(filteredCourses.length / itemsPerPage);
                const controls = document.getElementById('paginationControls');
                if (totalPages <= 1) {
                    controls.innerHTML = '';
                    return;
                }

                let html = '';
                html += `
                    <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-page="${currentPage - 1}">Anterior</a>
                    </li>
                `;

                for (let i = 1; i <= totalPages; i++) {
                    html += `
                        <li class="page-item ${i === currentPage ? 'active' : ''}">
                            <a class="page-link" href="#" data-page="${i}">${i}</a>
                        </li>
                    `;
                }

                html += `
                    <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-page="${currentPage + 1}">Siguiente</a>
                    </li>
                `;

                controls.innerHTML = html;

                controls.querySelectorAll('a.page-link').forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        const page = parseInt(this.dataset.page);
                        if (page >= 1 && page <= totalPages) {
                            currentPage = page;
                            renderCourses();
                        }
                    });
                });
            }

            function filterCourses() {
                const searchTerm = document.getElementById('searchInput').value.toLowerCase().trim();
                const modalidad = document.getElementById('modalidadSelect').value;
                const estado = document.getElementById('estadoSelect').value;
                const entidad = document.getElementById('entidadSelect').value;

                filteredCourses = courses.filter(course => {
                    const matchSearch = course.title.toLowerCase().includes(searchTerm) ||
                        course.institution.toLowerCase().includes(searchTerm);
                    const matchModalidad = modalidad === '' || course.modalidad === modalidad;
                    const matchEstado = estado === '' || course.estado === estado;
                    const matchEntidad = entidad === '' || course.institution === entidad;
                    return matchSearch && matchModalidad && matchEstado && matchEntidad;
                });

                currentPage = 1;
                renderCourses();
            }

            // ===== EVENTOS DE FILTROS =====
            document.getElementById('searchInput').addEventListener('input', filterCourses);
            document.getElementById('modalidadSelect').addEventListener('change', filterCourses);
            document.getElementById('estadoSelect').addEventListener('change', filterCourses);
            document.getElementById('entidadSelect').addEventListener('change', filterCourses);

            document.getElementById('clearFiltersBtn').addEventListener('click', function() {
                document.getElementById('searchInput').value = '';
                document.getElementById('modalidadSelect').value = '';
                document.getElementById('estadoSelect').value = '';
                document.getElementById('entidadSelect').value = '';
                filterCourses();
            });

            // ===== MODAL DETALLE CURSO =====
            function openCourseModal(id) {
                const course = courses.find(c => c.id === id);
                if (!course) return;

                selectedCourseId = id;
                document.getElementById('modalCourseTitle').textContent = course.title;

                let requisitosHtml = '';
                if (course.requisitos && course.requisitos.length > 0) {
                    requisitosHtml = course.requisitos.map(req => `<li>${req}</li>`).join('');
                }

                let beneficiosHtml = '';
                if (course.beneficios && course.beneficios.length > 0) {
                    beneficiosHtml = course.beneficios.map(b => `<li>${b}</li>`).join('');
                }

                const body = document.getElementById('modalCourseBody');
                body.innerHTML = `
                    <div class="detail-row">
                        <span class="detail-label"><i class="bi bi-building"></i> Institución</span>
                        <span class="detail-value">${course.institution} ${course.siglas ? '('+course.siglas+')' : ''}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label"><i class="bi bi-grid"></i> Modalidad</span>
                        <span class="detail-value">${course.modalidad.charAt(0).toUpperCase() + course.modalidad.slice(1)}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label"><i class="bi bi-people"></i> Cupos</span>
                        <span class="detail-value">${course.spots > 0 ? course.spots : 'Completo'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label"><i class="bi bi-calendar-event"></i> Inicio</span>
                        <span class="detail-value">${course.start_date}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label"><i class="bi bi-clock"></i> Duración</span>
                        <span class="detail-value">${course.duration}</span>
                    </div>
                    ${course.description ? `
                        <div class="detail-row">
                            <span class="detail-label"><i class="bi bi-info-circle"></i> Descripción</span>
                            <span class="detail-value">${course.description}</span>
                        </div>
                    ` : ''}
                    ${requisitosHtml ? `
                        <div class="detail-row">
                            <span class="detail-label"><i class="bi bi-list-check"></i> Requisitos</span>
                            <div class="detail-value"><ul class="requirements-list">${requisitosHtml}</ul></div>
                        </div>
                    ` : ''}
                    ${beneficiosHtml ? `
                        <div class="detail-row">
                            <span class="detail-label"><i class="bi bi-gift"></i> Beneficios</span>
                            <div class="detail-value"><ul class="requirements-list">${beneficiosHtml}</ul></div>
                        </div>
                    ` : ''}
                `;

                const btnSolicitar = document.getElementById('modalSolicitarBtn');
                const solicitado = course.solicitado;
                if (solicitado) {
                    let estadoTexto = '';
                    if (solicitado === 'pendiente') estadoTexto = '⏳ Pendiente de confirmación';
                    else if (solicitado === 'confirmado') estadoTexto = '✅ Confirmado';
                    else if (solicitado === 'rechazado') estadoTexto = '❌ Rechazado';
                    else estadoTexto = 'Solicitado';
                    btnSolicitar.style.display = 'inline-block';
                    btnSolicitar.textContent = estadoTexto;
                    btnSolicitar.className = 'btn btn-secondary';
                    btnSolicitar.onclick = function() { 
                        alert('Ya has solicitado plaza en este curso. Estado: ' + estadoTexto);
                    };
                } else if (course.estado === 'activo') {
                    btnSolicitar.style.display = 'inline-block';
                    btnSolicitar.textContent = 'Solicitar plaza';
                    btnSolicitar.className = 'btn btn-green';
                    btnSolicitar.onclick = function() { 
                        const modal = bootstrap.Modal.getInstance(document.getElementById('courseModal'));
                        modal.hide();
                        window.location.href = 'solicitar_plaza.php?curso_id=' + course.id;
                    };
                } else {
                    btnSolicitar.style.display = 'inline-block';
                    btnSolicitar.textContent = course.estado === 'proximamente' ? 'Próximamente' : 'No disponible';
                    btnSolicitar.className = 'btn btn-secondary';
                    btnSolicitar.onclick = function() { 
                        alert('Este curso no está actualmente abierto para inscripciones.'); 
                    };
                }

                const modal = new bootstrap.Modal(document.getElementById('courseModal'));
                modal.show();
            }

            // ===== INICIALIZAR =====
            renderCourses();

            // ===== AUTO-CERRAR ALERTAS FLASH =====
            document.addEventListener('DOMContentLoaded', function() {
                const flash = document.getElementById('flashMessage');
                if (flash) {
                    setTimeout(() => {
                        flash.classList.remove('show');
                        setTimeout(() => flash.remove(), 300);
                    }, 4000);
                }
            });
        </script>

    </div>
</body>
</html>
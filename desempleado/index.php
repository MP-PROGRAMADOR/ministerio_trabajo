<?php
session_start();
// ===== VERIFICAR SESIÓN =====
if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../login_desempleados.php');
    exit();
}

$titulo = 'Dashboard - Portal de Empleo';
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

    // ===== ESTADÍSTICAS REALES =====
    $postulaciones = 0;
    if ($buscador) {
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM favoritos WHERE buscador_id = ?");
        $stmt->execute([$buscador['id']]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $postulaciones = $result['total'] ?? 0;
    }

    // Ofertas Vistas = Total de ofertas activas
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM ofertas_empleo WHERE estado = 'abierta'");
    $stmt->execute();
    $ofertas_vistas = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // Cursos Inscritos = Total de cursos activos
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM cursos WHERE estado = 'activo'");
    $stmt->execute();
    $cursos_inscritos = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // Notificaciones (simuladas)
    $notificaciones = rand(0, 10);

    // ===== OFERTAS RECOMENDADAS =====
    $stmt = $pdo->prepare("
        SELECT o.*, e.nombre_empresa 
        FROM ofertas_empleo o 
        JOIN empleadores e ON o.empleador_id = e.id 
        WHERE o.estado = 'abierta' 
        ORDER BY o.fecha_publicacion DESC 
        LIMIT 5
    ");
    $stmt->execute();
    $ofertas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener IDs de ofertas a las que ya postuló el usuario
    $postulados_ids = [];
    if ($buscador) {
        $stmt = $pdo->prepare("SELECT oferta_id FROM favoritos WHERE buscador_id = ?");
        $stmt->execute([$buscador['id']]);
        $postulados_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // ===== CURSOS CON ESTADO DE SOLICITUD =====
    $stmt = $pdo->prepare("
        SELECT * 
        FROM cursos 
        WHERE estado = 'activo' 
        ORDER BY fecha_creacion DESC 
        LIMIT 4
    ");
    $stmt->execute();
    $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener inscripciones del usuario para estos cursos
    $solicitudes_cursos = [];
    if ($buscador && !empty($cursos)) {
        $curso_ids = array_column($cursos, 'id');
        $placeholders = implode(',', array_fill(0, count($curso_ids), '?'));
        $stmt = $pdo->prepare("SELECT curso_id, estado FROM inscripciones_cursos WHERE buscador_id = ? AND curso_id IN ($placeholders)");
        $stmt->execute(array_merge([$buscador['id']], $curso_ids));
        $solicitudes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($solicitudes as $sol) {
            $solicitudes_cursos[$sol['curso_id']] = $sol['estado'];
        }
    }

    // ===== NOTICIAS (estáticas) =====
    $noticias = [
        ['titulo' => 'Nueva convocatoria de empleo público', 'fecha' => '15/07/2026', 'badge' => 'Nuevo'],
        ['titulo' => 'Curso gratuito de inglés técnico', 'fecha' => '12/07/2026', 'badge' => 'Próximo'],
        ['titulo' => 'Feria de empleo en Malabo', 'fecha' => '20/07/2026', 'badge' => 'Evento']
    ];

    // ===== NOTIFICACIONES DE OFICINA =====
    $notificaciones_lista = [
        ['mensaje' => 'Nueva oferta en tu sector: Técnico de Soporte TI', 'fecha' => 'Hace 2 horas'],
        ['mensaje' => 'Tu postulación a GETESA está en revisión', 'fecha' => 'Hace 1 día'],
        ['mensaje' => 'Nuevo curso disponible: Administración de Redes', 'fecha' => 'Hace 3 días']
    ];

    // Obtener datos del usuario
    $stmt = $pdo->prepare("SELECT numero_expediente FROM usuarios WHERE id = ?");
    $stmt->execute([$id_usuario]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Error en dashboard: " . $e->getMessage());
    $perfil_completo = false;
    $buscador = null;
    $postulaciones = 0;
    $ofertas_vistas = 0;
    $cursos_inscritos = 0;
    $notificaciones = 0;
    $ofertas = [];
    $cursos = [];
    $postulados_ids = [];
    $solicitudes_cursos = [];
    $usuario = ['numero_expediente' => 'EG-00000'];
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
        box-shadow: 0 10px 25px rgba(201, 168, 76, 0.15);
        transform: translateY(-1px);
    }

    /* ===== ESTADÍSTICAS ===== */
    .stat-card {
        background: #ffffff;
        border: 1px solid var(--gov-border);
        border-radius: var(--gov-radius);
        padding: 1.2rem 1rem;
        transition: all 0.3s;
        text-align: center;
    }
    .stat-card:hover {
        border-color: var(--gov-gold);
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(201, 168, 76, 0.15);
    }
    .stat-card .stat-icon {
        font-size: 2rem;
        color: var(--gov-blue);
        opacity: 0.7;
        margin-bottom: 0.4rem;
    }
    .stat-card .stat-number {
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--gov-dark);
        line-height: 1.2;
    }
    .stat-card .stat-label {
        font-size: 0.8rem;
        color: #6b7a8a;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 500;
    }

    /* ===== BOTONES ===== */
    .btn-gov {
        background-color: var(--gov-blue);
        color: #ffffff;
        border: none;
        border-radius: var(--gov-radius-sm);
        padding: 0.6rem 1.2rem;
        font-weight: 500;
        transition: background-color 0.2s ease, transform 0.15s;
        box-shadow: 0 4px 12px rgba(11, 58, 96, 0.15);
    }
    .btn-gov:hover {
        background-color: var(--gov-blue-light);
        color: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(11, 58, 96, 0.20);
    }

    .btn-gov-outline {
        background: transparent;
        border: 2px solid var(--gov-blue);
        color: var(--gov-blue);
        border-radius: var(--gov-radius-sm);
        padding: 0.5rem 1.2rem;
        font-weight: 500;
        transition: all 0.2s;
    }
    .btn-gov-outline:hover {
        background: var(--gov-blue);
        color: white;
    }

    .btn-green {
        background-color: var(--gov-green);
        color: #ffffff;
        border: none;
        border-radius: var(--gov-radius-sm);
        padding: 0.6rem 1.2rem;
        font-weight: 500;
        transition: all 0.2s;
        box-shadow: 0 4px 12px rgba(30, 126, 52, 0.15);
    }
    .btn-green:hover {
        background-color: var(--gov-green-light);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(30, 126, 52, 0.20);
    }

    .btn-pill-custom {
        border-radius: 5px;
        padding: 0.4rem 1.2rem;
        font-size: 0.85rem;
        font-weight: 500;
    }

    /* ===== PERFIL ===== */
    .profile-detail-item {
        display: flex;
        justify-content: space-between;
        padding: 0.3rem 0;
        font-size: 0.9rem;
        border-bottom: 1px dashed #e9edf2;
    }
    .profile-detail-item:last-child {
        border-bottom: none;
    }
    .profile-detail-item .label {
        color: #6b7a8a;
    }
    .profile-detail-item .value {
        font-weight: 500;
        color: var(--gov-dark);
    }

    /* ===== LISTAS ===== */
    .list-item-custom {
        background-color: #F8FAFC;
        border: 1px solid #F1F5F9;
        border-radius: var(--gov-radius);
        transition: all 0.2s ease;
    }
    .list-item-custom:hover {
        background-color: #F1F5F9;
        border-color: var(--gov-gold);
    }

    /* ===== BADGES ===== */
    .badge-blue {
        background: var(--gov-blue);
        color: white;
        font-weight: 600;
        padding: 0.25rem 0.9rem;
        border-radius: 20px;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .badge-green {
        background: var(--gov-green);
        color: white;
        font-weight: 600;
        padding: 0.25rem 0.9rem;
        border-radius: 20px;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .badge-soft-blue {
        background: rgba(11, 58, 96, 0.08);
        color: var(--gov-blue);
        font-weight: 500;
        padding: 0.25rem 0.9rem;
        border-radius: 20px;
        font-size: 0.7rem;
    }
    .badge-soft-green {
        background: rgba(30, 126, 52, 0.08);
        color: var(--gov-green);
        font-weight: 500;
        padding: 0.25rem 0.9rem;
        border-radius: 20px;
        font-size: 0.7rem;
    }

    /* ===== BADGES DE ESTADO PARA CURSOS ===== */
    .badge-pendiente {
        background-color: #fff3cd;
        color: #856404;
        border: 1px solid #ffc107;
        padding: 0.3rem 0.8rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    .badge-confirmado {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #28a745;
        padding: 0.3rem 0.8rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    .badge-rechazado {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #dc3545;
        padding: 0.3rem 0.8rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
    }

    /* ===== ALERTAS ===== */
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
        border-bottom-color: var(--gov-gold);
    }

    /* ===== NOTICIAS ===== */
    .news-item {
        padding: 0.8rem 0;
        border-bottom: 1px solid var(--gov-border);
        transition: background 0.2s;
        border-radius: var(--gov-radius-sm);
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }
    .news-item:last-child {
        border-bottom: none;
    }
    .news-item:hover {
        background: #f8fafc;
        border-color: var(--gov-gold);
    }
    .news-item .news-title {
        font-weight: 600;
        color: var(--gov-dark);
        font-size: 0.95rem;
    }
    .news-item .news-meta {
        font-size: 0.75rem;
        color: #6b7a8a;
    }
    .news-item .news-badge {
        background: var(--gov-green);
        color: white;
        padding: 0.1rem 0.6rem;
        border-radius: 30px;
        font-size: 0.6rem;
        text-transform: uppercase;
        font-weight: 600;
    }

    /* ===== CURSOS ===== */
    .curso-item {
        padding: 0.8rem 0;
        border-bottom: 1px solid var(--gov-border);
        transition: background 0.2s;
        border-radius: var(--gov-radius-sm);
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }
    .curso-item:last-child {
        border-bottom: none;
    }
    .curso-item:hover {
        background: #f8fafc;
        border-color: var(--gov-gold);
    }
    .curso-item .curso-titulo {
        font-weight: 600;
        color: var(--gov-dark);
        font-size: 0.95rem;
    }
    .curso-item .curso-meta {
        font-size: 0.75rem;
        color: #6b7a8a;
    }
    .curso-item .curso-badge {
        background: var(--gov-green);
        color: white;
        padding: 0.1rem 0.6rem;
        border-radius: 30px;
        font-size: 0.6rem;
        text-transform: uppercase;
        font-weight: 600;
    }

    /* ===== TIMELINE ===== */
    .timeline-item {
        position: relative;
        padding-left: 24px;
        border-left: 2px solid var(--gov-border);
    }
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -6px;
        top: 6px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background-color: var(--gov-green);
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
        border-color: var(--gov-green);
        box-shadow: 0 0 0 4px rgba(30, 126, 52, 0.12);
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
        border-bottom: 2px solid var(--gov-green-light);
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

    /* ===== BORDER ===== */
    .border-green {
        border-color: var(--gov-green) !important;
    }
    .border-blue {
        border-color: var(--gov-blue) !important;
    }

    /* ===== TEXTOS ===== */
    .text-green {
        color: var(--gov-green) !important;
    }
    .text-blue {
        color: var(--gov-blue) !important;
    }
    .bg-green-soft {
        background-color: #e6f3e8 !important;
    }
    .bg-blue-soft {
        background-color: #e8eef3 !important;
    }

    .tracking-wider {
        letter-spacing: 0.05em;
    }

    /* ===== NOTIFICACIONES ===== */
    .notificacion-item {
        transition: all 0.2s;
    }
    .notificacion-item:hover {
        background: #f8fafc;
        border-color: var(--gov-gold);
        transform: translateX(4px);
    }
    .notificacion-item.no-leida {
        background: var(--bg-blue-soft);
        border-left-color: var(--gov-blue);
    }
    .notificacion-item .noti-icon {
        color: var(--gov-blue);
    }
    .badge-no-leida {
        background: var(--gov-blue);
        color: white;
    }
    .badge-estado-pendiente {
        background: #ffc107;
        color: #212529;
    }
    .badge-estado-en_revision {
        background: #0dcaf0;
        color: #212529;
    }
    .badge-estado-aprobado {
        background: var(--gov-green);
        color: white;
    }
    .badge-estado-rechazado {
        background: #dc3545;
        color: white;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 768px) {
        .stat-card .stat-number {
            font-size: 1.4rem;
        }
    }

    @media (max-width: 576px) {
        .dashboard-card {
            padding: 1.5rem !important;
        }
        .modal-body {
            padding: 1.2rem;
        }
        .modal-header {
            padding: 1.2rem;
        }
    }
</style>

<body>
    <canvas id="canvas-background"></canvas>
    <div class="main-wrapper">
        <main class="container py-5 flex-grow-1">
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

            <?php if ($perfil_completo): ?>
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon"><i class="bi bi-send"></i></div>
                            <div class="stat-number"><?php echo $postulaciones; ?></div>
                            <div class="stat-label">Postulaciones</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon"><i class="bi bi-eye"></i></div>
                            <div class="stat-number"><?php echo $ofertas_vistas; ?></div>
                            <div class="stat-label">Ofertas Vistas</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon"><i class="bi bi-journal-bookmark"></i></div>
                            <div class="stat-number"><?php echo $cursos_inscritos; ?></div>
                            <div class="stat-label">Cursos Inscritos</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon"><i class="bi bi-bell"></i></div>
                            <div class="stat-number"><?php echo $notificaciones; ?></div>
                            <div class="stat-label">Notificaciones</div>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-xl-4 col-lg-5">
                        <!-- Perfil -->
                        <div class="card dashboard-card p-4 text-center mb-4">
                            <?php 
                                $foto = $buscador['foto_carnet'] ?? '';
                                if (!empty($foto) && strpos($foto, 'http') === false) {
                                    $foto = '../' . $foto;
                                } elseif (empty($foto)) {
                                    $foto = 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/icons/person-bounding-box.svg';
                                }
                            ?>
                            <img src="<?php echo htmlspecialchars($foto); ?>"
                                class="rounded-circle border border-3 border-light mx-auto mb-3 shadow-sm"
                                style="width: 100px; height: 100px; object-fit: cover;" 
                                alt="Foto de perfil"
                                onerror="this.src='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/icons/person-bounding-box.svg'">
                            
                            <h4 class="fw-bold m-0 h5 text-dark"><?php echo htmlspecialchars($nombre_completo); ?></h4>
                            <p class="text-muted small mb-3">ID Expediente: <strong style="color: var(--gov-blue);"><?php echo htmlspecialchars($usuario['numero_expediente'] ?? 'EG-00000'); ?></strong></p>

                            <div class="text-start small mb-3">
                                <div class="profile-detail-item">
                                    <span class="label">Estado Laboral</span>
                                    <span class="value">
                                        <span class="badge <?php echo ($buscador['estado_laboral'] ?? 'desempleado') == 'desempleado' ? 'bg-success' : (($buscador['estado_laboral'] ?? '') == 'contratado' ? 'bg-success' : 'bg-danger'); ?>">
                                            <?php echo ucfirst($buscador['estado_laboral'] ?? 'desempleado'); ?>
                                        </span>
                                    </span>
                                </div>
                                <div class="profile-detail-item">
                                    <span class="label">Teléfono</span>
                                    <span class="value"><?php echo htmlspecialchars($buscador['telefono'] ?? 'No registrado'); ?></span>
                                </div>
                                <div class="profile-detail-item">
                                    <span class="label">Estado Civil</span>
                                    <span class="value"><?php echo ucfirst($buscador['estado_civil'] ?? 'No registrado'); ?></span>
                                </div>
                                <div class="profile-detail-item">
                                    <span class="label">Residencia</span>
                                    <span class="value"><?php echo ucfirst(str_replace('_', ' ', $buscador['ciudad_municipio'] ?? 'No registrado')) . ', ' . ucfirst(str_replace('_', ' ', $buscador['provincia'] ?? '')); ?></span>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button class="btn btn-gov btn-sm fw-semibold"><i class="bi bi-qr-code me-2"></i> Descargar Tarjeta Demandante</button>
                                <a href="perfil.php" class="btn btn-gov-outline btn-sm fw-semibold"><i class="bi bi-pencil-square me-2"></i> Editar Perfil</a>
                            </div>
                        </div>

                        <!-- Notificaciones de Oficina -->
                        <div class="card dashboard-card p-4 mb-4">
                            <h5 class="fw-bold mb-3 h6 text-uppercase tracking-wider text-muted"><i class="bi bi-chat-left-text text-primary me-2" style="color: var(--gov-blue) !important;"></i>Notificaciones de Oficina</h5>
                            <div class="d-flex flex-column gap-2">
                                <?php foreach ($notificaciones_lista as $notif): ?>
                                    <div class="p-2 rounded bg-info bg-opacity-10 border-0 small">
                                        <strong class="text-info-emphasis d-block" style="font-weight: 600;"><?php echo htmlspecialchars($notif['mensaje']); ?></strong>
                                        <span class="text-muted" style="font-size: 0.75rem;"><?php echo htmlspecialchars($notif['fecha']); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Noticias -->
                        <div class="card dashboard-card p-4">
                            <h5 class="fw-bold mb-3 h6 text-uppercase tracking-wider text-muted"><i class="bi bi-newspaper me-2" style="color: var(--gov-gold);"></i>Noticias y Eventos</h5>
                            <?php foreach ($noticias as $noticia): ?>
                                <div class="news-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="news-title"><?php echo htmlspecialchars($noticia['titulo']); ?></span>
                                        <span class="news-badge"><?php echo htmlspecialchars($noticia['badge']); ?></span>
                                    </div>
                                    <div class="news-meta"><i class="bi bi-calendar-event me-1"></i> <?php echo htmlspecialchars($noticia['fecha']); ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="col-xl-8 col-lg-7">
                        <div id="jobOffersSection">
                            <div class="card dashboard-card p-4 mb-4">
                                <h5 class="fw-bold mb-3 h6 text-uppercase tracking-wider text-muted">Ofertas Recomendadas</h5>
                                <div class="d-flex flex-column gap-3">
                                    <?php if (empty($ofertas)): ?>
                                        <p class="text-muted text-center">No hay ofertas disponibles en este momento.</p>
                                    <?php else: ?>
                                        <?php foreach ($ofertas as $oferta): ?>
                                            <div class="list-item-custom p-3 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                                                <div>
                                                    <span class="badge bg-gold" style="background: var(--gov-gold); color: white;"><?php echo ($oferta['estado'] ?? 'abierta') == 'abierta' ? 'Disponible' : 'Cerrada'; ?></span>
                                                    <h6 class="fw-bold m-0 text-dark mt-1"><?php echo htmlspecialchars($oferta['titulo_puesto']); ?></h6>
                                                    <p class="m-0 small text-muted">
                                                        <i class="bi bi-building me-1"></i><?php echo htmlspecialchars($oferta['nombre_empresa']); ?> · 
                                                        <i class="bi bi-geo-alt me-1"></i><?php echo ucfirst(str_replace('_', ' ', $oferta['provincia'] ?? 'No especificada')); ?>
                                                        <?php if ($oferta['salario_ofrecido']): ?>
                                                            · <i class="bi bi-coin me-1"></i><?php echo number_format($oferta['salario_ofrecido'], 0, ',', '.'); ?> FCFA
                                                        <?php endif; ?>
                                                    </p>
                                                </div>
                                                <div>
                                                    <?php if (in_array($oferta['id'], $postulados_ids)): ?>
                                                        <span class="badge" style="padding: 0.5rem 1rem; background: #d19003cb"> ⏳ Pendiente de confirmación </span>
                                                    <?php else: ?>
                                                        <a href="postular.php?oferta_id=<?php echo $oferta['id']; ?>" class="btn btn-sm btn-gov btn-pill-custom">Postularme</a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Cursos -->
                        <div class="card dashboard-card p-4 mb-4">
                            <h5 class="fw-bold mb-3 h6 text-uppercase tracking-wider text-muted"><i class="bi bi-journal-bookmark me-2" style="color: var(--gov-green);"></i>Planes Estatales de Capacitación</h5>
                            <?php if (empty($cursos)): ?>
                                <p class="text-muted text-center">No hay cursos disponibles en este momento.</p>
                            <?php else: ?>
                                <?php foreach ($cursos as $curso): ?>
                                    <div class="p-3 rounded list-item-custom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
                                        <div>
                                            <span class="badge bg-green" style="background: var(--gov-green); color: white;"><?php echo htmlspecialchars($curso['estado'] ?? 'Activo'); ?></span>
                                            <h6 class="fw-bold m-0 text-dark mt-1"><?php echo htmlspecialchars($curso['titulo_curso']); ?></h6>
                                            <p class="m-0 small text-muted">
                                                <i class="bi bi-clock me-1"></i><?php echo $curso['duracion_horas']; ?> horas · 
                                                <i class="bi bi-people me-1"></i><?php echo ucfirst($curso['modalidad'] ?? 'Presencial'); ?> · 
                                                <i class="bi bi-calendar me-1"></i><?php echo $curso['fecha_inicio'] ? date('d/m/Y', strtotime($curso['fecha_inicio'])) : 'Por definir'; ?>
                                            </p>
                                        </div>
                                        <div>
                                            <?php 
                                            $estado_solicitud = isset($solicitudes_cursos[$curso['id']]) ? $solicitudes_cursos[$curso['id']] : null;
                                            if ($estado_solicitud) {
                                                if ($estado_solicitud == 'pendiente') {
                                                    echo '<span class="badge-pendiente">⏳ Pendiente de confirmación</span>';
                                                } elseif ($estado_solicitud == 'confirmado') {
                                                    echo '<span class="badge-confirmado">✅ Confirmado</span>';
                                                } elseif ($estado_solicitud == 'rechazado') {
                                                    echo '<span class="badge-rechazado">❌ Rechazado</span>';
                                                } else {
                                                    echo '<span class="badge-pendiente">Solicitado</span>';
                                                }
                                            } else {
                                                echo '<a href="solicitar_plaza.php?curso_id=' . $curso['id'] . '" class="btn btn-sm btn-green btn-pill-custom text-nowrap">Solicitar Plaza</a>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <!-- Estado de candidaturas -->
                        <div class="card dashboard-card p-4 mb-4">
                            <h5 class="fw-bold mb-3 h6 text-uppercase tracking-wider text-muted">Estado de mis Candidaturas</h5>
                            <div class="d-flex flex-column gap-3 mt-2">
                                <?php if ($postulaciones > 0): ?>
                                    <div class="timeline-item">
                                        <span class="badge bg-light text-secondary border mb-1" style="font-size: 0.65rem; font-weight: 600;"><?php echo date('d/m/Y'); ?></span>
                                        <h6 class="fw-bold text-dark m-0 small">Tienes <?php echo $postulaciones; ?> postulaciones activas</h6>
                                        <p class="text-muted small m-0">Revisa el estado de tus aplicaciones en el panel de seguimiento.</p>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted text-center">No tienes candidaturas activas.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </main>
        <?php include '../componentes/footer_desempleado.php'; ?>
    </div>
    <script>
        const perfilCompleto = <?php echo $perfil_completo ? 'true' : 'false'; ?>;
    </script>
</body>
</html>
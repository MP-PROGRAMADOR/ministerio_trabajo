<?php
session_start();

// ===== VERIFICAR SESIÓN ADMINISTRATIVA =====
if (!isset($_SESSION['id_usuario']) || !in_array($_SESSION['rol'] ?? '', ['administrador', 'ministerio'])) {
    header('Location: ../login_admin.php');
    exit();
}

$titulo = 'Cursos y Capacitaciones - Panel de Administración';
include_once '../componentes/header_admin.php';
include_once '../conexion/conexion.php';

// ===== INICIALIZAR VARIABLES =====
$cursos = [];
$entidades = [];
$total_cursos = 0;
$cursos_activos = 0;
$cursos_proximamente = 0;
$cursos_finalizados = 0;

try {
    // ===== CONTAR ESTADÍSTICAS =====
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM cursos");
    $total_cursos = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM cursos WHERE estado = 'activo'");
    $cursos_activos = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM cursos WHERE estado = 'proximamente'");
    $cursos_proximamente = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM cursos WHERE estado = 'finalizado'");
    $cursos_finalizados = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // ===== OBTENER ENTIDADES PARA EL SELECT =====
    $stmt = $pdo->query("SELECT id, nombre_entidad, siglas FROM entidades_formadoras WHERE estado = 'activo' ORDER BY nombre_entidad");
    $entidades = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ===== OBTENER TODOS LOS CURSOS =====
    $stmt = $pdo->query("
        SELECT 
            c.id,
            c.codigo_curso,
            c.titulo_curso,
            c.descripcion_curso,
            c.duracion_horas,
            c.modalidad,
            c.fecha_inicio,
            c.fecha_fin,
            c.cupos_maximos,
            c.estado,
            c.imagen_portada,
            e.id AS entidad_id,
            e.nombre_entidad,
            e.siglas
        FROM cursos c
        INNER JOIN entidades_formadoras e ON c.entidad_id = e.id
        ORDER BY c.id DESC
    ");
    $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Error en capacitaciones: " . $e->getMessage());
    $cursos = [];
}
?>

<style>
    :root {
        --gov-blue: #0B3A60;
        --gov-blue-light: #1A4F7A;
        --gov-green: #1E7E34;
        --gov-green-light: #2E9B4A;
        --gov-gold: #C9A84C;
        --gov-dark: #0A192F;
        --gov-bg: #F0F5FA;
        --gov-border: #D0DDE8;
        --gov-radius: 12px;
        --gov-radius-sm: 8px;
        --gov-shadow: rgba(11, 58, 96, 0.08);
    }

    body {
        background: var(--gov-bg);
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }

    .custom-card {
        background: #ffffff;
        border: none;
        border-radius: var(--gov-radius);
        padding: 1.5rem;
        box-shadow: 0 2px 8px var(--gov-shadow);
        transition: all 0.3s ease;
        height: 100%;
    }
    .custom-card:hover {
        box-shadow: 0 8px 25px rgba(11, 58, 96, 0.10);
    }

    .stat-card {
        background: #ffffff;
        border: none;
        border-radius: var(--gov-radius);
        padding: 1.25rem 1.5rem;
        box-shadow: 0 2px 8px var(--gov-shadow);
        transition: all 0.3s ease;
        height: 100%;
    }
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(11, 58, 96, 0.12);
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
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .stat-card .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
    }
    .bg-primary-subtle { background: rgba(11, 58, 96, 0.08); }
    .text-primary { color: var(--gov-blue) !important; }
    .bg-success-subtle { background: rgba(30, 126, 52, 0.08); }
    .text-success { color: var(--gov-green) !important; }
    .bg-warning-subtle { background: rgba(255, 193, 7, 0.12); }
    .text-warning { color: #ffc107 !important; }
    .bg-danger-subtle { background: rgba(220, 53, 69, 0.08); }
    .text-danger { color: #dc3545 !important; }
    .bg-info-subtle { background: rgba(13, 202, 240, 0.12); }
    .text-info { color: #0dcaf0 !important; }

    .badge-estado {
        font-weight: 500;
        padding: 0.3rem 0.8rem;
        border-radius: 20px;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .badge-estado.activo {
        background: #d4edda;
        color: #155724;
    }
    .badge-estado.proximamente {
        background: #fff3cd;
        color: #856404;
    }
    .badge-estado.finalizado {
        background: #e2e3e5;
        color: #383d41;
    }

    .badge-modalidad {
        font-weight: 500;
        padding: 0.25rem 0.7rem;
        border-radius: 20px;
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .badge-modalidad.presencial {
        background: #cce5ff;
        color: #004085;
    }
    .badge-modalidad.online {
        background: #d1ecf1;
        color: #0c5460;
    }
    .badge-modalidad.hibrido {
        background: #fff3cd;
        color: #856404;
    }

    .table-wrap {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        max-height: none;
        overflow-y: visible;
    }

    .table {
        min-width: 100%;
        margin-bottom: 0;
    }
    .table th {
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        color: #6b7a8a;
        border-bottom: 2px solid var(--gov-border);
        background: var(--gov-bg);
        white-space: nowrap;
        padding: 0.75rem 1rem;
    }
    .table td {
        vertical-align: middle;
        padding: 0.75rem 1rem;
        word-wrap: break-word;
        max-width: 200px;
    }
    .table tbody tr:hover {
        background: rgba(11, 58, 96, 0.02);
    }

    .btn-outline-secondary {
        border: 1px solid var(--gov-border);
        color: var(--gov-dark);
        border-radius: var(--gov-radius-sm);
        padding: 0.3rem 0.7rem;
        font-size: 0.8rem;
        transition: all 0.2s;
    }
    .btn-outline-secondary:hover {
        background: var(--gov-bg);
        border-color: var(--gov-blue);
        color: var(--gov-blue);
    }

    .btn-primary {
        background: var(--gov-blue);
        border: none;
        border-radius: var(--gov-radius-sm);
        padding: 0.5rem 1.2rem;
        font-weight: 500;
        transition: all 0.3s;
    }
    .btn-primary:hover {
        background: var(--gov-blue-light);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(11, 58, 96, 0.2);
    }

    .btn-outline-primary {
        border: 1px solid var(--gov-blue);
        color: var(--gov-blue);
        border-radius: var(--gov-radius-sm);
        padding: 0.3rem 0.7rem;
        font-size: 0.8rem;
        transition: all 0.2s;
    }
    .btn-outline-primary:hover {
        background: var(--gov-blue);
        color: white;
    }

    .btn-outline-danger {
        border: 1px solid #dc3545;
        color: #dc3545;
        border-radius: var(--gov-radius-sm);
        padding: 0.3rem 0.7rem;
        font-size: 0.8rem;
        transition: all 0.2s;
    }
    .btn-outline-danger:hover {
        background: #dc3545;
        color: white;
    }

    .btn-light {
        background: var(--gov-bg);
        border: 1px solid var(--gov-border);
        color: var(--gov-blue);
        border-radius: var(--gov-radius-sm);
        padding: 0.3rem 0.7rem;
        font-size: 0.8rem;
        transition: all 0.2s;
    }
    .btn-light:hover {
        background: var(--gov-blue);
        color: white;
        border-color: var(--gov-blue);
    }

    .modal-content {
        border: none;
        border-radius: var(--gov-radius);
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    }
    .modal-header {
        border-bottom: 2px solid var(--gov-border);
        background: var(--gov-bg);
    }
    .modal-header .modal-title {
        font-weight: 700;
        color: var(--gov-blue);
    }
    .modal-footer {
        border-top: 1px solid var(--gov-border);
        background: var(--gov-bg);
    }
    .form-control, .form-select {
        border-radius: var(--gov-radius-sm);
        border: 1.5px solid var(--gov-border);
        padding: 0.6rem 1rem;
        transition: all 0.3s;
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--gov-blue);
        box-shadow: 0 0 0 3px rgba(11, 58, 96, 0.10);
    }
    .form-label {
        font-weight: 600;
        font-size: 0.85rem;
        color: var(--gov-dark);
    }

    .list-group-item {
        border: none;
        border-bottom: 1px solid var(--gov-border);
        padding: 0.75rem 0;
    }
    .list-group-item:last-child {
        border-bottom: none;
    }

    @media (max-width: 768px) {
        .stat-card .stat-number {
            font-size: 1.4rem;
        }
        .custom-card {
            padding: 1rem;
        }
        .table td, .table th {
            padding: 0.5rem 0.75rem;
            font-size: 0.85rem;
        }
        .table td {
            max-width: 120px;
        }
    }
    @media (max-width: 576px) {
        .stat-card {
            padding: 0.8rem;
        }
        .stat-card .stat-number {
            font-size: 1.2rem;
        }
        .table td, .table th {
            padding: 0.3rem 0.5rem;
            font-size: 0.75rem;
        }
        .table td {
            max-width: 80px;
        }
    }
</style>

<div class="d-flex" id="wrapper">
    <?php include_once '../componentes/menu_admin.php'; ?>

    <div class="container-fluid p-4">

        <!-- ===== ESTADÍSTICAS ===== -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="stat-label">Total Cursos</span>
                            <h3 class="stat-number"><?php echo number_format($total_cursos); ?></h3>
                            <small class="text-muted">Registrados en el sistema</small>
                        </div>
                        <div class="stat-icon bg-primary-subtle text-primary">
                            <i class="bi bi-journal-bookmark"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="stat-label">Activos</span>
                            <h3 class="stat-number text-success"><?php echo number_format($cursos_activos); ?></h3>
                            <small class="text-success">Disponibles para inscripción</small>
                        </div>
                        <div class="stat-icon bg-success-subtle text-success">
                            <i class="bi bi-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="stat-label">Próximamente</span>
                            <h3 class="stat-number text-warning"><?php echo number_format($cursos_proximamente); ?></h3>
                            <small class="text-warning">Próxima apertura</small>
                        </div>
                        <div class="stat-icon bg-warning-subtle text-warning">
                            <i class="bi bi-clock-history"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="stat-label">Finalizados</span>
                            <h3 class="stat-number text-danger"><?php echo number_format($cursos_finalizados); ?></h3>
                            <small class="text-danger">Capacitaciones completadas</small>
                        </div>
                        <div class="stat-icon bg-danger-subtle text-danger">
                            <i class="bi bi-check-all"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== TABLA DE CURSOS ===== -->
        <div class="row">
            <div class="col-12">
                <div class="custom-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold m-0">
                            <i class="bi bi-list-ul me-2"></i>
                            Catálogo de Cursos
                            <span class="badge bg-light text-dark border ms-2"><?php echo count($cursos); ?> total</span>
                        </h5>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalCrearCurso">
                            <i class="bi bi-plus-lg me-1"></i> Añadir Curso
                        </button>
                    </div>

                    <div class="table-wrap">
                        <table class="table table-hover align-middle" id="tablaCursos">
                            <thead>
                                <tr>
                                    <th style="min-width: 100px;">Código</th>
                                    <th style="min-width: 160px;">Título</th>
                                    <th style="min-width: 150px;">Entidad</th>
                                    <th style="min-width: 100px;">Modalidad</th>
                                    <th style="min-width: 80px;">Horas</th>
                                    <th style="min-width: 100px;">Periodo</th>
                                    <th style="min-width: 90px;">Estado</th>
                                    <th style="min-width: 140px;" class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($cursos)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            <i class="bi bi-journal-x fs-2 d-block mb-2"></i>
                                            No hay cursos registrados.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($cursos as $curso): ?>
                                        <tr>
                                            <td>
                                                <span class="font-monospace fw-bold text-primary">
                                                    <?php echo htmlspecialchars($curso['codigo_curso']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="fw-semibold" style="font-size: 0.9rem;">
                                                    <?php echo htmlspecialchars($curso['titulo_curso']); ?>
                                                </div>
                                                <small class="text-muted" style="font-size: 0.7rem;">
                                                    <i class="bi bi-people me-1"></i><?php echo (int)$curso['cupos_maximos']; ?> cupos
                                                </small>
                                            </td>
                                            <td>
                                                <div style="font-size: 0.85rem;">
                                                    <?php echo htmlspecialchars($curso['nombre_entidad']); ?>
                                                </div>
                                                <?php if (!empty($curso['siglas'])): ?>
                                                    <span class="badge bg-light text-dark border">
                                                        <?php echo htmlspecialchars($curso['siglas']); ?>
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge-modalidad <?php echo $curso['modalidad']; ?>">
                                                    <?php echo ucfirst($curso['modalidad']); ?>
                                                </span>
                                            </td>
                                            <td style="font-size: 0.85rem;">
                                                <?php echo (int)$curso['duracion_horas']; ?> hrs
                                            </td>
                                            <td style="font-size: 0.8rem;">
                                                <?php if ($curso['fecha_inicio']): ?>
                                                    <?php echo date('d/m/Y', strtotime($curso['fecha_inicio'])); ?>
                                                    <?php if ($curso['fecha_fin']): ?>
                                                        - <?php echo date('d/m/Y', strtotime($curso['fecha_fin'])); ?>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="text-muted">Por definir</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge-estado <?php echo $curso['estado']; ?>">
                                                    <?php echo ucfirst($curso['estado']); ?>
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <div class="d-flex gap-1 justify-content-end">
                                                    <button class="btn btn-outline-secondary btn-sm btn-editar-curso"
                                                            data-id="<?php echo $curso['id']; ?>"
                                                            data-codigo="<?php echo htmlspecialchars($curso['codigo_curso']); ?>"
                                                            data-titulo="<?php echo htmlspecialchars($curso['titulo_curso']); ?>"
                                                            data-entidad="<?php echo $curso['entidad_id']; ?>"
                                                            data-modalidad="<?php echo htmlspecialchars($curso['modalidad']); ?>"
                                                            data-duracion="<?php echo $curso['duracion_horas']; ?>"
                                                            data-cupos="<?php echo $curso['cupos_maximos']; ?>"
                                                            data-f-inicio="<?php echo $curso['fecha_inicio']; ?>"
                                                            data-f-fin="<?php echo $curso['fecha_fin']; ?>"
                                                            data-estado="<?php echo htmlspecialchars($curso['estado']); ?>"
                                                            data-descripcion="<?php echo htmlspecialchars($curso['descripcion_curso']); ?>"
                                                            data-portada="<?php echo htmlspecialchars($curso['imagen_portada'] ?? 'img/cursos/default.jpg'); ?>"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalEditarCurso"
                                                            title="Editar">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <button class="btn btn-outline-secondary btn-sm btn-ver-detalle"
                                                            data-id="<?php echo $curso['id']; ?>"
                                                            data-codigo="<?php echo htmlspecialchars($curso['codigo_curso']); ?>"
                                                            data-titulo="<?php echo htmlspecialchars($curso['titulo_curso']); ?>"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalDetalleCurso"
                                                            title="Ver detalles">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    <button class="btn btn-outline-secondary btn-sm text-danger" title="Eliminar">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- ===== MODAL CREAR CURSO ===== -->
<div class="modal fade" id="modalCrearCurso" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-journal-plus me-2"></i>Registrar Nuevo Curso
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="../php/procesar_curso.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Título del Curso *</label>
                            <input type="text" name="titulo_curso" class="form-control" placeholder="Ej: Especialización en Redes" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Entidad Formadora *</label>
                            <select name="entidad_id" class="form-select" required>
                                <option value="" disabled selected>-- Seleccionar Entidad --</option>
                                <?php foreach ($entidades as $entidad): ?>
                                    <option value="<?php echo $entidad['id']; ?>">
                                        <?php echo htmlspecialchars($entidad['nombre_entidad']); ?>
                                        <?php echo !empty($entidad['siglas']) ? '(' . htmlspecialchars($entidad['siglas']) . ')' : ''; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Modalidad *</label>
                            <select name="modalidad" class="form-select" required>
                                <option value="presencial">Presencial</option>
                                <option value="online">Online</option>
                                <option value="hibrido">Híbrido</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Duración (Horas) *</label>
                            <input type="number" name="duracion_horas" class="form-control" placeholder="40" min="1" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Cupos Máximos *</label>
                            <input type="number" name="cupos_maximos" class="form-control" value="30" min="1" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Fecha Inicio</label>
                            <input type="date" name="fecha_inicio" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Fecha Fin</label>
                            <input type="date" name="fecha_fin" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Estado</label>
                            <select name="estado" class="form-select">
                                <option value="activo">Activo</option>
                                <option value="proximamente">Próximamente</option>
                                <option value="finalizado">Finalizado</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Descripción *</label>
                            <textarea name="descripcion_curso" class="form-control" rows="3" placeholder="Detalles del curso..." required></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Imagen de Portada</label>
                            <input type="file" name="imagen_portada" class="form-control" accept="image/*">
                            <small class="text-muted">Formatos: JPG, PNG, WEBP (Máx. 2MB)</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check2-circle me-1"></i> Guardar Curso
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ===== MODAL EDITAR CURSO ===== -->
<div class="modal fade" id="modalEditarCurso" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-pencil-square me-2"></i>Editar Curso
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="../php/actualizar_curso.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" id="edit_id" name="id">
                <input type="hidden" id="edit_imagen_actual" name="imagen_actual">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Código</label>
                            <input type="text" id="edit_codigo_curso" class="form-control font-monospace bg-light" readonly>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Título *</label>
                            <input type="text" id="edit_titulo_curso" name="titulo_curso" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Entidad Formadora *</label>
                            <select id="edit_entidad_id" name="entidad_id" class="form-select" required>
                                <?php foreach ($entidades as $entidad): ?>
                                    <option value="<?php echo $entidad['id']; ?>">
                                        <?php echo htmlspecialchars($entidad['nombre_entidad']); ?>
                                        <?php echo !empty($entidad['siglas']) ? '(' . htmlspecialchars($entidad['siglas']) . ')' : ''; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Modalidad *</label>
                            <select id="edit_modalidad" name="modalidad" class="form-select" required>
                                <option value="presencial">Presencial</option>
                                <option value="online">Online</option>
                                <option value="hibrido">Híbrido</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Duración (Horas) *</label>
                            <input type="number" id="edit_duracion_horas" name="duracion_horas" class="form-control" min="1" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Cupos Máximos *</label>
                            <input type="number" id="edit_cupos_maximos" name="cupos_maximos" class="form-control" min="1" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Fecha Inicio</label>
                            <input type="date" id="edit_fecha_inicio" name="fecha_inicio" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Fecha Fin</label>
                            <input type="date" id="edit_fecha_fin" name="fecha_fin" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Estado</label>
                            <select id="edit_estado" name="estado" class="form-select">
                                <option value="activo">Activo</option>
                                <option value="proximamente">Próximamente</option>
                                <option value="finalizado">Finalizado</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Descripción *</label>
                            <textarea id="edit_descripcion_curso" name="descripcion_curso" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Cambiar Imagen de Portada</label>
                            <input type="file" id="edit_imagen_portada" name="imagen_portada" class="form-control" accept="image/*">
                            <small class="text-muted">Deja vacío para mantener la imagen actual.</small>
                            <div class="mt-2">
                                <img id="edit_previewPortada" src="img/cursos/default.jpg" alt="Portada" style="max-height: 100px; border-radius: 8px;">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check2-circle me-1"></i> Actualizar Curso
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ===== MODAL DETALLE CURSO ===== -->
<div class="modal fade" id="modalDetalleCurso" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-journal-text me-2"></i>Detalle del Curso
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="contenedorDetalleCurso">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="text-muted mt-2 mb-0">Cargando información...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ===== INICIALIZAR DATATABLE =====
    const tabla = document.getElementById('tablaCursos');
    if (tabla && tabla.querySelector('tbody tr') && tabla.querySelector('tbody tr').cells.length > 1) {
        if (typeof $.fn.DataTable !== 'undefined') {
            $(tabla).DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
                },
                pageLength: 10,
                order: [[0, 'desc']],
                responsive: true,
                columnDefs: [
                    { orderable: false, targets: 7 }
                ],
                scrollX: false,
                autoWidth: true,
                scrollY: false,
                scrollCollapse: false
            });
        }
    }

    // ===== EDITAR CURSO =====
    document.querySelectorAll('.btn-editar-curso').forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('edit_id').value = this.dataset.id || '';
            document.getElementById('edit_codigo_curso').value = this.dataset.codigo || '';
            document.getElementById('edit_titulo_curso').value = this.dataset.titulo || '';
            document.getElementById('edit_entidad_id').value = this.dataset.entidad || '';
            document.getElementById('edit_modalidad').value = this.dataset.modalidad || 'presencial';
            document.getElementById('edit_duracion_horas').value = this.dataset.duracion || '';
            document.getElementById('edit_cupos_maximos').value = this.dataset.cupos || '';
            document.getElementById('edit_fecha_inicio').value = this.dataset.fInicio || '';
            document.getElementById('edit_fecha_fin').value = this.dataset.fFin || '';
            document.getElementById('edit_estado').value = this.dataset.estado || 'activo';
            document.getElementById('edit_descripcion_curso').value = this.dataset.descripcion || '';
            
            const portada = this.dataset.portada || 'img/cursos/default.jpg';
            document.getElementById('edit_imagen_actual').value = portada;
            document.getElementById('edit_previewPortada').src = portada;
        });
    });

    // ===== PREVISUALIZACIÓN DE IMAGEN EN EDICIÓN =====
    document.getElementById('edit_imagen_portada')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('edit_previewPortada').src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });

    // ===== PREVISUALIZACIÓN DE IMAGEN EN CREACIÓN =====
    document.getElementById('imagen_portada')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('previewPortada')?.setAttribute('src', e.target.result);
            };
            reader.readAsDataURL(file);
        }
    });

    // ===== VER DETALLE CURSO =====
    document.querySelectorAll('.btn-ver-detalle').forEach(button => {
        button.addEventListener('click', function() {
            const cursoId = this.dataset.id;
            const contenedor = document.getElementById('contenedorDetalleCurso');

            contenedor.innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="text-muted mt-2 mb-0">Cargando información...</p>
                </div>`;

            fetch(`../php/obtener_detalle_curso.php?id=${cursoId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'error') {
                        contenedor.innerHTML = `<div class="alert alert-danger mb-0">${data.message}</div>`;
                        return;
                    }

                    const c = data.curso;
                    
                    const badgeEstado = c.estado === 'activo' ? 'bg-success' : 
                                       (c.estado === 'proximamente' ? 'bg-warning text-dark' : 'bg-secondary');
                    const badgeModalidad = c.modalidad === 'presencial' ? 'bg-primary' : 
                                          (c.modalidad === 'online' ? 'bg-info text-dark' : 'bg-warning text-dark');

                    contenedor.innerHTML = `
                        <div class="position-relative mb-4 rounded overflow-hidden shadow-sm" style="max-height: 200px;">
                            <img src="../${c.imagen_portada || 'img/cursos/default.jpg'}" class="w-100 object-fit-cover" style="max-height: 200px; object-position: center;" onerror="this.src='https://placehold.co/800x300?text=Portada+Curso';">
                            <span class="position-absolute top-0 end-0 m-3 badge ${badgeEstado} fs-7 text-capitalize shadow-sm">
                                ${c.estado}
                            </span>
                        </div>

                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="font-monospace fw-bold text-primary fs-6 bg-primary-subtle px-2 py-1 rounded">
                                ${c.codigo_curso}
                            </span>
                            <span class="badge ${badgeModalidad} text-capitalize fs-7">
                                ${c.modalidad}
                            </span>
                        </div>

                        <h4 class="fw-bold text-dark mb-2">${c.titulo_curso}</h4>
                        <p class="text-muted small mb-4">${c.descripcion_curso}</p>

                        <div class="card bg-light border-0 p-3 mb-3">
                            <div class="row g-3 small">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-building fs-5 text-primary me-2"></i>
                                        <div>
                                            <span class="text-muted d-block fs-8">Entidad Formadora</span>
                                            <strong class="text-dark">${c.nombre_entidad} ${c.siglas ? '(' + c.siglas + ')' : ''}</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-geo-alt fs-5 text-primary me-2"></i>
                                        <div>
                                            <span class="text-muted d-block fs-8">Provincia</span>
                                            <strong class="text-dark">${c.provincia || 'Bioko Norte'}</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-clock fs-5 text-primary me-2"></i>
                                        <div>
                                            <span class="text-muted d-block fs-8">Duración</span>
                                            <strong class="text-dark">${c.duracion_horas} Horas</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-people fs-5 text-primary me-2"></i>
                                        <div>
                                            <span class="text-muted d-block fs-8">Cupos</span>
                                            <strong class="text-dark">${c.cupos_maximos} Alumnos</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-calendar-range fs-5 text-primary me-2"></i>
                                        <div>
                                            <span class="text-muted d-block fs-8">Periodo</span>
                                            <strong class="text-dark">${c.fecha_inicio || 'Por definir'} - ${c.fecha_fin || 'Por definir'}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-muted small">
                            <i class="bi bi-info-circle me-1"></i> Código: ${c.codigo_curso}
                        </div>
                    `;
                })
                .catch(error => {
                    contenedor.innerHTML = `<div class="alert alert-danger mb-0">Error al cargar los datos.</div>`;
                });
        });
    });
});
</script>

<?php include_once '../componentes/footer_admin.php'; ?>
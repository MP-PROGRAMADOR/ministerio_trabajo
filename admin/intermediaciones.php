<?php
session_start();

// ===== VERIFICAR SESIÓN ADMINISTRATIVA =====
if (!isset($_SESSION['id_usuario']) || !in_array($_SESSION['rol'] ?? '', ['administrador', 'ministerio'])) {
    header('Location: ../login_admin.php');
    exit();
}

$titulo = 'Intermediaciones - Panel de Administración';
include_once '../componentes/header_admin.php';
include_once '../conexion/conexion.php';

// ===== INICIALIZAR VARIABLES =====
$intermediaciones = [];
$total_pendientes = 0;
$total_aprobados = 0;
$total_rechazados = 0;
$total_en_revision = 0;

try {
    // ===== CONTAR ESTADÍSTICAS =====
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM notificaciones_intermediacion");
    $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM notificaciones_intermediacion WHERE estado_ministerio = 'pendiente'");
    $total_pendientes = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM notificaciones_intermediacion WHERE estado_ministerio = 'aprobado'");
    $total_aprobados = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM notificaciones_intermediacion WHERE estado_ministerio = 'rechazado'");
    $total_rechazados = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM notificaciones_intermediacion WHERE estado_ministerio = 'en_revision'");
    $total_en_revision = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // ===== OBTENER TODAS LAS INTERMEDIACIONES =====
    $stmt = $pdo->query("
        SELECT 
            ni.id,
            ni.codigo_seguimiento,
            ni.estado_ministerio,
            ni.fecha_creacion,
            ni.motivo_empresa,
            u_b.nombre as buscador_nombre,
            u_b.apellidos as buscador_apellidos,
            u_b.numero_expediente as buscador_expediente,
            b.estado_laboral as buscador_estado,
            b.provincia as buscador_provincia,
            e.nombre_empresa,
            e.rnc_ruc,
            o.titulo_puesto,
            o.salario_ofrecido
        FROM notificaciones_intermediacion ni
        INNER JOIN buscadores_empleo b ON ni.buscador_id = b.id
        INNER JOIN usuarios u_b ON b.usuario_id = u_b.id
        INNER JOIN empleadores e ON ni.empleador_id = e.id
        LEFT JOIN ofertas_empleo o ON ni.oferta_id = o.id
        WHERE ni.estado_ministerio = 'en_revision' -- <-- FILTRO AÑADIDO AQUÍ
        ORDER BY ni.fecha_creacion DESC
    ");
    $intermediaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Error en intermediaciones: " . $e->getMessage());
    $intermediaciones = [];
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
    .bg-warning-subtle { background: rgba(255, 193, 7, 0.12); }
    .text-warning { color: #ffc107 !important; }
    .bg-success-subtle { background: rgba(30, 126, 52, 0.08); }
    .text-success { color: var(--gov-green) !important; }
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
    .badge-estado.pendiente {
        background: #fff3cd;
        color: #856404;
    }
    .badge-estado.aprobado {
        background: #d4edda;
        color: #155724;
    }
    .badge-estado.rechazado {
        background: #f8d7da;
        color: #721c24;
    }
    .badge-estado.en_revision {
        background: #cce5ff;
        color: #004085;
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

    .btn-success {
        background: var(--gov-green);
        border: none;
        border-radius: var(--gov-radius-sm);
        padding: 0.3rem 0.7rem;
        font-size: 0.8rem;
        transition: all 0.2s;
    }
    .btn-success:hover {
        background: var(--gov-green-light);
        transform: translateY(-1px);
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
                            <span class="stat-label">Pendientes</span>
                            <h3 class="stat-number text-warning"><?php echo number_format($total_pendientes); ?></h3>
                            <small class="text-warning">Requieren revisión</small>
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
                            <span class="stat-label">En Revisión</span>
                            <h3 class="stat-number text-info"><?php echo number_format($total_en_revision); ?></h3>
                            <small class="text-info">En proceso</small>
                        </div>
                        <div class="stat-icon bg-info-subtle text-info">
                            <i class="bi bi-arrow-repeat"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="stat-label">Aprobados</span>
                            <h3 class="stat-number text-success"><?php echo number_format($total_aprobados); ?></h3>
                            <small class="text-success">Intermediaciones exitosas</small>
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
                            <span class="stat-label">Rechazados</span>
                            <h3 class="stat-number text-danger"><?php echo number_format($total_rechazados); ?></h3>
                            <small class="text-danger">Solicitudes denegadas</small>
                        </div>
                        <div class="stat-icon bg-danger-subtle text-danger">
                            <i class="bi bi-x-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== TABLA DE INTERMEDIACIONES ===== -->
        <div class="row">
            <div class="col-12">
                <div class="custom-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold m-0">
                            <i class="bi bi-list-ul me-2"></i>
                            Lista de Intermediaciones
                            <span class="badge bg-light text-dark border ms-2"><?php echo count($intermediaciones); ?> total</span>
                        </h5>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAñadir">
                            <i class="bi bi-plus-lg me-1"></i> Añadir
                        </button>
                    </div>

                    <div class="table-wrap">
                        <table class="table table-hover align-middle" id="tablaIntermediaciones">
                            <thead>
                                <tr>
                                    <th style="min-width: 140px;">Cód. Seguimiento</th>
                                    <th style="min-width: 100px;">Origen</th>
                                    <th style="min-width: 150px;">Desempleado</th>
                                    <th style="min-width: 160px;">Empresa / Oferta</th>
                                    <th style="min-width: 120px;">Estado</th>
                                    <th style="min-width: 100px;">Fecha</th>
                                    <th style="min-width: 140px;" class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($intermediaciones)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                            No hay intermediaciones registradas.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($intermediaciones as $inter): ?>
                                        <tr>
                                            <td>
                                                <span class="font-monospace fw-bold text-primary">
                                                    <?php echo htmlspecialchars($inter['codigo_seguimiento']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border">
                                                    Empleador
                                                </span>
                                            </td>
                                            <td>
                                                <div class="fw-semibold" style="font-size: 0.85rem;">
                                                    <?php echo htmlspecialchars($inter['buscador_nombre'] . ' ' . $inter['buscador_apellidos']); ?>
                                                </div>
                                                <small class="text-muted" style="font-size: 0.7rem;">
                                                    EXP: <?php echo htmlspecialchars($inter['buscador_expediente']); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <div style="font-size: 0.85rem; font-weight: 500;">
                                                    <?php echo htmlspecialchars($inter['nombre_empresa']); ?>
                                                </div>
                                                <small class="text-muted" style="font-size: 0.7rem;">
                                                    <?php echo htmlspecialchars($inter['titulo_puesto'] ?? 'Contacto Directo'); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <?php
                                                $estado = $inter['estado_ministerio'] ?? 'pendiente';
                                                $badge_class = match ($estado) {
                                                    'aprobado' => 'badge-estado aprobado',
                                                    'rechazado' => 'badge-estado rechazado',
                                                    'en_revision' => 'badge-estado en_revision',
                                                    default => 'badge-estado pendiente'
                                                };
                                                $estado_label = match ($estado) {
                                                    'aprobado' => 'Aprobado',
                                                    'rechazado' => 'Rechazado',
                                                    'en_revision' => 'En Revisión',
                                                    default => 'Pendiente'
                                                };
                                                ?>
                                                <span class="<?php echo $badge_class; ?>">
                                                    <?php echo $estado_label; ?>
                                                </span>
                                            </td>
                                            <td style="font-size: 0.8rem;">
                                                <?php echo date('d M, Y', strtotime($inter['fecha_creacion'])); ?>
                                            </td>
                                            <td class="text-end">
                                                <div class="d-flex gap-1 justify-content-end">
                                                    <?php if ($estado === 'pendiente'): ?>
                                                        <button class="btn btn-sm btn-success rounded-circle" title="Aprobar">
                                                            <i class="bi bi-check-lg"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-danger rounded-circle" title="Rechazar">
                                                            <i class="bi bi-x-lg"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                    <button class="btn btn-sm btn-light rounded-circle btn-ver-detalle"
                                                            data-codigo="<?php echo htmlspecialchars($inter['codigo_seguimiento']); ?>"
                                                            data-candidato="<?php echo htmlspecialchars($inter['buscador_nombre'] . ' ' . $inter['buscador_apellidos']); ?>"
                                                            data-expediente="<?php echo htmlspecialchars($inter['buscador_expediente']); ?>"
                                                            data-estado="<?php echo htmlspecialchars($inter['buscador_estado']); ?>"
                                                            data-provincia="<?php echo htmlspecialchars($inter['buscador_provincia']); ?>"
                                                            data-empresa="<?php echo htmlspecialchars($inter['nombre_empresa']); ?>"
                                                            data-puesto="<?php echo htmlspecialchars($inter['titulo_puesto'] ?? 'N/A'); ?>"
                                                            data-ruc="<?php echo htmlspecialchars($inter['rnc_ruc'] ?? 'N/A'); ?>"
                                                            data-salario="<?php echo $inter['salario_ofrecido'] ? number_format($inter['salario_ofrecido'], 0, ',', '.') . ' XAF' : 'No especificado'; ?>"
                                                            data-motivo="<?php echo htmlspecialchars($inter['motivo_empresa'] ?? 'Sin observaciones.'); ?>"
                                                            data-estado-ministerio="<?php echo $estado_label; ?>"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalDetalleNotificacion"
                                                            title="Ver detalles">
                                                        <i class="bi bi-eye"></i>
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

<!-- ===== MODAL AÑADIR ===== -->
<div class="modal fade" id="modalAñadir" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-plus-circle me-2" style="color: var(--gov-gold);"></i>
                    Nueva Intermediación
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Código de Seguimiento</label>
                        <input type="text" class="form-control" placeholder="Ej. MITRAD-2026-XX" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Buscador</label>
                        <select class="form-select" required>
                            <option value="">Seleccionar...</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Empresa</label>
                        <select class="form-select" required>
                            <option value="">Seleccionar...</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Oferta (Opcional)</label>
                        <select class="form-select">
                            <option value="">Seleccionar...</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Motivo</label>
                        <textarea class="form-control" rows="3" placeholder="Motivo de la intermediación..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Estado Inicial</label>
                        <select class="form-select">
                            <option value="pendiente">Pendiente</option>
                            <option value="en_revision">En Revisión</option>
                            <option value="aprobado">Aprobado</option>
                            <option value="rechazado">Rechazado</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary">
                    <i class="bi bi-check2-circle me-1"></i> Guardar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ===== MODAL DETALLE NOTIFICACIÓN ===== -->
<div class="modal fade" id="modalDetalleNotificacion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold">Expediente de Intermediación: <span id="modal-codigo" class="text-primary"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6 border-end">
                        <h6 class="fw-bold text-muted" style="font-size: 0.8rem; text-transform: uppercase;">Datos del Candidato</h6>
                        <p class="mb-1"><strong>Nombre:</strong> <span id="modal-candidato"></span></p>
                        <p class="mb-1"><strong>Nº Expediente:</strong> <span id="modal-expediente"></span></p>
                        <p class="mb-1"><strong>Estado Actual:</strong> <span id="modal-estado" class="badge bg-secondary"></span></p>
                        <p class="mb-0"><strong>Provincia:</strong> <span id="modal-provincia"></span></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold text-muted" style="font-size: 0.8rem; text-transform: uppercase;">Datos de la Empresa / Oferta</h6>
                        <p class="mb-1"><strong>Empresa:</strong> <span id="modal-empresa"></span></p>
                        <p class="mb-1"><strong>Puesto:</strong> <span id="modal-puesto"></span></p>
                        <p class="mb-1"><strong>RUC / RNC:</strong> <span id="modal-ruc"></span></p>
                        <p class="mb-0"><strong>Salario:</strong> <span id="modal-salario"></span></p>
                    </div>
                    <div class="col-12">
                        <hr>
                        <label class="form-label fw-bold">Motivo de Intermediación</label>
                        <div id="modal-motivo" class="p-3 bg-light rounded text-muted"></div>
                    </div>
                    <div class="col-12">
                        <hr>
                        <label class="form-label fw-bold">Estado Ministerio</label>
                        <div><span id="modal-estado-ministerio" class="badge bg-warning">Pendiente</span></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-danger"><i class="bi bi-x-circle me-1"></i> Rechazar</button>
                <button type="button" class="btn btn-success"><i class="bi bi-check-circle me-1"></i> Aprobar</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ===== INICIALIZAR DATATABLE =====
    const tabla = document.getElementById('tablaIntermediaciones');
    if (tabla && tabla.querySelector('tbody tr') && tabla.querySelector('tbody tr').cells.length > 1) {
        if (typeof $.fn.DataTable !== 'undefined') {
            $(tabla).DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
                },
                pageLength: 10,
                order: [[5, 'desc']],
                responsive: true,
                columnDefs: [
                    { orderable: false, targets: 6 }
                ],
                scrollX: false,
                autoWidth: true,
                scrollY: false,
                scrollCollapse: false
            });
        }
    }

    // ===== PASAR DATOS AL MODAL DE DETALLE =====
    document.querySelectorAll('.btn-ver-detalle').forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('modal-codigo').textContent = this.dataset.codigo || 'N/A';
            document.getElementById('modal-candidato').textContent = this.dataset.candidato || 'N/A';
            document.getElementById('modal-expediente').textContent = this.dataset.expediente || 'N/A';
            
            const estadoEl = document.getElementById('modal-estado');
            const estado = this.dataset.estado || 'desempleado';
            const badgeMap = {
                'desempleado': 'bg-warning',
                'contratado': 'bg-success',
                'suspendido': 'bg-danger'
            };
            estadoEl.className = 'badge ' + (badgeMap[estado] || 'bg-secondary');
            estadoEl.textContent = estado.charAt(0).toUpperCase() + estado.slice(1);
            
            document.getElementById('modal-provincia').textContent = this.dataset.provincia || 'N/A';
            document.getElementById('modal-empresa').textContent = this.dataset.empresa || 'N/A';
            document.getElementById('modal-puesto').textContent = this.dataset.puesto || 'N/A';
            document.getElementById('modal-ruc').textContent = this.dataset.ruc || 'N/A';
            document.getElementById('modal-salario').textContent = this.dataset.salario || 'N/A';
            document.getElementById('modal-motivo').textContent = this.dataset.motivo || 'Sin observaciones.';
            
            const estadoMinisterio = this.dataset.estadoMinisterio || 'Pendiente';
            const estadoMinisterioEl = document.getElementById('modal-estado-ministerio');
            const badgeMapMinisterio = {
                'Pendiente': 'bg-warning',
                'En Revisión': 'bg-info',
                'Aprobado': 'bg-success',
                'Rechazado': 'bg-danger'
            };
            estadoMinisterioEl.className = 'badge ' + (badgeMapMinisterio[estadoMinisterio] || 'bg-secondary');
            estadoMinisterioEl.textContent = estadoMinisterio;
        });
    });
});
</script>

<?php include_once '../componentes/footer_admin.php'; ?>
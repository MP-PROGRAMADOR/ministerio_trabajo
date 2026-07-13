<?php
session_start();

// ===== VERIFICAR SESIÓN ADMINISTRATIVA =====
if (!isset($_SESSION['id_usuario']) || !in_array($_SESSION['rol'] ?? '', ['administrador', 'ministerio'])) {
    header('Location: ../login_admin.php');
    exit();
}

$titulo = 'Panel de Administración - Portal de Empleo';
include_once '../componentes/header_admin.php';
include_once '../conexion/conexion.php';

// ===== INICIALIZAR VARIABLES =====
$total_desempleados = 0;
$total_empresas = 0;
$intermediaciones_pendientes = 0;
$total_cursos_activos = 0;
$buscadores = [];
$cursos = [];
$entidades = [];
$notificaciones = [];
$labels_circular = ['Desempleados', 'Contratados', 'Suspendidos'];
$data_circular = [0, 0, 0];
$labels_barras = ['Pendientes', 'En Revisión', 'Aprobados', 'Rechazados'];
$data_barras = [0, 0, 0, 0];

try {
    // ===== 1. TOTAL DESEMPLEADOS =====
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM buscadores_empleo");
    $row = $stmt->fetch();
    $total_desempleados = $row['total'] ?? 0;

    // ===== 2. TOTAL EMPRESAS =====
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM empleadores");
    $row = $stmt->fetch();
    $total_empresas = $row['total'] ?? 0;

    // ===== 3. INTERMEDIACIONES PENDIENTES =====
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM notificaciones_intermediacion WHERE estado_ministerio = 'pendiente'");
    $row = $stmt->fetch();
    $intermediaciones_pendientes = $row['total'] ?? 0;

    // ===== 4. TOTAL CURSOS ACTIVOS =====
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM cursos WHERE estado = 'activo'");
    $row = $stmt->fetch();
    $total_cursos_activos = $row['total'] ?? 0;

    // ===== 5. GRÁFICO CIRCULAR =====
    $stmt = $pdo->query("SELECT estado_laboral, COUNT(*) as total FROM buscadores_empleo GROUP BY estado_laboral");
    while ($row = $stmt->fetch()) {
        if ($row['estado_laboral'] == 'desempleado') $data_circular[0] = (int)$row['total'];
        elseif ($row['estado_laboral'] == 'contratado') $data_circular[1] = (int)$row['total'];
        elseif ($row['estado_laboral'] == 'suspendido') $data_circular[2] = (int)$row['total'];
    }

    // ===== 6. GRÁFICO BARRAS =====
    $stmt = $pdo->query("SELECT estado_ministerio, COUNT(*) as total FROM notificaciones_intermediacion GROUP BY estado_ministerio");
    while ($row = $stmt->fetch()) {
        if ($row['estado_ministerio'] == 'pendiente') $data_barras[0] = (int)$row['total'];
        elseif ($row['estado_ministerio'] == 'en_revision') $data_barras[1] = (int)$row['total'];
        elseif ($row['estado_ministerio'] == 'aprobado') $data_barras[2] = (int)$row['total'];
        elseif ($row['estado_ministerio'] == 'rechazado') $data_barras[3] = (int)$row['total'];
    }

    // ===== 7. BUSCADORES =====
    $stmt = $pdo->query("
        SELECT u.id, u.numero_expediente, u.nombre, u.apellidos, u.documento_identidad,
               b.provincia, b.ciudad_municipio, b.estado_laboral,
               d.copia_dip, d.cv
        FROM usuarios u
        JOIN buscadores_empleo b ON u.id = b.usuario_id
        LEFT JOIN documentos d ON u.id = d.usuario_id
        WHERE u.rol = 'buscador'
        ORDER BY u.fecha_registro DESC
        LIMIT 10
    ");
    $buscadores = $stmt->fetchAll();

    // ===== 8. CURSOS =====
    $stmt = $pdo->query("
        SELECT c.*, ef.nombre_entidad as entidad_mostrar
        FROM cursos c
        JOIN entidades_formadoras ef ON c.entidad_id = ef.id
        WHERE c.estado IN ('activo', 'proximamente')
        ORDER BY c.fecha_creacion DESC
        LIMIT 10
    ");
    $cursos = $stmt->fetchAll();

    // ===== 9. ENTIDADES FORMADORAS =====
    $stmt = $pdo->query("
        SELECT id, nombre_entidad, siglas 
        FROM entidades_formadoras 
        WHERE estado = 'activo'
        ORDER BY nombre_entidad
    ");
    $entidades = $stmt->fetchAll();

    // ===== 10. NOTIFICACIONES =====
    $stmt = $pdo->query("
        SELECT 
            ni.id, ni.codigo_seguimiento, ni.estado_ministerio, ni.fecha_creacion, ni.motivo_empresa,
            u.nombre as buscador_nombre, u.apellidos as buscador_apellidos, u.numero_expediente as buscador_expediente,
            b.estado_laboral as buscador_estado, b.provincia as buscador_provincia,
            e.nombre_empresa, e.rnc_ruc,
            o.titulo_puesto, o.salario_ofrecido
        FROM notificaciones_intermediacion ni
        JOIN buscadores_empleo b ON ni.buscador_id = b.id
        JOIN usuarios u ON b.usuario_id = u.id
        JOIN empleadores e ON ni.empleador_id = e.id
        LEFT JOIN ofertas_empleo o ON ni.oferta_id = o.id
        ORDER BY ni.fecha_creacion DESC
        LIMIT 20
    ");
    $notificaciones = $stmt->fetchAll();

} catch (PDOException $e) {
    error_log("Error en admin: " . $e->getMessage());
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
    .bg-info-subtle { background: rgba(13, 202, 240, 0.12); }
    .text-info { color: #0dcaf0 !important; }

    .badge {
        font-weight: 500;
        padding: 0.3rem 0.8rem;
        border-radius: 20px;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .bg-success-subtle { background: #d4edda; color: #155724; }
    .bg-danger-subtle { background: #f8d7da; color: #721c24; }
    .bg-info-subtle { background: #cce5ff; color: #004085; }
    .bg-warning-subtle { background: #fff3cd; color: #856404; }

    .card {
        border: none;
        border-radius: var(--gov-radius);
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px 0 rgba(0, 0, 0, 0.03);
    }

    .card-header {
        background: #ffffff;
        border-bottom: 1px solid var(--gov-border);
        padding: 1rem 1.25rem;
    }

    .table th {
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        color: #6b7a8a;
        border-bottom: 2px solid var(--gov-border);
        background: var(--gov-bg);
        padding: 0.75rem 1rem;
    }
    .table td {
        vertical-align: middle;
        padding: 0.75rem 1rem;
    }
    .table tbody tr:hover {
        background: rgba(11, 58, 96, 0.02);
    }

    .btn-primary {
        background: var(--gov-blue);
        border: none;
        border-radius: var(--gov-radius-sm);
        padding: 0.4rem 1rem;
        font-weight: 500;
        font-size: 0.85rem;
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
        .stat-card {
            padding: 1rem;
        }
        .table td, .table th {
            padding: 0.5rem 0.75rem;
            font-size: 0.85rem;
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
    }
</style>

<div class="d-flex" id="wrapper">

    <?php include_once '../componentes/menu_admin.php'; ?>

    <div class="container-fluid p-4">

        <!-- ===== ESTADÍSTICAS ===== -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="stat-label">Desempleados Registrados</span>
                            <h3 class="stat-number"><?php echo number_format($total_desempleados); ?></h3>
                            <small class="text-success"><i class="bi bi-person-check"></i> Activos en sistema</small>
                        </div>
                        <div class="stat-icon bg-primary-subtle text-primary">
                            <i class="bi bi-people fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="stat-label">Empresas Activas</span>
                            <h3 class="stat-number"><?php echo number_format($total_empresas); ?></h3>
                            <small class="text-muted">Región Bioko / Continental</small>
                        </div>
                        <div class="stat-icon bg-success-subtle text-success">
                            <i class="bi bi-building fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="stat-label">Intermediaciones Pendientes</span>
                            <h3 class="stat-number text-warning"><?php echo number_format($intermediaciones_pendientes); ?></h3>
                            <small class="text-warning"><i class="bi bi-exclamation-circle"></i> Requiere revisión</small>
                        </div>
                        <div class="stat-icon bg-warning-subtle text-warning">
                            <i class="bi bi-shield-exclamation fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="stat-label">Cursos en Oferta</span>
                            <h3 class="stat-number text-info"><?php echo number_format($total_cursos_activos); ?></h3>
                            <small class="text-info">Entidades Formadoras</small>
                        </div>
                        <div class="stat-icon bg-info-subtle text-info">
                            <i class="bi bi-journal-text fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== GRÁFICOS ===== -->
        <div class="row g-4 mb-4">
            <div class="col-12 col-lg-5">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold">Reporte Circular: Estado Laboral</h6>
                        <small class="text-muted">Proporción de ciudadanos desempleados, contratados y suspendidos</small>
                    </div>
                    <div class="card-body d-flex justify-content-center align-items-center" style="min-height: 250px;">
                        <canvas id="chartEstadoLaboral"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-7">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold">Reporte de Barras: Intermediaciones Gubernamentales</h6>
                        <small class="text-muted">Estado de tramitación de expedientes en el Ministerio</small>
                    </div>
                    <div class="card-body" style="min-height: 250px;">
                        <canvas id="chartIntermediaciones"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== NOTIFICACIONES ===== -->
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="mb-0 fw-bold">Notificaciones de Intermediación</h6>
                    <small class="text-muted">Control gubernamental de vinculación entre empresas y trabajadores</small>
                </div>
                <button class="btn btn-outline-primary btn-sm rounded-pill"><i class="bi bi-download me-1"></i> Exportar</button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Cód. Seguimiento</th>
                            <th>Candidato</th>
                            <th>Empresa / Oferta</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($notificaciones)): ?>
                            <tr><td colspan="6" class="text-center text-muted py-3">No hay notificaciones registradas.</td></tr>
                        <?php else: ?>
                            <?php foreach ($notificaciones as $n): ?>
                                <tr>
                                    <td><span class="font-monospace fw-bold text-primary"><?php echo htmlspecialchars($n['codigo_seguimiento']); ?></span></td>
                                    <td>
                                        <div class="fw-semibold"><?php echo htmlspecialchars($n['buscador_nombre'] . ' ' . $n['buscador_apellidos']); ?></div>
                                        <small class="text-muted"><?php echo htmlspecialchars($n['buscador_expediente']); ?></small>
                                    </td>
                                    <td>
                                        <div><?php echo htmlspecialchars($n['nombre_empresa']); ?></div>
                                        <small class="text-muted"><?php echo htmlspecialchars($n['titulo_puesto'] ?? 'Contacto Directo'); ?></small>
                                    </td>
                                    <td>
                                        <?php
                                        $badge_class = match ($n['estado_ministerio']) {
                                            'aprobado' => 'bg-success-subtle text-success',
                                            'rechazado' => 'bg-danger-subtle text-danger',
                                            'en_revision' => 'bg-info-subtle text-info',
                                            default => 'bg-warning-subtle text-warning'
                                        };
                                        ?>
                                        <span class="badge <?php echo $badge_class; ?>"><?php echo ucfirst(str_replace('_', ' ', $n['estado_ministerio'])); ?></span>
                                    </td>
                                    <td><?php echo date('d M, Y', strtotime($n['fecha_creacion'])); ?></td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-light rounded-circle btn-ver-detalle"
                                            data-bs-toggle="modal" data-bs-target="#modalDetalleNotificacion"
                                            data-codigo="<?php echo htmlspecialchars($n['codigo_seguimiento']); ?>"
                                            data-candidato="<?php echo htmlspecialchars($n['buscador_nombre'] . ' ' . $n['buscador_apellidos']); ?>"
                                            data-expediente="<?php echo htmlspecialchars($n['buscador_expediente']); ?>"
                                            data-estado="<?php echo htmlspecialchars($n['buscador_estado']); ?>"
                                            data-provincia="<?php echo htmlspecialchars($n['buscador_provincia']); ?>"
                                            data-empresa="<?php echo htmlspecialchars($n['nombre_empresa']); ?>"
                                            data-puesto="<?php echo htmlspecialchars($n['titulo_puesto'] ?? 'N/A'); ?>"
                                            data-ruc="<?php echo htmlspecialchars($n['rnc_ruc'] ?? 'N/A'); ?>"
                                            data-salario="<?php echo $n['salario_ofrecido'] ? number_format($n['salario_ofrecido'], 0, ',', '.') . ' XAF' : 'No especificado'; ?>"
                                            data-motivo="<?php echo htmlspecialchars($n['motivo_empresa'] ?? 'Sin observaciones.'); ?>">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ===== BUSCADORES ===== -->
        <div class="row g-4">
            <div class="col-12 col-xl-8">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                        <h6 class="mb-0 fw-bold">Expedientes de Buscadores</h6>
                        <a href="buscadores_empleo.php" class="btn btn-primary btn-sm"> Ver todos <i class="bi bi-arrow-right me-1"></i> </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Expediente</th>
                                    <th>Nombre</th>
                                    <th>Ubicación</th>
                                    <th>Estado</th>
                                    <th class="text-end">Documentos</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($buscadores)): ?>
                                    <tr><td colspan="5" class="text-center text-muted py-3">No hay registros.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($buscadores as $b): ?>
                                        <tr>
                                            <td><span class="font-monospace fw-semibold"><?php echo htmlspecialchars($b['numero_expediente']); ?></span></td>
                                            <td><?php echo htmlspecialchars($b['nombre'] . ' ' . $b['apellidos']); ?></td>
                                            <td><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $b['provincia'])) . ', ' . ucfirst(str_replace('_', ' ', $b['ciudad_municipio']))); ?></td>
                                            <td>
                                                <?php
                                                $badge_b = match ($b['estado_laboral']) {
                                                    'contratado' => 'bg-success-subtle text-success',
                                                    'suspendido' => 'bg-danger-subtle text-danger',
                                                    default => 'bg-warning-subtle text-warning'
                                                };
                                                ?>
                                                <span class="badge <?php echo $badge_b; ?>"><?php echo ucfirst($b['estado_laboral']); ?></span>
                                            </td>
                                            <td class="text-end">
                                                <?php if (!empty($b['copia_dip'])): ?>
                                                    <a href="../<?php echo htmlspecialchars($b['copia_dip']); ?>" target="_blank" class="btn btn-sm btn-outline-secondary" title="DIP"><i class="bi bi-card-heading"></i></a>
                                                <?php endif; ?>
                                                <?php if (!empty($b['cv'])): ?>
                                                    <a href="../<?php echo htmlspecialchars($b['cv']); ?>" target="_blank" class="btn btn-sm btn-outline-secondary" title="CV"><i class="bi bi-file-earmark-person"></i></a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                        <h6 class="mb-0 fw-bold">Cursos Activos</h6>
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalNuevoCurso"><i class="bi bi-plus-circle"></i> Nuevo</button>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <?php if (empty($cursos)): ?>
                                <p class="text-muted text-center py-3">No hay cursos.</p>
                            <?php else: ?>
                                <?php foreach ($cursos as $c): ?>
                                    <div class="list-group-item px-0 py-3">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1 fw-bold"><?php echo htmlspecialchars($c['titulo_curso']); ?></h6>
                                            <?php
                                            $badge_c = match ($c['estado']) {
                                                'activo' => 'bg-success-subtle text-success',
                                                'proximamente' => 'bg-warning-subtle text-warning',
                                                default => 'bg-secondary-subtle text-secondary'
                                            };
                                            ?>
                                            <span class="badge <?php echo $badge_c; ?>"><?php echo ucfirst($c['estado']); ?></span>
                                        </div>
                                        <p class="mb-1 text-muted fs-7"><?php echo htmlspecialchars($c['entidad_mostrar']); ?></p>
                                        <small class="text-secondary"><i class="bi bi-clock me-1"></i> <?php echo (int) $c['duracion_horas']; ?> Horas</small>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- ===== MODALES ===== -->
<div class="modal fade" id="modalDetalleNotificacion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold">Expediente: <span id="modal-codigo" class="text-primary"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6 border-end">
                        <h6 class="fw-bold text-muted">Datos del Candidato</h6>
                        <p><strong>Nombre:</strong> <span id="modal-candidato"></span></p>
                        <p><strong>Expediente:</strong> <span id="modal-expediente"></span></p>
                        <p><strong>Estado:</strong> <span id="modal-estado" class="badge bg-secondary"></span></p>
                        <p><strong>Provincia:</strong> <span id="modal-provincia"></span></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold text-muted">Datos de la Empresa</h6>
                        <p><strong>Empresa:</strong> <span id="modal-empresa"></span></p>
                        <p><strong>Puesto:</strong> <span id="modal-puesto"></span></p>
                        <p><strong>RUC:</strong> <span id="modal-ruc"></span></p>
                        <p><strong>Salario:</strong> <span id="modal-salario"></span></p>
                    </div>
                    <div class="col-12">
                        <hr>
                        <label class="fw-bold">Motivo</label>
                        <div id="modal-motivo" class="p-3 bg-light rounded text-muted"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button class="btn btn-danger"><i class="bi bi-x-circle me-1"></i> Rechazar</button>
                <button class="btn btn-success"><i class="bi bi-check-circle me-1"></i> Aprobar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalNuevoCurso" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Nuevo Curso</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="guardar_curso.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Código</label>
                        <input type="text" name="codigo_curso" class="form-control" placeholder="Ej. CUR-2026-005" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Título</label>
                        <input type="text" name="titulo_curso" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Entidad Formadora</label>
                        <select name="entidad_id" class="form-select" required>
                            <option value="" disabled selected>Seleccione</option>
                            <?php foreach ($entidades as $e): ?>
                                <option value="<?php echo $e['id']; ?>"><?php echo htmlspecialchars($e['nombre_entidad']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Duración (Horas)</label>
                            <input type="number" name="duracion_horas" class="form-control" placeholder="60" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Estado</label>
                            <select name="estado" class="form-select">
                                <option value="activo">Activo</option>
                                <option value="proximamente">Próximamente</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 mt-2">
                        <label class="form-label fw-semibold">Descripción</label>
                        <textarea name="descripcion_curso" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="modal-footer px-0 pb-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // GRÁFICO CIRCULAR
    const ctxCircular = document.getElementById('chartEstadoLaboral');
    if (ctxCircular) {
        new Chart(ctxCircular.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($labels_circular); ?>,
                datasets: [{
                    data: <?php echo json_encode($data_circular); ?>,
                    backgroundColor: ['#ffc107', '#198754', '#dc3545'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    }
                }
            }
        });
    }

    // GRÁFICO DE BARRAS
    const ctxBarras = document.getElementById('chartIntermediaciones');
    if (ctxBarras) {
        new Chart(ctxBarras.getContext('2d'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($labels_barras); ?>,
                datasets: [{
                    label: 'Cantidad de Expedientes',
                    data: <?php echo json_encode($data_barras); ?>,
                    backgroundColor: ['#ffc107', '#0dcaf0', '#198754', '#dc3545'],
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }

    // MODAL DETALLE
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
        });
    });
});
</script>

<?php include_once '../componentes/footer_admin.php'; ?>
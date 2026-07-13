<?php
session_start();

// ===== VERIFICAR SESIÓN =====
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'empleador') {
    header('Location: ../login_empleador.php');
    exit();
}

$titulo = 'Candidatos / Alertas - Portal de Empleo';
include_once '../componentes/header_empleador.php';
include_once '../conexion/conexion.php';

$id_usuario = $_SESSION['id_usuario'];
$nombre_empresa = $_SESSION['nombre_empresa'] ?? 'Mi Empresa';

// ===== INICIALIZAR VARIABLES =====
$candidatos = [];
$intermediaciones = [];
$notificaciones_pendientes = 0;
$total_candidatos = 0;
$empleador_id = null;

try {
    // Obtener el ID del empleador
    $stmt = $pdo->prepare("SELECT id, nombre_empresa FROM empleadores WHERE usuario_id = ?");
    $stmt->execute([$id_usuario]);
    $empleador = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$empleador) {
        header('Location: completar_perfil_empleador.php');
        exit();
    }

    $empleador_id = $empleador['id'];
    $nombre_empresa = $empleador['nombre_empresa'];
    $_SESSION['nombre_empresa'] = $nombre_empresa;

    // ===== 1. OBTENER CANDIDATOS (POSTULACIONES) =====
    $stmt = $pdo->prepare("
        SELECT 
            p.*,
            u.id as usuario_id,
            u.nombre,
            u.apellidos,
            u.numero_expediente,
            u.documento_identidad,
            u.correo_electronico,
            b.telefono,
            b.estado_laboral,
            b.provincia,
            b.ciudad_municipio,
            b.foto_carnet,
            o.titulo_puesto,
            o.salario_ofrecido
        FROM postulaciones p
        JOIN ofertas_empleo o ON p.oferta_id = o.id
        JOIN buscadores_empleo b ON p.buscador_id = b.id
        JOIN usuarios u ON b.usuario_id = u.id
        WHERE o.empleador_id = ?
        ORDER BY p.fecha_postulacion DESC
    ");
    $stmt->execute([$empleador_id]);
    $candidatos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total_candidatos = count($candidatos);

    // ===== 2. OBTENER INTERMEDIACIONES (Alertas) =====
    $stmt = $pdo->prepare("
        SELECT 
            ni.*,
            u.nombre as buscador_nombre,
            u.apellidos as buscador_apellidos,
            u.numero_expediente as buscador_expediente,
            b.telefono as buscador_telefono,
            b.estado_laboral as buscador_estado,
            o.titulo_puesto,
            o.salario_ofrecido
        FROM notificaciones_intermediacion ni
        JOIN buscadores_empleo b ON ni.buscador_id = b.id
        JOIN usuarios u ON b.usuario_id = u.id
        LEFT JOIN ofertas_empleo o ON ni.oferta_id = o.id
        WHERE ni.empleador_id = ?
        ORDER BY ni.fecha_creacion DESC
    ");
    $stmt->execute([$empleador_id]);
    $intermediaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ===== 3. CONTAR PENDIENTES =====
    $notificaciones_pendientes = count(array_filter($intermediaciones, function($i) {
        return $i['estado_ministerio'] == 'pendiente';
    }));

} catch (PDOException $e) {
    error_log("Error en candidatos: " . $e->getMessage());
    $candidatos = [];
    $intermediaciones = [];
    $total_candidatos = 0;
    $notificaciones_pendientes = 0;
}
?>

<style>
    /* ===== ESTILOS MEJORADOS ===== */
    :root {
        --gov-blue: #0B3A60;
        --gov-blue-light: #1A4F7A;
        --gov-green: #1E7E34;
        --gov-green-light: #2E9B4A;
        --gov-gold: #C9A84C;
        --gov-gold-light: #E8D5A3;
        --gov-dark: #0A192F;
        --gov-bg: #F8FAFC;
        --gov-border: #E2E8F0;
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

    .candidato-card {
        background: var(--gov-bg);
        border-radius: var(--gov-radius-sm);
        padding: 1rem 1.25rem;
        transition: all 0.2s;
        border: 1px solid transparent;
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .candidato-card:hover {
        border-color: var(--gov-gold-light);
        background: #ffffff;
        transform: translateX(4px);
    }
    .candidato-card .avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: var(--gov-blue-soft);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.2rem;
        color: var(--gov-blue);
        flex-shrink: 0;
    }
    .candidato-card .avatar img {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
    }
    .candidato-card .info {
        flex: 1;
        min-width: 150px;
    }
    .candidato-card .info .nombre {
        font-weight: 600;
        color: var(--gov-dark);
    }
    .candidato-card .info .detalle {
        font-size: 0.85rem;
        color: #6b7a8a;
    }
    .candidato-card .acciones {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .intermediacion-item {
        background: var(--gov-bg);
        border-radius: var(--gov-radius-sm);
        padding: 1rem 1.25rem;
        transition: all 0.2s;
        border-left: 4px solid transparent;
    }
    .intermediacion-item:hover {
        background: #ffffff;
        border-color: var(--gov-gold-light);
    }
    .intermediacion-item.pendiente {
        border-left-color: #ffc107;
        background: #fffdf0;
    }
    .intermediacion-item.en_revision {
        border-left-color: #0dcaf0;
        background: #f0f9ff;
    }
    .intermediacion-item.aprobado {
        border-left-color: #198754;
        background: #f0fff4;
    }
    .intermediacion-item.rechazado {
        border-left-color: #dc3545;
        background: #fff5f5;
    }
    .intermediacion-item .codigo {
        font-family: monospace;
        font-weight: 600;
        font-size: 0.85rem;
        color: var(--gov-blue);
    }

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
    .badge-estado.en_revision {
        background: #cce5ff;
        color: #004085;
    }
    .badge-estado.aprobado {
        background: #d4edda;
        color: #155724;
    }
    .badge-estado.rechazado {
        background: #f8d7da;
        color: #721c24;
    }
    .badge-estado.revisado {
        background: #e2e3e5;
        color: #383d41;
    }
    .badge-estado.interesado {
        background: #d4edda;
        color: #155724;
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
    .btn-outline-success {
        border: 1px solid var(--gov-green);
        color: var(--gov-green);
        border-radius: var(--gov-radius-sm);
        padding: 0.3rem 0.8rem;
        font-size: 0.8rem;
        transition: all 0.2s;
    }
    .btn-outline-success:hover {
        background: var(--gov-green);
        color: white;
    }
    .btn-outline-danger {
        border: 1px solid #dc3545;
        color: #dc3545;
        border-radius: var(--gov-radius-sm);
        padding: 0.3rem 0.8rem;
        font-size: 0.8rem;
        transition: all 0.2s;
    }
    .btn-outline-danger:hover {
        background: #dc3545;
        color: white;
    }
    .btn-outline-secondary {
        border: 1px solid var(--gov-border);
        color: var(--gov-dark);
        border-radius: var(--gov-radius-sm);
        padding: 0.3rem 0.8rem;
        font-size: 0.8rem;
        transition: all 0.2s;
    }
    .btn-outline-secondary:hover {
        background: var(--gov-bg);
        border-color: var(--gov-blue);
        color: var(--gov-blue);
    }

    .badge-soft-blue {
        background: rgba(11, 58, 96, 0.08);
        color: var(--gov-blue);
        font-weight: 500;
        padding: 0.3rem 0.9rem;
        border-radius: 20px;
        font-size: 0.75rem;
    }

    .table-candidatos {
        border-radius: var(--gov-radius-sm);
        overflow: hidden;
    }
    .table-candidatos thead {
        background: var(--gov-bg);
    }
    .table-candidatos th {
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        color: #6b7a8a;
        border-bottom: 2px solid var(--gov-border);
        padding: 0.75rem 1rem;
    }
    .table-candidatos td {
        padding: 0.75rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
    }
    .table-candidatos tbody tr:hover {
        background: var(--gov-bg);
    }

    .avatar-mini {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: var(--gov-blue-soft);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.8rem;
        color: var(--gov-blue);
        flex-shrink: 0;
    }
    .avatar-mini img {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        object-fit: cover;
    }

    @media (max-width: 768px) {
        .candidato-card {
            flex-direction: column;
            align-items: stretch;
            text-align: center;
        }
        .candidato-card .avatar {
            margin: 0 auto;
        }
        .candidato-card .acciones {
            justify-content: center;
        }
        .stat-card .stat-number {
            font-size: 1.4rem;
        }
        .table-candidatos td, .table-candidatos th {
            padding: 0.5rem 0.75rem;
            font-size: 0.85rem;
        }
    }
</style>

<body>

<?php include_once '../componentes/menu_empleador.php'; ?>

<main class="container py-4">

    <!-- ===== TÍTULO ===== -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center gap-3">
                <i class="bi bi-people-fill fs-1" style="color: var(--gov-blue);"></i>
                <div>
                    <h1 class="h3 fw-bold m-0" style="color: var(--gov-dark);">Candidatos / Alertas</h1>
                    <p class="text-muted m-0">Gestiona los candidatos que han postulado y las alertas de intermediación</p>
                </div>
            </div>
            <hr class="mb-0 mt-3">
        </div>
    </div>

    <!-- ===== ESTADÍSTICAS ===== -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="stat-label">Total Candidatos</span>
                        <h3 class="stat-number"><?php echo number_format($total_candidatos); ?></h3>
                        <small class="text-muted">Postulaciones recibidas</small>
                    </div>
                    <div class="stat-icon bg-primary-subtle text-primary p-3 rounded-circle">
                        <i class="bi bi-person-check fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="stat-label">Pendientes de Revisión</span>
                        <h3 class="stat-number text-warning"><?php echo number_format($notificaciones_pendientes); ?></h3>
                        <small class="text-warning"><i class="bi bi-clock-history"></i> Requieren atención</small>
                    </div>
                    <div class="stat-icon bg-warning-subtle text-warning p-3 rounded-circle">
                        <i class="bi bi-hourglass-split fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="stat-label">En Revisión</span>
                        <h3 class="stat-number text-info"><?php 
                            $en_revision = count(array_filter($intermediaciones, function($i) {
                                return $i['estado_ministerio'] == 'en_revision';
                            }));
                            echo number_format($en_revision);
                        ?></h3>
                        <small class="text-info">En proceso ministerial</small>
                    </div>
                    <div class="stat-icon bg-info-subtle text-info p-3 rounded-circle">
                        <i class="bi bi-arrow-repeat fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="stat-label">Aprobados</span>
                        <h3 class="stat-number text-success"><?php 
                            $aprobados = count(array_filter($intermediaciones, function($i) {
                                return $i['estado_ministerio'] == 'aprobado';
                            }));
                            echo number_format($aprobados);
                        ?></h3>
                        <small class="text-success"><i class="bi bi-check-circle"></i> Gestionados con éxito</small>
                    </div>
                    <div class="stat-icon bg-success-subtle text-success p-3 rounded-circle">
                        <i class="bi bi-check2-circle fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== SECCIÓN DE INTERMEDIACIONES (ALERTAS) ===== -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="custom-card">
                <h5 class="fw-bold mb-3">
                    <i class="bi bi-shield-exclamation me-2" style="color: var(--gov-gold);"></i>
                    Alertas de Intermediación
                    <?php if ($notificaciones_pendientes > 0): ?>
                        <span class="badge bg-danger ms-2"><?php echo $notificaciones_pendientes; ?> pendientes</span>
                    <?php endif; ?>
                </h5>

                <?php if (empty($intermediaciones)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                        No hay alertas de intermediación en este momento.
                    </div>
                <?php else: ?>
                    <div class="row g-3">
                        <?php foreach ($intermediaciones as $inter): ?>
                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="intermediacion-item <?php echo htmlspecialchars($inter['estado_ministerio']); ?>">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <span class="codigo"><?php echo htmlspecialchars($inter['codigo_seguimiento']); ?></span>
                                        <span class="badge-estado <?php echo htmlspecialchars($inter['estado_ministerio']); ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $inter['estado_ministerio'])); ?>
                                        </span>
                                    </div>
                                    <h6 class="fw-semibold mb-1">
                                        <?php echo htmlspecialchars($inter['titulo_puesto'] ?? 'Contacto Directo'); ?>
                                    </h6>
                                    <p class="text-muted small mb-2">
                                        <i class="bi bi-person me-1"></i>
                                        <?php echo htmlspecialchars($inter['buscador_nombre'] . ' ' . $inter['buscador_apellidos']); ?>
                                        <span class="text-muted" style="font-size: 0.7rem;">
                                            (<?php echo htmlspecialchars($inter['buscador_expediente']); ?>)
                                        </span>
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted" style="font-size: 0.7rem;">
                                            <i class="bi bi-clock me-1"></i>
                                            <?php echo date('d/m/Y H:i', strtotime($inter['fecha_creacion'])); ?>
                                        </small>
                                        <?php if ($inter['estado_ministerio'] == 'pendiente'): ?>
                                            <button class="btn btn-sm btn-outline-success" onclick="aprobarIntermediacion('<?php echo htmlspecialchars($inter['codigo_seguimiento']); ?>')">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger" onclick="rechazarIntermediacion('<?php echo htmlspecialchars($inter['codigo_seguimiento']); ?>')">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        <?php endif; ?>
                                        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalDetalleCandidato"
                                            data-codigo="<?php echo htmlspecialchars($inter['codigo_seguimiento']); ?>"
                                            data-nombre="<?php echo htmlspecialchars($inter['buscador_nombre'] . ' ' . $inter['buscador_apellidos']); ?>"
                                            data-expediente="<?php echo htmlspecialchars($inter['buscador_expediente']); ?>"
                                            data-telefono="<?php echo htmlspecialchars($inter['buscador_telefono'] ?? 'No registrado'); ?>"
                                            data-estado="<?php echo htmlspecialchars($inter['buscador_estado']); ?>"
                                            data-puesto="<?php echo htmlspecialchars($inter['titulo_puesto'] ?? 'N/A'); ?>"
                                            data-salario="<?php echo $inter['salario_ofrecido'] ? number_format($inter['salario_ofrecido'], 0, ',', '.') . ' XAF' : 'No especificado'; ?>"
                                            data-fecha="<?php echo date('d/m/Y', strtotime($inter['fecha_creacion'])); ?>"
                                            title="Ver detalles">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ===== SECCIÓN DE CANDIDATOS ===== -->
    <div class="row">
        <div class="col-12">
            <div class="custom-card">
                <h5 class="fw-bold mb-3">
                    <i class="bi bi-person-check me-2" style="color: var(--gov-green);"></i>
                    Candidatos Registrados
                    <span class="badge-soft-blue ms-2"><?php echo $total_candidatos; ?> total</span>
                </h5>

                <?php if (empty($candidatos)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-people fs-2 d-block mb-2"></i>
                        No has recibido postulaciones aún.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-candidatos">
                            <thead>
                                <tr>
                                    <th>Candidato</th>
                                    <th>Puesto</th>
                                    <th>Ubicación</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                    <th class="text-end">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($candidatos as $c): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <?php 
                                                    $foto = !empty($c['foto_carnet']) ? '../' . $c['foto_carnet'] : '';
                                                ?>
                                                <div class="avatar-mini">
                                                    <?php if ($foto): ?>
                                                        <img src="<?php echo htmlspecialchars($foto); ?>" alt="Foto">
                                                    <?php else: ?>
                                                        <?php echo strtoupper(substr($c['nombre'], 0, 1) . substr($c['apellidos'], 0, 1)); ?>
                                                    <?php endif; ?>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold" style="font-size: 0.9rem;">
                                                        <?php echo htmlspecialchars($c['nombre'] . ' ' . $c['apellidos']); ?>
                                                    </div>
                                                    <small class="text-muted" style="font-size: 0.75rem;">
                                                        <?php echo htmlspecialchars($c['numero_expediente']); ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold" style="font-size: 0.9rem;">
                                                <?php echo htmlspecialchars($c['titulo_puesto']); ?>
                                            </div>
                                            <?php if ($c['salario_ofrecido']): ?>
                                                <small class="text-muted" style="font-size: 0.7rem;">
                                                    <?php echo number_format($c['salario_ofrecido'], 0, ',', '.') . ' XAF'; ?>
                                                </small>
                                            <?php endif; ?>
                                        </td>
                                        <td style="font-size: 0.85rem;">
                                            <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $c['ciudad_municipio'] ?? '')) . ', ' . ucfirst(str_replace('_', ' ', $c['provincia'] ?? ''))); ?>
                                        </td>
                                        <td>
                                            <span class="badge-estado <?php echo htmlspecialchars($c['estado']); ?>">
                                                <?php echo ucfirst($c['estado']); ?>
                                            </span>
                                        </td>
                                        <td style="font-size: 0.85rem;">
                                            <?php echo date('d/m/Y', strtotime($c['fecha_postulacion'])); ?>
                                        </td>
                                        <td class="text-end">
                                            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalDetalleCandidato"
                                                data-codigo="<?php echo htmlspecialchars($c['numero_expediente']); ?>"
                                                data-nombre="<?php echo htmlspecialchars($c['nombre'] . ' ' . $c['apellidos']); ?>"
                                                data-expediente="<?php echo htmlspecialchars($c['numero_expediente']); ?>"
                                                data-telefono="<?php echo htmlspecialchars($c['telefono'] ?? 'No registrado'); ?>"
                                                data-estado="<?php echo htmlspecialchars($c['estado_laboral']); ?>"
                                                data-puesto="<?php echo htmlspecialchars($c['titulo_puesto']); ?>"
                                                data-salario="<?php echo $c['salario_ofrecido'] ? number_format($c['salario_ofrecido'], 0, ',', '.') . ' XAF' : 'No especificado'; ?>"
                                                data-fecha="<?php echo date('d/m/Y', strtotime($c['fecha_postulacion'])); ?>"
                                                data-email="<?php echo htmlspecialchars($c['correo_electronico']); ?>"
                                                data-dip="<?php echo htmlspecialchars($c['documento_identidad']); ?>"
                                                title="Ver detalles">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</main>

<!-- ===== MODAL DETALLE CANDIDATO ===== -->
<div class="modal fade" id="modalDetalleCandidato" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-person-badge me-2" style="color: var(--gov-gold);"></i>
                    Detalle del Candidato
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6 border-end">
                        <h6 class="fw-bold text-muted uppercase" style="font-size: 0.8rem;">Datos del Candidato</h6>
                        <p class="mb-1"><strong>Nombre:</strong> <span id="modal-nombre"></span></p>
                        <p class="mb-1"><strong>Nº Expediente:</strong> <span id="modal-expediente"></span></p>
                        <p class="mb-1"><strong>DIP:</strong> <span id="modal-dip"></span></p>
                        <p class="mb-1"><strong>Teléfono:</strong> <span id="modal-telefono"></span></p>
                        <p class="mb-1"><strong>Correo:</strong> <span id="modal-email"></span></p>
                        <p class="mb-0"><strong>Estado Laboral:</strong> <span id="modal-estado-lab"></span></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold text-muted uppercase" style="font-size: 0.8rem;">Datos de la Postulación</h6>
                        <p class="mb-1"><strong>Puesto:</strong> <span id="modal-puesto"></span></p>
                        <p class="mb-1"><strong>Salario:</strong> <span id="modal-salario"></span></p>
                        <p class="mb-1"><strong>Fecha:</strong> <span id="modal-fecha"></span></p>
                        <p class="mb-0"><strong>Código:</strong> <span id="modal-codigo" class="font-monospace text-primary"></span></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="contactarCandidato()">
                    <i class="bi bi-envelope me-1"></i> Contactar
                </button>
            </div>
        </div>
    </div>
</div>

<?php include_once '../componentes/footer_empleador.php'; ?>

<script>
    // ===== PASAR DATOS AL MODAL =====
    document.addEventListener('DOMContentLoaded', function() {
        const buttons = document.querySelectorAll('[data-bs-toggle="modal"][data-bs-target="#modalDetalleCandidato"]');
        buttons.forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('modal-nombre').textContent = this.dataset.nombre || 'N/A';
                document.getElementById('modal-expediente').textContent = this.dataset.expediente || 'N/A';
                document.getElementById('modal-dip').textContent = this.dataset.dip || 'N/A';
                document.getElementById('modal-telefono').textContent = this.dataset.telefono || 'N/A';
                document.getElementById('modal-email').textContent = this.dataset.email || 'N/A';
                document.getElementById('modal-puesto').textContent = this.dataset.puesto || 'N/A';
                document.getElementById('modal-salario').textContent = this.dataset.salario || 'N/A';
                document.getElementById('modal-fecha').textContent = this.dataset.fecha || 'N/A';
                document.getElementById('modal-codigo').textContent = this.dataset.codigo || 'N/A';
                
                const estadoLab = document.getElementById('modal-estado-lab');
                const estado = this.dataset.estado || 'desempleado';
                const badgeMap = {
                    'desempleado': 'badge bg-warning text-dark',
                    'contratado': 'badge bg-success',
                    'suspendido': 'badge bg-danger'
                };
                estadoLab.className = badgeMap[estado] || 'badge bg-secondary';
                estadoLab.textContent = estado.charAt(0).toUpperCase() + estado.slice(1);
            });
        });
    });

    // ===== FUNCIONES DE ACCIÓN =====
    function aprobarIntermediacion(codigo) {
        if (confirm(`¿Aprobar la intermediación ${codigo}?`)) {
            alert(`✅ Intermediación ${codigo} aprobada.`);
            // Aquí iría la llamada AJAX para aprobar
        }
    }

    function rechazarIntermediacion(codigo) {
        if (confirm(`¿Rechazar la intermediación ${codigo}?`)) {
            alert(`❌ Intermediación ${codigo} rechazada.`);
            // Aquí iría la llamada AJAX para rechazar
        }
    }

    function contactarCandidato() {
        const nombre = document.getElementById('modal-nombre').textContent;
        const email = document.getElementById('modal-email').textContent;
        if (email && email !== 'N/A') {
            window.location.href = `mailto:${email}?subject=Interés en su perfil profesional`;
        } else {
            alert('No hay correo electrónico disponible para este candidato.');
        }
    }
</script>

</body>
</html>
<?php
session_start();

// ===== VERIFICAR SESIÓN ADMINISTRATIVA =====
if (!isset($_SESSION['id_usuario']) || !in_array($_SESSION['rol'] ?? '', ['administrador', 'ministerio'])) {
    header('Location: ../login_admin.php');
    exit();
}

$titulo = 'Postulaciones de Empleo - Panel de Administración';
include_once '../componentes/header_admin.php';
include_once '../conexion/conexion.php';

// ===== INICIALIZAR VARIABLES =====
$postulaciones = [];
$total_postulaciones = 0;
$postulaciones_pendientes = 0;
$postulaciones_revisadas = 0;
$postulaciones_interesadas = 0;
$postulaciones_rechazadas = 0;

try {
    // ===== CONTAR ESTADÍSTICAS =====
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM postulaciones");
    $total_postulaciones = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM postulaciones WHERE estado = 'pendiente'");
    $postulaciones_pendientes = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM postulaciones WHERE estado = 'revisado'");
    $postulaciones_revisadas = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM postulaciones WHERE estado = 'interesado'");
    $postulaciones_interesadas = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM postulaciones WHERE estado = 'rechazado'");
    $postulaciones_rechazadas = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // ===== OBTENER TODAS LAS POSTULACIONES CON SUS RELACIONES =====
    $stmt = $pdo->query("
        SELECT 
            p.id AS postulacion_id,
            p.mensaje_presentacion,
            p.estado AS estado_postulacion,
            p.fecha_postulacion,
            o.titulo_puesto,
            o.id AS oferta_id,
            emp.nombre_empresa,
            u.numero_expediente,
            u.nombre AS buscador_nombre,
            u.apellidos AS buscador_apellidos,
            u.correo_electronico AS buscador_correo,
            b.telefono AS buscador_telefono,
            b.estado_laboral
        FROM postulaciones p
        INNER JOIN ofertas_empleo o ON p.oferta_id = o.id
        INNER JOIN empleadores emp ON o.empleador_id = emp.id
        INNER JOIN buscadores_empleo b ON p.buscador_id = b.id
        INNER JOIN usuarios u ON b.usuario_id = u.id
        ORDER BY p.fecha_postulacion DESC
    ");
    $postulaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Error en postulaciones_admin: " . $e->getMessage());
    $postulaciones = [];
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

    .bg-primary-subtle {
        background: rgba(11, 58, 96, 0.08);
    }

    .text-primary {
        color: var(--gov-blue) !important;
    }

    .bg-success-subtle {
        background: rgba(30, 126, 52, 0.08);
    }

    .text-success {
        color: var(--gov-green) !important;
    }

    .bg-danger-subtle {
        background: rgba(220, 53, 69, 0.08);
    }

    .text-danger {
        color: #dc3545 !important;
    }

    .bg-warning-subtle {
        background: rgba(255, 193, 7, 0.12);
    }

    .text-warning {
        color: #ffc107 !important;
    }

    .badge-estado {
        font-weight: 500;
        padding: 0.3rem 0.8rem;
        border-radius: 20px;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .badge-estado.abierta {
        background: #d4edda;
        color: #155724;
    }

    .badge-estado.cerrada {
        background: #e2e3e5;
        color: #383d41;
    }

    .table-wrap {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
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

    .truncate-text {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        max-height: 3rem;
    }

    @media (max-width: 768px) {
        .stat-card .stat-number {
            font-size: 1.4rem;
        }

        .custom-card {
            padding: 1rem;
        }

        .table td,
        .table th {
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

        .table td,
        .table th {
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


        <!-- ===== TABLA DE OFERTAS ===== -->
        <div class="container-fluid p-4">

           <div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="stat-label">Total Postulaciones</span>
                    <h3 class="stat-number text-primary"><?php echo $total_postulaciones; ?></h3>
                    <small class="text-muted">Candidaturas enviadas</small>
                </div>
                <div class="stat-icon bg-primary-subtle text-primary">
                    <i class="bi bi-person-badge"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="stat-label">Pendientes</span>
                    <h3 class="stat-number text-warning"><?php echo $postulaciones_pendientes; ?></h3>
                    <small class="text-warning">Por revisar por el Ministerio</small>
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
                    <span class="stat-label">En Proceso</span>
                    <h3 class="stat-number text-info">
                        <?php echo ($postulaciones_revisadas + $postulaciones_interesadas); ?>
                    </h3>
                    <small class="text-info">Revisados / Interesados</small>
                </div>
                <div class="stat-icon bg-info-subtle text-info">
                    <i class="bi bi-arrow-right-circle"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="stat-label">Rechazadas</span>
                    <h3 class="stat-number text-danger"><?php echo $postulaciones_rechazadas; ?></h3>
                    <small class="text-danger">No seleccionados / descartados</small>
                </div>
                <div class="stat-icon bg-danger-subtle text-danger">
                    <i class="bi bi-x-circle"></i>
                </div>
            </div>
        </div>
    </div>
</div>





            <div class="row">
                <div class="col-12">
                    <div class="custom-card">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold m-0">
                                <i class="bi bi-person-badge me-2"></i>
                                Postulaciones Recibidas
                                <span class="badge bg-light text-dark border ms-2"><?php echo $total_postulaciones; ?>
                                    total</span>
                            </h5>
                        </div>

                        <div class="table-wrap">
                            <table class="table table-hover align-middle" id="tablaPostulaciones">
                                <thead>
                                    <tr>
                                        <th style="min-width: 130px;">Buscador (Expediente)</th>
                                        <th style="min-width: 140px;">Puesto Solicitado</th>
                                        <th style="min-width: 130px;">Empresa</th>
                                        <th style="min-width: 100px;">Contacto</th>
                                        <th style="min-width: 90px;">Estado</th>
                                        <th style="min-width: 100px;">Fecha Postulación</th>
                                        <th style="min-width: 120px;" class="text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($postulaciones)): ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">
                                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                                No hay postulaciones registradas en el sistema.
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($postulaciones as $postulacion): ?>
                                            <tr>
                                                <td>
                                                    <div class="fw-semibold" style="font-size: 0.9rem;">
                                                        <?php echo htmlspecialchars($postulacion['buscador_nombre'] . ' ' . $postulacion['buscador_apellidos']); ?>
                                                    </div>
                                                    <small class="text-primary fw-bold" style="font-size: 0.75rem;">
                                                        <?php echo htmlspecialchars($postulacion['numero_expediente']); ?>
                                                    </small>
                                                    <span class="badge bg-secondary" style="font-size: 0.65rem;">
                                                        <?php echo ucfirst($postulacion['estado_laboral']); ?>
                                                    </span>
                                                </td>

                                                <td>
                                                    <div class="fw-semibold" style="font-size: 0.9rem;">
                                                        <?php echo htmlspecialchars($postulacion['titulo_puesto']); ?>
                                                    </div>
                                                </td>

                                                <td>
                                                    <span style="font-size: 0.85rem;">
                                                        <?php echo htmlspecialchars($postulacion['nombre_empresa']); ?>
                                                    </span>
                                                </td>

                                                <td style="font-size: 0.8rem;">
                                                    <div><i
                                                            class="bi bi-telephone me-1"></i><?php echo htmlspecialchars($postulacion['buscador_telefono']); ?>
                                                    </div>
                                                    <div class="text-muted"><i
                                                            class="bi bi-envelope me-1"></i><?php echo htmlspecialchars($postulacion['buscador_correo']); ?>
                                                    </div>
                                                </td>

                                                <td>
                                                    <?php
                                                    $badge_class = 'bg-secondary';
                                                    if ($postulacion['estado_postulacion'] === 'pendiente')
                                                        $badge_class = 'bg-warning text-dark';
                                                    elseif ($postulacion['estado_postulacion'] === 'revisado')
                                                        $badge_class = 'bg-info text-white';
                                                    elseif ($postulacion['estado_postulacion'] === 'interesado')
                                                        $badge_class = 'bg-success text-white';
                                                    elseif ($postulacion['estado_postulacion'] === 'rechazado')
                                                        $badge_class = 'bg-danger text-white';
                                                    ?>
                                                    <span class="badge <?php echo $badge_class; ?>">
                                                        <?php echo ucfirst($postulacion['estado_postulacion']); ?>
                                                    </span>
                                                </td>

                                                <td style="font-size: 0.8rem;">
                                                    <?php echo date('d/m/Y H:i', strtotime($postulacion['fecha_postulacion'])); ?>
                                                </td>

                                                <td class="text-end">
                                                    <div class="d-flex gap-1 justify-content-end">
                                                        <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal"
                                                            data-bs-target="#modalPostulacion<?php echo $postulacion['postulacion_id']; ?>"
                                                            title="Ver Mensaje de Presentación">
                                                            <i class="bi bi-chat-text"></i>
                                                        </button>

                                                        <button class="btn btn-outline-primary btn-sm" title="Cambiar Estado">
                                                            <i class="bi bi-gear"></i>
                                                        </button>
                                                    </div>

                                                    <div class="modal fade text-start"
                                                        id="modalPostulacion<?php echo $postulacion['postulacion_id']; ?>"
                                                        tabindex="-1" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Mensaje de Presentación</h5>
                                                                    <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <h6>Candidato: <span
                                                                            class="text-muted"><?php echo htmlspecialchars($postulacion['buscador_nombre'] . ' ' . $postulacion['buscador_apellidos']); ?></span>
                                                                    </h6>
                                                                    <hr>
                                                                    <p class="text-justify">
                                                                        <?php echo nl2br(htmlspecialchars($postulacion['mensaje_presentacion'] ?? 'El candidato no ha adjuntado un mensaje de presentación adicional.')); ?>
                                                                    </p>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary btn-sm"
                                                                        data-bs-dismiss="modal">Cerrar</button>
                                                                </div>
                                                            </div>
                                                        </div>
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
</div>




<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>


<?php include_once '../componentes/footer_admin.php'; ?>
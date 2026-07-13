<?php
session_start();

// ===== VERIFICAR SESIÓN =====
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'empleador') {
    header('Location: ../login_empleador.php');
    exit();
}

$titulo = 'Panel de Empresa - Portal de Empleo';
include_once '../componentes/header_empleador.php';
include_once '../conexion/conexion.php';

$id_usuario = $_SESSION['id_usuario'];
$nombre_empresa = $_SESSION['nombre_empresa'] ?? 'Mi Empresa';

// ===== INICIALIZAR VARIABLES =====
$total_ofertas = 0;
$ofertas_activas = 0;
$solicitudes_pendientes = 0;
$contrataciones = 0;
$ofertas = [];
$intermediaciones = [];

try {
    $stmt = $pdo->prepare("SELECT id, nombre_empresa FROM empleadores WHERE usuario_id = ?");
    $stmt->execute([$id_usuario]);
    $empleador = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$empleador) {
        header('Location: completar_perfil_empleador.php');
        exit();
    }

    $empleador_id = $empleador['id'];
    $nombre_empresa = $empleador['nombre_empresa'];

    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM ofertas_empleo WHERE empleador_id = ?");
    $stmt->execute([$empleador_id]);
    $total_ofertas = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM ofertas_empleo WHERE empleador_id = ? AND estado = 'abierta'");
    $stmt->execute([$empleador_id]);
    $ofertas_activas = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    $stmt = $pdo->prepare("
        SELECT COUNT(*) as total 
        FROM notificaciones_intermediacion ni
        WHERE ni.empleador_id = ? AND ni.estado_ministerio = 'pendiente'
    ");
    $stmt->execute([$empleador_id]);
    $solicitudes_pendientes = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    $stmt = $pdo->prepare("
        SELECT COUNT(*) as total 
        FROM notificaciones_intermediacion ni
        WHERE ni.empleador_id = ? AND ni.estado_ministerio = 'aprobado'
    ");
    $stmt->execute([$empleador_id]);
    $contrataciones = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    $stmt = $pdo->prepare("
        SELECT * FROM ofertas_empleo 
        WHERE empleador_id = ? 
        ORDER BY fecha_publicacion DESC 
        LIMIT 5
    ");
    $stmt->execute([$empleador_id]);
    $ofertas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("
        SELECT 
            ni.*,
            u.nombre as buscador_nombre,
            u.apellidos as buscador_apellidos,
            u.numero_expediente as buscador_expediente,
            o.titulo_puesto
        FROM notificaciones_intermediacion ni
        JOIN buscadores_empleo b ON ni.buscador_id = b.id
        JOIN usuarios u ON b.usuario_id = u.id
        LEFT JOIN ofertas_empleo o ON ni.oferta_id = o.id
        WHERE ni.empleador_id = ?
        ORDER BY ni.fecha_creacion DESC
        LIMIT 5
    ");
    $stmt->execute([$empleador_id]);
    $intermediaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Error en panel empleador: " . $e->getMessage());
    $empleador = null;
    $empleador_id = null;
}

$_SESSION['nombre_empresa'] = $nombre_empresa;

include_once '../componentes/menu_empleador.php';
?>

<!-- ===== CONTENIDO ===== -->
<style>
    :root {
        --gov-blue: #0B3A60;
        --gov-blue-light: #1A4F7A;
        --gov-blue-soft: #E8EEF3;
        --gov-blue-bg: #F0F5FA;
        --gov-green: #1E7E34;
        --gov-green-light: #2E9B4A;
        --gov-gold: #C9A84C;
        --gov-gold-light: #E8D5A3;
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
    .stat-card .icon-box {
        width: 48px;
        height: 48px;
        border-radius: var(--gov-radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
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
    .btn-light {
        background: var(--gov-blue-bg);
        border: 1px solid var(--gov-border);
        color: var(--gov-blue);
        border-radius: var(--gov-radius-sm);
        padding: 0.25rem 0.6rem;
        transition: all 0.2s;
    }
    .btn-light:hover {
        background: var(--gov-blue);
        color: white;
        border-color: var(--gov-blue);
    }
    .btn-light.text-danger {
        color: #dc3545;
    }
    .btn-light.text-danger:hover {
        background: #dc3545;
        color: white;
    }

    .badge.bg-success-subtle {
        background: #d4edda !important;
        color: #155724 !important;
    }
    .badge.bg-secondary-subtle {
        background: #e2e3e5 !important;
        color: #383d41 !important;
    }
    .badge.bg-warning-subtle {
        background: #fff3cd !important;
        color: #856404 !important;
    }
    .badge.bg-info-subtle {
        background: #cce5ff !important;
        color: #004085 !important;
    }
    .badge.bg-primary-subtle {
        background: #cce5ff !important;
        color: #004085 !important;
    }

    .table th {
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        color: #6b7a8a;
        border-bottom: 2px solid var(--gov-border);
    }
    .table td {
        vertical-align: middle;
    }

    @media (max-width: 768px) {
        .stat-card .stat-number {
            font-size: 1.4rem;
        }
        .custom-card {
            padding: 1rem;
        }
        .stat-card {
            padding: 1rem;
        }
    }
    @media (max-width: 576px) {
        .stat-card {
            padding: 0.8rem;
        }
        .stat-card .stat-number {
            font-size: 1.2rem;
        }
    }
</style>

<!-- ===== ESTADÍSTICAS ===== -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="stat-label">Ofertas Publicadas</span>
                    <h3 class="stat-number"><?php echo number_format($total_ofertas); ?></h3>
                    <small class="text-success fw-semibold"><i class="bi bi-arrow-up-short"></i> Total acumulado</small>
                </div>
                <div class="icon-box bg-primary-subtle text-primary">
                    <i class="bi bi-file-earmark-post"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="stat-label">Ofertas Activas</span>
                    <h3 class="stat-number text-primary"><?php echo number_format($ofertas_activas); ?></h3>
                    <small class="text-muted fw-semibold">Vacantes abiertas</small>
                </div>
                <div class="icon-box bg-success-subtle text-success">
                    <i class="bi bi-briefcase-check"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="stat-label">Solicitudes de Contacto</span>
                    <h3 class="stat-number text-warning"><?php echo number_format($solicitudes_pendientes); ?></h3>
                    <small class="text-warning fw-semibold"><i class="bi bi-clock-history"></i> Pendientes Ministerio</small>
                </div>
                <div class="icon-box bg-warning-subtle text-warning">
                    <i class="bi bi-person-lines-fill"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="stat-label">Contrataciones</span>
                    <h3 class="stat-number text-info"><?php echo number_format($contrataciones); ?></h3>
                    <small class="text-info fw-semibold">Gestionadas con éxito</small>
                </div>
                <div class="icon-box bg-info-subtle text-info">
                    <i class="bi bi-award"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ===== CONTENIDO PRINCIPAL ===== -->
<div class="row g-4">

    <div class="col-12 col-xl-8">
        <div class="custom-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold m-0">Ofertas de Empleo Recientes</h5>
                    <small class="text-muted">Gestiona tus vacantes de trabajo</small>
                </div>
                <button class="btn btn-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalNuevaOferta">
                    <i class="bi bi-plus-lg me-1"></i> Publicar Oferta
                </button>
            </div>

            <?php if (empty($ofertas)): ?>
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                    No has publicado ninguna oferta aún.
                    <div class="mt-2">
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalNuevaOferta">
                            <i class="bi bi-plus-lg me-1"></i> Publicar tu primera oferta
                        </button>
                    </div>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Puesto / Vacante</th>
                                <th>Ubicación</th>
                                <th>Salario</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ofertas as $oferta): ?>
                                <tr>
                                    <td>
                                        <div class="fw-semibold"><?php echo htmlspecialchars($oferta['titulo_puesto']); ?></div>
                                        <small class="text-muted">Pub: <?php echo date('d/m/Y', strtotime($oferta['fecha_publicacion'])); ?></small>
                                    </td>
                                    <td><span class="badge bg-light text-dark border"><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $oferta['provincia']))); ?></span></td>
                                    <td><?php echo $oferta['salario_ofrecido'] ? number_format($oferta['salario_ofrecido'], 0, ',', '.') . ' XAF' : 'No especificado'; ?></td>
                                    <td>
                                        <span class="badge <?php echo $oferta['estado'] == 'abierta' ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-secondary-subtle text-secondary border'; ?>">
                                            <?php echo ucfirst($oferta['estado']); ?>
                                        </span>
                                    </td>
                                    
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-12 col-xl-4">
        <div class="custom-card">
            <h5 class="fw-bold mb-1">Estado de Intermediación</h5>
            <small class="text-muted d-block mb-3">Alertas y revisiones con el Ministerio</small>

            <?php if (empty($intermediaciones)): ?>
                <div class="text-center text-muted py-3">
                    <i class="bi bi-clock-history fs-4 d-block mb-2"></i>
                    No hay solicitudes de intermediación.
                </div>
            <?php else: ?>
                <div class="d-flex flex-column gap-3">
                    <?php foreach ($intermediaciones as $inter): ?>
                        <div class="p-3 border rounded-3 bg-light-subtle">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-primary"><?php echo htmlspecialchars($inter['codigo_seguimiento']); ?></span>
                                <span class="badge <?php 
                                    echo $inter['estado_ministerio'] == 'pendiente' ? 'bg-warning text-dark' : 
                                        ($inter['estado_ministerio'] == 'aprobado' ? 'bg-success' : 
                                        ($inter['estado_ministerio'] == 'en_revision' ? 'bg-info' : 'bg-danger')); 
                                ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $inter['estado_ministerio'])); ?>
                                </span>
                            </div>
                            <h6 class="mb-1 fw-semibold">
                                <?php echo htmlspecialchars($inter['titulo_puesto'] ?? 'Contacto Directo'); ?>
                            </h6>
                            <p class="text-muted small mb-2">
                                <i class="bi bi-person me-1"></i>
                                <?php echo htmlspecialchars($inter['buscador_nombre'] . ' ' . $inter['buscador_apellidos']); ?>
                                <span class="text-muted" style="font-size: 0.7rem;">
                                    (<?php echo htmlspecialchars($inter['buscador_expediente']); ?>)
                                </span>
                            </p>
                            <small class="text-muted" style="font-size: 0.75rem;">
                                <i class="bi bi-clock me-1"></i>
                                <?php echo date('d/m/Y H:i', strtotime($inter['fecha_creacion'])); ?>
                            </small>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>

<!-- ===== MODAL NUEVA OFERTA ===== -->
<div class="modal fade" id="modalNuevaOferta" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Publicar Vacante de Empleo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="guardar_oferta.php" method="POST">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Título del Puesto / Vacante *</label>
                            <input type="text" name="titulo_puesto" class="form-control" placeholder="Ej. Ingeniero Civil, Contable" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Provincia *</label>
                            <select name="provincia" class="form-select" required>
                                <option value="">Seleccionar...</option>
                                <option value="Bioko Norte">Bioko Norte</option>
                                <option value="Litoral">Litoral</option>
                                <option value="Bioko Sur">Bioko Sur</option>
                                <option value="Centro Sur">Centro Sur</option>
                                <option value="Kie-Ntem">Kie-Ntem</option>
                                <option value="Wele-Nzas">Wele-Nzas</option>
                                <option value="Annobón">Annobón</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Salario Ofrecido (XAF) - Opcional</label>
                            <input type="number" name="salario_ofrecido" class="form-control" placeholder="Ej. 350000" min="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Estado Inicial</label>
                            <select name="estado" class="form-select">
                                <option value="abierta" selected>Abierta</option>
                                <option value="cerrada">Cerrada</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Fecha Límite (Opcional)</label>
                            <input type="date" name="fecha_limite" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Descripción del Puesto *</label>
                            <textarea name="descripcion" class="form-control" rows="3" placeholder="Describe las funciones principales del puesto..." required></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Requisitos Solicitados *</label>
                            <textarea name="requisitos" class="form-control" rows="3" placeholder="Requisitos académicos, experiencia, idiomas..." required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-send me-2"></i>Publicar Vacante
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
echo '</div>'; // Cierra page-content
echo '</main>'; // Cierra main-content
echo '</div>'; // Cierra wrapper

include_once '../componentes/footer_empleador.php';
?>
<?php
session_start();

// ===== VERIFICAR SESIÓN ADMINISTRATIVA =====
if (!isset($_SESSION['id_usuario']) || !in_array($_SESSION['rol'] ?? '', ['administrador', 'ministerio'])) {
    header('Location: ../login_admin.php');
    exit();
}

$titulo = 'Entidades Formadoras - Panel de Administración';
include_once '../componentes/header_admin.php';
include_once '../conexion/conexion.php';

// ===== INICIALIZAR VARIABLES =====
$entidades = [];
$total_entidades = 0;
$total_cursos = 0;

try {
    // ===== CONTAR ESTADÍSTICAS =====
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM entidades_formadoras");
    $total_entidades = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM cursos");
    $total_cursos = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // ===== OBTENER TODAS LAS ENTIDADES =====
    $stmt = $pdo->query("SELECT * FROM entidades_formadoras ORDER BY id DESC");
    $entidades = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Error en entidades: " . $e->getMessage());
    $entidades = [];
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
    .bg-info-subtle { background: rgba(13, 202, 240, 0.12); }
    .text-info { color: #0dcaf0 !important; }
    .bg-warning-subtle { background: rgba(255, 193, 7, 0.12); }
    .text-warning { color: #ffc107 !important; }

    .badge-tipo {
        font-weight: 500;
        padding: 0.25rem 0.7rem;
        border-radius: 20px;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .badge-tipo.publica {
        background: #cce5ff;
        color: #004085;
    }
    .badge-tipo.privada {
        background: #d1ecf1;
        color: #0c5460;
    }
    .badge-tipo.ong {
        background: #fff3cd;
        color: #856404;
    }
    .badge-tipo.internacional {
        background: #d6d8db;
        color: #383d41;
    }

    .badge-estado {
        font-weight: 500;
        padding: 0.25rem 0.7rem;
        border-radius: 20px;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .badge-estado.activo {
        background: #d4edda;
        color: #155724;
    }
    .badge-estado.inactivo {
        background: #f8d7da;
        color: #721c24;
    }
    .badge-estado.suspendido {
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
                            <span class="stat-label">Total Entidades</span>
                            <h3 class="stat-number"><?php echo number_format($total_entidades); ?></h3>
                            <small class="text-muted">Registradas en el sistema</small>
                        </div>
                        <div class="stat-icon bg-primary-subtle text-primary">
                            <i class="bi bi-building"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="stat-label">Cursos Ofrecidos</span>
                            <h3 class="stat-number text-success"><?php echo number_format($total_cursos); ?></h3>
                            <small class="text-success">Capacitaciones disponibles</small>
                        </div>
                        <div class="stat-icon bg-success-subtle text-success">
                            <i class="bi bi-journal-bookmark"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="stat-label">Tipos</span>
                            <h3 class="stat-number text-info">4</h3>
                            <small class="text-info">Categorías de entidades</small>
                        </div>
                        <div class="stat-icon bg-info-subtle text-info">
                            <i class="bi bi-tags"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="stat-label">Activas</span>
                            <h3 class="stat-number text-success"><?php 
                                $activas = array_filter($entidades, function($e) { return ($e['estado'] ?? 'activo') === 'activo'; });
                                echo number_format(count($activas));
                            ?></h3>
                            <small class="text-success">En funcionamiento</small>
                        </div>
                        <div class="stat-icon bg-success-subtle text-success">
                            <i class="bi bi-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== TABLA DE ENTIDADES ===== -->
        <div class="row">
            <div class="col-12">
                <div class="custom-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold m-0">
                            <i class="bi bi-list-ul me-2"></i>
                            Lista de Entidades
                            <span class="badge bg-light text-dark border ms-2"><?php echo count($entidades); ?> total</span>
                        </h5>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAñadirEntidad">
                            <i class="bi bi-plus-lg me-1"></i> Añadir Entidad
                        </button>
                    </div>

                    <div class="table-wrap">
                        <table class="table table-hover align-middle" id="tablaEntidades">
                            <thead>
                                <tr>
                                    <th style="min-width: 110px;">Cód. Entidad</th>
                                    <th style="min-width: 160px;">Nombre / Siglas</th>
                                    <th style="min-width: 100px;">Tipo</th>
                                    <th style="min-width: 170px;">Contacto</th>
                                    <th style="min-width: 110px;">Ubicación</th>
                                    <th style="min-width: 90px;">Estado</th>
                                    <th style="min-width: 150px;" class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($entidades)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                            No hay entidades formadoras registradas.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($entidades as $entidad): ?>
                                        <?php
                                        $tipo = strtolower($entidad['tipo_entidad'] ?? 'publica');
                                        $estado = $entidad['estado'] ?? 'activo';
                                        ?>
                                        <tr>
                                            <td>
                                                <span class="font-monospace fw-bold text-primary">
                                                    <?php echo htmlspecialchars($entidad['codigo_entidad']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="fw-semibold" style="font-size: 0.9rem;">
                                                    <?php echo htmlspecialchars($entidad['nombre_entidad']); ?>
                                                </div>
                                                <?php if (!empty($entidad['siglas'])): ?>
                                                    <span class="badge bg-light text-dark border">
                                                        <?php echo htmlspecialchars($entidad['siglas']); ?>
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge-tipo <?php echo $tipo; ?>">
                                                    <?php echo ucfirst($tipo); ?>
                                                </span>
                                            </td>
                                            <td style="font-size: 0.85rem;">
                                                <?php if (!empty($entidad['correo_electronico'])): ?>
                                                    <div class="small">
                                                        <i class="bi bi-envelope me-1 text-muted"></i>
                                                        <?php echo htmlspecialchars($entidad['correo_electronico']); ?>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if (!empty($entidad['telefono'])): ?>
                                                    <div class="small text-muted">
                                                        <i class="bi bi-telephone me-1"></i>
                                                        <?php echo htmlspecialchars($entidad['telefono']); ?>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if (empty($entidad['correo_electronico']) && empty($entidad['telefono'])): ?>
                                                    <span class="text-muted small"><em>Sin contacto</em></span>
                                                <?php endif; ?>
                                            </td>
                                            <td style="font-size: 0.85rem;">
                                                <?php echo htmlspecialchars($entidad['provincia'] ?? 'No especificada'); ?>
                                            </td>
                                            <td>
                                                <span class="badge-estado <?php echo $estado; ?>">
                                                    <?php echo ucfirst($estado); ?>
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <div class="d-flex gap-1 justify-content-end">
                                                    <button class="btn btn-outline-secondary btn-sm btn-editar-entidad"
                                                            data-id="<?php echo $entidad['id']; ?>"
                                                            data-codigo="<?php echo htmlspecialchars($entidad['codigo_entidad']); ?>"
                                                            data-nombre="<?php echo htmlspecialchars($entidad['nombre_entidad']); ?>"
                                                            data-siglas="<?php echo htmlspecialchars($entidad['siglas'] ?? ''); ?>"
                                                            data-tipo="<?php echo htmlspecialchars($entidad['tipo_entidad']); ?>"
                                                            data-provincia="<?php echo htmlspecialchars($entidad['provincia']); ?>"
                                                            data-responsable="<?php echo htmlspecialchars($entidad['responsable_contacto'] ?? ''); ?>"
                                                            data-telefono="<?php echo htmlspecialchars($entidad['telefono'] ?? ''); ?>"
                                                            data-correo="<?php echo htmlspecialchars($entidad['correo_electronico'] ?? ''); ?>"
                                                            data-direccion="<?php echo htmlspecialchars($entidad['direccion'] ?? ''); ?>"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalEditarEntidad"
                                                            title="Editar">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <button class="btn btn-outline-secondary btn-sm btn-ver-cursos"
                                                            data-id="<?php echo $entidad['id']; ?>"
                                                            data-nombre="<?php echo htmlspecialchars($entidad['nombre_entidad']); ?>"
                                                            data-siglas="<?php echo htmlspecialchars($entidad['siglas'] ?? ''); ?>"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalVerCursos"
                                                            title="Ver Cursos">
                                                        <i class="bi bi-journal-text"></i>
                                                    </button>
                                                    <button class="btn btn-outline-secondary btn-sm text-danger" title="Suspender">
                                                        <i class="bi bi-slash-circle"></i>
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

<!-- ===== MODAL AÑADIR ENTIDAD ===== -->
<div class="modal fade" id="modalAñadirEntidad" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-building-add me-2"></i>Registrar Nueva Entidad Formadora
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="../php/procesar_entidad.php" method="POST">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Nombre de la Entidad *</label>
                            <input type="text" name="nombre_entidad" class="form-control" placeholder="Ej: Instituto Técnico Superior" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Siglas / Nombre Corto</label>
                            <input type="text" name="siglas" class="form-control" placeholder="Ej: ITSFP">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tipo de Entidad *</label>
                            <select name="tipo_entidad" class="form-select" required>
                                <option value="publica">Pública (Gubernamental / Estatal)</option>
                                <option value="privada">Privada</option>
                                <option value="ong">Organización / ONG</option>
                                <option value="internacional">Cooperación Internacional</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Provincia Principal *</label>
                            <select name="provincia" class="form-select" required>
                                <option value="Bioko Norte">Bioko Norte (Malabo)</option>
                                <option value="Litoral">Litoral (Bata)</option>
                                <option value="Wele-Nzas">Wele-Nzas (Mongomo)</option>
                                <option value="Kie-Ntem">Kie-Ntem (Ebebiyín)</option>
                                <option value="Centro Sur">Centro Sur (Evinayong)</option>
                                <option value="Bioko Sur">Bioko Sur (Luba)</option>
                                <option value="Annobón">Annobón (San Antonio de Palé)</option>
                                <option value="Djibloho">Djibloho (Ciudad de la Paz)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Persona de Contacto</label>
                            <input type="text" name="responsable_contacto" class="form-control" placeholder="Nombre del encargado">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Teléfono de Contacto</label>
                            <input type="tel" name="telefono" class="form-control" placeholder="222 000 000">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Correo Electrónico</label>
                            <input type="email" name="correo_electronico" class="form-control" placeholder="contacto@entidad.gq">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Dirección Física / Sede</label>
                            <textarea name="direccion" class="form-control" rows="2" placeholder="Barrio, Calle, Referencia..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check2-circle me-1"></i> Guardar Entidad
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ===== MODAL EDITAR ENTIDAD ===== -->
<div class="modal fade" id="modalEditarEntidad" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-pencil-square me-2"></i>Editar Entidad Formadora
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="../php/actualizar_entidad.php" method="POST">
                <input type="hidden" id="edit_id" name="id">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Cód. Entidad</label>
                            <input type="text" id="edit_codigo_entidad" class="form-control font-monospace bg-light" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombre de la Entidad *</label>
                            <input type="text" id="edit_nombre_entidad" name="nombre_entidad" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Siglas</label>
                            <input type="text" id="edit_siglas" name="siglas" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tipo de Entidad *</label>
                            <select id="edit_tipo_entidad" name="tipo_entidad" class="form-select" required>
                                <option value="publica">Pública (Gubernamental / Estatal)</option>
                                <option value="privada">Privada</option>
                                <option value="ong">Organización / ONG</option>
                                <option value="internacional">Cooperación Internacional</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Provincia Principal *</label>
                            <select id="edit_provincia" name="provincia" class="form-select" required>
                                <option value="Bioko Norte">Bioko Norte (Malabo)</option>
                                <option value="Litoral">Litoral (Bata)</option>
                                <option value="Wele-Nzas">Wele-Nzas (Mongomo)</option>
                                <option value="Kie-Ntem">Kie-Ntem (Ebebiyín)</option>
                                <option value="Centro Sur">Centro Sur (Evinayong)</option>
                                <option value="Bioko Sur">Bioko Sur (Luba)</option>
                                <option value="Annobón">Annobón (San Antonio de Palé)</option>
                                <option value="Djibloho">Djibloho (Ciudad de la Paz)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Persona de Contacto</label>
                            <input type="text" id="edit_responsable_contacto" name="responsable_contacto" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Teléfono de Contacto</label>
                            <input type="tel" id="edit_telefono" name="telefono" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Correo Electrónico</label>
                            <input type="email" id="edit_correo_electronico" name="correo_electronico" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Dirección Física / Sede</label>
                            <textarea id="edit_direccion" name="direccion" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check2-circle me-1"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ===== MODAL VER CURSOS ===== -->
<div class="modal fade" id="modalVerCursos" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-journal-bookmark-fill me-2"></i>
                    Cursos de: <span id="lblNombreEntidad" class="text-warning"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="contenedorCursosEntidad">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="text-muted mt-2 mb-0">Cargando cursos...</p>
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
    const tabla = document.getElementById('tablaEntidades');
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
                    { orderable: false, targets: 6 }
                ],
                scrollX: false,
                autoWidth: true,
                scrollY: false,
                scrollCollapse: false
            });
        }
    }

    // ===== EDITAR ENTIDAD =====
    document.querySelectorAll('.btn-editar-entidad').forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('edit_id').value = this.dataset.id || '';
            document.getElementById('edit_codigo_entidad').value = this.dataset.codigo || '';
            document.getElementById('edit_nombre_entidad').value = this.dataset.nombre || '';
            document.getElementById('edit_siglas').value = this.dataset.siglas || '';
            document.getElementById('edit_tipo_entidad').value = this.dataset.tipo || 'publica';
            document.getElementById('edit_provincia').value = this.dataset.provincia || 'Bioko Norte';
            document.getElementById('edit_responsable_contacto').value = this.dataset.responsable || '';
            document.getElementById('edit_telefono').value = this.dataset.telefono || '';
            document.getElementById('edit_correo_electronico').value = this.dataset.correo || '';
            document.getElementById('edit_direccion').value = this.dataset.direccion || '';
        });
    });

    // ===== VER CURSOS =====
    document.querySelectorAll('.btn-ver-cursos').forEach(button => {
        button.addEventListener('click', function() {
            const entidadId = this.dataset.id;
            const nombreEntidad = this.dataset.nombre;
            const siglas = this.dataset.siglas;

            document.getElementById('lblNombreEntidad').textContent = siglas ? `${nombreEntidad} (${siglas})` : nombreEntidad;

            const contenedor = document.getElementById('contenedorCursosEntidad');
            contenedor.innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="text-muted mt-2 mb-0">Cargando cursos...</p>
                </div>`;

            fetch(`../php/obtener_cursos_entidad.php?entidad_id=${entidadId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'error') {
                        contenedor.innerHTML = `<div class="alert alert-danger mb-0">${data.message}</div>`;
                        return;
                    }

                    if (data.cursos.length === 0) {
                        contenedor.innerHTML = `
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-journal-x fs-1 d-block mb-2 text-secondary"></i>
                                <p class="mb-0">Esta entidad no tiene cursos registrados.</p>
                            </div>`;
                        return;
                    }

                    let html = `
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr class="fs-7 text-uppercase">
                                        <th>Código</th>
                                        <th>Título</th>
                                        <th>Modalidad</th>
                                        <th>Duración</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>`;

                    data.cursos.forEach(curso => {
                        const modalidadClass = curso.modalidad === 'presencial' ? 'bg-primary' : 
                                               curso.modalidad === 'online' ? 'bg-info' : 'bg-warning text-dark';
                        const estadoClass = curso.estado === 'activo' ? 'bg-success' : 
                                           curso.estado === 'proximamente' ? 'bg-warning text-dark' : 'bg-secondary';
                        html += `
                            <tr>
                                <td><span class="font-monospace fw-bold text-primary">${curso.codigo_curso}</span></td>
                                <td class="fw-semibold">${curso.titulo_curso}</td>
                                <td><span class="badge ${modalidadClass} text-capitalize">${curso.modalidad}</span></td>
                                <td>${curso.duracion_horas} hrs</td>
                                <td><span class="badge ${estadoClass} text-capitalize">${curso.estado}</span></td>
                            </tr>`;
                    });

                    html += `</tbody></table></div>`;
                    contenedor.innerHTML = html;
                })
                .catch(error => {
                    contenedor.innerHTML = `
                        <div class="alert alert-danger mb-0">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>Error al cargar los cursos.
                        </div>`;
                });
        });
    });
});
</script>

<?php include_once '../componentes/footer_admin.php'; ?>
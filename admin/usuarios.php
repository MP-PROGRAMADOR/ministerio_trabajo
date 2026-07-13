<?php
session_start();

// ===== VERIFICAR SESIÓN ADMINISTRATIVA =====
if (!isset($_SESSION['id_usuario']) || !in_array($_SESSION['rol'] ?? '', ['administrador', 'ministerio'])) {
    header('Location: ../login_admin.php');
    exit();
}

$titulo = 'Control de Usuarios - Panel de Administración';
include_once '../componentes/header_admin.php';
include_once '../conexion/conexion.php';

// ===== INICIALIZAR VARIABLES =====
$usuarios = [];
$total_usuarios = 0;
$total_buscadores = 0;
$total_empleadores = 0;
$total_administradores = 0;
$total_ministerio = 0;

try {
    // ===== CONTAR ESTADÍSTICAS =====
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM usuarios");
    $total_usuarios = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM usuarios WHERE rol = 'buscador'");
    $total_buscadores = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM usuarios WHERE rol = 'empleador'");
    $total_empleadores = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM usuarios WHERE rol = 'administrador'");
    $total_administradores = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM usuarios WHERE rol = 'ministerio'");
    $total_ministerio = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // ===== OBTENER TODOS LOS USUARIOS =====
    $stmt = $pdo->query("
        SELECT 
            id,
            numero_expediente,
            nombre,
            apellidos,
            nombre_usuario,
            correo_electronico,
            documento_identidad,
            rol,
            correo_verificado,
            DATE_FORMAT(fecha_registro, '%d/%m/%Y') AS fecha_registro
        FROM usuarios
        ORDER BY id DESC
    ");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Error en usuarios: " . $e->getMessage());
    $usuarios = [];
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
    .bg-info-subtle { background: rgba(13, 202, 240, 0.12); }
    .text-info { color: #0dcaf0 !important; }
    .bg-danger-subtle { background: rgba(220, 53, 69, 0.08); }
    .text-danger { color: #dc3545 !important; }

    .badge-rol {
        font-weight: 500;
        padding: 0.25rem 0.7rem;
        border-radius: 20px;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .badge-rol.administrador {
        background: #f8d7da;
        color: #721c24;
    }
    .badge-rol.ministerio {
        background: #cce5ff;
        color: #004085;
    }
    .badge-rol.empleador {
        background: #d1ecf1;
        color: #0c5460;
    }
    .badge-rol.buscador {
        background: #d4edda;
        color: #155724;
    }

    .badge-verificado {
        font-weight: 500;
        padding: 0.25rem 0.7rem;
        border-radius: 20px;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .badge-verificado.verificado {
        background: #d4edda;
        color: #155724;
    }
    .badge-verificado.pendiente {
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

    .btn-warning {
        background: #ffc107;
        color: #212529;
        border: none;
        border-radius: var(--gov-radius-sm);
        padding: 0.5rem 1.2rem;
        font-weight: 500;
        transition: all 0.3s;
    }
    .btn-warning:hover {
        background: #e0a800;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3);
        color: #212529;
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
                            <span class="stat-label">Total Usuarios</span>
                            <h3 class="stat-number"><?php echo number_format($total_usuarios); ?></h3>
                            <small class="text-muted">Registrados en el sistema</small>
                        </div>
                        <div class="stat-icon bg-primary-subtle text-primary">
                            <i class="bi bi-people"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="stat-label">Buscadores</span>
                            <h3 class="stat-number text-success"><?php echo number_format($total_buscadores); ?></h3>
                            <small class="text-success">Ciudadanos desempleados</small>
                        </div>
                        <div class="stat-icon bg-success-subtle text-success">
                            <i class="bi bi-person-check"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="stat-label">Empleadores</span>
                            <h3 class="stat-number text-info"><?php echo number_format($total_empleadores); ?></h3>
                            <small class="text-info">Empresas registradas</small>
                        </div>
                        <div class="stat-icon bg-info-subtle text-info">
                            <i class="bi bi-building"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="stat-label">Administradores</span>
                            <h3 class="stat-number text-danger"><?php echo number_format($total_administradores + $total_ministerio); ?></h3>
                            <small class="text-danger">Personal del sistema</small>
                        </div>
                        <div class="stat-icon bg-danger-subtle text-danger">
                            <i class="bi bi-shield-lock"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== TABLA DE USUARIOS ===== -->
        <div class="row">
            <div class="col-12">
                <div class="custom-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold m-0">
                            <i class="bi bi-list-ul me-2"></i>
                            Lista de Usuarios
                            <span class="badge bg-light text-dark border ms-2"><?php echo count($usuarios); ?> total</span>
                        </h5>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAgregarUsuario">
                            <i class="bi bi-plus-lg me-1"></i> Añadir Usuario
                        </button>
                    </div>

                    <div class="table-wrap">
                        <table class="table table-hover align-middle" id="tablaUsuarios">
                            <thead>
                                <tr>
                                    <th style="min-width: 100px;">Expediente</th>
                                    <th style="min-width: 160px;">Usuario / Nombre</th>
                                    <th style="min-width: 150px;">Documento / Email</th>
                                    <th style="min-width: 100px;">Rol</th>
                                    <th style="min-width: 100px;">Verificación</th>
                                    <th style="min-width: 90px;">Registro</th>
                                    <th style="min-width: 130px;" class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($usuarios)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                            No hay usuarios registrados.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($usuarios as $usr): ?>
                                        <tr>
                                            <td>
                                                <span class="font-monospace fw-bold text-primary">
                                                    <?php echo htmlspecialchars($usr['numero_expediente']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="fw-semibold" style="font-size: 0.9rem;">
                                                    <?php echo htmlspecialchars($usr['nombre'] . ' ' . $usr['apellidos']); ?>
                                                </div>
                                                <small class="text-muted" style="font-size: 0.7rem;">
                                                    <i class="bi bi-person me-1"></i>@<?php echo htmlspecialchars($usr['nombre_usuario']); ?>
                                                </small>
                                            </td>
                                            <td style="font-size: 0.85rem;">
                                                <div class="small fw-semibold text-dark">
                                                    <i class="bi bi-card-heading me-1"></i>
                                                    <?php echo htmlspecialchars($usr['documento_identidad']); ?>
                                                </div>
                                                <small class="text-muted d-block" style="font-size: 0.7rem;">
                                                    <?php echo htmlspecialchars($usr['correo_electronico']); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <span class="badge-rol <?php echo $usr['rol']; ?>">
                                                    <?php echo ucfirst($usr['rol']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge-verificado <?php echo ($usr['correo_verificado'] == 1) ? 'verificado' : 'pendiente'; ?>">
                                                    <?php echo ($usr['correo_verificado'] == 1) ? 'Verificado' : 'Pendiente'; ?>
                                                </span>
                                            </td>
                                            <td style="font-size: 0.8rem;">
                                                <?php echo htmlspecialchars($usr['fecha_registro']); ?>
                                            </td>
                                            <td class="text-end">
                                                <div class="d-flex gap-1 justify-content-end">
                                                    <button class="btn btn-outline-secondary btn-sm btn-editar-usuario"
                                                            data-id="<?php echo $usr['id']; ?>"
                                                            data-expediente="<?php echo htmlspecialchars($usr['numero_expediente']); ?>"
                                                            data-nombre="<?php echo htmlspecialchars($usr['nombre']); ?>"
                                                            data-apellidos="<?php echo htmlspecialchars($usr['apellidos']); ?>"
                                                            data-username="<?php echo htmlspecialchars($usr['nombre_usuario']); ?>"
                                                            data-email="<?php echo htmlspecialchars($usr['correo_electronico']); ?>"
                                                            data-documento="<?php echo htmlspecialchars($usr['documento_identidad']); ?>"
                                                            data-rol="<?php echo htmlspecialchars($usr['rol']); ?>"
                                                            data-verificado="<?php echo $usr['correo_verificado']; ?>"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalEditarUsuario"
                                                            title="Editar">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <button class="btn btn-outline-secondary btn-sm btn-ver-usuario"
                                                            data-id="<?php echo $usr['id']; ?>"
                                                            data-expediente="<?php echo htmlspecialchars($usr['numero_expediente']); ?>"
                                                            data-nombre="<?php echo htmlspecialchars($usr['nombre'] . ' ' . $usr['apellidos']); ?>"
                                                            data-username="<?php echo htmlspecialchars($usr['nombre_usuario']); ?>"
                                                            data-email="<?php echo htmlspecialchars($usr['correo_electronico']); ?>"
                                                            data-documento="<?php echo htmlspecialchars($usr['documento_identidad']); ?>"
                                                            data-rol="<?php echo htmlspecialchars($usr['rol']); ?>"
                                                            data-verificado="<?php echo $usr['correo_verificado']; ?>"
                                                            data-registro="<?php echo htmlspecialchars($usr['fecha_registro']); ?>"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalDetalleUsuario"
                                                            title="Ver detalles">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    <button class="btn btn-outline-secondary btn-sm text-danger btn-eliminar-usuario"
                                                            data-id="<?php echo $usr['id']; ?>"
                                                            data-nombre="<?php echo htmlspecialchars($usr['nombre_usuario']); ?>"
                                                            title="Eliminar">
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

<!-- ===== MODAL AGREGAR USUARIO ===== -->
<div class="modal fade" id="modalAgregarUsuario" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-person-plus-fill me-2"></i>Registrar Nuevo Usuario
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="../php/procesar_usuarios.php" method="POST">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombre(s) *</label>
                            <input type="text" name="nombre" class="form-control" placeholder="Ej: Salvador" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Apellidos *</label>
                            <input type="text" name="apellidos" class="form-control" placeholder="Ej: Mete Bijeri" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombre de Usuario *</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted">@</span>
                                <input type="text" name="nombre_usuario" class="form-control" placeholder="ej: smete" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Documento de Identidad *</label>
                            <input type="text" name="documento_identidad" class="form-control font-monospace" placeholder="Ej: 123456789" required>
                        </div>
                        <div class="col-md-7">
                            <label class="form-label fw-semibold">Correo Electrónico *</label>
                            <input type="email" name="correo_electronico" class="form-control" placeholder="ejemplo@dominio.com" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">Rol Institucional *</label>
                            <select name="rol" class="form-select" required>
                                <option value="buscador">Buscador de Empleo</option>
                                <option value="empleador">Empleador / Empresa</option>
                                <option value="ministerio">Personal Ministerio</option>
                                <option value="administrador">Administrador del Sistema</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Contraseña *</label>
                            <input type="password" name="password" class="form-control" minlength="8" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Confirmar Contraseña *</label>
                            <input type="password" name="password_confirm" class="form-control" minlength="8" required>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="correo_verificado" value="1" checked>
                                <label class="form-check-label fw-semibold">Marcar correo como verificado</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check2-circle me-1"></i> Guardar Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ===== MODAL EDITAR USUARIO ===== -->
<div class="modal fade" id="modalEditarUsuario" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-pencil-square me-2"></i>Editar Usuario
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="../php/actualizar_usuario.php" method="POST">
                <input type="hidden" id="edit_id" name="id">
                <div class="modal-body p-4">
                    <div class="alert alert-secondary py-2 px-3 small mb-4">
                        <i class="bi bi-folder2-open me-1 text-primary"></i>
                        Expediente: <strong id="lbl_edit_expediente" class="font-monospace text-primary">EG-00000</strong>
                        <span class="ms-3">ID: <span id="lbl_edit_id">0</span></span>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombre(s) *</label>
                            <input type="text" id="edit_nombre" name="nombre" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Apellidos *</label>
                            <input type="text" id="edit_apellidos" name="apellidos" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombre de Usuario *</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted">@</span>
                                <input type="text" id="edit_nombre_usuario" name="nombre_usuario" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Documento de Identidad *</label>
                            <input type="text" id="edit_documento_identidad" name="documento_identidad" class="form-control font-monospace" required>
                        </div>
                        <div class="col-md-7">
                            <label class="form-label fw-semibold">Correo Electrónico *</label>
                            <input type="email" id="edit_correo_electronico" name="correo_electronico" class="form-control" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">Rol Institucional *</label>
                            <select id="edit_rol" name="rol" class="form-select" required>
                                <option value="buscador">Buscador de Empleo</option>
                                <option value="empleador">Empleador / Empresa</option>
                                <option value="ministerio">Personal Ministerio</option>
                                <option value="administrador">Administrador del Sistema</option>
                            </select>
                        </div>
                        <div class="col-12 border-top pt-3 mt-3">
                            <p class="text-muted small mb-2">
                                <i class="bi bi-shield-lock me-1"></i>
                                <strong>Cambiar Contraseña (Opcional):</strong> Deje vacío para conservar la actual.
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nueva Contraseña</label>
                            <input type="password" id="edit_password" name="password" class="form-control" minlength="8" placeholder="••••••••">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Confirmar Contraseña</label>
                            <input type="password" id="edit_password_confirm" name="password_confirm" class="form-control" minlength="8" placeholder="••••••••">
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="edit_correo_verificado" name="correo_verificado" value="1">
                                <label class="form-check-label fw-semibold">Cuenta con correo verificado</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning text-dark">
                        <i class="bi bi-arrow-repeat me-1"></i> Actualizar Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ===== MODAL DETALLE USUARIO ===== -->
<div class="modal fade" id="modalDetalleUsuario" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-person-badge-fill me-2"></i>Ficha Informativa del Usuario
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
                    <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold fs-3 me-3" style="width: 60px; height: 60px;">
                        <span id="view_iniciales">US</span>
                    </div>
                    <div>
                        <h5 class="mb-1 fw-bold text-dark" id="view_nombre_completo">Nombre Apellidos</h5>
                        <div class="text-muted small">
                            <span class="me-3"><i class="bi bi-at me-1"></i><span id="view_username">usuario</span></span>
                            <span><i class="bi bi-folder2-open me-1"></i>Expediente: <strong id="view_expediente" class="font-monospace text-primary">EG-00000</strong></span>
                        </div>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 h-100">
                            <small class="text-muted d-block mb-1"><i class="bi bi-card-heading me-1"></i>Documento</small>
                            <span class="fw-bold font-monospace text-dark fs-6" id="view_documento">---</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 h-100">
                            <small class="text-muted d-block mb-1"><i class="bi bi-envelope me-1"></i>Correo</small>
                            <span class="fw-semibold text-dark fs-6" id="view_email">---</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded-3 h-100">
                            <small class="text-muted d-block mb-1"><i class="bi bi-shield-check me-1"></i>Rol</small>
                            <span id="view_badge_rol" class="badge text-capitalize">---</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded-3 h-100">
                            <small class="text-muted d-block mb-1"><i class="bi bi-check-circle me-1"></i>Verificación</small>
                            <span id="view_badge_verificado" class="badge">---</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded-3 h-100">
                            <small class="text-muted d-block mb-1"><i class="bi bi-calendar-event me-1"></i>Registro</small>
                            <span class="fw-semibold text-dark" id="view_fecha_registro">--/--/----</span>
                        </div>
                    </div>
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
    const tabla = document.getElementById('tablaUsuarios');
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

    // ===== VALIDAR CONTRASEÑAS EN AGREGAR =====
    document.querySelector('#modalAgregarUsuario form')?.addEventListener('submit', function(e) {
        const pass = this.querySelector('input[name="password"]').value;
        const passConfirm = this.querySelector('input[name="password_confirm"]').value;
        if (pass !== passConfirm) {
            e.preventDefault();
            alert('Las contraseñas no coinciden.');
        }
    });

    // ===== EDITAR USUARIO =====
    document.querySelectorAll('.btn-editar-usuario').forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('edit_id').value = this.dataset.id || '';
            document.getElementById('lbl_edit_id').textContent = this.dataset.id || '0';
            document.getElementById('lbl_edit_expediente').textContent = this.dataset.expediente || 'EG-00000';
            document.getElementById('edit_nombre').value = this.dataset.nombre || '';
            document.getElementById('edit_apellidos').value = this.dataset.apellidos || '';
            document.getElementById('edit_nombre_usuario').value = this.dataset.username || '';
            document.getElementById('edit_documento_identidad').value = this.dataset.documento || '';
            document.getElementById('edit_correo_electronico').value = this.dataset.email || '';
            document.getElementById('edit_rol').value = this.dataset.rol || 'buscador';
            document.getElementById('edit_correo_verificado').checked = (this.dataset.verificado == "1");
            
            document.getElementById('edit_password').value = '';
            document.getElementById('edit_password_confirm').value = '';
        });
    });

    // ===== VALIDAR CONTRASEÑAS EN EDICIÓN =====
    document.querySelector('#modalEditarUsuario form')?.addEventListener('submit', function(e) {
        const pass = this.querySelector('input[name="password"]').value;
        const passConfirm = this.querySelector('input[name="password_confirm"]').value;
        if (pass !== '' || passConfirm !== '') {
            if (pass !== passConfirm) {
                e.preventDefault();
                alert('Las contraseñas no coinciden.');
            }
        }
    });

    // ===== VER DETALLE USUARIO =====
    document.querySelectorAll('.btn-ver-usuario').forEach(button => {
        button.addEventListener('click', function() {
            const nombre = this.dataset.nombre || 'Sin Nombre';
            const username = this.dataset.username || '---';
            const expediente = this.dataset.expediente || '---';
            const documento = this.dataset.documento || '---';
            const email = this.dataset.email || '---';
            const rol = this.dataset.rol || 'buscador';
            const verificado = this.dataset.verificado == "1";
            const registro = this.dataset.registro || '---';

            document.getElementById('view_nombre_completo').textContent = nombre;
            document.getElementById('view_username').textContent = username;
            document.getElementById('view_expediente').textContent = expediente;
            document.getElementById('view_documento').textContent = documento;
            document.getElementById('view_email').textContent = email;
            document.getElementById('view_fecha_registro').textContent = registro;

            const iniciales = nombre.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
            document.getElementById('view_iniciales').textContent = iniciales || 'US';

            const badgeRol = document.getElementById('view_badge_rol');
            const rolMap = {
                'administrador': 'bg-danger-subtle text-danger border border-danger-subtle',
                'ministerio': 'bg-primary-subtle text-primary border border-primary-subtle',
                'empleador': 'bg-info-subtle text-info border border-info-subtle',
                'buscador': 'bg-success-subtle text-success border border-success-subtle'
            };
            badgeRol.textContent = rol;
            badgeRol.className = 'badge text-capitalize px-2 py-1 ' + (rolMap[rol] || 'bg-secondary-subtle text-secondary');

            const badgeVerificado = document.getElementById('view_badge_verificado');
            if (verificado) {
                badgeVerificado.textContent = 'Verificado';
                badgeVerificado.className = 'badge bg-success-subtle text-success border border-success-subtle';
            } else {
                badgeVerificado.textContent = 'Pendiente';
                badgeVerificado.className = 'badge bg-warning-subtle text-warning border border-warning-subtle';
            }
        });
    });

    // ===== ELIMINAR USUARIO =====
    document.querySelectorAll('.btn-eliminar-usuario').forEach(button => {
        button.addEventListener('click', function() {
            if (confirm('¿Estás seguro de eliminar al usuario "' + this.dataset.nombre + '"? Esta acción no se puede deshacer.')) {
                window.location.href = '../php/eliminar_usuario.php?id=' + this.dataset.id;
            }
        });
    });
});
</script>

<?php include_once '../componentes/footer_admin.php'; ?>
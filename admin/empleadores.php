<?php
session_start();

// ===== VERIFICAR SESIÓN ADMINISTRATIVA =====
if (!isset($_SESSION['id_usuario']) || !in_array($_SESSION['rol'] ?? '', ['administrador', 'ministerio'])) {
    header('Location: ../login_admin.php');
    exit();
}

$titulo = 'Empresas y Empleadores - Panel de Administración';
include_once '../componentes/header_admin.php';
include_once '../conexion/conexion.php';

// ===== INICIALIZAR VARIABLES =====
$empleadores = [];
$total_empleadores = 0;
$total_ofertas = 0;
$sectores = [];
$provincias = [];

try {
    // ===== CONTAR ESTADÍSTICAS =====
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM empleadores");
    $total_empleadores = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM ofertas_empleo");
    $total_ofertas = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // ===== OBTENER SECTORES ÚNICOS =====
    $stmt = $pdo->query("SELECT DISTINCT sector_industrial FROM empleadores ORDER BY sector_industrial");
    $sectores = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // ===== OBTENER PROVINCIAS ÚNICAS =====
    $stmt = $pdo->query("SELECT DISTINCT direccion FROM empleadores");
    $provincias_raw = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($provincias_raw as $dir) {
        if (!empty($dir)) {
            $partes = explode(',', $dir);
            $prov = trim(end($partes));
            if (!empty($prov) && !in_array($prov, $provincias)) {
                $provincias[] = $prov;
            }
        }
    }

    // ===== OBTENER TODOS LOS EMPLEADORES =====
    $stmt = $pdo->query("
        SELECT 
            e.id,
            e.nombre_empresa,
            e.rnc_ruc,
            e.sector_industrial,
            e.telefono_corporativo,
            e.direccion,
            e.sitio_web,
            u.id as usuario_id,
            u.nombre,
            u.apellidos,
            u.correo_electronico,
            u.nombre_usuario,
            u.documento_identidad,
            u.fecha_registro,
            (SELECT COUNT(*) FROM ofertas_empleo WHERE empleador_id = e.id) as total_ofertas
        FROM empleadores e
        INNER JOIN usuarios u ON e.usuario_id = u.id
        ORDER BY e.id DESC
    ");
    $empleadores = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Error en empleadores: " . $e->getMessage());
    $empleadores = [];
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

    .badge-sector {
        font-weight: 500;
        padding: 0.25rem 0.7rem;
        border-radius: 20px;
        font-size: 0.7rem;
        background: var(--gov-bg);
        color: var(--gov-blue);
        border: 1px solid var(--gov-border);
    }

    .table th {
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        color: #6b7a8a;
        border-bottom: 2px solid var(--gov-border);
        background: var(--gov-bg);
    }
    .table td {
        vertical-align: middle;
        padding: 0.75rem 1rem;
    }
    .table tbody tr:hover {
        background: rgba(11, 58, 96, 0.02);
    }

    .avatar-empresa {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--gov-blue);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.85rem;
        flex-shrink: 0;
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
        .avatar-empresa {
            width: 32px;
            height: 32px;
            font-size: 0.7rem;
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
                            <span class="stat-label">Total Empresas</span>
                            <h3 class="stat-number"><?php echo number_format($total_empleadores); ?></h3>
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
                            <span class="stat-label">Ofertas Publicadas</span>
                            <h3 class="stat-number text-success"><?php echo number_format($total_ofertas); ?></h3>
                            <small class="text-success">Vacantes activas</small>
                        </div>
                        <div class="stat-icon bg-success-subtle text-success">
                            <i class="bi bi-file-earmark-post"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="stat-label">Sectores</span>
                            <h3 class="stat-number text-info"><?php echo number_format(count($sectores)); ?></h3>
                            <small class="text-info">Industrias representadas</small>
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
                            <span class="stat-label">Provincias</span>
                            <h3 class="stat-number text-primary"><?php echo number_format(count($provincias)); ?></h3>
                            <small class="text-muted">Ubicaciones</small>
                        </div>
                        <div class="stat-icon bg-primary-subtle text-primary">
                            <i class="bi bi-geo-alt"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== TABLA DE EMPLEADORES ===== -->
        <div class="row">
            <div class="col-12">
                <div class="custom-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold m-0">
                            <i class="bi bi-list-ul me-2"></i>
                            Lista de Empresas
                            <span class="badge bg-light text-dark border ms-2"><?php echo $total_empleadores; ?> total</span>
                        </h5>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalNuevoEmpleador">
                            <i class="bi bi-plus-lg me-1"></i> Nueva Empresa
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="tablaEmpleadores">
                            <thead>
                                <tr>
                                    <th>Empresa</th>
                                    <th>RUC / RNC</th>
                                    <th>Sector</th>
                                    <th>Contacto</th>
                                    <th>Ubicación</th>
                                    <th>Ofertas</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($empleadores)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                            No hay empresas registradas en el sistema.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($empleadores as $emp): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="avatar-empresa">
                                                        <?php echo strtoupper(substr($emp['nombre_empresa'], 0, 2)); ?>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold" style="font-size: 0.9rem;">
                                                            <?php echo htmlspecialchars($emp['nombre_empresa']); ?>
                                                        </div>
                                                        <small class="text-muted" style="font-size: 0.7rem;">
                                                            <?php echo htmlspecialchars($emp['nombre'] . ' ' . $emp['apellidos']); ?>
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="font-monospace" style="font-size: 0.85rem;">
                                                    <?php echo htmlspecialchars($emp['rnc_ruc'] ?? 'No registrado'); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge-sector">
                                                    <?php echo htmlspecialchars($emp['sector_industrial']); ?>
                                                </span>
                                            </td>
                                            <td style="font-size: 0.85rem;">
                                                <div><i class="bi bi-telephone me-1"></i><?php echo htmlspecialchars($emp['telefono_corporativo']); ?></div>
                                                <small class="text-muted" style="font-size: 0.7rem;">
                                                    <i class="bi bi-envelope me-1"></i><?php echo htmlspecialchars($emp['correo_electronico']); ?>
                                                </small>
                                            </td>
                                            <td style="font-size: 0.85rem;">
                                                <?php 
                                                    $dir = $emp['direccion'] ?? '';
                                                    if (!empty($dir)) {
                                                        $partes = explode(',', $dir);
                                                        echo htmlspecialchars(trim(end($partes)));
                                                    } else {
                                                        echo '<span class="text-muted">No especificada</span>';
                                                    }
                                                ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border">
                                                    <?php echo $emp['total_ofertas']; ?> ofertas
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <div class="d-flex gap-1 justify-content-end">
                                                    <button class="btn btn-outline-secondary btn-sm btn-ver-empresa" 
                                                            data-id="<?php echo $emp['id']; ?>"
                                                            data-nombre="<?php echo htmlspecialchars($emp['nombre_empresa']); ?>"
                                                            data-ruc="<?php echo htmlspecialchars($emp['rnc_ruc'] ?? 'No registrado'); ?>"
                                                            data-sector="<?php echo htmlspecialchars($emp['sector_industrial']); ?>"
                                                            data-telefono="<?php echo htmlspecialchars($emp['telefono_corporativo']); ?>"
                                                            data-direccion="<?php echo htmlspecialchars($emp['direccion'] ?? 'No especificada'); ?>"
                                                            data-web="<?php echo htmlspecialchars($emp['sitio_web'] ?? 'No registrado'); ?>"
                                                            data-responsable="<?php echo htmlspecialchars($emp['nombre'] . ' ' . $emp['apellidos']); ?>"
                                                            data-email="<?php echo htmlspecialchars($emp['correo_electronico']); ?>"
                                                            data-ofertas="<?php echo $emp['total_ofertas']; ?>"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#modalDetalleEmpresa"
                                                            title="Ver detalles">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    <button class="btn btn-outline-secondary btn-sm" title="Editar">
                                                        <i class="bi bi-pencil"></i>
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

<!-- ===== MODAL NUEVO EMPLEADOR ===== -->
<div class="modal fade" id="modalNuevoEmpleador" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-building-add me-2" style="color: var(--gov-gold);"></i>
                    Registrar Nueva Empresa
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="../php/registrar_empleador.php" method="POST">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombre de la Empresa *</label>
                            <input type="text" name="nombre_empresa" class="form-control" placeholder="Ej. GETESA S.A." required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">RUC / RNC</label>
                            <input type="text" name="rnc_ruc" class="form-control" placeholder="Ej. RNC-12345">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Sector Industrial *</label>
                            <select name="sector_industrial" class="form-select" required>
                                <option value="">Seleccionar...</option>
                                <?php foreach ($sectores as $sector): ?>
                                    <option value="<?php echo htmlspecialchars($sector); ?>"><?php echo htmlspecialchars($sector); ?></option>
                                <?php endforeach; ?>
                                <option value="Otros">Otros</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Teléfono Corporativo *</label>
                            <input type="tel" name="telefono_corporativo" class="form-control" placeholder="+240 222 333 444" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Dirección *</label>
                            <input type="text" name="direccion" class="form-control" placeholder="Ej. Malabo, Bioko Norte" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Sitio Web</label>
                            <input type="url" name="sitio_web" class="form-control" placeholder="https://www.empresa.gq">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Correo del Responsable *</label>
                            <input type="email" name="correo_responsable" class="form-control" placeholder="responsable@empresa.gq" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Contraseña *</label>
                            <input type="password" name="password" class="form-control" placeholder="Mínimo 8 caracteres" minlength="8" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombre del Responsable *</label>
                            <input type="text" name="nombre_responsable" class="form-control" placeholder="Nombre del representante" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Apellidos del Responsable *</label>
                            <input type="text" name="apellidos_responsable" class="form-control" placeholder="Apellidos" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Documento del Responsable *</label>
                            <input type="text" name="documento_responsable" class="form-control" placeholder="DIP del responsable" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check2-circle me-1"></i> Registrar Empresa
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ===== MODAL DETALLE EMPRESA ===== -->
<div class="modal fade" id="modalDetalleEmpresa" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-building me-2" style="color: var(--gov-gold);"></i>
                    <span id="modal-empresa-nombre">Empresa</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="bg-light p-3 rounded-3">
                            <h6 class="fw-bold text-muted" style="font-size: 0.8rem; text-transform: uppercase;">Información General</h6>
                            <div class="row g-2">
                                <div class="col-6">
                                    <small class="text-muted d-block">RUC / RNC</small>
                                    <strong id="modal-empresa-ruc">-</strong>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Sector</small>
                                    <strong id="modal-empresa-sector">-</strong>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Teléfono</small>
                                    <strong id="modal-empresa-telefono">-</strong>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Sitio Web</small>
                                    <strong id="modal-empresa-web">-</strong>
                                </div>
                                <div class="col-12">
                                    <small class="text-muted d-block">Dirección</small>
                                    <strong id="modal-empresa-direccion">-</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="bg-light p-3 rounded-3">
                            <h6 class="fw-bold text-muted" style="font-size: 0.8rem; text-transform: uppercase;">Responsable</h6>
                            <div class="row g-2">
                                <div class="col-6">
                                    <small class="text-muted d-block">Nombre</small>
                                    <strong id="modal-empresa-responsable">-</strong>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Correo</small>
                                    <strong id="modal-empresa-email">-</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="bg-light p-3 rounded-3">
                            <h6 class="fw-bold text-muted" style="font-size: 0.8rem; text-transform: uppercase;">Estadísticas</h6>
                            <div class="row g-2">
                                <div class="col-6">
                                    <small class="text-muted d-block">Ofertas Publicadas</small>
                                    <strong id="modal-empresa-ofertas">-</strong>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Estado</small>
                                    <strong><span class="badge bg-success">Activo</span></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary"><i class="bi bi-pencil me-1"></i> Editar</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ===== INICIALIZAR DATATABLE =====
    const tabla = document.getElementById('tablaEmpleadores');
    if (tabla && tabla.querySelector('tbody tr') && tabla.querySelector('tbody tr').cells.length > 1) {
        if (typeof $.fn.DataTable !== 'undefined') {
            $(tabla).DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
                },
                pageLength: 10,
                order: [[0, 'asc']],
                responsive: true,
                columnDefs: [
                    { orderable: false, targets: 6 }
                ]
            });
        }
    }

    // ===== PASAR DATOS AL MODAL DE DETALLE =====
    document.querySelectorAll('.btn-ver-empresa').forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('modal-empresa-nombre').textContent = this.dataset.nombre || 'N/A';
            document.getElementById('modal-empresa-ruc').textContent = this.dataset.ruc || 'N/A';
            document.getElementById('modal-empresa-sector').textContent = this.dataset.sector || 'N/A';
            document.getElementById('modal-empresa-telefono').textContent = this.dataset.telefono || 'N/A';
            document.getElementById('modal-empresa-web').textContent = this.dataset.web || 'N/A';
            document.getElementById('modal-empresa-direccion').textContent = this.dataset.direccion || 'N/A';
            document.getElementById('modal-empresa-responsable').textContent = this.dataset.responsable || 'N/A';
            document.getElementById('modal-empresa-email').textContent = this.dataset.email || 'N/A';
            document.getElementById('modal-empresa-ofertas').textContent = this.dataset.ofertas || '0';
        });
    });
});
</script>

<?php include_once '../componentes/footer_admin.php'; ?>
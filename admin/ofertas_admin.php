<?php
session_start();

// ===== VERIFICAR SESIÓN ADMINISTRATIVA =====
if (!isset($_SESSION['id_usuario']) || !in_array($_SESSION['rol'] ?? '', ['administrador', 'ministerio'])) {
    header('Location: ../login_admin.php');
    exit();
}

$titulo = 'Ofertas de Empleo - Panel de Administración';
include_once '../componentes/header_admin.php';
include_once '../conexion/conexion.php';

// ===== INICIALIZAR VARIABLES =====
$ofertas = [];
$total_ofertas = 0;
$ofertas_abiertas = 0;
$ofertas_cerradas = 0;
$empleadores = [];

try {
    // ===== CONTAR ESTADÍSTICAS =====
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM ofertas_empleo");
    $total_ofertas = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM ofertas_empleo WHERE estado = 'abierta'");
    $ofertas_abiertas = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM ofertas_empleo WHERE estado = 'cerrada'");
    $ofertas_cerradas = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // ===== OBTENER EMPLEADORES PARA EL SELECT =====
    $stmt = $pdo->query("SELECT id, nombre_empresa FROM empleadores ORDER BY nombre_empresa");
    $empleadores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ===== OBTENER TODAS LAS OFERTAS =====
    $stmt = $pdo->query("
        SELECT 
            o.id,
            o.titulo_puesto,
            o.descripcion,
            o.requisitos,
            o.provincia,
            o.salario_ofrecido,
            o.estado,
            o.fecha_publicacion,
            e.nombre_empresa,
            e.sector_industrial
        FROM ofertas_empleo o
        INNER JOIN empleadores e ON o.empleador_id = e.id
        ORDER BY o.fecha_publicacion DESC
    ");
    $ofertas = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Error en ofertas_admin: " . $e->getMessage());
    $ofertas = [];
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
    .bg-danger-subtle { background: rgba(220, 53, 69, 0.08); }
    .text-danger { color: #dc3545 !important; }
    .bg-warning-subtle { background: rgba(255, 193, 7, 0.12); }
    .text-warning { color: #ffc107 !important; }

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
                            <span class="stat-label">Total Ofertas</span>
                            <h3 class="stat-number"><?php echo number_format($total_ofertas); ?></h3>
                            <small class="text-muted">Registradas en el sistema</small>
                        </div>
                        <div class="stat-icon bg-primary-subtle text-primary">
                            <i class="bi bi-file-earmark-post"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="stat-label">Activas</span>
                            <h3 class="stat-number text-success"><?php echo number_format($ofertas_abiertas); ?></h3>
                            <small class="text-success">Vacantes disponibles</small>
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
                            <span class="stat-label">Cerradas</span>
                            <h3 class="stat-number text-danger"><?php echo number_format($ofertas_cerradas); ?></h3>
                            <small class="text-danger">Vacantes finalizadas</small>
                        </div>
                        <div class="stat-icon bg-danger-subtle text-danger">
                            <i class="bi bi-x-circle"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="stat-label">Empresas</span>
                            <h3 class="stat-number text-warning"><?php echo number_format(count($empleadores)); ?></h3>
                            <small class="text-warning">Con ofertas publicadas</small>
                        </div>
                        <div class="stat-icon bg-warning-subtle text-warning">
                            <i class="bi bi-building"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== TABLA DE OFERTAS ===== -->
        <div class="row">
            <div class="col-12">
                <div class="custom-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold m-0">
                            <i class="bi bi-list-ul me-2"></i>
                            Lista de Ofertas
                            <span class="badge bg-light text-dark border ms-2"><?php echo $total_ofertas; ?> total</span>
                        </h5>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalNuevaOferta">
                            <i class="bi bi-plus-lg me-1"></i> Nueva Oferta
                        </button>
                    </div>

                    <div class="table-wrap">
                        <table class="table table-hover align-middle" id="tablaOfertas">
                            <thead>
                                <tr>
                                    <th style="min-width: 140px;">Puesto</th>
                                    <th style="min-width: 130px;">Empresa</th>
                                    <th style="min-width: 100px;">Provincia</th>
                                    <th style="min-width: 100px;">Salario</th>
                                    <th style="min-width: 90px;">Estado</th>
                                    <th style="min-width: 100px;">Fecha</th>
                                    <th style="min-width: 120px;" class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($ofertas)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                            No hay ofertas de empleo registradas.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($ofertas as $oferta): ?>
                                        <tr>
                                            <td>
                                                <div class="fw-semibold" style="font-size: 0.9rem;">
                                                    <?php echo htmlspecialchars($oferta['titulo_puesto']); ?>
                                                </div>
                                                <small class="text-muted" style="font-size: 0.7rem;">
                                                    <?php echo htmlspecialchars($oferta['sector_industrial'] ?? 'N/A'); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <span style="font-size: 0.85rem;">
                                                    <?php echo htmlspecialchars($oferta['nombre_empresa']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border">
                                                    <?php echo htmlspecialchars($oferta['provincia']); ?>
                                                </span>
                                            </td>
                                            <td style="font-size: 0.85rem;">
                                                <?php echo $oferta['salario_ofrecido'] 
                                                    ? number_format($oferta['salario_ofrecido'], 0, ',', '.') . ' XAF' 
                                                    : '<span class="text-muted">No especificado</span>'; ?>
                                            </td>
                                            <td>
                                                <span class="badge-estado <?php echo $oferta['estado']; ?>">
                                                    <?php echo ucfirst($oferta['estado']); ?>
                                                </span>
                                            </td>
                                            <td style="font-size: 0.8rem;">
                                                <?php echo date('d/m/Y', strtotime($oferta['fecha_publicacion'])); ?>
                                            </td>
                                            <td class="text-end">
                                                <div class="d-flex gap-1 justify-content-end">
                                                    <button class="btn btn-outline-secondary btn-sm btn-ver-oferta"
                                                            data-id="<?php echo $oferta['id']; ?>"
                                                            data-titulo="<?php echo htmlspecialchars($oferta['titulo_puesto']); ?>"
                                                            data-empresa="<?php echo htmlspecialchars($oferta['nombre_empresa']); ?>"
                                                            data-provincia="<?php echo htmlspecialchars($oferta['provincia']); ?>"
                                                            data-salario="<?php echo $oferta['salario_ofrecido']; ?>"
                                                            data-estado="<?php echo $oferta['estado']; ?>"
                                                            data-descripcion="<?php echo htmlspecialchars($oferta['descripcion']); ?>"
                                                            data-requisitos="<?php echo htmlspecialchars($oferta['requisitos']); ?>"
                                                            data-fecha="<?php echo date('d/m/Y', strtotime($oferta['fecha_publicacion'])); ?>"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalDetalleOferta"
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

<!-- ===== MODAL NUEVA OFERTA ===== -->
<div class="modal fade" id="modalNuevaOferta" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-file-earmark-plus me-2" style="color: var(--gov-gold);"></i>
                    Publicar Nueva Oferta
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="../php/registrar_oferta_admin.php" method="POST">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Título del Puesto *</label>
                            <input type="text" name="titulo_puesto" class="form-control" placeholder="Ej. Ingeniero Civil" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Empresa *</label>
                            <select name="empleador_id" class="form-select" required>
                                <option value="">Seleccionar...</option>
                                <?php foreach ($empleadores as $emp): ?>
                                    <option value="<?php echo $emp['id']; ?>"><?php echo htmlspecialchars($emp['nombre_empresa']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Provincia *</label>
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
                            <label class="form-label fw-semibold">Salario (XAF) - Opcional</label>
                            <input type="number" name="salario_ofrecido" class="form-control" placeholder="Ej. 350000" min="0" step="5000">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Estado</label>
                            <select name="estado" class="form-select">
                                <option value="abierta">Abierta</option>
                                <option value="cerrada">Cerrada</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Descripción del Puesto *</label>
                            <textarea name="descripcion" class="form-control" rows="3" placeholder="Funciones y responsabilidades..." required></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Requisitos *</label>
                            <textarea name="requisitos" class="form-control" rows="2" placeholder="Formación, experiencia, idiomas..." required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check2-circle me-1"></i> Publicar Oferta
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ===== MODAL DETALLE OFERTA ===== -->
<div class="modal fade" id="modalDetalleOferta" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-file-earmark-text me-2" style="color: var(--gov-gold);"></i>
                    <span id="modal-oferta-titulo">Oferta</span>
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
                                    <small class="text-muted d-block">Empresa</small>
                                    <strong id="modal-oferta-empresa">-</strong>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Provincia</small>
                                    <strong id="modal-oferta-provincia">-</strong>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Salario</small>
                                    <strong id="modal-oferta-salario">-</strong>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Estado</small>
                                    <strong id="modal-oferta-estado">-</strong>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Fecha Publicación</small>
                                    <strong id="modal-oferta-fecha">-</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="bg-light p-3 rounded-3">
                            <h6 class="fw-bold text-muted" style="font-size: 0.8rem; text-transform: uppercase;">Descripción</h6>
                            <p id="modal-oferta-descripcion" class="mb-0" style="font-size: 0.9rem;">-</p>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="bg-light p-3 rounded-3">
                            <h6 class="fw-bold text-muted" style="font-size: 0.8rem; text-transform: uppercase;">Requisitos</h6>
                            <p id="modal-oferta-requisitos" class="mb-0" style="font-size: 0.9rem;">-</p>
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

<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ===== INICIALIZAR DATATABLE =====
    const tabla = document.getElementById('tablaOfertas');
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
                autoWidth: true
            });
        }
    }

    // ===== PASAR DATOS AL MODAL DE DETALLE =====
    document.querySelectorAll('.btn-ver-oferta').forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('modal-oferta-titulo').textContent = this.dataset.titulo || 'N/A';
            document.getElementById('modal-oferta-empresa').textContent = this.dataset.empresa || 'N/A';
            document.getElementById('modal-oferta-provincia').textContent = this.dataset.provincia || 'N/A';
            
            const salario = this.dataset.salario;
            document.getElementById('modal-oferta-salario').textContent = salario ? 
                new Intl.NumberFormat('es-ES').format(salario) + ' XAF' : 'No especificado';
            
            const estado = this.dataset.estado || 'cerrada';
            const estadoHtml = estado === 'abierta' ? 
                '<span class="badge bg-success">Abierta</span>' : 
                '<span class="badge bg-secondary">Cerrada</span>';
            document.getElementById('modal-oferta-estado').innerHTML = estadoHtml;
            
            document.getElementById('modal-oferta-fecha').textContent = this.dataset.fecha || 'N/A';
            document.getElementById('modal-oferta-descripcion').textContent = this.dataset.descripcion || 'No hay descripción.';
            document.getElementById('modal-oferta-requisitos').textContent = this.dataset.requisitos || 'No hay requisitos especificados.';
        });
    });
});
</script>

<?php include_once '../componentes/footer_admin.php'; ?>
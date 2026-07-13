<?php
session_start();

// ===== VERIFICAR SESIÓN ADMINISTRATIVA =====
if (!isset($_SESSION['id_usuario']) || !in_array($_SESSION['rol'] ?? '', ['administrador', 'ministerio'])) {
    header('Location: ../login_admin.php');
    exit();
}

$titulo = 'Buscadores de Empleo - Panel de Administración';
include_once '../componentes/header_admin.php';
include_once '../conexion/conexion.php';

// ===== INICIALIZAR VARIABLES =====
$buscadores = [];
$total_buscadores = 0;
$desempleados = 0;
$contratados = 0;
$suspendidos = 0;

try {
    // ===== CONTAR ESTADÍSTICAS =====
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM buscadores_empleo");
    $total_buscadores = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM buscadores_empleo WHERE estado_laboral = 'desempleado'");
    $desempleados = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM buscadores_empleo WHERE estado_laboral = 'contratado'");
    $contratados = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM buscadores_empleo WHERE estado_laboral = 'suspendido'");
    $suspendidos = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // ===== OBTENER TODOS LOS BUSCADORES =====
    $stmt = $pdo->query("
        SELECT 
            b.id,
            b.telefono,
            b.estado_civil,
            b.foto_carnet,
            b.provincia,
            b.distrito,
            b.ciudad_municipio,
            b.estado_laboral,
            u.id as usuario_id,
            u.numero_expediente,
            u.nombre,
            u.apellidos,
            u.documento_identidad,
            u.correo_electronico,
            u.fecha_registro,
            d.copia_dip,
            d.cv,
            d.titulos,
            d.otros_documentos
        FROM buscadores_empleo b
        INNER JOIN usuarios u ON b.usuario_id = u.id
        LEFT JOIN documentos d ON u.id = d.usuario_id
        ORDER BY u.fecha_registro DESC
    ");
    $buscadores = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Error en buscadores_empleo: " . $e->getMessage());
    $buscadores = [];
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

    .badge-estado {
        font-weight: 500;
        padding: 0.3rem 0.8rem;
        border-radius: 20px;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .badge-estado.desempleado {
        background: #fff3cd;
        color: #856404;
    }
    .badge-estado.contratado {
        background: #d4edda;
        color: #155724;
    }
    .badge-estado.suspendido {
        background: #f8d7da;
        color: #721c24;
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

    .avatar-mini {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #e8eef3;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.8rem;
        color: var(--gov-blue);
        flex-shrink: 0;
        overflow: hidden;
    }
    .avatar-mini img {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        object-fit: cover;
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
    }
</style>

<div class="d-flex" id="wrapper">
    <?php include_once '../componentes/menu_admin.php'; ?>

    <div class="container-fluid p-4">

        <!-- ===== TÍTULO ===== -->
     

        <!-- ===== ESTADÍSTICAS ===== -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="stat-label">Total Buscadores</span>
                            <h3 class="stat-number"><?php echo number_format($total_buscadores); ?></h3>
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
                            <span class="stat-label">Desempleados</span>
                            <h3 class="stat-number text-warning"><?php echo number_format($desempleados); ?></h3>
                            <small class="text-warning">En busca de empleo</small>
                        </div>
                        <div class="stat-icon bg-warning-subtle text-warning">
                            <i class="bi bi-person"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="stat-label">Contratados</span>
                            <h3 class="stat-number text-success"><?php echo number_format($contratados); ?></h3>
                            <small class="text-success">Con empleo formal</small>
                        </div>
                        <div class="stat-icon bg-success-subtle text-success">
                            <i class="bi bi-briefcase-check"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="stat-label">Suspendidos</span>
                            <h3 class="stat-number text-danger"><?php echo number_format($suspendidos); ?></h3>
                            <small class="text-danger">Cuentas suspendidas</small>
                        </div>
                        <div class="stat-icon bg-danger-subtle text-danger">
                            <i class="bi bi-person-x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== TABLA DE BUSCADORES ===== -->
        <div class="row">
            <div class="col-12">
                <div class="custom-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold m-0">
                            <i class="bi bi-list-ul me-2"></i>
                            Lista de Buscadores
                            <span class="badge bg-light text-dark border ms-2"><?php echo $total_buscadores; ?> total</span>
                        </h5>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalNuevoBuscador">
                            <i class="bi bi-plus-lg me-1"></i> Nuevo Buscador
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="tablaBuscadores">
                            <thead>
                                <tr>
                                    <th>Expediente</th>
                                    <th>Nombre Completo</th>
                                    <th>Contacto</th>
                                    <th>Ubicación</th>
                                    <th>Estado</th>
                                    <th>Registro</th>
                                    <th class="text-end">Documentos</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($buscadores)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                            No hay buscadores registrados en el sistema.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($buscadores as $b): ?>
                                        <tr>
                                            <td>
                                                <span class="font-monospace fw-semibold"><?php echo htmlspecialchars($b['numero_expediente']); ?></span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="avatar-mini">
                                                        <?php 
                                                            $foto = !empty($b['foto_carnet']) ? '../' . $b['foto_carnet'] : '';
                                                            if ($foto && file_exists($foto)): 
                                                        ?>
                                                            <img src="<?php echo htmlspecialchars($foto); ?>" alt="Foto">
                                                        <?php else: ?>
                                                            <?php echo strtoupper(substr($b['nombre'], 0, 1) . substr($b['apellidos'], 0, 1)); ?>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold" style="font-size: 0.9rem;">
                                                            <?php echo htmlspecialchars($b['nombre'] . ' ' . $b['apellidos']); ?>
                                                        </div>
                                                        <small class="text-muted" style="font-size: 0.7rem;">
                                                            DIP: <?php echo htmlspecialchars($b['documento_identidad']); ?>
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td style="font-size: 0.85rem;">
                                                <div><i class="bi bi-telephone me-1"></i><?php echo htmlspecialchars($b['telefono']); ?></div>
                                                <small class="text-muted" style="font-size: 0.7rem;">
                                                    <i class="bi bi-envelope me-1"></i><?php echo htmlspecialchars($b['correo_electronico']); ?>
                                                </small>
                                            </td>
                                            <td style="font-size: 0.85rem;">
                                                <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $b['ciudad_municipio'])) . ', ' . ucfirst(str_replace('_', ' ', $b['provincia']))); ?>
                                            </td>
                                            <td>
                                                <span class="badge-estado <?php echo htmlspecialchars($b['estado_laboral']); ?>">
                                                    <?php echo ucfirst($b['estado_laboral']); ?>
                                                </span>
                                            </td>
                                            <td style="font-size: 0.8rem;">
                                                <?php echo date('d/m/Y', strtotime($b['fecha_registro'])); ?>
                                            </td>
                                            <td class="text-end">
                                                <div class="d-flex gap-1 justify-content-end flex-wrap">
                                                    <?php if (!empty($b['copia_dip'])): ?>
                                                        <a href="../<?php echo htmlspecialchars($b['copia_dip']); ?>" target="_blank" class="btn btn-outline-secondary btn-sm" title="DIP"><i class="bi bi-card-heading"></i></a>
                                                    <?php endif; ?>
                                                    <?php if (!empty($b['cv'])): ?>
                                                        <a href="../<?php echo htmlspecialchars($b['cv']); ?>" target="_blank" class="btn btn-outline-secondary btn-sm" title="CV"><i class="bi bi-file-earmark-person"></i></a>
                                                    <?php endif; ?>
                                                    <?php if (!empty($b['titulos'])): ?>
                                                        <a href="../<?php echo htmlspecialchars($b['titulos']); ?>" target="_blank" class="btn btn-outline-secondary btn-sm" title="Títulos"><i class="bi bi-journal-bookmark"></i></a>
                                                    <?php endif; ?>
                                                    <?php if (!empty($b['otros_documentos'])): ?>
                                                        <a href="../<?php echo htmlspecialchars($b['otros_documentos']); ?>" target="_blank" class="btn btn-outline-secondary btn-sm" title="Otros"><i class="bi bi-folder"></i></a>
                                                    <?php endif; ?>
                                                    <?php if (empty($b['copia_dip']) && empty($b['cv'])): ?>
                                                        <span class="text-muted" style="font-size: 0.7rem;">Sin documentos</span>
                                                    <?php endif; ?>
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

<!-- ===== MODAL NUEVO BUSCADOR ===== -->
<div class="modal fade" id="modalNuevoBuscador" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-person-plus me-2" style="color: var(--gov-gold);"></i>
                    Registrar Nuevo Buscador
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="../php/registrar_buscador.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombre *</label>
                            <input type="text" name="nombre" class="form-control" placeholder="Nombre del ciudadano" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Apellidos *</label>
                            <input type="text" name="apellidos" class="form-control" placeholder="Apellidos" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Documento de Identidad (DIP) *</label>
                            <input type="text" name="documento_identidad" class="form-control" placeholder="Ej. 123456789" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Correo Electrónico *</label>
                            <input type="email" name="correo_electronico" class="form-control" placeholder="ejemplo@correo.gq" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Teléfono *</label>
                            <input type="tel" name="telefono" class="form-control" placeholder="+240 222 333 444" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Estado Civil *</label>
                            <select name="estado_civil" class="form-select" required>
                                <option value="">Seleccionar...</option>
                                <option value="soltero">Soltero/a</option>
                                <option value="casado">Casado/a</option>
                                <option value="divorciado">Divorciado/a</option>
                                <option value="viudo">Viudo/a</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Provincia *</label>
                            <select name="provincia" class="form-select" required>
                                <option value="">Seleccionar...</option>
                                <option value="bioko_norte">Bioko Norte</option>
                                <option value="bioko_sur">Bioko Sur</option>
                                <option value="litoral">Litoral</option>
                                <option value="centro_sur">Centro Sur</option>
                                <option value="kie_ntem">Kié-Ntem</option>
                                <option value="wele_nzas">Wele-Nzas</option>
                                <option value="annobon">Annobón</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ciudad / Municipio *</label>
                            <input type="text" name="ciudad_municipio" class="form-control" placeholder="Ej. Malabo, Bata" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Foto Carnet</label>
                            <input type="file" name="foto_carnet" class="form-control" accept="image/*">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Contraseña *</label>
                            <input type="password" name="password" class="form-control" placeholder="Mínimo 8 caracteres" minlength="8" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check2-circle me-1"></i> Registrar Buscador
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar DataTable si hay datos
    const tabla = document.getElementById('tablaBuscadores');
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
                ]
            });
        }
    }
});
</script>

<?php include_once '../componentes/footer_admin.php'; ?>
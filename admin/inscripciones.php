<?php
session_start();

// ===== 1. VERIFICAR SESIÓN ADMINISTRATIVA =====
if (!isset($_SESSION['id_usuario']) || !in_array($_SESSION['rol'] ?? '', ['administrador', 'ministerio'])) {
    header('Location: ../login_admin.php');
    exit();
}

$titulo = 'Control de Desempleados - Panel de Administración';
include_once '../componentes/header_admin.php';
include_once '../conexion/conexion.php';

// ===== 2. CAPTURAR FILTROS DESDE GET =====
$filtro_busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';
$filtro_sede = isset($_GET['sede']) ? trim($_GET['sede']) : '';
$filtro_estado = isset($_GET['estado']) ? trim($_GET['estado']) : '';

// ===== 3. INICIALIZAR VARIABLES =====
$personas = [];
$total_inscritos = 0;
$total_seleccionados = 0;
$total_lista_espera = 0;
$total_bata = 0;
$total_malabo = 0;

try {
    // ===== CONTAR ESTADÍSTICAS (Específicas de personas_desempleadas) =====
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM personas_desempleadas");
    $total_inscritos = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM personas_desempleadas WHERE estado_seleccion = 'SELECCIONADO'");
    $total_seleccionados = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM personas_desempleadas WHERE estado_seleccion = 'LISTA_ESPERA'");
    $total_lista_espera = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM personas_desempleadas WHERE sede_formacion = 'BATA'");
    $total_bata = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM personas_desempleadas WHERE sede_formacion = 'MALABO'");
    $total_malabo = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // ===== CONSTRUIR CONSULTA FILTRADA =====
    $sql = "
        SELECT 
            id,
            nombre,
            apellido,
            telefono,
            email,
            dip_numero,
            dip_copia_pdf,
            provincia,
            distrito,
            sede_formacion,
            profesion_oficio,
            DATE_FORMAT(fecha_desempleo, '%d/%m/%Y') AS fecha_desempleo_formateada,
            foto_carnet_url,
            estado_seleccion,
            DATE_FORMAT(creado_en, '%d/%m/%Y %H:%i') AS fecha_registro
        FROM personas_desempleadas
        WHERE 1=1
    ";

    $params = [];

    // Aplicar filtro de búsqueda de texto (Busca por Nombre, Apellido, DIP o Profesión)
    if ($filtro_busqueda !== '') {
        $sql .= " AND (nombre LIKE ? OR apellido LIKE ? OR dip_numero LIKE ? OR profesion_oficio LIKE ? OR provincia LIKE ? OR distrito LIKE ?)";
        $buscar_val = "%$filtro_busqueda%";
        array_push($params, $buscar_val, $buscar_val, $buscar_val, $buscar_val, $buscar_val, $buscar_val);
    }

    // Aplicar filtro de sede de formación (BATA / MALABO)
    if ($filtro_sede !== '') {
        $sql .= " AND sede_formacion = ?";
        $params[] = $filtro_sede;
    }

    // Aplicar filtro de estado de selección
    if ($filtro_estado !== '') {
        $sql .= " AND estado_seleccion = ?";
        $params[] = $filtro_estado;
    }

    $sql .= " ORDER BY id DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $personas = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Error en personas_desempleadas: " . $e->getMessage());
    $personas = [];
}

// Construir enlace de impresión dinámico con los mismos filtros aplicados
$pdf_url = "../php/imprimir_desempleados.php?" . http_build_query([
    'busqueda' => $filtro_busqueda,
    'sede' => $filtro_sede,
    'estado' => $filtro_estado
]);
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

    .bg-warning-subtle {
        background: rgba(255, 193, 7, 0.12);
    }

    .text-warning {
        color: #ffc107 !important;
    }

    .bg-info-subtle {
        background: rgba(13, 202, 240, 0.12);
    }

    .text-info {
        color: #0dcaf0 !important;
    }

    .bg-danger-subtle {
        background: rgba(220, 53, 69, 0.08);
    }

    .text-danger {
        color: #dc3545 !important;
    }

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

    .form-control,
    .form-select {
        border-radius: var(--gov-radius-sm);
        border: 1.5px solid var(--gov-border);
        padding: 0.6rem 1rem;
        transition: all 0.3s;
    }

    .form-control:focus,
    .form-select:focus {
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

        <!-- ===== ESTADÍSTICAS ===== -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="stat-label">Total Inscritos</span>
                            <h3 class="stat-number"><?php echo number_format($total_inscritos); ?></h3>
                            <small class="text-muted">Solicitudes totales</small>
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
                            <span class="stat-label">Seleccionados</span>
                            <h3 class="stat-number text-success"><?php echo number_format($total_seleccionados); ?></h3>
                            <small class="text-success">Admitidos a cursos</small>
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
                            <span class="stat-label">En Lista de Espera</span>
                            <h3 class="stat-number text-warning"><?php echo number_format($total_lista_espera); ?></h3>
                            <small class="text-warning">Cupos pendientes</small>
                        </div>
                        <div class="stat-icon bg-warning-subtle text-warning">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="stat-label">Distribución Sede</span>
                            <h3 class="stat-number text-info" style="font-size: 1.4rem;">
                                Bata: <?php echo $total_bata; ?> | Malabo: <?php echo $total_malabo; ?>
                            </h3>
                            <small class="text-info">Preferencia de formación</small>
                        </div>
                        <div class="stat-icon bg-info-subtle text-info">
                            <i class="bi bi-geo-alt"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== TABLA DE USUARIOS ===== -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="custom-card p-3"
                    style="background: #f8f9fa; border-radius: 8px; border: 1px solid #e3e6f0;">
                    <form method="GET" action="" class="row g-2 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-secondary">Buscar Candidato</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                                <input type="text" name="busqueda" class="form-control"
                                    placeholder="Nombre, DIP, oficio, distrito..."
                                    value="<?php echo htmlspecialchars($filtro_busqueda); ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-secondary">Sede de Formación</label>
                            <select name="sede" class="form-select form-select-sm">
                                <option value="">Todas las sedes</option>
                                <option value="BATA" <?php echo $filtro_sede === 'BATA' ? 'selected' : ''; ?>>Bata
                                </option>
                                <option value="MALABO" <?php echo $filtro_sede === 'MALABO' ? 'selected' : ''; ?>>Malabo
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-secondary">Estado Selección</label>
                            <select name="estado" class="form-select form-select-sm">
                                <option value="">Todos los estados</option>
                                <option value="PENDIENTE" <?php echo $filtro_estado === 'PENDIENTE' ? 'selected' : ''; ?>>
                                    Pendiente</option>
                                <option value="SELECCIONADO" <?php echo $filtro_estado === 'SELECCIONADO' ? 'selected' : ''; ?>>Seleccionado</option>
                                <option value="LISTA_ESPERA" <?php echo $filtro_estado === 'LISTA_ESPERA' ? 'selected' : ''; ?>>Lista de Espera</option>
                                <option value="NO_SELECCIONADO" <?php echo $filtro_estado === 'NO_SELECCIONADO' ? 'selected' : ''; ?>>No Seleccionado</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex gap-1">
                            <button type="submit" class="btn btn-dark btn-sm flex-fill">
                                <i class="bi bi-funnel-fill"></i> Filtrar
                            </button>
                            <a href="?" class="btn btn-outline-secondary btn-sm" title="Limpiar filtros">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>




<div class="mensaje-alerta-container mb-3">
    <?php if (isset($_SESSION['exito'])): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm d-flex align-items-center" role="alert" id="alerta-autoclose">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <div>
                <?php 
                    echo $_SESSION['exito']; 
                    unset($_SESSION['exito']); // Limpiamos la variable para que no se repita al recargar
                ?>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm d-flex align-items-center" role="alert" id="alerta-autoclose">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
            <div>
                <?php 
                    echo $_SESSION['error']; 
                    unset($_SESSION['error']); // Limpiamos la variable
                ?>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
</div>




        <div class="row">
            <div class="col-12">
                <div class="custom-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold m-0">
                            <i class="bi bi-list-ul me-2"></i>
                            Solicitudes de Formación (Desempleados)
                            <span class="badge bg-light text-dark border ms-2"><?php echo count($personas); ?>
                                total</span>
                        </h5>
                        <div class="d-flex gap-2">
                            <a href="<?php echo $pdf_url; ?>" target="_blank" class="btn btn-outline-danger btn-sm">
                                <i class="bi bi-file-pdf me-1"></i> Exportar PDF
                            </a>
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#modalAgregarCandidato">
                                <i class="bi bi-plus-lg me-1"></i> Registrar Candidato
                            </button>
                        </div>
                    </div>

                    <div class="table-wrap">
                        <table class="table table-hover align-middle" id="tablaDesempleados">
                            <thead>
                                <tr>
                                    <th style="min-width: 60px;">Foto</th>
                                    <th style="min-width: 160px;">Candidato</th>
                                    <th style="min-width: 150px;">DIP (PDF) / Contacto</th>
                                    <th style="min-width: 130px;">Procedencia</th>
                                    <th style="min-width: 100px;">Sede Preferida</th>
                                    <th style="min-width: 110px;">Profesión / Oficio</th>
                                    <th style="min-width: 110px;">Estado Selección</th>
                                    <th style="min-width: 130px;" class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($personas)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                            No hay solicitudes registradas con los criterios aplicados.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($personas as $per): ?>
                                        <tr>
                                            <td>
                                                <?php if (!empty($per['foto_carnet_url'])): ?>
                                                    <img src="<?php echo htmlspecialchars($per['foto_carnet_url']); ?>" alt="Foto"
                                                        class="rounded"
                                                        style="width: 45px; height: 55px; object-fit: cover; border: 1px solid #ddd;">
                                                <?php else: ?>
                                                    <div class="bg-secondary-subtle d-flex align-items-center justify-content-center rounded text-secondary"
                                                        style="width: 45px; height: 55px; font-size: 1.2rem;">
                                                        <i class="bi bi-person"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>

                                            <td>
                                                <div class="fw-semibold" style="font-size: 0.9rem;">
                                                    <?php echo htmlspecialchars($per['nombre'] . ' ' . $per['apellido']); ?>
                                                </div>
                                                <small class="text-muted d-block" style="font-size: 0.75rem;">
                                                    <i
                                                        class="bi bi-telephone me-1"></i><?php echo htmlspecialchars($per['telefono']); ?>
                                                </small>
                                                <?php if (!empty($per['email'])): ?>
                                                    <small class="text-muted d-block" style="font-size: 0.7rem;">
                                                        <i
                                                            class="bi bi-envelope me-1"></i><?php echo htmlspecialchars($per['email']); ?>
                                                    </small>
                                                <?php endif; ?>
                                            </td>

                                            <td style="font-size: 0.85rem;">
                                                <div class="small fw-semibold text-dark">
                                                    <i class="bi bi-card-heading me-1"></i>
                                                    <?php echo htmlspecialchars($per['dip_numero']); ?>
                                                </div>
                                                <a href="<?php echo htmlspecialchars($per['dip_copia_pdf']); ?>" target="_blank"
                                                    class="btn btn-link btn-sm p-0 text-danger" style="font-size: 0.75rem;"
                                                    title="Ver DIP Escaneado en PDF">
                                                    <i class="bi bi-filetype-pdf me-1"></i>Ver DIP (PDF)
                                                </a>
                                            </td>

                                            <td style="font-size: 0.8rem;">
                                                <div class="fw-bold text-secondary">
                                                    <?php echo htmlspecialchars($per['provincia']); ?>
                                                </div>
                                                <small
                                                    class="text-muted"><?php echo htmlspecialchars($per['distrito']); ?></small>
                                            </td>

                                            <td>
                                                <span
                                                    class="badge <?php echo $per['sede_formacion'] === 'BATA' ? 'bg-primary' : 'bg-dark'; ?>">
                                                    <?php echo $per['sede_formacion']; ?>
                                                </span>
                                            </td>

                                            <td style="font-size: 0.85rem;">
                                                <div class="fw-semibold text-dark">
                                                    <?php echo htmlspecialchars($per['profesion_oficio'] ?? 'Sin especificar'); ?>
                                                </div>
                                                <small class="text-muted" style="font-size: 0.75rem;">Desempleado desde:
                                                    <?php echo $per['fecha_desempleo_formateada'] ?? 'N/D'; ?></small>
                                            </td>

                                            <td>
                                                <?php
                                                $clase_badge = 'bg-secondary';
                                                if ($per['estado_seleccion'] === 'SELECCIONADO')
                                                    $clase_badge = 'bg-success';
                                                elseif ($per['estado_seleccion'] === 'LISTA_ESPERA')
                                                    $clase_badge = 'bg-warning text-dark';
                                                elseif ($per['estado_seleccion'] === 'NO_SELECCIONADO')
                                                    $clase_badge = 'bg-danger';
                                                ?>
                                                <span class="badge <?php echo $clase_badge; ?>">
                                                    <?php echo str_replace('_', ' ', $per['estado_seleccion']); ?>
                                                </span>
                                            </td>

                                            <td class="text-end">
                                                <div class="d-flex gap-1 justify-content-end">
                                                    <button class="btn btn-outline-secondary btn-sm btn-editar-candidato"
                                                        data-id="<?php echo $per['id']; ?>"
                                                        data-nombre="<?php echo htmlspecialchars($per['nombre']); ?>"
                                                        data-apellido="<?php echo htmlspecialchars($per['apellido']); ?>"
                                                        data-telefono="<?php echo htmlspecialchars($per['telefono']); ?>"
                                                        data-email="<?php echo htmlspecialchars($per['email']); ?>"
                                                        data-dip="<?php echo htmlspecialchars($per['dip_numero']); ?>"
                                                        data-provincia="<?php echo htmlspecialchars($per['provincia']); ?>"
                                                        data-distrito="<?php echo htmlspecialchars($per['distrito']); ?>"
                                                        data-sede="<?php echo htmlspecialchars($per['sede_formacion']); ?>"
                                                        data-profesion="<?php echo htmlspecialchars($per['profesion_oficio']); ?>"
                                                        data-estado="<?php echo htmlspecialchars($per['estado_seleccion']); ?>"
                                                        data-bs-toggle="modal" data-bs-target="#modalEditarCandidato"
                                                        title="Cambiar estado / Editar">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>

                                                    <button
                                                        class="btn btn-outline-secondary btn-sm text-danger btn-eliminar-candidato"
                                                        data-id="<?php echo $per['id']; ?>"
                                                        data-nombre="<?php echo htmlspecialchars($per['nombre'] . ' ' . $per['apellido']); ?>"
                                                        title="Eliminar registro del Ministerio">
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
<div class="modal fade" id="modalAgregarCandidato" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-person-plus-fill me-2"></i>Registrar Candidato a Formación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <form action="../php/procesar_desempleados.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombre(s) *</label>
                            <input type="text" name="nombre" class="form-control" placeholder="Ej: Salvador" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Apellidos *</label>
                            <input type="text" name="apellido" class="form-control" placeholder="Ej: Mete Bijeri" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Número de Teléfono *</label>
                            <input type="tel" name="telefono" class="form-control" placeholder="Ej: +240 222 123 456" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Correo Electrónico <span class="text-muted fw-normal">(Opcional)</span></label>
                            <input type="email" name="email" class="form-control" placeholder="ejemplo@dominio.com">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Número de DIP *</label>
                            <input type="text" name="dip_numero" class="form-control font-monospace" placeholder="Ej: 0948372-EG" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">DIP Escaneado (Solo PDF) *</label>
                            <input type="file" name="dip_copia_pdf" class="form-control" accept=".pdf" required>
                            <div class="form-text text-muted" style="font-size: 0.75rem;">Debe estar en formato PDF.</div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Provincia *</label>
                            <select name="provincia" id="selectProvincia" class="form-select" required onchange="cargarDistritos()">
                                <option value="" disabled selected>-- Seleccione Provincia --</option>
                                <option value="Bioko Norte">Bioko Norte</option>
                                <option value="Bioko Sur">Bioko Sur</option>
                                <option value="Litoral">Litoral</option>
                                <option value="Centro Sur">Centro Sur</option>
                                <option value="Kié-Ntem">Kié-Ntem</option>
                                <option value="Wele-Nzas">Wele-Nzas</option>
                                <option value="Annobón">Annobón</option>
                                <option value="Djibloho">Djibloho</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Distrito *</label>
                            <select name="distrito" id="selectDistrito" class="form-select" required disabled>
                                <option value="" disabled selected>-- Seleccione primero Provincia --</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Sede de Formación *</label>
                            <select name="sede_formacion" class="form-select" required>
                                <option value="" disabled selected>-- Seleccione Ciudad --</option>
                                <option value="BATA">Bata</option>
                                <option value="MALABO">Malabo</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Última Profesión u Oficio</label>
                            <input type="text" name="profesion_oficio" class="form-control" placeholder="Ej: Electricista, Oficinista, etc.">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Fecha desde que está Desempleado</label>
                            <input type="date" name="fecha_desempleo" class="form-control">
                        </div>
                        
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Foto Tamaño Carnet <span class="text-muted fw-normal">(Opcional)</span></label>
                            <input type="file" name="foto_carnet" id="inputFotoCarnet" class="form-control" accept="image/*" onchange="previsualizarFoto(event)">
                            <div class="form-text text-muted" style="font-size: 0.75rem;">Formatos válidos: JPG, PNG, JPEG.</div>
                        </div>
                        <div class="col-md-4 d-flex flex-column align-items-center justify-content-center">
                            <label class="form-label fw-semibold mb-1 small text-secondary">Vista Previa</label>
                            <div id="contenedorPreview" class="bg-light border d-flex align-items-center justify-content-center rounded text-muted" 
                                 style="width: 80px; height: 100px; overflow: hidden; position: relative;">
                                <i id="iconoPreview" class="bi bi-person fs-1"></i>
                                <img id="imagenPreview" src="" alt="Vista previa" class="d-none w-100 h-100" style="object-fit: cover;">
                            </div>
                        </div>

                    </div>
                </div>
                
                <div class="modal-footer d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check2-circle me-1"></i> Guardar Solicitante
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>



<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

<script>
// 1. Script para previsualizar la foto carnet inmediatamente al seleccionarla
function previsualizarFoto(event) {
    const input = event.target;
    const reader = new FileReader();
    
    reader.onload = function(){
        const imagenPreview = document.getElementById('imagenPreview');
        const iconoPreview = document.getElementById('iconoPreview');
        
        imagenPreview.src = reader.result;
        imagenPreview.classList.remove('d-none'); // Muestra la foto
        iconoPreview.classList.add('d-none'); // Oculta el icono por defecto
    };
    
    if(input.files && input.files[0]) {
        reader.readAsDataURL(input.files[0]);
    } else {
        // En caso de deseleccionar la imagen, restauramos el estado por defecto
        document.getElementById('imagenPreview').classList.add('d-none');
        document.getElementById('iconoPreview').classList.remove('d-none');
    }
}

// 2. Diccionario de Provincias y sus correspondientes Distritos (Guinea Ecuatorial)
const distritosPorProvincia = {
    "Bioko Norte": ["Malabo", "Baney"],
    "Bioko Sur": ["Luba", "Riaba"],
    "Litoral": ["Bata", "Mbini", "Cogo"],
    "Centro Sur": ["Evinayong", "Akurenam", "Niefang"],
    "Kié-Ntem": ["Ebebiyín", "Micomeseng", "Nsok-Nsomo"],
    "Wele-Nzas": ["Mongomo", "Añisok", "Nsork", "Mengomeyén"],
    "Annobón": ["San Antonio de Palé"],
    "Djibloho": ["Ciudad de la Paz"]
};

// 3. Script para cargar dinámicamente los distritos en base a la provincia elegida
function cargarDistritos() {
    const provinciaSeleccionada = document.getElementById('selectProvincia').value;
    const selectDistrito = document.getElementById('selectDistrito');
    
    // Limpiamos los distritos anteriores
    selectDistrito.innerHTML = '<option value="" disabled selected>-- Seleccione Distrito --</option>';
    
    if (provinciaSeleccionada && distritosPorProvincia[provinciaSeleccionada]) {
        selectDistrito.removeAttribute('disabled');
        
        // Poblamos el select con los distritos correctos
        distritosPorProvincia[provinciaSeleccionada].forEach(distrito => {
            const option = document.createElement('option');
            option.value = distrito;
            option.textContent = distrito;
            selectDistrito.appendChild(option);
        });
    } else {
        selectDistrito.setAttribute('disabled', 'true');
    }
}
</script>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        // ===== INICIALIZAR DATATABLE =====
        const tabla = document.getElementById('tablaDesempleados');
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
        document.querySelector('#modalAgregarUsuario form')?.addEventListener('submit', function (e) {
            const pass = this.querySelector('input[name="password"]').value;
            const passConfirm = this.querySelector('input[name="password_confirm"]').value;
            if (pass !== passConfirm) {
                e.preventDefault();
                alert('Las contraseñas no coinciden.');
            }
        });







    });
</script>


<script>
document.addEventListener("DOMContentLoaded", function() {
    // Buscar la alerta de autocierre
    const alerta = document.getElementById("alerta-autoclose");
    
    if (alerta) {
        // Esperar 5000ms (5 segundos) antes de iniciar la animación de cierre
        setTimeout(function() {
            // Utilizamos la API de Bootstrap para cerrar la alerta de manera fluida
            const bsAlert = new bootstrap.Alert(alerta);
            bsAlert.close();
        }, 5000);
    }
});
</script>

<?php include_once '../componentes/footer_admin.php'; ?>
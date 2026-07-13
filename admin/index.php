<?php
session_start();

// ===== VERIFICAR SESIÓN =====
if (!isset($_SESSION['id_usuario']) || !in_array($_SESSION['rol'] ?? '', ['administrador', 'ministerio'])) {
    header('Location: ../login_admin.php');
    exit();
}

$titulo = 'Panel de Administración - Portal de Empleo';
include_once '../componentes/header_admin.php';

// ===== CONEXIÓN A BD =====
try {
    include_once '../conexion/conexion.php';
} catch (Exception $e) {
    die("Error de conexión: " . $e->getMessage());
}

// ===== INICIALIZAR TODAS LAS VARIABLES =====
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

// ============================================================
//  CONSULTAS - CADA UNA CON SU PROPIO TRY-CATCH
// ============================================================
try {
    // TOTAL DESEMPLEADOS
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM buscadores_empleo");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_desempleados = $result['total'] ?? 0;

    // TOTAL EMPRESAS
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM empleadores");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_empresas = $result['total'] ?? 0;

    // CURSOS ACTIVOS
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM cursos WHERE estado = 'activo'");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $total_cursos_activos = $result['total'] ?? 0;
    } catch (PDOException $e) {
        $total_cursos_activos = 0;
    }

    // INTERMEDIACIONES PENDIENTES
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM notificaciones_intermediacion WHERE estado_ministerio = 'pendiente'");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $intermediaciones_pendientes = $result['total'] ?? 0;
    } catch (PDOException $e) {
        $intermediaciones_pendientes = 0;
    }

    // DATOS GRÁFICO CIRCULAR
    try {
        $stmt = $pdo->query("SELECT estado_laboral, COUNT(*) as total FROM buscadores_empleo GROUP BY estado_laboral");
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $data_circular = [0, 0, 0];
        foreach ($resultados as $row) {
            if ($row['estado_laboral'] == 'desempleado') $data_circular[0] = (int)$row['total'];
            elseif ($row['estado_laboral'] == 'contratado') $data_circular[1] = (int)$row['total'];
            elseif ($row['estado_laboral'] == 'suspendido') $data_circular[2] = (int)$row['total'];
        }
    } catch (PDOException $e) {
        $data_circular = [0, 0, 0];
    }

    // DATOS GRÁFICO BARRAS
    try {
        $stmt = $pdo->query("SELECT estado_ministerio, COUNT(*) as total FROM notificaciones_intermediacion GROUP BY estado_ministerio");
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $data_barras = [0, 0, 0, 0];
        foreach ($resultados as $row) {
            if ($row['estado_ministerio'] == 'pendiente') $data_barras[0] = (int)$row['total'];
            elseif ($row['estado_ministerio'] == 'en_revision') $data_barras[1] = (int)$row['total'];
            elseif ($row['estado_ministerio'] == 'aprobado') $data_barras[2] = (int)$row['total'];
            elseif ($row['estado_ministerio'] == 'rechazado') $data_barras[3] = (int)$row['total'];
        }
    } catch (PDOException $e) {
        $data_barras = [0, 0, 0, 0];
    }

    // BUSCADORES
    try {
        $stmt = $pdo->query("
            SELECT 
                u.id, u.numero_expediente, u.nombre, u.apellidos, u.documento_identidad,
                b.provincia, b.ciudad_municipio, b.estado_laboral,
                d.copia_dip, d.cv
            FROM usuarios u
            JOIN buscadores_empleo b ON u.id = b.usuario_id
            LEFT JOIN documentos d ON u.id = d.usuario_id
            WHERE u.rol = 'buscador'
            ORDER BY u.fecha_registro DESC
            LIMIT 10
        ");
        $buscadores = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $buscadores = [];
    }

    // CURSOS
    try {
        $stmt = $pdo->query("
            SELECT 
                c.*,
                ef.nombre_entidad as entidad_mostrar,
                ef.siglas
            FROM cursos c
            JOIN entidades_formadoras ef ON c.entidad_id = ef.id
            WHERE c.estado IN ('activo', 'proximamente')
            ORDER BY c.fecha_creacion DESC
            LIMIT 10
        ");
        $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $cursos = [];
    }

    // ENTIDADES FORMADORAS
    try {
        $stmt = $pdo->query("
            SELECT id, nombre_entidad, siglas 
            FROM entidades_formadoras 
            WHERE estado = 'activo'
            ORDER BY nombre_entidad
        ");
        $entidades = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $entidades = [];
    }

    // NOTIFICACIONES - CON LAS COLUMNAS QUE EXISTAN
    try {
        // Primero verificar qué columnas existen
        $stmt = $pdo->query("DESCRIBE notificaciones_intermediacion");
        $columnas = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Construir consulta dinámica
        $select = "ni.id, ni.codigo_seguimiento, ni.estado_ministerio, ni.fecha_creacion";
        if (in_array('motivo_empresa', $columnas)) {
            $select .= ", ni.motivo_empresa";
        }
        if (in_array('mensaje_motivo', $columnas)) {
            $select .= ", ni.mensaje_motivo";
        }
        if (in_array('origen', $columnas)) {
            $select .= ", ni.origen";
        }
        
        $query = "
            SELECT $select,
                u.nombre as buscador_nombre,
                u.apellidos as buscador_apellidos,
                u.numero_expediente as buscador_expediente,
                b.estado_laboral as buscador_estado,
                b.provincia as buscador_provincia,
                e.nombre_empresa,
                e.rnc_ruc,
                o.titulo_puesto,
                o.salario_ofrecido
            FROM notificaciones_intermediacion ni
            JOIN buscadores_empleo b ON ni.buscador_id = b.id
            JOIN usuarios u ON b.usuario_id = u.id
            JOIN empleadores e ON ni.empleador_id = e.id
            LEFT JOIN ofertas_empleo o ON ni.oferta_id = o.id
            ORDER BY ni.fecha_creacion DESC
            LIMIT 20
        ";
        $stmt = $pdo->query($query);
        $notificaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $notificaciones = [];
        error_log("Error en notificaciones: " . $e->getMessage());
    }

} catch (PDOException $e) {
    error_log("Error general en admin: " . $e->getMessage());
    // No mostrar error al usuario, solo usar valores por defecto
}
?>

<!-- ===== CONTENIDO HTML ===== -->
<div class="d-flex" id="wrapper">

    <?php include_once '../componentes/menu_admin.php'; ?>

    <div class="container-fluid p-4">

        <!-- ===== ESTADÍSTICAS ===== -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card stat-card h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted fs-7 fw-medium">Desempleados Registrados</span>
                            <h3 class="fw-bold my-1"><?php echo number_format($total_desempleados); ?></h3>
                            <small class="text-success fw-semibold"><i class="bi bi-person-check"></i> Activos en sistema</small>
                        </div>
                        <div class="stat-icon bg-primary-subtle text-primary p-3 rounded-circle">
                            <i class="bi bi-people fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card stat-card h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted fs-7 fw-medium">Empresas Activas</span>
                            <h3 class="fw-bold my-1"><?php echo number_format($total_empresas); ?></h3>
                            <small class="text-muted fw-semibold">Región Bioko / Continental</small>
                        </div>
                        <div class="stat-icon bg-success-subtle text-success p-3 rounded-circle">
                            <i class="bi bi-building fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card stat-card h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted fs-7 fw-medium">Intermediaciones Pendientes</span>
                            <h3 class="fw-bold my-1"><?php echo number_format($intermediaciones_pendientes); ?></h3>
                            <small class="text-warning fw-semibold"><i class="bi bi-exclamation-circle"></i> Requiere revisión</small>
                        </div>
                        <div class="stat-icon bg-warning-subtle text-warning p-3 rounded-circle">
                            <i class="bi bi-shield-exclamation fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card stat-card h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted fs-7 fw-medium">Cursos en Oferta</span>
                            <h3 class="fw-bold my-1"><?php echo number_format($total_cursos_activos); ?></h3>
                            <small class="text-info fw-semibold">Entidades Formadoras</small>
                        </div>
                        <div class="stat-icon bg-info-subtle text-info p-3 rounded-circle">
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
                        <small class="text-muted">Proporción de ciudadanos</small>
                    </div>
                    <div class="card-body d-flex justify-content-center align-items-center" style="min-height: 250px;">
                        <canvas id="chartEstadoLaboral"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-7">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold">Reporte de Barras: Intermediaciones</h6>
                        <small class="text-muted">Estado de tramitación de expedientes</small>
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
                    <small class="text-muted">Control gubernamental de vinculación</small>
                </div>
                <button class="btn btn-outline-primary btn-sm rounded-pill"><i class="bi bi-download me-1"></i> Exportar</button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Código</th>
                            <th>Candidato</th>
                            <th>Empresa</th>
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
                                            data-motivo="<?php echo htmlspecialchars($n['motivo_empresa'] ?? $n['mensaje_motivo'] ?? 'Sin observaciones.'); ?>">
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
                        <a href="#" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i> Registrar</a>
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
                                                    <a href="<?php echo htmlspecialchars($b['copia_dip']); ?>" target="_blank" class="btn btn-sm btn-outline-secondary" title="DIP"><i class="bi bi-card-heading"></i></a>
                                                <?php endif; ?>
                                                <?php if (!empty($b['cv'])): ?>
                                                    <a href="<?php echo htmlspecialchars($b['cv']); ?>" target="_blank" class="btn btn-sm btn-outline-secondary" title="CV"><i class="bi bi-file-earmark-person"></i></a>
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
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
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
                    label: 'Expedientes',
                    data: <?php echo json_encode($data_barras); ?>,
                    backgroundColor: ['#ffc107', '#0dcaf0', '#198754', '#dc3545'],
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } },
                plugins: { legend: { display: false } }
            }
        });
    }

    // MODAL DETALLE
    document.querySelectorAll('.btn-ver-detalle').forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('modal-codigo').textContent = this.dataset.codigo || 'N/A';
            document.getElementById('modal-candidato').textContent = this.dataset.candidato || 'N/A';
            document.getElementById('modal-expediente').textContent = this.dataset.expediente || 'N/A';
            document.getElementById('modal-provincia').textContent = this.dataset.provincia || 'N/A';
            document.getElementById('modal-empresa').textContent = this.dataset.empresa || 'N/A';
            document.getElementById('modal-puesto').textContent = this.dataset.puesto || 'N/A';
            document.getElementById('modal-ruc').textContent = this.dataset.ruc || 'N/A';
            document.getElementById('modal-salario').textContent = this.dataset.salario || 'N/A';
            document.getElementById('modal-motivo').textContent = this.dataset.motivo || 'Sin observaciones.';
            
            const estadoEl = document.getElementById('modal-estado');
            const estado = this.dataset.estado || 'desempleado';
            const badgeMap = { 'desempleado': 'bg-warning', 'contratado': 'bg-success', 'suspendido': 'bg-danger' };
            estadoEl.className = 'badge ' + (badgeMap[estado] || 'bg-secondary');
            estadoEl.textContent = estado.charAt(0).toUpperCase() + estado.slice(1);
        });
    });
});
</script>

<?php include_once '../componentes/footer_admin.php'; ?>
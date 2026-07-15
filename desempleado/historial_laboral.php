<?php
session_start();

// ===== VERIFICAR SESIÓN =====
if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../login_desempleados.php');
    exit();
}

$titulo = 'Mi Historial Laboral - Portal de Empleo';
include '../componentes/header_desempleado.php';
include '../conexion/conexion.php';

$id_usuario = $_SESSION['id_usuario'];
$nombre_completo = $_SESSION['nombre_completo'] ?? '';

// ===== OBTENER DATOS DEL USUARIO =====
try {
    // Verificar buscadores_empleo
    $stmt = $pdo->prepare("SELECT * FROM buscadores_empleo WHERE usuario_id = ?");
    $stmt->execute([$id_usuario]);
    $buscador = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar documentos
    $stmt_doc = $pdo->prepare("SELECT id FROM documentos WHERE usuario_id = ?");
    $stmt_doc->execute([$id_usuario]);
    $documentos = $stmt_doc->fetch();
    
    $perfil_completo = ($buscador && $documentos);

    // ============================================================
    // 1. OBTENER POSTULACIONES REALES DESDE BBDD
    // ============================================================
    $postulaciones = [];
    if ($buscador) {
        $stmt = $pdo->prepare("
            SELECT 
                p.id,
                p.oferta_id,
                p.fecha_postulacion,
                p.estado AS estado_postulacion,
                p.mensaje_presentacion,
                o.titulo_puesto,
                e.nombre_empresa,
                o.descripcion AS oferta_descripcion
            FROM postulaciones p
            JOIN ofertas_empleo o ON p.oferta_id = o.id
            JOIN empleadores e ON o.empleador_id = e.id
            WHERE p.buscador_id = ?
            ORDER BY p.fecha_postulacion DESC
        ");
        $stmt->execute([$buscador['id']]);
        $postulaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ============================================================
    // 2. OBTENER EXPERIENCIA LABORAL REAL DESDE BBDD
    // ============================================================
    $experiencia_laboral = [];
    if ($buscador) {
        $stmt = $pdo->prepare("
            SELECT 
                id,
                empresa,
                puesto,
                fecha_inicio,
                fecha_fin,
                descripcion
            FROM experiencia_laboral 
            WHERE usuario_id = ? 
            ORDER BY fecha_inicio DESC
        ");
        $stmt->execute([$id_usuario]);
        $experiencia_laboral = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ============================================================
    // 3. UNIFICAR AMBOS TIPOS DE REGISTROS EN UN SOLO ARRAY
    // ============================================================
    $historial = [];

    // Agregar postulaciones
    foreach ($postulaciones as $p) {
        $historial[] = [
            'id' => 'p_' . $p['id'], // prefijo para distinguir
            'tipo' => 'postulacion',
            'fecha_inicio' => date('d/m/Y', strtotime($p['fecha_postulacion'])),
            'fecha_fin' => '—',
            'puesto' => $p['titulo_puesto'],
            'empresa' => $p['nombre_empresa'],
            'descripcion' => $p['mensaje_presentacion'] ?? 'Postulación a oferta laboral.',
            'estado' => $p['estado_postulacion'], // pendiente, revisado, interesado, rechazado
            'extra' => [
                'oferta_id' => $p['oferta_id'],
                'fecha_postulacion' => $p['fecha_postulacion']
            ]
        ];
    }

    // Agregar experiencia laboral
    foreach ($experiencia_laboral as $exp) {
        // Determinar estado (activo si fecha_fin es NULL, sino finalizado)
        $estado_exp = ($exp['fecha_fin'] === null) ? 'activo' : 'finalizado';
        $historial[] = [
            'id' => 'e_' . $exp['id'],
            'tipo' => 'experiencia',
            'fecha_inicio' => date('d/m/Y', strtotime($exp['fecha_inicio'])),
            'fecha_fin' => $exp['fecha_fin'] ? date('d/m/Y', strtotime($exp['fecha_fin'])) : 'Actualidad',
            'puesto' => $exp['puesto'],
            'empresa' => $exp['empresa'],
            'descripcion' => $exp['descripcion'],
            'estado' => $estado_exp, // activo o finalizado
            'extra' => [
                'exp_id' => $exp['id']
            ]
        ];
    }

    // Ordenar por fecha de inicio (más reciente primero)
    usort($historial, function($a, $b) {
        $dateA = DateTime::createFromFormat('d/m/Y', $a['fecha_inicio']);
        $dateB = DateTime::createFromFormat('d/m/Y', $b['fecha_inicio']);
        if ($a['fecha_inicio'] === '—') return 1;
        if ($b['fecha_inicio'] === '—') return -1;
        return $dateB <=> $dateA;
    });

    // ============================================================
    // 4. ESTADÍSTICAS
    // ============================================================
    $total_registros = count($historial);
    
    // Postulaciones en proceso (pendiente, revisado, interesado)
    $en_proceso = count(array_filter($historial, function($item) {
        return $item['tipo'] === 'postulacion' && in_array($item['estado'], ['pendiente', 'revisado', 'interesado']);
    }));
    
    // Preseleccionados (interesado)
    $preseleccionados = count(array_filter($historial, function($item) {
        return $item['tipo'] === 'postulacion' && $item['estado'] === 'interesado';
    }));
    
    // Finalizados (rechazados + experiencias finalizadas + experiencias activas contadas como finalizadas? Mejor contamos finalizados como rechazado + experiencias finalizadas)
    $finalizados = count(array_filter($historial, function($item) {
        return ($item['tipo'] === 'postulacion' && $item['estado'] === 'rechazado') ||
               ($item['tipo'] === 'experiencia' && $item['estado'] === 'finalizado');
    }));

} catch (PDOException $e) {
    error_log("Error en historial laboral: " . $e->getMessage());
    $buscador = null;
    $perfil_completo = false;
    $historial = [];
    $total_registros = 0;
    $en_proceso = 0;
    $preseleccionados = 0;
    $finalizados = 0;
}

// ===== INCLUIR MENÚ =====
include '../componentes/menu_desempleado.php';
?>

<style>
    /* ===== ESTILOS (los mismos que antes, sin cambios) ===== */
    .btn-gov {
        background-color: var(--gov-blue);
        color: #ffffff;
        border: none;
        border-radius: var(--gov-radius-sm);
        padding: 0.6rem 1.2rem;
        font-weight: 500;
        transition: background-color 0.2s ease, transform 0.15s;
    }
    .btn-gov:hover {
        background-color: var(--gov-blue-light);
        color: #ffffff;
        transform: translateY(-1px);
    }
    .btn-outline-secondary {
        border: 2px solid var(--gov-border);
        color: var(--gov-dark);
        background: transparent;
        border-radius: var(--gov-radius-sm);
        font-weight: 500;
        transition: all 0.25s;
    }
    .btn-outline-secondary:hover {
        background: var(--gov-blue);
        color: white;
        border-color: var(--gov-blue);
    }
    .btn-ver {
        border: 2px solid var(--gov-blue);
        color: var(--gov-blue);
        background: transparent;
        border-radius: var(--gov-radius-sm);
        font-weight: 500;
        transition: all 0.25s;
    }
    .btn-ver :hover {
        background: var(--gov-blue);
        color: white;
        border-color: var(--gov-blue);
    }

    .form-control-search {
        border-radius: var(--gov-radius-sm) !important;
        border: 1.5px solid var(--gov-border);
        padding: 0.7rem 1.2rem;
        font-size: 0.95rem;
        transition: border-color 0.3s, box-shadow 0.3s;
        width: 100%;
    }
    .form-control-search:focus {
        border-color: var(--gov-blue);
        box-shadow: 0 0 0 4px rgba(11, 58, 96, 0.12);
    }
    .form-select-custom {
        border-radius: var(--gov-radius-sm);
        border: 1.5px solid var(--gov-border);
        padding: 0.7rem 1.2rem;
        font-size: 0.95rem;
        transition: border-color 0.3s;
    }
    .form-select-custom:focus {
        border-color: var(--gov-gold);
        box-shadow: 0 0 0 4px rgba(201, 168, 76, 0.12);
    }

    .table-custom {
        border-radius: var(--gov-radius);
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.02);
    }
    .table-custom thead {
        background-color: #f8fafc;
    }
    .table-custom th {
        font-weight: 600;
        color: var(--gov-dark);
        border-bottom: 2px solid var(--gov-border);
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        padding: 0.8rem 1rem;
    }
    .table-custom td {
        padding: 0.8rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
    }
    .table-custom tbody tr:hover {
        background-color: #f8fafc;
    }

    .badge-estado {
        font-weight: 500;
        padding: 0.35rem 0.8rem;
        border-radius: 20px;
        font-size: 0.75rem;
        display: inline-block;
    }
    .badge-estado.pendiente { background: #fff3cd; color: #856404; }
    .badge-estado.revisado { background: #cce5ff; color: #004085; }
    .badge-estado.interesado { background: #d4edda; color: #155724; }
    .badge-estado.rechazado { background: #f8d7da; color: #721c24; }
    .badge-estado.activo { background: #d4edda; color: #155724; }
    .badge-estado.finalizado { background: #e2e3e5; color: #383d41; }

    .stat-mini {
        background: #ffffff;
        border: 1px solid var(--gov-border);
        border-radius: var(--gov-radius-sm);
        padding: 0.8rem 1rem;
        display: flex;
        align-items: center;
        gap: 0.8rem;
        transition: all 0.2s;
    }
    .stat-mini:hover {
        border-color: var(--gov-gold);
        background: #fafbfc;
    }
    .stat-mini .stat-icon {
        font-size: 1.5rem;
        color: var(--gov-blue);
        opacity: 0.6;
        width: 2rem;
        text-align: center;
    }
    .stat-mini .stat-number {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--gov-dark);
        line-height: 1;
    }
    .stat-mini .stat-label {
        font-size: 0.75rem;
        color: #6b7a8a;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .pagination-custom .page-link {
        border-radius: var(--gov-radius-sm) !important;
        margin: 0 4px;
        border: 1px solid var(--gov-border);
        color: var(--gov-blue);
        transition: all 0.2s;
    }
    .pagination-custom .page-link:hover {
        background: var(--gov-gold);
        color: white;
        border-color: var(--gov-gold);
    }
    .pagination-custom .page-item.active .page-link {
        background: var(--gov-blue);
        border-color: var(--gov-blue);
        color: white;
    }

    .modal-content {
        border-radius: var(--gov-radius);
        border: none;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        overflow: hidden;
    }
    .modal-header {
        border-bottom: 2px solid var(--gov-gold-light);
        background: rgba(255, 255, 255, 0.95);
        padding: 1.5rem 2rem;
    }
    .modal-header .modal-title {
        font-weight: 700;
        color: var(--gov-blue);
    }
    .modal-body {
        padding: 2rem;
        background: #FAFBFC;
    }
    .modal-body .detail-row {
        display: flex;
        align-items: baseline;
        padding: 0.6rem 0;
        border-bottom: 1px solid rgba(0, 0, 0, 0.04);
    }
    .modal-body .detail-row:last-child { border-bottom: none; }
    .modal-body .detail-label {
        font-weight: 600;
        color: var(--gov-dark);
        width: 130px;
        flex-shrink: 0;
        font-size: 0.9rem;
    }
    .modal-body .detail-value {
        color: #2D3A4A;
        font-size: 0.95rem;
    }
    .modal-body .detail-value i {
        color: var(--gov-gold);
        margin-right: 6px;
        width: 1.2rem;
    }
    .modal-body .descripcion-text {
        background: #f1f5f9;
        padding: 1rem;
        border-radius: var(--gov-radius-sm);
        font-size: 0.95rem;
        color: var(--gov-dark);
        border-left: 3px solid var(--gov-blue);
        margin-top: 0.5rem;
    }
    .modal-footer {
        border-top: 1px solid rgba(0, 0, 0, 0.05);
        background: white;
        padding: 1.2rem 2rem;
    }

    footer {
        background: rgba(255, 255, 255, 0.9) !important;
        backdrop-filter: blur(8px);
        border-top: 1px solid rgba(201, 168, 76, 0.15);
    }

    @media (max-width: 576px) {
        .dashboard-card { padding: 1.5rem !important; }
        .stat-mini { flex-wrap: wrap; }
        .modal-body { padding: 1.2rem; }
        .modal-body .detail-label {
            width: 100%;
            margin-bottom: 2px;
        }
        .modal-body .detail-row { flex-wrap: wrap; }
    }
</style>

<body>
    <canvas id="canvas-background"></canvas>
    <div class="main-wrapper">

        <main class="container py-5 flex-grow-1">

            <!-- ALERTA EXPEDIENTE INCOMPLETO -->
            <?php if (!$perfil_completo): ?>
                <div id="incompleteProfileBanner" class="alert alert-profile-incomplete p-4 mb-4">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                        <div class="d-flex align-items-start gap-3">
                            <i class="bi bi-exclamation-octagon-fill text-danger fs-3 mt-1"></i>
                            <div>
                                <h4 class="fw-bold m-0 h5 text-dark">Expediente Digital Incompleto</h4>
                                <p class="m-0 text-muted small mt-1">Conforme a la normativa del Sistema Nacional de Empleo, debe completar su perfil adjuntando su Documento de Identidad Personal (DIP) y su Currículum Vitae para acceder a la visualización de ofertas laborales vigentes.</p>
                            </div>
                        </div>
                        <a href="completar_perfil.php" class="btn btn-danger btn-sm text-nowrap px-4 py-2 rounded-pill fw-bold shadow-sm">
                            <i class="bi bi-pencil-square me-1"></i> Completar Perfil Ahora
                        </a>
                    </div>
                </div>

                <div class="registro-pendiente mb-4">
                    <div class="icono"><i class="bi bi-info-circle-fill"></i></div>
                    <div class="texto">
                        <strong>¡Atención!</strong> Para acceder a todas las funcionalidades del portal (postulaciones, ofertas personalizadas, cursos, etc.) debe completar su registro en el <a href="completar_perfil.php" class="btn-link">Sistema Nacional de Empleo</a>.
                    </div>
                    <a href="completar_perfil.php" class="btn btn-gov btn-sm rounded-pill px-4">Completar registro</a>
                </div>
            <?php endif; ?>

            <!-- ===== CONTENIDO PRINCIPAL ===== -->
            <div class="row g-4">

                <!-- TÍTULO DE LA PÁGINA -->
                <div class="col-12">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <i class="bi bi-file-earmark-arrow-down fs-1" style="color: var(--gov-blue);"></i>
                        <div>
                            <h1 class="h3 fw-bold m-0" style="color: var(--gov-dark);"> Mi Historial Laboral</h1>
                            <p class="text-muted m-0">Consulta todas tus postulaciones y experiencias laborales registradas.</p>
                        </div>
                    </div>
                    <hr class="mb-4">
                </div>

                <!-- ESTADÍSTICAS RÁPIDAS -->
                <div class="col-12">
                    <div class="row g-3 mb-3">
                        <div class="col-6 col-md-3">
                            <div class="stat-mini">
                                <div class="stat-icon"><i class="bi bi-send"></i></div>
                                <div>
                                    <div class="stat-number"><?php echo $total_registros; ?></div>
                                    <div class="stat-label">Total registros</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="stat-mini">
                                <div class="stat-icon"><i class="bi bi-hourglass-split"></i></div>
                                <div>
                                    <div class="stat-number"><?php echo $en_proceso; ?></div>
                                    <div class="stat-label">En proceso</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="stat-mini">
                                <div class="stat-icon"><i class="bi bi-check2-circle" style="color: var(--gov-green);"></i></div>
                                <div>
                                    <div class="stat-number"><?php echo $preseleccionados; ?></div>
                                    <div class="stat-label">Preseleccionados</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="stat-mini">
                                <div class="stat-icon"><i class="bi bi-x-circle" style="color: #dc3545;"></i></div>
                                <div>
                                    <div class="stat-number"><?php echo $finalizados; ?></div>
                                    <div class="stat-label">Finalizados</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FILTROS Y BÚSQUEDA -->
                <div class="col-12">
                    <div class="card dashboard-card p-4 mb-4">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small text-muted">Buscar</label>
                                <input type="text" id="searchHistorial" class="form-control form-control-search" placeholder="Buscar por puesto o empresa...">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold small text-muted">Estado</label>
                                <select id="estadoFiltro" class="form-select form-select-custom">
                                    <option value="">Todos</option>
                                    <option value="pendiente">Pendiente</option>
                                    <option value="revisado">En revisión</option>
                                    <option value="interesado">Interesado</option>
                                    <option value="rechazado">Rechazado</option>
                                    <option value="activo">Activo (experiencia)</option>
                                    <option value="finalizado">Finalizado</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-gov w-100" id="filtrarBtn"><i class="bi bi-funnel me-1"></i>Filtrar</button>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-outline-secondary w-100" id="limpiarFiltrosBtn"><i class="bi bi-arrow-counterclockwise me-1"></i>Limpiar</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TABLA DE HISTORIAL -->
                <div class="col-12">
                    <div class="card dashboard-card p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold m-0 h6 text-uppercase tracking-wider text-muted">
                                <i class="bi bi-list-ul me-2"></i>Registros de actividad
                            </h5>
                            <span class="badge-soft-blue" id="registrosCount"><?php echo count($historial); ?> registros</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-custom" id="historialTable">
                                <thead>
                                    <tr>
                                        <th>Periodo</th>
                                        <th>Puesto / Oferta</th>
                                        <th>Empresa</th>
                                        <th>Estado</th>
                                        <th class="text-center">Acción</th>
                                    </tr>
                                </thead>
                                <tbody id="historialBody">
                                    <!-- Datos cargados por PHP/JS -->
                                </tbody>
                            </table>
                        </div>
                        <nav class="mt-3">
                            <ul class="pagination justify-content-center pagination-custom" id="paginationHistorial">
                                <!-- Generado por JS -->
                            </ul>
                        </nav>
                    </div>
                </div>

            </div>
        </main>

        <!-- ===== MODAL DETALLE DE REGISTRO ===== -->
        <div class="modal fade" id="detalleModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-file-earmark-text me-2" style="color: var(--gov-gold);"></i>Detalle del registro</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" id="modalDetalleBody">
                        <!-- Contenido dinámico -->
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-gov" data-bs-dismiss="modal"><i class="bi bi-check2 me-2"></i>Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        <?php include '../componentes/footer_desempleado.php'; ?>

        <script>
            // ===== DATOS DEL HISTORIAL DESDE PHP =====
            const historialData = <?php echo json_encode($historial); ?>;

            let currentPage = 1;
            const itemsPerPage = 3;
            let filteredData = [...historialData];

            // ===== MAPEO DE ESTADOS A BADGES =====
            function getBadgeClass(estado) {
                const map = {
                    'pendiente': 'badge-estado pendiente',
                    'revisado': 'badge-estado revisado',
                    'interesado': 'badge-estado interesado',
                    'rechazado': 'badge-estado rechazado',
                    'activo': 'badge-estado activo',
                    'finalizado': 'badge-estado finalizado'
                };
                return map[estado] || 'badge-estado';
            }

            function getEstadoLabel(estado) {
                const map = {
                    'pendiente': '⏳ Pendiente',
                    'revisado': '🔍 En revisión',
                    'interesado': '✅ Interesado',
                    'rechazado': '❌ Rechazado',
                    'activo': '🟢 Activo',
                    'finalizado': '⚪ Finalizado'
                };
                return map[estado] || estado;
            }

            // ===== ABRIR MODAL DE DETALLE =====
            function abrirDetalle(id) {
                const item = historialData.find(d => d.id === id);
                if (!item) {
                    alert('Registro no encontrado.');
                    return;
                }

                const body = document.getElementById('modalDetalleBody');
                const tipoLabel = item.tipo === 'postulacion' ? 'Postulación' : 'Experiencia laboral';

                body.innerHTML = `
                    <div class="detail-row">
                        <span class="detail-label"><i class="bi bi-tag"></i> Tipo</span>
                        <span class="detail-value"><span class="badge bg-secondary">${tipoLabel}</span></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label"><i class="bi bi-briefcase"></i> Puesto / Oferta</span>
                        <span class="detail-value"><strong>${item.puesto}</strong></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label"><i class="bi bi-building"></i> Empresa</span>
                        <span class="detail-value">${item.empresa}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label"><i class="bi bi-calendar-event"></i> Periodo</span>
                        <span class="detail-value">${item.fecha_inicio} - ${item.fecha_fin}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label"><i class="bi bi-tag"></i> Estado</span>
                        <span class="detail-value"><span class="${getBadgeClass(item.estado)}">${getEstadoLabel(item.estado)}</span></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label"><i class="bi bi-info-circle"></i> Descripción</span>
                        <div class="detail-value">
                            <div class="descripcion-text">${item.descripcion || 'Sin descripción disponible.'}</div>
                        </div>
                    </div>
                `;

                const modal = new bootstrap.Modal(document.getElementById('detalleModal'));
                modal.show();
            }

            // ===== RENDERIZAR TABLA =====
            function renderHistorial() {
                const start = (currentPage - 1) * itemsPerPage;
                const end = start + itemsPerPage;
                const pageItems = filteredData.slice(start, end);

                const tbody = document.getElementById('historialBody');
                const totalRegistros = filteredData.length;

                document.getElementById('registrosCount').textContent = `${totalRegistros} registros`;

                if (totalRegistros === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                No se encontraron registros que coincidan con los filtros.
                            </td>
                        </tr>
                    `;
                    document.getElementById('paginationHistorial').innerHTML = '';
                    return;
                }

                let html = '';
                pageItems.forEach(item => {
                    html += `
                        <tr>
                            <td><span class="fw-medium">${item.fecha_inicio} - ${item.fecha_fin}</span></td>
                            <td><strong>${item.puesto}</strong></td>
                            <td>${item.empresa}</td>
                            <td><span class="${getBadgeClass(item.estado)}">${getEstadoLabel(item.estado)}</span></td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-ver " onclick="abrirDetalle('${item.id}')">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });
                tbody.innerHTML = html;

                renderPagination();
            }

            // ===== RENDERIZAR PAGINACIÓN =====
            function renderPagination() {
                const totalPages = Math.ceil(filteredData.length / itemsPerPage);
                const controls = document.getElementById('paginationHistorial');

                if (totalPages <= 1) {
                    controls.innerHTML = '';
                    return;
                }

                let html = '';
                html += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${currentPage - 1}">Anterior</a></li>`;

                for (let i = 1; i <= totalPages; i++) {
                    html += `<li class="page-item ${i === currentPage ? 'active' : ''}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
                }

                html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${currentPage + 1}">Siguiente</a></li>`;

                controls.innerHTML = html;

                controls.querySelectorAll('a.page-link').forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        const page = parseInt(this.dataset.page);
                        if (page >= 1 && page <= totalPages) {
                            currentPage = page;
                            renderHistorial();
                        }
                    });
                });
            }

            // ===== FILTRADO =====
            function filtrarHistorial() {
                const search = document.getElementById('searchHistorial').value.toLowerCase().trim();
                const estado = document.getElementById('estadoFiltro').value;

                filteredData = historialData.filter(item => {
                    const matchSearch = item.puesto.toLowerCase().includes(search) || 
                                       item.empresa.toLowerCase().includes(search);
                    const matchEstado = estado === '' || item.estado === estado;
                    return matchSearch && matchEstado;
                });

                currentPage = 1;
                renderHistorial();
            }

            // ===== EVENTOS =====
            document.getElementById('filtrarBtn').addEventListener('click', filtrarHistorial);
            document.getElementById('limpiarFiltrosBtn').addEventListener('click', function() {
                document.getElementById('searchHistorial').value = '';
                document.getElementById('estadoFiltro').value = '';
                filtrarHistorial();
            });

            document.getElementById('searchHistorial').addEventListener('input', filtrarHistorial);
            document.getElementById('estadoFiltro').addEventListener('change', filtrarHistorial);

            // ===== INICIALIZAR =====
            renderHistorial();
        </script>

    </div>
</body>
</html>
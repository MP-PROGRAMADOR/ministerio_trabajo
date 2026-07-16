<?php
session_start();

// ===== VERIFICAR SESIÓN (solo administrador) =====
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'administrador') {
    header('Location: ../login_admin.php');
    exit();
}

// ============================================================
// SI SE SOLICITA LIMPIAR FILTROS (vía GET), REDIRIGIR SIN PARÁMETROS
// ============================================================
if (isset($_GET['limpiar'])) {
    header('Location: inscripciones.php');
    exit();
}

// ============================================================
// DATOS DE EJEMPLO (15 desempleados inscritos)
// ============================================================
$desempleados_ejemplo = [
    ['id' => 1, 'nombre' => 'María', 'apellidos' => 'García Pérez', 'expediente' => 'EG-12345', 'telefono' => '+240 222 111 222', 'provincia' => 'bioko_norte', 'estado_laboral' => 'desempleado', 'fecha_inscripcion' => '2026-01-15', 'lugar_formacion' => 'Malabo', 'foto' => 'https://ui-avatars.com/api/?name=Maria+Garcia&background=0B3A60&color=fff&size=80'],
    ['id' => 2, 'nombre' => 'Carlos', 'apellidos' => 'Mendoza Rivas', 'expediente' => 'EG-67890', 'telefono' => '+240 333 444 555', 'provincia' => 'litoral', 'estado_laboral' => 'desempleado', 'fecha_inscripcion' => '2026-02-20', 'lugar_formacion' => 'Bata', 'foto' => 'https://ui-avatars.com/api/?name=Carlos+Mendoza&background=0B3A60&color=fff&size=80'],
    ['id' => 3, 'nombre' => 'Ana', 'apellidos' => 'López Torres', 'expediente' => 'EG-24680', 'telefono' => '+240 555 666 777', 'provincia' => 'bioko_sur', 'estado_laboral' => 'desempleado', 'fecha_inscripcion' => '2026-03-10', 'lugar_formacion' => 'Malabo', 'foto' => 'https://ui-avatars.com/api/?name=Ana+Lopez&background=0B3A60&color=fff&size=80'],
    ['id' => 4, 'nombre' => 'Pedro', 'apellidos' => 'Jiménez Díaz', 'expediente' => 'EG-13579', 'telefono' => '+240 777 888 999', 'provincia' => 'centro_sur', 'estado_laboral' => 'desempleado', 'fecha_inscripcion' => '2026-04-05', 'lugar_formacion' => 'Bata', 'foto' => 'https://ui-avatars.com/api/?name=Pedro+Jimenez&background=0B3A60&color=fff&size=80'],
    ['id' => 5, 'nombre' => 'Laura', 'apellidos' => 'Martínez Sánchez', 'expediente' => 'EG-98765', 'telefono' => '+240 999 000 111', 'provincia' => 'kie_ntem', 'estado_laboral' => 'desempleado', 'fecha_inscripcion' => '2026-05-12', 'lugar_formacion' => 'Malabo', 'foto' => 'https://ui-avatars.com/api/?name=Laura+Martinez&background=0B3A60&color=fff&size=80'],
    ['id' => 6, 'nombre' => 'José', 'apellidos' => 'González Fernández', 'expediente' => 'EG-54321', 'telefono' => '+240 111 222 333', 'provincia' => 'wele_nzas', 'estado_laboral' => 'desempleado', 'fecha_inscripcion' => '2026-06-01', 'lugar_formacion' => 'Bata', 'foto' => 'https://ui-avatars.com/api/?name=Jose+Gonzalez&background=0B3A60&color=fff&size=80'],
    ['id' => 7, 'nombre' => 'Carmen', 'apellidos' => 'Ramírez Morales', 'expediente' => 'EG-11223', 'telefono' => '+240 444 555 666', 'provincia' => 'bioko_norte', 'estado_laboral' => 'desempleado', 'fecha_inscripcion' => '2026-07-18', 'lugar_formacion' => 'Malabo', 'foto' => 'https://ui-avatars.com/api/?name=Carmen+Ramirez&background=0B3A60&color=fff&size=80'],
    ['id' => 8, 'nombre' => 'David', 'apellidos' => 'Suárez Gómez', 'expediente' => 'EG-44556', 'telefono' => '+240 666 777 888', 'provincia' => 'litoral', 'estado_laboral' => 'desempleado', 'fecha_inscripcion' => '2026-08-22', 'lugar_formacion' => 'Bata', 'foto' => 'https://ui-avatars.com/api/?name=David+Suarez&background=0B3A60&color=fff&size=80'],
    ['id' => 9, 'nombre' => 'Elena', 'apellidos' => 'Ortega Vega', 'expediente' => 'EG-77889', 'telefono' => '+240 888 999 000', 'provincia' => 'bioko_sur', 'estado_laboral' => 'desempleado', 'fecha_inscripcion' => '2026-09-14', 'lugar_formacion' => 'Malabo', 'foto' => 'https://ui-avatars.com/api/?name=Elena+Ortega&background=0B3A60&color=fff&size=80'],
    ['id' => 10, 'nombre' => 'Miguel', 'apellidos' => 'Navarro Gil', 'expediente' => 'EG-99000', 'telefono' => '+240 000 111 222', 'provincia' => 'centro_sur', 'estado_laboral' => 'desempleado', 'fecha_inscripcion' => '2026-10-30', 'lugar_formacion' => 'Bata', 'foto' => 'https://ui-avatars.com/api/?name=Miguel+Navarro&background=0B3A60&color=fff&size=80'],
    ['id' => 11, 'nombre' => 'Sofía', 'apellidos' => 'Ramos Díaz', 'expediente' => 'EG-11234', 'telefono' => '+240 111 222 333', 'provincia' => 'bioko_norte', 'estado_laboral' => 'desempleado', 'fecha_inscripcion' => '2026-11-05', 'lugar_formacion' => 'Malabo', 'foto' => 'https://ui-avatars.com/api/?name=Sofia+Ramos&background=0B3A60&color=fff&size=80'],
    ['id' => 12, 'nombre' => 'Luis', 'apellidos' => 'Torres Gil', 'expediente' => 'EG-22345', 'telefono' => '+240 222 333 444', 'provincia' => 'litoral', 'estado_laboral' => 'desempleado', 'fecha_inscripcion' => '2026-11-12', 'lugar_formacion' => 'Bata', 'foto' => 'https://ui-avatars.com/api/?name=Luis+Torres&background=0B3A60&color=fff&size=80'],
    ['id' => 13, 'nombre' => 'Isabel', 'apellidos' => 'Vega Mora', 'expediente' => 'EG-33456', 'telefono' => '+240 333 444 555', 'provincia' => 'bioko_sur', 'estado_laboral' => 'desempleado', 'fecha_inscripcion' => '2026-11-20', 'lugar_formacion' => 'Malabo', 'foto' => 'https://ui-avatars.com/api/?name=Isabel+Vega&background=0B3A60&color=fff&size=80'],
    ['id' => 14, 'nombre' => 'Javier', 'apellidos' => 'Soto Romero', 'expediente' => 'EG-44567', 'telefono' => '+240 444 555 666', 'provincia' => 'centro_sur', 'estado_laboral' => 'desempleado', 'fecha_inscripcion' => '2026-12-01', 'lugar_formacion' => 'Bata', 'foto' => 'https://ui-avatars.com/api/?name=Javier+Soto&background=0B3A60&color=fff&size=80'],
    ['id' => 15, 'nombre' => 'Lucía', 'apellidos' => 'Méndez Castro', 'expediente' => 'EG-55678', 'telefono' => '+240 555 666 777', 'provincia' => 'kie_ntem', 'estado_laboral' => 'desempleado', 'fecha_inscripcion' => '2026-12-10', 'lugar_formacion' => 'Malabo', 'foto' => 'https://ui-avatars.com/api/?name=Lucia+Mendez&background=0B3A60&color=fff&size=80']
];

// ============================================================
// FILTRADO Y PAGINACIÓN
// ============================================================
$filtro_nombre = isset($_GET['nombre']) ? trim($_GET['nombre']) : '';
$filtro_apellido = isset($_GET['apellido']) ? trim($_GET['apellido']) : '';
$filtro_provincia = isset($_GET['provincia']) ? $_GET['provincia'] : '';
$filtro_estado = isset($_GET['estado']) ? $_GET['estado'] : '';
$filtro_lugar = isset($_GET['lugar']) ? $_GET['lugar'] : '';

$desempleados_filtrados = array_filter($desempleados_ejemplo, function($emp) use ($filtro_nombre, $filtro_apellido, $filtro_provincia, $filtro_estado, $filtro_lugar) {
    $coincide = true;
    if (!empty($filtro_nombre) && stripos($emp['nombre'], $filtro_nombre) === false) $coincide = false;
    if (!empty($filtro_apellido) && stripos($emp['apellidos'], $filtro_apellido) === false) $coincide = false;
    if (!empty($filtro_provincia) && $emp['provincia'] !== $filtro_provincia) $coincide = false;
    if (!empty($filtro_estado) && $emp['estado_laboral'] !== $filtro_estado) $coincide = false;
    if (!empty($filtro_lugar) && $emp['lugar_formacion'] !== $filtro_lugar) $coincide = false;
    return $coincide;
});

$desempleados_filtrados = array_values($desempleados_filtrados);
$total_registros = count($desempleados_filtrados);

// ============================================================
// EXPORTACIÓN (si se solicita)
// ============================================================
if (isset($_GET['export'])) {
    $export_type = $_GET['export'];
    $nombre_archivo = 'reporte_desempleados_' . date('Ymd_His');
    $contenido = '<html><head><meta charset="UTF-8"><title>Reporte de Desempleados Inscritos</title>';
    $contenido .= '<style>
        table { border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; }
        th, td { border: 1px solid #999; padding: 6px 10px; text-align: left; }
        th { background: #0B3A60; color: #fff; }
        .title { text-align: center; font-size: 18px; font-weight: bold; margin-bottom: 20px; }
    </style></head><body>';
    $contenido .= '<div class="title">Listado de Desempleados Inscritos - Ministerio de Trabajo</div>';
    $contenido .= '<table>';
    $contenido .= '<tr><th>ID</th><th>Expediente</th><th>Nombre</th><th>Apellidos</th><th>Teléfono</th><th>Provincia</th><th>Estado</th><th>Lugar Formación</th><th>Fecha Inscripción</th></tr>';
    foreach ($desempleados_filtrados as $emp) {
        $contenido .= '<tr>';
        $contenido .= '<td>' . $emp['id'] . '</td>';
        $contenido .= '<td>' . htmlspecialchars($emp['expediente']) . '</td>';
        $contenido .= '<td>' . htmlspecialchars($emp['nombre']) . '</td>';
        $contenido .= '<td>' . htmlspecialchars($emp['apellidos']) . '</td>';
        $contenido .= '<td>' . htmlspecialchars($emp['telefono']) . '</td>';
        $contenido .= '<td>' . htmlspecialchars(ucfirst(str_replace('_', ' ', $emp['provincia']))) . '</td>';
        $contenido .= '<td>' . htmlspecialchars(ucfirst($emp['estado_laboral'])) . '</td>';
        $contenido .= '<td>' . htmlspecialchars($emp['lugar_formacion']) . '</td>';
        $contenido .= '<td>' . date('d/m/Y', strtotime($emp['fecha_inscripcion'])) . '</td>';
        $contenido .= '</tr>';
    }
    $contenido .= '</table>';
    $contenido .= '<p style="margin-top:20px;">Generado: ' . date('d/m/Y H:i') . '</p>';
    $contenido .= '</body></html>';

    if ($export_type == 'word') {
        header('Content-Type: application/msword');
        header('Content-Disposition: attachment; filename="' . $nombre_archivo . '.doc"');
        echo $contenido;
        exit;
    } elseif ($export_type == 'excel') {
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $nombre_archivo . '.xls"');
        echo $contenido;
        exit;
    }
}

// ============================================================
// PAGINACIÓN (después de exportación)
// ============================================================
$registros_por_pagina = 5;
$total_paginas = ceil($total_registros / $registros_por_pagina);
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($pagina_actual < 1) $pagina_actual = 1;
if ($pagina_actual > $total_paginas && $total_paginas > 0) $pagina_actual = $total_paginas;
$inicio = ($pagina_actual - 1) * $registros_por_pagina;
$desempleados_pagina = array_slice($desempleados_filtrados, $inicio, $registros_por_pagina);

$provincias_nombres = [
    'bioko_norte' => 'Bioko Norte',
    'bioko_sur' => 'Bioko Sur',
    'litoral' => 'Litoral',
    'centro_sur' => 'Centro Sur',
    'kie_ntem' => 'Kié-Ntem',
    'wele_nzas' => 'Wele-Nzas'
];
$estados_nombres = [
    'desempleado' => 'Desempleado',
    'contratado' => 'Contratado',
    'suspendido' => 'Suspendido'
];

// ============================================================
// INCLUIR HEADER Y MENÚ
// ============================================================
$titulo = 'Desempleados Inscritos - Portal de Empleo';
include_once '../componentes/header_admin.php';
include_once '../componentes/menu_admin.php';
?>

<style>
    /* ===== ESTILOS ===== */
    .inscripciones-container {
        background: #ffffff;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        margin-top: 0.5rem; /* separación del header */
    }
    .inscripciones-container .table thead {
        background: #f8f9fa;
    }
    .inscripciones-container .table th {
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        color: #495057;
        border-bottom: 2px solid #dee2e6;
        white-space: nowrap;
    }
    .inscripciones-container .table td {
        vertical-align: middle;
        font-size: 0.9rem;
    }
    .badge-estado {
        font-weight: 500;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.7rem;
        text-transform: uppercase;
    }
    .badge-desempleado {
        background: #fff3cd;
        color: #856404;
    }
    .badge-contratado {
        background: #d4edda;
        color: #155724;
    }
    .badge-suspendido {
        background: #f8d7da;
        color: #721c24;
    }
    .btn-inscribir, .btn-imprimir, .btn-export {
        border-radius: 5px !important;
        padding: 0.4rem 1.2rem;
        font-weight: 600;
        border: none;
        transition: all 0.3s;
        font-size: 0.85rem;
    }
    .btn-inscribir {
        background: #0B3A60;
        color: #fff;
    }
    .btn-inscribir:hover {
        background: #1A4F7A;
        color: #fff;
    }
    .btn-imprimir {
        background: #6c757d;
        color: #fff;
    }
    .btn-imprimir:hover {
        background: #5a6268;
        color: #fff;
    }
    .btn-export-word {
        background: #2b5797;
        color: #fff;
    }
    .btn-export-word:hover {
        background: #1d3f6e;
        color: #fff;
    }
    .btn-export-excel {
        background: #217346;
        color: #fff;
    }
    .btn-export-excel:hover {
        background: #165a33;
        color: #fff;
    }
    .filtros-form .form-control,
    .filtros-form .form-select {
        border-radius: 5px;
        border: 1px solid #ced4da;
        padding: 0.35rem 0.75rem;
        height: 36px;
        font-size: 0.85rem;
    }
    .filtros-form .btn-filtrar,
    .filtros-form .btn-limpiar {
        border-radius: 5px;
        padding: 0.35rem 1.2rem;
        border: none;
        transition: all 0.3s;
        height: 36px;
        font-weight: 500;
        font-size: 0.85rem;
    }
    .filtros-form .btn-filtrar {
        background: #0B3A60;
        color: #fff;
    }
    .filtros-form .btn-filtrar:hover {
        background: #1A4F7A;
    }
    .filtros-form .btn-limpiar {
        background: #e9ecef;
        color: #495057;
    }
    .filtros-form .btn-limpiar:hover {
        background: #dde1e6;
    }
    .pagination .page-link {
        color: #0B3A60;
        border-radius: 5px;
        font-size: 0.85rem;
    }
    .pagination .page-item.active .page-link {
        background: #0B3A60;
        border-color: #0B3A60;
        color: #fff;
    }
    .pagination .page-item.disabled .page-link {
        color: #6c757d;
    }
    .main-content {
        padding-left: 2rem !important;
        padding-top: 0.5rem !important;
    }
    @media (max-width: 768px) {
        .main-content {
            padding-left: 0.5rem !important;
        }
        .table-responsive {
            font-size: 0.8rem;
        }
    }
    @media print {
        .no-print { display: none !important; }
        .inscripciones-container { box-shadow: none; border: 1px solid #ddd; }
        .table { font-size: 11px; }
        .main-content { padding-left: 0 !important; }
    }
    .export-buttons .btn {
        margin-right: 0.3rem;
    }
    .avatar-mini {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #0B3A60;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: #fff;
        font-size: 0.8rem;
        text-transform: uppercase;
    }
    .btn-ver {
        background: #0B3A60;
        color: #fff;
        border-radius: 5px;
        padding: 0.2rem 0.8rem;
        font-size: 0.75rem;
        border: none;
        transition: all 0.3s;
        text-decoration: none;
        display: inline-block;
    }
    .btn-ver:hover {
        background: #1A4F7A;
        color: #fff;
    }
    .table th, .table td {
        padding: 0.6rem 0.5rem;
    }
</style>

<div class="row">
    <div class="col-12">
        <div class="inscripciones-container">
            <!-- Título y botones de acción -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                <h4 class="fw-bold mb-0">
                    <i class="bi bi-people me-2" style="color: #0B3A60;"></i>
                    Desempleados Inscritos
                    <span class="badge bg-secondary ms-2"><?php echo $total_registros; ?></span>
                </h4>
                <div class="d-flex flex-wrap gap-2 no-print">
                    <a href="inscribir_desempleado.php" class="btn btn-inscribir">
                        <i class="bi bi-person-plus me-1"></i> Inscribir nuevo
                    </a>
                    <button onclick="window.print()" class="btn btn-imprimir">
                        <i class="bi bi-printer me-1"></i> Imprimir
                    </button>
                    <div class="btn-group export-buttons" role="group">
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['export'=>'word'])); ?>" class="btn btn-export-word">
                            <i class="bi bi-file-earmark-word me-1"></i> Word
                        </a>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['export'=>'excel'])); ?>" class="btn btn-export-excel">
                            <i class="bi bi-file-earmark-excel me-1"></i> Excel
                        </a>
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <form method="GET" class="row g-2 filtros-form no-print mb-3 align-items-end">
                <div class="col-md-2">
                    <input type="text" name="nombre" class="form-control" placeholder="Nombre" value="<?php echo htmlspecialchars($filtro_nombre); ?>">
                </div>
                <div class="col-md-2">
                    <input type="text" name="apellido" class="form-control" placeholder="Apellido" value="<?php echo htmlspecialchars($filtro_apellido); ?>">
                </div>
                <div class="col-md-2">
                    <select name="provincia" class="form-select">
                        <option value="">Provincia</option>
                        <?php foreach ($provincias_nombres as $key => $nombre): ?>
                            <option value="<?php echo $key; ?>" <?php echo ($filtro_provincia == $key) ? 'selected' : ''; ?>><?php echo $nombre; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="estado" class="form-select">
                        <option value="">Estado</option>
                        <option value="desempleado" <?php echo ($filtro_estado == 'desempleado') ? 'selected' : ''; ?>>Desempleado</option>
                        <option value="contratado" <?php echo ($filtro_estado == 'contratado') ? 'selected' : ''; ?>>Contratado</option>
                        <option value="suspendido" <?php echo ($filtro_estado == 'suspendido') ? 'selected' : ''; ?>>Suspendido</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="lugar" class="form-select">
                        <option value="">Lugar formación</option>
                        <option value="Malabo" <?php echo ($filtro_lugar == 'Malabo') ? 'selected' : ''; ?>>Malabo</option>
                        <option value="Bata" <?php echo ($filtro_lugar == 'Bata') ? 'selected' : ''; ?>>Bata</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-filtrar w-100"><i class="bi bi-funnel me-1"></i> Filtrar</button>
                    <button type="submit" name="limpiar" value="1" class="btn btn-limpiar w-100"><i class="bi bi-eraser me-1"></i> Limpiar</button>
                </div>
            </form>

            <!-- Tabla -->
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th style="width:50px;">ID</th>
                            <th style="width:50px;">Foto</th>
                            <th>Expediente</th>
                            <th>Nombre</th>
                            <th>Apellidos</th>
                            <th>Teléfono</th>
                            <th>Provincia</th>
                            <th>Estado</th>
                            <th>Lugar formación</th>
                            <th>Fecha inscripción</th>
                            <th style="width:80px;" class="text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($desempleados_pagina) > 0): ?>
                            <?php foreach ($desempleados_pagina as $emp): ?>
                                <tr>
                                    <td><?php echo $emp['id']; ?></td>
                                    <td>
                                        <span class="avatar-mini">
                                            <?php echo strtoupper(substr($emp['nombre'], 0, 1) . substr($emp['apellidos'], 0, 1)); ?>
                                        </span>
                                    </td>
                                    <td><strong><?php echo htmlspecialchars($emp['expediente']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($emp['nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($emp['apellidos']); ?></td>
                                    <td><?php echo htmlspecialchars($emp['telefono']); ?></td>
                                    <td><?php echo $provincias_nombres[$emp['provincia']] ?? $emp['provincia']; ?></td>
                                    <td>
                                        <span class="badge-estado badge-<?php echo $emp['estado_laboral']; ?>">
                                            <?php echo $estados_nombres[$emp['estado_laboral']] ?? $emp['estado_laboral']; ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($emp['lugar_formacion']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($emp['fecha_inscripcion'])); ?></td>
                                    <td class="text-center">
                                        <a href="ver_inscripcion.php?id=<?php echo $emp['id']; ?>" class="btn btn-ver">
                                            <i class="bi bi-eye"></i> Ver
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="11" class="text-center text-muted py-4">
                                    No se encontraron desempleados con los filtros aplicados.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <?php if ($total_paginas > 1): ?>
                <nav aria-label="Navegación de páginas" class="no-print mt-3">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?php echo ($pagina_actual <= 1) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?pagina=<?php echo $pagina_actual - 1; ?>&<?php echo http_build_query(array_filter(['nombre'=>$filtro_nombre, 'apellido'=>$filtro_apellido, 'provincia'=>$filtro_provincia, 'estado'=>$filtro_estado, 'lugar'=>$filtro_lugar])); ?>">
                                Anterior
                            </a>
                        </li>
                        <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                            <li class="page-item <?php echo ($i == $pagina_actual) ? 'active' : ''; ?>">
                                <a class="page-link" href="?pagina=<?php echo $i; ?>&<?php echo http_build_query(array_filter(['nombre'=>$filtro_nombre, 'apellido'=>$filtro_apellido, 'provincia'=>$filtro_provincia, 'estado'=>$filtro_estado, 'lugar'=>$filtro_lugar])); ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?php echo ($pagina_actual >= $total_paginas) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?pagina=<?php echo $pagina_actual + 1; ?>&<?php echo http_build_query(array_filter(['nombre'=>$filtro_nombre, 'apellido'=>$filtro_apellido, 'provincia'=>$filtro_provincia, 'estado'=>$filtro_estado, 'lugar'=>$filtro_lugar])); ?>">
                                Siguiente
                            </a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php
echo '</div>'; // Cierra page-content
echo '</main>'; // Cierra main-content
echo '</div>'; // Cierra wrapper
include_once '../componentes/footer_admin.php';
?>
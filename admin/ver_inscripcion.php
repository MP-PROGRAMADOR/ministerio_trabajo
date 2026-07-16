<?php
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'administrador') {
    header('Location: ../login_admin.php');
    exit();
}

$titulo = 'Detalle del Desempleado - Portal de Empleo';
include_once '../componentes/header_admin.php';
include_once '../componentes/menu_admin.php';

// Simular datos (en producción, consulta a BD)
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$desempleado = null;

// Datos de ejemplo (mismo array que en inscripciones.php)
$desempleados_ejemplo = [
    ['id' => 1, 'nombre' => 'María', 'apellidos' => 'García Pérez', 'expediente' => 'EG-12345', 'telefono' => '+240 222 111 222', 'provincia' => 'bioko_norte', 'estado_laboral' => 'desempleado', 'fecha_inscripcion' => '2026-01-15', 'lugar_formacion' => 'Malabo'],
    ['id' => 2, 'nombre' => 'Carlos', 'apellidos' => 'Mendoza Rivas', 'expediente' => 'EG-67890', 'telefono' => '+240 333 444 555', 'provincia' => 'litoral', 'estado_laboral' => 'desempleado', 'fecha_inscripcion' => '2026-02-20', 'lugar_formacion' => 'Bata'],
    // ... (copiar todos los 15 del otro archivo)
];

// Buscar el desempleado por ID
foreach ($desempleados_ejemplo as $emp) {
    if ($emp['id'] == $id) {
        $desempleado = $emp;
        break;
    }
}

if (!$desempleado) {
    echo '<div class="alert alert-danger">Desempleado no encontrado.</div>';
    include_once '../componentes/footer_admin.php';
    exit();
}
?>

<style>
    .detalle-container {
        background: #fff;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .detalle-item {
        margin-bottom: 1rem;
        border-bottom: 1px solid #f0f0f0;
        padding-bottom: 0.5rem;
    }
    .detalle-item label {
        font-weight: 600;
        color: #495057;
        font-size: 0.9rem;
    }
    .detalle-item .valor {
        font-size: 1rem;
        color: #212529;
    }
    .btn-volver {
        background: #6c757d;
        color: #fff;
        border-radius: 5px;
        padding: 0.4rem 1.5rem;
        border: none;
        transition: all 0.3s;
        text-decoration: none;
        display: inline-block;
    }
    .btn-volver:hover {
        background: #5a6268;
        color: #fff;
    }
    .btn-imprimir-detalle {
        background: #0B3A60;
        color: #fff;
        border-radius: 5px;
        padding: 0.4rem 1.5rem;
        border: none;
        transition: all 0.3s;
    }
    .btn-imprimir-detalle:hover {
        background: #1A4F7A;
        color: #fff;
    }
    @media print {
        .no-print { display: none !important; }
        .detalle-container { box-shadow: none; border: 1px solid #ddd; }
    }
</style>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="detalle-container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold"><i class="bi bi-person-circle me-2"></i> Detalle del Desempleado</h4>
                <div class="no-print">
                    <button onclick="window.print()" class="btn btn-imprimir-detalle"><i class="bi bi-printer"></i> Imprimir</button>
                    <a href="inscripciones.php" class="btn btn-volver"><i class="bi bi-arrow-left"></i> Volver</a>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="detalle-item">
                        <label>ID</label>
                        <div class="valor"><?php echo $desempleado['id']; ?></div>
                    </div>
                    <div class="detalle-item">
                        <label>Expediente</label>
                        <div class="valor"><?php echo htmlspecialchars($desempleado['expediente']); ?></div>
                    </div>
                    <div class="detalle-item">
                        <label>Nombre</label>
                        <div class="valor"><?php echo htmlspecialchars($desempleado['nombre']); ?></div>
                    </div>
                    <div class="detalle-item">
                        <label>Apellidos</label>
                        <div class="valor"><?php echo htmlspecialchars($desempleado['apellidos']); ?></div>
                    </div>
                    <div class="detalle-item">
                        <label>Teléfono</label>
                        <div class="valor"><?php echo htmlspecialchars($desempleado['telefono']); ?></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="detalle-item">
                        <label>Provincia</label>
                        <div class="valor"><?php echo ucfirst(str_replace('_', ' ', $desempleado['provincia'])); ?></div>
                    </div>
                    <div class="detalle-item">
                        <label>Estado laboral</label>
                        <div class="valor"><span class="badge badge-desempleado"><?php echo ucfirst($desempleado['estado_laboral']); ?></span></div>
                    </div>
                    <div class="detalle-item">
                        <label>Lugar de formación</label>
                        <div class="valor"><?php echo htmlspecialchars($desempleado['lugar_formacion']); ?></div>
                    </div>
                    <div class="detalle-item">
                        <label>Fecha de inscripción</label>
                        <div class="valor"><?php echo date('d/m/Y', strtotime($desempleado['fecha_inscripcion'])); ?></div>
                    </div>
                </div>
            </div>
            <div class="mt-4 text-center no-print">
                <a href="inscripciones.php" class="btn btn-volver"><i class="bi bi-arrow-left"></i> Volver al listado</a>
            </div>
        </div>
    </div>
</div>

<?php
echo '</div>';
echo '</main>';
echo '</div>';
include_once '../componentes/footer_admin.php';
?>
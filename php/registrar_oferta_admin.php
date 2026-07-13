<?php
session_start();

// ===== VERIFICAR SESIÓN =====
if (!isset($_SESSION['id_usuario']) || !in_array($_SESSION['rol'] ?? '', ['administrador', 'ministerio'])) {
    header('Location: ../login_admin.php');
    exit();
}

include_once '../conexion/conexion.php';

// ===== OBTENER DATOS =====
$empleador_id = $_POST['empleador_id'] ?? 0;
$titulo_puesto = trim($_POST['titulo_puesto'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$requisitos = trim($_POST['requisitos'] ?? '');
$provincia = trim($_POST['provincia'] ?? '');
$salario_ofrecido = $_POST['salario_ofrecido'] ?? null;
$estado = $_POST['estado'] ?? 'abierta';

// ===== VALIDAR CAMPOS =====
if (empty($empleador_id) || empty($titulo_puesto) || empty($descripcion) || empty($requisitos) || empty($provincia)) {
    $_SESSION['alerta'] = ['tipo' => 'danger', 'mensaje' => 'Todos los campos obligatorios deben estar llenos.'];
    header('Location: ../admin/ofertas_admin.php');
    exit();
}

try {
    // ===== INSERTAR OFERTA =====
    $stmt = $pdo->prepare("
        INSERT INTO ofertas_empleo (empleador_id, titulo_puesto, descripcion, requisitos, provincia, salario_ofrecido, estado)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $empleador_id,
        $titulo_puesto,
        $descripcion,
        $requisitos,
        $provincia,
        $salario_ofrecido ?: null,
        $estado
    ]);

    $_SESSION['alerta'] = ['tipo' => 'success', 'mensaje' => 'Oferta publicada exitosamente.'];
    header('Location: ../admin/ofertas_admin.php');
    exit();

} catch (PDOException $e) {
    error_log("Error al registrar oferta: " . $e->getMessage());
    $_SESSION['alerta'] = ['tipo' => 'danger', 'mensaje' => 'Error al publicar la oferta: ' . $e->getMessage()];
    header('Location: ../admin/ofertas_admin.php');
    exit();
}
<?php
session_start();

// ===== VERIFICAR SESIÓN =====
if (!isset($_SESSION['id_usuario']) || !in_array($_SESSION['rol'] ?? '', ['administrador', 'ministerio'])) {
    header('Location: ../login_admin.php');
    exit();
}

include_once '../conexion/conexion.php';

// ===== OBTENER DATOS =====
$nombre_empresa = trim($_POST['nombre_empresa'] ?? '');
$rnc_ruc = trim($_POST['rnc_ruc'] ?? '');
$sector_industrial = trim($_POST['sector_industrial'] ?? '');
$telefono_corporativo = trim($_POST['telefono_corporativo'] ?? '');
$direccion = trim($_POST['direccion'] ?? '');
$sitio_web = trim($_POST['sitio_web'] ?? '');
$correo_responsable = trim($_POST['correo_responsable'] ?? '');
$password = $_POST['password'] ?? '';
$nombre_responsable = trim($_POST['nombre_responsable'] ?? '');
$apellidos_responsable = trim($_POST['apellidos_responsable'] ?? '');
$documento_responsable = trim($_POST['documento_responsable'] ?? '');

// ===== VALIDAR CAMPOS =====
if (empty($nombre_empresa) || empty($sector_industrial) || empty($telefono_corporativo) || 
    empty($direccion) || empty($correo_responsable) || empty($password) || 
    empty($nombre_responsable) || empty($apellidos_responsable) || empty($documento_responsable)) {
    $_SESSION['alerta'] = ['tipo' => 'danger', 'mensaje' => 'Todos los campos obligatorios deben estar llenos.'];
    header('Location: ../admin/empleadores.php');
    exit();
}

if (strlen($password) < 8) {
    $_SESSION['alerta'] = ['tipo' => 'danger', 'mensaje' => 'La contraseña debe tener al menos 8 caracteres.'];
    header('Location: ../admin/empleadores.php');
    exit();
}

if (!filter_var($correo_responsable, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['alerta'] = ['tipo' => 'danger', 'mensaje' => 'El correo electrónico no es válido.'];
    header('Location: ../admin/empleadores.php');
    exit();
}

try {
    // ===== VERIFICAR SI YA EXISTE =====
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE correo_electronico = ? OR documento_identidad = ?");
    $stmt->execute([$correo_responsable, $documento_responsable]);
    if ($stmt->fetch()) {
        $_SESSION['alerta'] = ['tipo' => 'danger', 'mensaje' => 'Ya existe un usuario con ese correo o documento.'];
        header('Location: ../admin/empleadores.php');
        exit();
    }

    // ===== GENERAR NÚMERO DE EXPEDIENTE =====
    $numero_expediente = 'EG-' . str_pad(rand(10000, 99999), 5, '0', STR_PAD_LEFT);

    // ===== ENCRIPTAR CONTRASEÑA =====
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // ===== INICIAR TRANSACCIÓN =====
    $pdo->beginTransaction();

    // ===== 1. INSERTAR USUARIO =====
    $stmt = $pdo->prepare("
        INSERT INTO usuarios (numero_expediente, nombre, apellidos, nombre_usuario, correo_electronico, documento_identidad, password, rol, correo_verificado)
        VALUES (?, ?, ?, ?, ?, ?, ?, 'empleador', 1)
    ");
    $nombre_usuario = strtolower(substr($nombre_responsable, 0, 1) . $apellidos_responsable);
    $stmt->execute([
        $numero_expediente, 
        $nombre_responsable, 
        $apellidos_responsable, 
        $nombre_usuario, 
        $correo_responsable, 
        $documento_responsable, 
        $password_hash
    ]);
    $usuario_id = $pdo->lastInsertId();

    // ===== 2. INSERTAR EMPLEADOR =====
    $stmt = $pdo->prepare("
        INSERT INTO empleadores (usuario_id, nombre_empresa, rnc_ruc, sector_industrial, telefono_corporativo, direccion, sitio_web)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $usuario_id, 
        $nombre_empresa, 
        $rnc_ruc ?: null, 
        $sector_industrial, 
        $telefono_corporativo, 
        $direccion, 
        $sitio_web ?: null
    ]);

    // ===== 3. CONFIRMAR TRANSACCIÓN =====
    $pdo->commit();

    $_SESSION['alerta'] = ['tipo' => 'success', 'mensaje' => 'Empresa registrada exitosamente. Expediente: ' . $numero_expediente];
    header('Location: ../admin/empleadores.php');
    exit();

} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Error al registrar empresa: " . $e->getMessage());
    $_SESSION['alerta'] = ['tipo' => 'danger', 'mensaje' => 'Error al registrar la empresa: ' . $e->getMessage()];
    header('Location: ../admin/empleadores.php');
    exit();
}
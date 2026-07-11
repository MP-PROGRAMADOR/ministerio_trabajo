<?php
session_start();

// Verificar sesión
if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../login_desempleados.php');
    exit();
}

include '../conexion/conexion.php';

$id_usuario = $_SESSION['id_usuario'];
$password_actual = $_POST['password_actual'] ?? '';
$password_nueva = $_POST['password_nueva'] ?? '';

// Validar campos
if (empty($password_actual) || empty($password_nueva)) {
    $_SESSION['mensaje_error'] = "Todos los campos son obligatorios.";
    header('Location: ../desempleado/perfil.php');
    exit();
}

// Validar longitud de la nueva contraseña
if (strlen($password_nueva) < 8) {
    $_SESSION['mensaje_error'] = "La nueva contraseña debe tener al menos 8 caracteres.";
    header('Location: ../desempleado/perfil.php');
    exit();
}

try {
    // Obtener la contraseña actual del usuario
    $stmt = $pdo->prepare("SELECT password FROM usuarios WHERE id = ?");
    $stmt->execute([$id_usuario]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        $_SESSION['mensaje_error'] = "Usuario no encontrado.";
        header('Location: ../desempleado/perfil.php');
        exit();
    }

    // Verificar que la contraseña actual sea correcta
    if (!password_verify($password_actual, $usuario['password'])) {
        $_SESSION['mensaje_error'] = "La contraseña actual es incorrecta.";
        header('Location: ../desempleado/perfil.php');
        exit();
    }

    // Encriptar y actualizar la nueva contraseña
    $password_hash = password_hash($password_nueva, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
    $stmt->execute([$password_hash, $id_usuario]);

    $_SESSION['mensaje_exito'] = "Contraseña actualizada correctamente.";
    header('Location: ../desempleado/perfil.php');
    exit();

} catch (PDOException $e) {
    error_log("Error al cambiar contraseña: " . $e->getMessage());
    $_SESSION['mensaje_error'] = "Error al cambiar la contraseña.";
    header('Location: ../desempleado/perfil.php');
    exit();
}
<?php
session_start();

// Ruta para redirigir al usuario (por ejemplo, al login para que ya pueda entrar)
$pagina_login = '../login_desempleados.php'; 

// 1. Verificar si el token llegó a través de la URL
if (!isset($_GET['token']) || empty(trim($_GET['token']))) {
    $_SESSION['error'] = "El token de verificación no es válido o ha expirado.";
    header("Location: $pagina_login");
    exit();
}

$token = trim($_GET['token']);

// 2. Incluir la conexión a la base de datos
require_once '../conexion/conexion.php';

try {
    // 3. Buscar si existe un usuario con ese token y que aún no esté verificado
    $query = "SELECT id_usuario, correo_verificado FROM usuarios WHERE token_verificacion = :token LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':token' => $token]);
    $usuario = $stmt->fetch();

    if ($usuario) {
        if ($usuario['correo_verificado'] == 1) {
            $_SESSION['exito'] = "Esta cuenta ya había sido verificada anteriormente. Puede iniciar sesión.";
        } else {
            // 4. Activar la cuenta: cambiar correo_verificado a 1 y limpiar el token
            $query_update = "UPDATE usuarios SET correo_verificado = 1, token_verificacion = NULL WHERE id_usuario = :id";
            $stmt_update = $pdo->prepare($query_update);
            $stmt_update->execute([':id' => $usuario['id_usuario']]);

            $_SESSION['exito'] = "¡Cuenta verificada con éxito! Ya puede iniciar sesión en el portal.";
        }
    } else {
        // Si el token no existe en la base de datos
        $_SESSION['error'] = "El enlace de verificación es inválido o ya ha sido utilizado.";
    }

    // 5. Redirigir al login (donde se mostrarán los mensajes de éxito o error que configuramos antes)
    header("Location: $pagina_login");
    exit();

} catch (PDOException $e) {
    error_log("Error en la verificación de cuenta: " . $e->getMessage());
    $_SESSION['error'] = "Hubo un error interno al procesar la verificación.";
    header("Location: $pagina_login");
    exit();
}
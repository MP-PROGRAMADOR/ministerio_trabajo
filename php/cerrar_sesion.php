<?php
// 1. Inicializar la sesión para poder manipularla
session_start();

// 2. Desvincular todas las variables de sesión del servidor
$_SESSION = array();

// 3. Borrar la cookie de sesión del navegador (Muy importante para la seguridad)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. Destruir finalmente la sesión en el servidor
session_destroy();

// 5. Redirigir al usuario al portal público o al login
// Ajusta la ruta según la ubicación de tu archivo index.php o login.php
header("Location: ../index.php");
exit();
?>
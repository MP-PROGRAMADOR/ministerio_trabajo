<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Validar si el usuario está logueado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../php/cerrar_sesion.php");
    exit();
}

// 2. Control estricto de Rol: Solo permitimos al 'buscador'
// Si intenta entrar un administrador, ministerio o empleador, destruimos su sesión por intruso
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'buscador') {
    header("Location: ../php/cerrar_sesion.php");
    exit();
}


$nombre_completo=$_SESSION['nombre_completo'];
// Si pasa los dos filtros anteriores, el usuario es un buscador válido y puede ver la página
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control - Portal de Empleo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
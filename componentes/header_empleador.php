<?php
// php/auth_empleador.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Función para proteger páginas que requieren autenticación de Empleador.
 * @param array $rolesPermitidos Lista de roles permitidos (por defecto 'empleador' y 'administrador').
 */
function protegerPaginaEmpleador(array $rolesPermitidos = ['empleador', 'administrador']) {
    
    // 1. Verificar si el usuario ha iniciado sesión
    if (!isset($_SESSION['id_usuario']) || empty($_SESSION['id_usuario'])) {
        header('Location: ../login_empleadores.php?error=no_session');
        exit();
    }

    // 2. Verificar que el rol del usuario esté dentro de los permitidos
    $rolUsuario = $_SESSION['rol'] ?? '';

    if (!in_array($rolUsuario, $rolesPermitidos, true)) {
        header('Location: ./index.php?error=acceso_denegado');
        exit();
    }

    // 3. Verificar que la cuenta tenga un ID de empleador/empresa asociado en sesión
    if ($rolUsuario === 'empleador' && empty($_SESSION['empleador_id'])) {
        header('Location: ../login_empleadores.php?error=perfil_incompleto');
        exit();
    }
}

// ⚠️ EJECUCIÓN DE LA PROTECCIÓN DE LA PÁGINA
protegerPaginaEmpleador(['empleador']);

// Variables globales de sesión listas para usar en las vistas/páginas del panel
$id_usuario      = $_SESSION['id_usuario'];
$empleador_id    = $_SESSION['empleador_id'] ?? null;
$nombre_completo = $_SESSION['nombre_completo'] ?? '';
$nombre_empresa  = $_SESSION['nombre_empresa'] ?? '';
$numero_exp      = $_SESSION['numero_expediente'] ?? '';

// Conexión a la base de datos
require_once '../conexion/conexion.php'; 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Empresa | Sistema Nacional de Empleo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --sidebar-width: 260px;
            --primary-color: #0d6efd;
            --bg-body: #f4f6f9;
            --card-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            --transition-speed: 0.3s;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-body);
            overflow-x: hidden;
        }

        /* --- SIDEBAR --- */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #111827;
            color: #fff;
            z-index: 1000;
            transition: all var(--transition-speed);
        }

        .sidebar-brand {
            padding: 1.5rem 1.25rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-menu {
            padding: 1rem 0;
            list-style: none;
            margin: 0;
        }

        .sidebar-menu .nav-link {
            color: #9ca3af;
            padding: 0.8rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
            border-left: 4px solid transparent;
            transition: all 0.2s ease;
        }

        .sidebar-menu .nav-link:hover,
        .sidebar-menu .nav-link.active {
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.05);
            border-left-color: var(--primary-color);
        }

        /* --- MAIN CONTENT --- */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 2rem;
            transition: all var(--transition-speed);
        }

        /* --- NAVBAR SUPERIOR --- */
        .top-navbar {
            background-color: #ffffff;
            border-radius: 12px;
            padding: 0.8rem 1.5rem;
            margin-bottom: 2rem;
            box-shadow: var(--card-shadow);
        }

        /* --- TARJETAS DE ESTADÍSTICAS --- */
        .stat-card {
            border: none;
            border-radius: 14px;
            box-shadow: var(--card-shadow);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
        }

        .icon-box {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
        }

        /* --- TABLAS Y TARJETAS GENERALES --- */
        .custom-card {
            border: none;
            border-radius: 14px;
            box-shadow: var(--card-shadow);
            background-color: #ffffff;
        }

        .table th {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6b7280;
            font-weight: 600;
            border-bottom-width: 1px;
        }

        .avatar-company {
            width: 40px;
            height: 40px;
            background-color: #e0e7ff;
            color: #4f46e5;
            font-weight: 700;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* --- RESPONSIVE DESKTOP/MOBILE --- */
        @media (max-width: 991.98px) {
            .sidebar {
                margin-left: calc(-1 * var(--sidebar-width));
            }
            .sidebar.active {
                margin-left: 0;
            }
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }
        }
    </style>
</head>
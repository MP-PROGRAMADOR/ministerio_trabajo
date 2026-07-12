<?php
// php/auth.php (o cabecera de la página)

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Función para proteger páginas que requieren autenticación.
 * @param array $rolesPermitidos Lista de roles permitidos.
 */
function protegerPagina(array $rolesPermitidos = ['administrador']) {
    // 1. Verificar si el usuario ha iniciado sesión
    if (!isset($_SESSION['id_usuario']) || empty($_SESSION['id_usuario'])) {
        header('Location: ../login_admin.php?error=no_session');
        exit();
    }

    // 2. Verificar que el rol del usuario coincida exactamente con los permitidos
    $rolUsuario = $_SESSION['rol'] ?? '';

    if (!in_array($rolUsuario, $rolesPermitidos, true)) {
        header('Location: ../index.php?error=acceso_denegado');
        exit();
    }
}

// ⚠️ ¡AQUÍ ESTÁ LA CLAVE! Llama a la función para ejecutar la validación
protegerPagina(['administrador']);

// Una vez asegurado que la sesión existe, ya puedes asignar las variables de sesión de forma segura:
$id_usuario     = $_SESSION['id_usuario'];
$nombre_usuario = $_SESSION['nombre_usuario']; 

require_once '../conexion/conexion.php'; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Intermediación - Ministerio de Trabajo (GE)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">


    <!-- En el <head> -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css"></style>

    <style>
        :root {
            --sidebar-width: 280px;
            --primary-color: #0d6efd;
            --sidebar-bg: #0f172a;
            --sidebar-color: #94a3b8;
            --body-bg: #f8fafc;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--body-bg);
            color: #334155;
            overflow-x: hidden;
        }

        /* Sidebar Styling */
        #sidebar-wrapper {
            min-height: 100vh;
            width: var(--sidebar-width);
            background-color: var(--sidebar-bg);
            transition: all 0.3s ease;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1000;
        }

        .sidebar-heading {
            padding: 1.25rem 1.5rem;
            color: #fff;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-heading img {
            height: 40px;
        }

        .sidebar-nav {
            padding: 1rem 0;
        }

        .sidebar-nav .nav-item {
            margin: 0.2rem 1rem;
        }

        .sidebar-nav .nav-link {
            color: var(--sidebar-color);
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .sidebar-nav .nav-link:hover, 
        .sidebar-nav .nav-link.active {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.08);
        }

        .sidebar-nav .nav-link.active {
            background-color: var(--primary-color);
        }

        /* Page Content */
        #page-content-wrapper {
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            transition: all 0.3s ease;
        }

        /* Cards & Components */
        .card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px 0 rgba(0, 0, 0, 0.03);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        /* Custom Badges */
        .badge-desempleado { background-color: #ef4444; color: #fff; }
        .badge-contratado { background-color: #10b981; color: #fff; }
        .badge-suspendido { background-color: #f59e0b; color: #fff; }
        
        .badge-pendiente { background-color: #f59e0b; color: #fff; }
        .badge-aprobado { background-color: #10b981; color: #fff; }
        .badge-revision { background-color: #3b82f6; color: #fff; }
        .badge-rechazado { background-color: #64748b; color: #fff; }

        /* Responsive */
        @media (max-width: 991.98px) {
            #sidebar-wrapper {
                margin-left: calc(-1 * var(--sidebar-width));
            }
            #page-content-wrapper {
                margin-left: 0;
                width: 100%;
            }
            #wrapper.toggled #sidebar-wrapper {
                margin-left: 0;
            }
        }


/* Personalización DataTables Moderno */
.dataTables_wrapper .dataTables_paginate .paginate_button.active .page-link {
    background-color: var(--primary-color, #0d6efd) !important;
    border-color: var(--primary-color, #0d6efd) !important;
}

.dataTables_wrapper .form-control, 
.dataTables_wrapper .form-select {
    border-radius: 0.5rem;
    font-size: 0.875rem;
    border-color: #e2e8f0;
}

.dataTables_wrapper .form-control:focus, 
.dataTables_wrapper .form-select:focus {
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
}

table.dataTable.no-footer {
    border-bottom: 1px solid #e2e8f0 !important;
}

.dataTables_info, .dataTables_paginate {
    font-size: 0.875rem;
    color: #64748b;
}


    </style>
</head>
<body>
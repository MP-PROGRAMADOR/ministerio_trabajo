<?php
// ============================================================
// MENÚ PARA EMPLEADORES - SIN VERDE EN EL ASIDE
// ============================================================

// Verificar sesión
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'empleador') {
    echo '<script>window.location.href = "../login_empleador.php";</script>';
    exit();
}

$id_usuario = $_SESSION['id_usuario'];
$nombre_empresa = $_SESSION['nombre_empresa'] ?? 'Mi Empresa';

// Incluir conexión si no está definida
if (!isset($pdo)) {
    include_once '../conexion/conexion.php';
}

// ===== OBTENER DATOS DE LA EMPRESA =====
$empresa_data = null;
$notificaciones = 0;
$iniciales = 'EM';

try {
    $stmt = $pdo->prepare("
        SELECT 
            e.id,
            e.nombre_empresa,
            e.sector_industrial,
            e.rnc_ruc,
            u.numero_expediente
        FROM empleadores e
        JOIN usuarios u ON e.usuario_id = u.id
        WHERE e.usuario_id = ?
    ");
    $stmt->execute([$id_usuario]);
    $empresa_data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($empresa_data) {
        $nombre_empresa = $empresa_data['nombre_empresa'];
        $_SESSION['nombre_empresa'] = $nombre_empresa;
        
        $palabras = explode(' ', $nombre_empresa);
        $iniciales = '';
        foreach ($palabras as $palabra) {
            if (!empty($palabra)) {
                $iniciales .= strtoupper(substr($palabra, 0, 1));
            }
        }
        $iniciales = substr($iniciales, 0, 2);
        
        $stmt_notif = $pdo->prepare("
            SELECT COUNT(*) as total 
            FROM notificaciones_intermediacion 
            WHERE empleador_id = ? AND estado_ministerio = 'pendiente'
        ");
        $stmt_notif->execute([$empresa_data['id']]);
        $result = $stmt_notif->fetch(PDO::FETCH_ASSOC);
        $notificaciones = $result['total'] ?? 0;
    }

} catch (PDOException $e) {
    error_log("Error en menú empleador: " . $e->getMessage());
    $empresa_data = null;
    $notificaciones = 0;
}

$pagina_actual = basename($_SERVER['PHP_SELF']);
$expediente = $empresa_data['rnc_ruc'] ?? $empresa_data['numero_expediente'] ?? 'N/A';
?>

<style>
    /* ===== PALETA DE COLORES ===== */
    :root {
        --gov-blue-dark: #072A45;
        --gov-blue: #0B3A60;
        --gov-blue-medium: #1A4F7A;
        --gov-blue-light: #2A6A9A;
        --gov-blue-soft: #E8EEF3;
        --gov-blue-bg: #EAF1F8;
        --gov-blue-bright: #4A8AB5;
        --gov-gold: #C9A84C;
        --gov-dark: #0A192F;
        --gov-bg: #F0F5FA;
        --gov-border: #D0DDE8;
        --gov-radius: 10px;
        --gov-radius-sm: 6px;
        --gov-shadow: rgba(11, 58, 96, 0.08);
    }

    /* ===== LAYOUT ===== */
    html, body {
        height: 100%;
        margin: 0;
        padding: 0;
        background: var(--gov-bg);
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }

    #wrapper {
        display: flex !important;
        min-height: 100vh;
        width: 100%;
        align-items: stretch;
        margin: 0;
        padding: 0;
    }

    /* ===== SIDEBAR - SOLO AZULES ===== */
    .sidebar {
        width: 240px;
        min-height: 100vh;
        background: linear-gradient(180deg, var(--gov-blue-dark) 0%, var(--gov-blue) 40%, var(--gov-blue-medium) 100%);
        color: #fff;
        padding: 1.2rem 0.8rem;
        position: sticky;
        top: 0;
        height: 100vh;
        overflow-y: auto;
        flex-shrink: 0;
        transition: all 0.3s ease;
        z-index: 1040;
        box-shadow: 2px 0 12px rgba(7, 42, 69, 0.15);
        border-right: 1px solid rgba(255,255,255,0.05);
    }
    .sidebar::-webkit-scrollbar {
        width: 3px;
    }
    .sidebar::-webkit-scrollbar-thumb {
        background: rgba(255,255,255,0.15);
        border-radius: 10px;
    }

    .sidebar-brand {
        padding: 0 0.5rem 1.2rem 0.5rem;
        border-bottom: 1px solid rgba(255,255,255,0.06);
        margin-bottom: 1.2rem;
    }
    .sidebar-brand h6 {
        font-size: 0.95rem;
        letter-spacing: -0.2px;
        margin: 0;
        color: #ffffff;
        font-weight: 700;
    }
    .sidebar-brand small {
        font-size: 0.6rem;
        opacity: 0.6;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: rgba(255,255,255,0.7);
    }
    .sidebar-brand i {
        color: var(--gov-blue-bright) !important;
        font-size: 1.6rem;
    }

    .sidebar-menu {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .sidebar-menu li {
        margin-bottom: 1px;
    }
    .sidebar-menu .nav-link {
        display: flex;
        align-items: center;
        gap: 0.7rem;
        padding: 0.6rem 0.8rem;
        border-radius: var(--gov-radius-sm);
        color: rgba(255,255,255,0.7);
        text-decoration: none;
        transition: all 0.25s ease;
        font-weight: 500;
        font-size: 0.85rem;
        position: relative;
    }
    .sidebar-menu .nav-link:hover {
        background: rgba(255,255,255,0.08);
        color: #ffffff;
    }
    .sidebar-menu .nav-link.active {
        background: rgba(255,255,255,0.12);
        color: #ffffff;
        box-shadow: inset 3px 0 0 var(--gov-blue-bright);
    }
    .sidebar-menu .nav-link i {
        font-size: 1.1rem;
        width: 1.3rem;
        text-align: center;
        flex-shrink: 0;
        color: rgba(255,255,255,0.5);
    }
    .sidebar-menu .nav-link.active i {
        color: var(--gov-blue-bright);
    }
    .sidebar-menu .nav-link .badge-notif {
        background: #dc3545;
        color: white;
        font-size: 0.55rem;
        padding: 0.1rem 0.45rem;
        border-radius: 20px;
        margin-left: auto;
        font-weight: 600;
    }
    .sidebar-menu .nav-link.text-danger {
        color: rgba(255,150,150,0.7) !important;
    }
    .sidebar-menu .nav-link.text-danger:hover {
        color: #ff6b6b !important;
        background: rgba(255,0,0,0.08);
    }
    .sidebar-menu .nav-link.text-danger i {
        color: rgba(255,150,150,0.5);
    }

    .sidebar-divider {
        border-top: 1px solid rgba(255,255,255,0.06);
        margin: 0.8rem 0.5rem;
    }

    /* ===== MAIN CONTENT ===== */
    .main-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        background: var(--gov-bg);
        overflow-x: hidden;
        width: 100%;
        padding: 0;
        margin: 0;
    }

    /* ===== TOP NAVBAR - CON MÁRGEN Y SEPARACIÓN ===== */
    .top-navbar {
        background: #ffffff;
        padding: 0.8rem 2rem;
        border-bottom: 1px solid var(--gov-border);
        flex-shrink: 0;
        box-shadow: 0 1px 4px rgba(11, 58, 96, 0.04);
        width: 100%;
        margin: 0;
        border-radius: 0;
        /* Eliminamos cualquier borde izquierdo que pueda juntarse con el aside */
        margin-left: 0;
        border-left: none;
    }
    .top-navbar h5 {
        font-size: 1rem;
        color: var(--gov-blue);
        margin: 0;
        font-weight: 700;
    }
    .top-navbar .badge {
        font-weight: 500;
        background: var(--gov-blue-soft);
        color: var(--gov-blue);
    }

    /* ===== CONTENIDO DE LA PÁGINA ===== */
    .page-content {
        flex: 1;
        padding: 1.5rem 2rem;
        width: 100%;
        max-width: 100%;
        margin: 0;
    }

    /* ===== AVATAR - SIN VERDE ===== */
    .avatar-company {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: var(--gov-blue);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.85rem;
        flex-shrink: 0;
    }

    .btn-light {
        background: var(--gov-bg);
        border: 1px solid var(--gov-border);
        border-radius: 50%;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        color: var(--gov-blue);
    }
    .btn-light:hover {
        background: var(--gov-blue);
        color: white;
        border-color: var(--gov-blue);
    }

    /* ===== DROPDOWN ===== */
    .dropdown-menu {
        border: none;
        border-radius: var(--gov-radius);
        box-shadow: 0 10px 40px rgba(0,0,0,0.10);
        padding: 0.5rem 0;
    }
    .dropdown-header {
        font-weight: 700;
        color: var(--gov-blue);
        padding: 0.5rem 1rem;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .dropdown-item {
        padding: 0.4rem 1rem;
        font-size: 0.85rem;
        color: var(--gov-dark);
        transition: all 0.15s;
    }
    .dropdown-item:hover {
        background: var(--gov-blue-soft);
        color: var(--gov-blue);
    }
    .dropdown-item.text-primary {
        color: var(--gov-blue) !important;
        font-weight: 600;
    }

    .fs-7 {
        font-size: 0.8rem;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 991.98px) {
        .sidebar {
            position: fixed;
            left: -260px;
            top: 0;
            height: 100vh;
            z-index: 1050;
            transition: left 0.3s ease;
            width: 260px;
            padding: 1rem 0.6rem;
        }
        .sidebar.show {
            left: 0;
        }
        .top-navbar {
            padding: 0.6rem 1rem;
        }
        .page-content {
            padding: 1rem;
        }
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.4);
            z-index: 1045;
        }
        .sidebar-overlay.show {
            display: block;
        }
    }

    @media (max-width: 576px) {
        .top-navbar h5 {
            font-size: 0.85rem;
        }
        .top-navbar small {
            font-size: 0.6rem;
        }
        .top-navbar .avatar-company {
            width: 32px;
            height: 32px;
            font-size: 0.7rem;
        }
        .top-navbar {
            padding: 0.5rem 0.8rem;
        }
        .page-content {
            padding: 0.8rem;
        }
        .sidebar {
            width: 240px;
            padding: 0.8rem 0.5rem;
        }
        .sidebar-menu .nav-link {
            padding: 0.5rem 0.6rem;
            font-size: 0.8rem;
        }
        .sidebar-menu .nav-link i {
            font-size: 1rem;
            width: 1.1rem;
        }
    }
</style>

<!-- ===== SIDEBAR OVERLAY ===== -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- ===== WRAPPER (CONTENEDOR PRINCIPAL) ===== -->
<div id="wrapper">

    <!-- ===== SIDEBAR - SOLO AZULES ===== -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand d-flex align-items-center gap-2">
            <i class="bi bi-building-fill-gear fs-3"></i>
            <div>
                <h6 class="fw-bold m-0 text-white">Portal Empresa</h6>
                <small class="text-white-50">Oficina Central de Empleo</small>
            </div>
        </div>

        <ul class="sidebar-menu">
            <li>
                <a href="index.php" class="nav-link <?php echo $pagina_actual == 'index.php' ? 'active' : ''; ?>">
                    <i class="bi bi-grid-1x2-fill"></i>
                    <span>Panel Principal</span>
                </a>
            </li>
            <li>
                <a href="ofertas_empleo.php" class="nav-link <?php echo $pagina_actual == 'ofertas_empleo.php' ? 'active' : ''; ?>">
                    <i class="bi bi-briefcase-fill"></i>
                    <span>Mis Ofertas</span>
                </a>
            </li>
            <li>
                <a href="candidatos.php" class="nav-link <?php echo $pagina_actual == 'candidatos.php' ? 'active' : ''; ?>">
                    <i class="bi bi-people-fill"></i>
                    <span>Postulaciones </span>
                    
                </a>
            </li>
            <li>
                <a href="datos_empresas.php" class="nav-link <?php echo $pagina_actual == 'datos_empresas.php' ? 'active' : ''; ?>">
                    <i class="bi bi-building-gear"></i>
                    <span>Datos Empresa</span>
                </a>
            </li>
            <li class="sidebar-divider"></li>
            <li>
                <a href="../php/cerrar_sesionE.php" class="nav-link text-danger">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Cerrar Sesión</span>
                </a>
            </li>
        </ul>
    </aside>

    <!-- ===== MAIN CONTENT ===== -->
    <main class="main-content">

        <!-- ===== TOP NAVBAR (CON SEPARACIÓN Y MÁRGEN) ===== -->
        <header class="top-navbar d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-light d-lg-none" id="toggleSidebar">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <div>
                    <h5 class="fw-bold m-0"><?php echo htmlspecialchars($nombre_empresa); ?></h5>
                    <small class="text-muted">
                        RUC: <span class="badge"><?php echo htmlspecialchars($expediente); ?></span>
                    </small>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <!-- Notificaciones -->
                <div class="dropdown">
                    <button class="btn btn-light rounded-circle position-relative p-2" type="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-bell fs-5"></i>
                        <?php if ($notificaciones > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                                <span class="visually-hidden"><?php echo $notificaciones; ?> notificaciones</span>
                            </span>
                        <?php endif; ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="width: 280px;">
                        <li>
                            <h6 class="dropdown-header">Notificaciones</h6>
                        </li>
                        <?php if ($notificaciones > 0): ?>
                            <li>
                                <a class="dropdown-item text-primary" href="candidatos.php">
                                    <i class="bi bi-clock-history me-2"></i>
                                    Tienes <?php echo $notificaciones; ?> solicitud(es) pendiente(s)
                                </a>
                            </li>
                        <?php else: ?>
                            <li>
                                <a class="dropdown-item text-muted" href="#">
                                    <i class="bi bi-check-circle me-2"></i>
                                    Sin notificaciones pendientes
                                </a>
                            </li>
                        <?php endif; ?>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-center small text-muted" href="alertas.php">
                                Ver todas las alertas
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Avatar empresa - SIN VERDE -->
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar-company"><?php echo htmlspecialchars($iniciales); ?></div>
                    <div class="d-none d-md-block">
                        <p class="mb-0 fw-semibold fs-7"><?php echo htmlspecialchars($nombre_empresa); ?></p>
                        <small class="text-muted d-block" style="font-size: 0.7rem;">
                            <?php echo htmlspecialchars($empresa_data['sector_industrial'] ?? 'Empresa'); ?>
                        </small>
                    </div>
                </div>
            </div>
        </header>

        <!-- ===== CONTENIDO DE LA PÁGINA ===== -->
        <div class="page-content">
            <!-- Aquí se inyecta el contenido de cada página -->
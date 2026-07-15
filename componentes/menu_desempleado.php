<?php
// ============================================================
// MENÚ PARA DESEMPLEADOS - DINÁMICO DESDE BBDD
// ============================================================

// Verificar sesión - Usando la variable que ya existe en tu login
if (!isset($_SESSION['id_usuario'])) {
    echo '<script>window.location.href = "../login_desempleados.php";</script>';
    exit();
}

// Usar las variables que YA tienes en la sesión
$id_usuario = $_SESSION['id_usuario'];
$nombre_completo = $_SESSION['nombre_completo'] ?? 'Usuario';

// Incluir conexión si no está definida
if (!isset($pdo)) {
    include_once '../conexion/conexion.php';
}

$perfil_completo = false;
$buscador = null;
$notificaciones = 0;
$foto_perfil = 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/icons/person-bounding-box.svg';

try {
    // Verificar buscadores_empleo
    $stmt = $pdo->prepare("SELECT * FROM buscadores_empleo WHERE usuario_id = ?");
    $stmt->execute([$id_usuario]);
    $buscador = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar documentos
    $stmt_doc = $pdo->prepare("SELECT id FROM documentos WHERE usuario_id = ?");
    $stmt_doc->execute([$id_usuario]);
    $documentos = $stmt_doc->fetch();

    $perfil_completo = ($buscador && $documentos);

    // ===== CONTAR NOTIFICACIONES =====
    if ($buscador) {
        // 1. Notificaciones de intermediación
        $stmt_notif = $pdo->prepare("SELECT COUNT(*) as total FROM notificaciones_intermediacion WHERE buscador_id = ?");
        $stmt_notif->execute([$buscador['id']]);
        $result = $stmt_notif->fetch(PDO::FETCH_ASSOC);
        $notificaciones = is_array($result) ? (int)($result['total'] ?? 0) : 0;

        // 2. Favoritos
        $stmt_fav = $pdo->prepare("SELECT COUNT(*) as total FROM favoritos WHERE buscador_id = ?");
        $stmt_fav->execute([$buscador['id']]);
        $result = $stmt_fav->fetch(PDO::FETCH_ASSOC);
        $notificaciones += is_array($result) ? (int)($result['total'] ?? 0) : 0;
    }

    // ===== FOTO DE PERFIL =====
    if ($buscador && !empty($buscador['foto_carnet'])) {
        $foto_perfil = $buscador['foto_carnet'];
        if (strpos($foto_perfil, 'http') === false) {
            $foto_perfil = '../' . $foto_perfil;
        }
    }
} catch (PDOException $e) {
    error_log("Error en menú: " . $e->getMessage());
}
?>

<style>
    /* ===== ESTILOS DEL MENÚ ===== */
    :root {
        --gov-blue: #0B3A60;
        --gov-blue-light: #1A4F7A;
        --gov-blue-dark: #072A45;
        --gov-blue-soft: #E8EEF3;
        --gov-green: #1E7E34;
        --gov-green-light: #2E9B4A;
        --gov-green-soft: #E6F3E8;
        --gov-gold: #C9A84C;
        --gov-gold-light: #E8D5A3;
        --gov-dark: #0A192F;
        --gov-bg: #F8FAFC;
        --gov-border: #E2E8F0;
        --gov-radius: 8px;
        --gov-radius-sm: 4px;
        --gov-shadow: rgba(11, 58, 96, 0.12);
    }
     body, html {
        min-height: 100vh;
        margin: 0;
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        background-color: var(--gov-bg);
        color: var(--gov-dark);
        position: relative;
    }
    #canvas-background {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 0;
        pointer-events: none;
        opacity: 0.3;
    }
    .main-wrapper {
        position: relative;
        z-index: 1;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    .navbar-portal {
        background: rgba(255, 255, 255, 0.96) !important;
        backdrop-filter: blur(12px);
        border-bottom: 3px solid var(--gov-blue);
        box-shadow: 0 4px 20px rgba(11, 58, 96, 0.04);
        position: relative;
        z-index: 1050;
    }
    .navbar-nav .nav-link {
        font-weight: 500;
        color: var(--gov-dark) !important;
        padding: 0.5rem 1rem;
        border-radius: var(--gov-radius-sm);
        transition: all 0.2s;
    }
    .navbar-nav .nav-link:hover,
    .navbar-nav .nav-link.active {
        background: rgba(11, 58, 96, 0.06);
        color: var(--gov-blue) !important;
    }
    .navbar-nav .nav-link.active {
        background: rgba(11, 58, 96, 0.10);
        font-weight: 600;
    }
    /* ===== DROPDOWN PERFIL ===== */

    .profile-dropdown { position: relative !important; }
    .profile-menu-img {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        object-fit: cover;
        cursor: pointer;
        transition: border-color 0.3s, transform 0.2s;
    }
    .profile-menu-img:hover {
        border-color: var(--gov-blue);
        transform: scale(1.04);
    }
    .custom-profile-menu {
        background: rgba(255, 255, 255, 0.98) !important;
        backdrop-filter: blur(12px);
        border: 1px solid rgba(11, 58, 96, 0.15) !important;
        border-radius: var(--gov-radius) !important;
        box-shadow: 0 12px 40px rgba(11, 58, 96, 0.12) !important;
        padding: 8px !important;
        z-index: 1060 !important;
        min-width: 240px;
    }
    .custom-profile-menu .dropdown-item {
        border-radius: var(--gov-radius-sm);
        padding: 0.6rem 1rem;
        font-weight: 500;
        color: var(--gov-dark);
        transition: all 0.15s;
    }
    .custom-profile-menu .dropdown-item:hover {
        background: rgba(11, 58, 96, 0.05);
        color: var(--gov-blue);
    }
    .custom-profile-menu .dropdown-item i { color: var(--gov-blue); }
    

    @media (min-width: 992px) {
        .custom-profile-menu {
            position: absolute !important;
            right: 0 !important;
            left: auto !important;
            transform: translateY(8px) !important;
        }
        .profile-dropdown:hover .custom-profile-menu {
            display: block !important;
            opacity: 1 !important;
            visibility: visible !important;
        }
    }
    @media (max-width: 991.98px) {
        .custom-profile-menu {
            position: absolute !important;
            left: 50% !important;
            right: auto !important;
            transform: translate(-50%, 10px) !important;
            width: 260px !important;
            max-width: calc(100vw - 30px) !important;
        }
        .profile-dropdown:hover .custom-profile-menu {
            display: block !important;
            opacity: 1 !important;
            visibility: visible !important;
        }
    }
</style>

<nav class="navbar navbar-expand-lg navbar-light navbar-portal py-2">
    <div class="container">
      
            <img src="../src/img/logo_nuevo.png" alt="Escudo de Guinea Ecuatorial" style="height: 45px; width: auto; object-fit: contain;" onerror="this.src='https://placehold.co/45x50?text=Logo'">
            <div class="d-flex flex-column lh-1 border-start ps-3 border-secondary border-opacity-25">
                <span style="color: var(--gov-dark); font-size: 1.15rem; letter-spacing: -0.3px; font-weight: 700; margin-bottom: 3px;">PortalEmpleo</span>
                <span class="text-muted" style="font-size: 0.62rem; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">Oficina Central de Empleo</span>
            </div>
        
        <div class="d-flex align-items-center order-lg-last gap-3">
            <a href="notificaciones.php" class="text-muted position-relative me-1" title="Notificaciones">
                <i class="bi bi-bell fs-5" style="color: var(--gov-blue);"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem; margin-top: -2px;"><?php echo $notificaciones; ?></span>
            </a>

            <div class="nav-item dropdown profile-dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="profileMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="<?php echo htmlspecialchars($foto_perfil); ?>"
                        class="profile-menu-img"
                        alt="Foto de perfil"
                        onerror="this.src='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/icons/person-bounding-box.svg'">
                </a>
                <ul class="dropdown-menu dropdown-menu-end custom-profile-menu" aria-labelledby="profileMenu">
                    <li>
                        <div class="px-3 py-2 border-bottom mb-2 bg-light rounded-top">
                            <p class="m-0 small fw-bold text-dark"><?php echo htmlspecialchars($nombre_completo); ?></p>
                            <p class="m-0 <?php echo $perfil_completo ? 'text-success' : 'text-warning'; ?> fw-bold" style="font-size: 0.7rem;">
                                <i class="bi bi-circle-fill me-1" style="font-size: 0.4rem;"></i>
                                <?php echo $perfil_completo ? '✅ Perfil Completado' : '⚠️ Perfil Pendiente'; ?>
                            </p>
                            <?php if ($buscador): ?>
                                <p class="m-0 text-muted" style="font-size: 0.65rem;">
                                    <i class="bi bi-person me-1"></i>
                                    <?php echo ucfirst($buscador['estado_laboral'] ?? 'desempleado'); ?>
                                    <?php if (!empty($buscador['ciudad_municipio'])): ?>
                                        · <i class="bi bi-geo-alt me-1"></i><?php echo ucfirst(str_replace('_', ' ', $buscador['ciudad_municipio'])); ?>
                                    <?php endif; ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </li>
                    <li><a class="dropdown-item small py-2" href="perfil.php"><i class="bi bi-person-gear me-2 text-muted"></i> Mi Perfil</a></li>
                    <li><a class="dropdown-item small py-2" href="notificaciones.php"><i class="bi bi-bell me-2 text-muted"></i> Mis Notificaciones</a></li>
                    <li><a class="dropdown-item small py-2" href="historial_laboral.php"><i class="bi bi-file-earmark-arrow-down me-2 text-muted"></i> Mi Historial Laboral</a></li>
                    <?php if (!$perfil_completo): ?>
                        <li><a class="dropdown-item small py-2 text-danger fw-bold" href="completar_perfil.php">
                                <i class="bi bi-exclamation-triangle-fill me-2 text-danger"></i> Completar Perfil
                            </a></li>
                    <?php endif; ?>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item small text-danger fw-bold py-2" href="../php/cerrar_sesion.php">
                            <i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión
                        </a></li>
                </ul>
            </div>
        </div>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto align-items-lg-center ms-lg-4 gap-1">
                <li class="nav-item">
                    <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active fw-bold' : 'text-secondary small fw-medium' ?>"
                        style="<?= (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'color: var(--gov-blue);' : '' ?>"
                        href="index.php">Panel General</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'bolsa_trabajo.php') ? 'active fw-bold' : 'text-secondary small fw-medium' ?>"
                        style="<?= (basename($_SERVER['PHP_SELF']) == 'bolsa_trabajo.php') ? 'color: var(--gov-blue);' : '' ?>"
                        href="bolsa_trabajo.php">Bolsa de Trabajo</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'cursos_publicos.php') ? 'active fw-bold' : 'text-secondary small fw-medium' ?>"
                        style="<?= (basename($_SERVER['PHP_SELF']) == 'cursos_publicos.php') ? 'color: var(--gov-blue);' : '' ?>"
                        href="cursos_publicos.php">Cursos Públicos</a>
                </li>
                <?php if (!$perfil_completo): ?>
                    <li class="nav-item">
                        <a class="nav-link text-danger fw-bold small" href="completar_perfil.php">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i> Completar Perfil
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
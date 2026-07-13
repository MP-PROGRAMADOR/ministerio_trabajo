<?php
// ============================================================
// MENÚ ADMINISTRADOR - DINÁMICO Y CON ENLACE ACTIVO
// ============================================================

// Obtener el nombre del archivo actual
$pagina_actual = basename($_SERVER['PHP_SELF']);

// Datos del usuario (deben venir de la sesión)
$nombre_usuario = $_SESSION['nombre_usuario'] ?? $_SESSION['nombre_completo'] ?? 'Admin';

// ===== CONTAR NOTIFICACIONES PENDIENTES =====
$notificaciones_pendientes = 0;
try {
    if (isset($pdo)) {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM notificaciones_intermediacion WHERE estado_ministerio = 'pendiente'");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $notificaciones_pendientes = $result['total'] ?? 0;
    }
} catch (PDOException $e) {
    $notificaciones_pendientes = 0;
}
?>

<div id="sidebar-wrapper">
    <div class="sidebar-heading d-flex align-items-center gap-2">
        <img src="../src/img/logo_,ministerio.png" alt="Logo Ministerio" class="img-fluid" style="max-height: 40px; width: auto;">
        <div>
            <h6 class="mb-0 text-white fw-bold text-center">MINISTERIO DE TRABAJO</h6>
            <small class="text-center" style="font-size: 10px; color: gray">GUINEA ECUATORIAL</small>
        </div>
    </div>

    <ul class="nav flex-column sidebar-nav">
        <!-- Panel General -->
        <li class="nav-item">
            <a class="nav-link <?php echo ($pagina_actual == 'index.php') ? 'active' : ''; ?>" href="index.php">
                <i class="bi bi-grid-1x2-fill"></i> Panel General
            </a>
        </li>

        <!-- Intermediaciones -->
        <li class="nav-item">
            <a class="nav-link <?php echo ($pagina_actual == 'intermediaciones.php') ? 'active' : ''; ?>" href="intermediaciones.php">
                <i class="bi bi-shield-check"></i> Intermediaciones
                <?php if ($notificaciones_pendientes > 0): ?>
                    <span class="badge bg-danger ms-auto"><?php echo $notificaciones_pendientes; ?></span>
                <?php endif; ?>
            </a>
        </li>

        <!-- Buscadores de Empleo -->
        <li class="nav-item">
            <a class="nav-link <?php echo ($pagina_actual == 'buscadores_empleo.php') ? 'active' : ''; ?>" href="buscadores_empleo.php">
                <i class="bi bi-people-fill"></i> Buscadores de Empleo
            </a>
        </li>

        <!-- Empresas y Empleadores -->
        <li class="nav-item">
            <a class="nav-link <?php echo ($pagina_actual == 'empleadores.php') ? 'active' : ''; ?>" href="empleadores.php">
                <i class="bi bi-briefcase-fill"></i> Empresas y Empleadores
            </a>
        </li>

        <!-- Ofertas de Empleo -->
        <li class="nav-item">
            <a class="nav-link <?php echo ($pagina_actual == 'ofertas_admin.php') ? 'active' : ''; ?>" href="ofertas_admin.php">
                <i class="bi bi-card-checklist"></i> Ofertas de Empleo
            </a>
        </li>

        <!-- Capacitación / Cursos -->
        <li class="nav-item">
            <a class="nav-link <?php echo ($pagina_actual == 'capacitaciones.php') ? 'active' : ''; ?>" href="capacitaciones.php">
                <i class="bi bi-journal-bookmark-fill"></i> Capacitación / Cursos
            </a>
        </li>

        <!-- Entidades / Centros -->
        <li class="nav-item">
            <a class="nav-link <?php echo ($pagina_actual == 'entidades.php') ? 'active' : ''; ?>" href="entidades.php">
                <i class="bi bi-building-fill"></i> Entidades / Centros
            </a>
        </li>

        <!-- Separador Administración -->
        <li class="nav-item mt-3">
            <small class="text-uppercase  px-3 font-monospace fs-7">Administración</small>
        </li>

        <!-- Control de Usuarios -->
        <li class="nav-item">
            <a class="nav-link <?php echo ($pagina_actual == 'usuarios.php') ? 'active' : ''; ?>" href="usuarios.php">
                <i class="bi bi-person-gear"></i> Control de Usuarios
            </a>
        </li>

        <!-- Cerrar Sesión -->
        <li class="nav-item">
            <a class="nav-link text-danger" href="../php/cerrar_sesionA.php">
                <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
            </a>
        </li>
    </ul>
</div>

<div id="page-content-wrapper">
    
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom px-4 py-3 sticky-top">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-light d-lg-none" id="menu-toggle"><i class="bi bi-list"></i></button>
            <h5 class="mb-0 fw-bold">Portal de Intermediación Laboral</h5>
        </div>

        <div class="ms-auto d-flex align-items-center gap-3">
            <!-- Notificaciones -->
            <div class="dropdown">
                <button class="btn btn-light rounded-circle position-relative" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-bell"></i>
                    <?php if ($notificaciones_pendientes > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            <?php echo $notificaciones_pendientes; ?>
                        </span>
                    <?php endif; ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2">
                    <li><h6 class="dropdown-header">Notificaciones Pendientes</h6></li>
                    <?php if ($notificaciones_pendientes > 0): ?>
                        <li>
                            <a class="dropdown-item small" href="intermediaciones.php">
                                Tienes <?php echo $notificaciones_pendientes; ?> solicitud(es) pendiente(s) de revisión
                            </a>
                        </li>
                    <?php else: ?>
                        <li><a class="dropdown-item small text-muted" href="#">No hay notificaciones pendientes</a></li>
                    <?php endif; ?>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item small text-center text-muted" href="intermediaciones.php">Ver todas las intermediaciones</a></li>
                </ul>
            </div>

            <div class="vr"></div>

            <!-- Perfil Usuario -->
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle gap-2" data-bs-toggle="dropdown">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <strong><?php echo strtoupper(substr($nombre_usuario, 0, 2)); ?></strong>
                    </div>
                    <div class="d-none d-md-block text-start">
                        <span class="d-block fw-semibold text-dark fs-7"><?php echo htmlspecialchars($nombre_usuario); ?></span>
                        <small class="text-muted fs-8">Ministerio de Trabajo</small>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2">
                    <li><a class="dropdown-item" href="perfil_admin.php"><i class="bi bi-person me-2"></i> Mi Perfil</a></li>
                    <li><a class="dropdown-item" href="configuracion.php"><i class="bi bi-gear me-2"></i> Ajustes</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="../php/cerrar_sesionA.php"><i class="bi bi-box-arrow-right me-2"></i> Salir</a></li>
                </ul>
            </div>
        </div>
    </nav>
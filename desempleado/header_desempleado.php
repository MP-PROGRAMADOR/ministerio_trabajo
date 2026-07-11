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

// Si pasa los dos filtros anteriores, el usuario es un buscador válido y puede ver la página
?>
<nav class="navbar navbar-expand-lg navbar-light navbar-portal py-2">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center gap-3" href="index.php">
            <img src="../img/logo_,ministerio.png" alt="Escudo de Guinea Ecuatorial" style="height: 45px; width: auto; object-fit: contain;" onerror="this.src='https://placehold.co/45x50?text=Logo'">
            <div class="d-flex flex-column lh-1 border-start ps-3 border-secondary border-opacity-25">
                <span style="color: var(--gov-dark); font-size: 1.15rem; letter-spacing: -0.3px; font-weight: 700;">PortalEmpleo</span>
                <span class="text-muted" style="font-size: 0.62rem; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">Min. de Trabajo, Fomento del Empleo y Seguridad Social</span>
            </div>
        </a>

        <div class="d-flex align-items-center order-lg-last gap-3">
            <a href="#" class="text-muted position-relative me-1" title="Notificaciones">
                <i class="bi bi-bell fs-5" style="color: var(--gov-blue);"></i>
                <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-white rounded-circle"></span>
            </a>

            <div class="nav-item dropdown profile-dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="profileMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=150&q=80" class="profile-menu-img" alt="Foto de perfil">
                </a>
                <ul class="dropdown-menu dropdown-menu-end custom-profile-menu" aria-labelledby="profileMenu">
                    <li>
                        <div class="px-3 py-2 border-bottom mb-2 bg-light rounded-top">
                            <p class="m-0 small fw-bold text-dark">Ana Trini Maye</p>
                            <p class="m-0 text-success fw-bold" style="font-size: 0.7rem;"><i class="bi bi-circle-fill me-1" style="font-size: 0.4rem;"></i> Buscador Activo</p>
                        </div>
                    </li>
                    <li><a class="dropdown-item small py-2" href="perfil.php"><i class="bi bi-person-gear me-2 text-muted"></i> Mi Perfil</a></li>
                    <li><a class="dropdown-item small py-2" href="historial_laboral.php"><i class="bi bi-file-earmark-arrow-down me-2 text-muted"></i> Mi Historial Laboral</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item small text-danger fw-bold py-2" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión</a></li>
                </ul>
            </div>
        </div>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto align-items-lg-center ms-lg-4 gap-1">
                <li class="nav-item"><a class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active fw-bold' : 'text-secondary small fw-medium' ?>" style="<?= (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'color: var(--gov-blue);' : '' ?>" href="index.php">Panel General</a></li>
                <li class="nav-item"><a class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'bolsa_trabajo.php') ? 'active fw-bold' : 'text-secondary small fw-medium' ?>" style="<?= (basename($_SERVER['PHP_SELF']) == 'bolsa_trabajo.php') ? 'color: var(--gov-blue);' : '' ?>" href="bolsa_trabajo.php">Bolsa de Trabajo</a></li>
                <li class="nav-item"><a class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'cursos_publicos.php') ? 'active fw-bold' : 'text-secondary small fw-medium' ?>" style="<?= (basename($_SERVER['PHP_SELF']) == 'cursos_publicos.php') ? 'color: var(--gov-blue);' : '' ?>" href="cursos_publicos.php">Cursos Públicos</a></li>
            </ul>
        </div>
    </div>
</nav>
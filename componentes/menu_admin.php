  <div id="sidebar-wrapper">
       <div class="sidebar-heading d-flex align-items-center gap-2">
    <!-- Reemplazamos el icono por tu logo -->
    <img src="../src/img/logo_,ministerio.png" alt="Logo Ministerio" class="img-fluid" style="max-height: 40px; width: auto;">
    
    <div>
        <h6 class="mb-0 text-white fw-bold">MINISTERIO DE TRABAJO</h6>
        <small class="text-muted" style="font-size: 10px;">GUINEA ECUATORIAL</small>
    </div>
</div>

        <ul class="nav flex-column sidebar-nav">
            <li class="nav-item">
                <a class="nav-link active" href="index.php"><i class="bi bi-grid-1x2-fill"></i> Panel General</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="intermediaciones.php"><i class="bi bi-shield-check"></i> Intermediaciones <span class="badge bg-danger ms-auto">3</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#buscadores"><i class="bi bi-people-fill"></i> Buscadores de Empleo</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#empleadores"><i class="bi bi-briefcase-fill"></i> Empresas y Empleadores</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#ofertas"><i class="bi bi-card-checklist"></i> Ofertas de Empleo</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="capacitaciones.php"><i class="bi bi-journal-bookmark-fill"></i> Capacitación / Cursos</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="entidades.php"><i class="bi bi-journal-bookmark-fill"></i> Entidades / Centros</a>
            </li>
            <li class="nav-item mt-3">
                <small class="text-uppercase text-muted px-3 font-monospace fs-7">Administración</small>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="usuarios.php"><i class="bi bi-person-gear"></i> Control de Usuarios</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-danger" href="#logout"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a>
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
                <div class="dropdown">
                    <button class="btn btn-light rounded-circle position-relative" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-bell"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            3
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2">
                        <li><h6 class="dropdown-header">Notificaciones Pendientes</h6></li>
                        <li><a class="dropdown-item small" href="#">Nueva solicitud de intermediación: <strong>MITRAD-2026-X9</strong></a></li>
                        <li><a class="dropdown-item small" href="#">Empresa registrada: <strong>GETESA</strong></a></li>
                    </ul>
                </div>

                <div class="vr"></div>

                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle gap-2" data-bs-toggle="dropdown">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                            <strong>MIN</strong>
                        </div>
                        <div class="d-none d-md-block text-start">
                            <span class="d-block fw-semibold text-dark fs-7"><?= $nombre_usuario; ?></span>
                            <small class="text-muted fs-8">Ministerio de Trabajo</small>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2">
                        <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i> Mi Perfil</a></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i> Ajustes</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="../php/cerrar_sesionA.php"><i class="bi bi-box-arrow-right me-2"></i> Salir</a></li>
                    </ul>
                </div>
            </div>
        </nav>
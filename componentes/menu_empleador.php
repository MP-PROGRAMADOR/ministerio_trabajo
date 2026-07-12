<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand d-flex align-items-center gap-2">
        <i class="bi bi-building-fill-gear text-primary fs-3"></i>
        <div>
            <h6 class="fw-bold m-0 text-white">Portal Empresa</h6>
            <small class="text-white-50" style="font-size: 0.75rem;">Ministerio de Trabajo</small>
        </div>
    </div>

    <ul class="sidebar-menu">
        <li>
            <a href="index.php" class="nav-link active">
                <i class="bi bi-grid-1x2-fill"></i>
                <span>Panel Principal</span>
            </a>
        </li>
        <li>
            <a href="ofertas_empleo.php" class="nav-link">
                <i class="bi bi-briefcase-fill"></i>
                <span>Mis Ofertas de Empleo</span>
            </a>
        </li>
        <li>
            <a href="#intermediacion" class="nav-link">
                <i class="bi bi-people-fill"></i>
                <span>Candidatos / Alertas</span>
            </a>
        </li>
        <li>
            <a href="#perfil" class="nav-link">
                <i class="bi bi-building-gear"></i>
                <span>Datos de la Empresa</span>
            </a>
        </li>
        <li class="mt-4 border-top border-secondary pt-3">
            <a href="../php/cerrar_sesionE.php" class="nav-link text-danger">
                <i class="bi bi-box-arrow-right"></i>
                <span>Cerrar Sesión</span>
            </a>
        </li>
    </ul>
</aside>

<main class="main-content">

    <header class="top-navbar d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-light d-lg-none" id="toggleSidebar">
                <i class="bi bi-list fs-5"></i>
            </button>
            <div>
                <h5 class="fw-bold m-0">¡Bienvenido, GETESA!</h5>
                <small class="text-muted">Expediente Empresa: <span
                        class="badge bg-light text-dark border">EG-48291</span></small>
            </div>
        </div>

        <div class="d-flex align-items-center gap-3">
            <div class="dropdown">
                <button class="btn btn-light rounded-circle position-relative p-2" type="button"
                    data-bs-toggle="dropdown">
                    <i class="bi bi-bell fs-5"></i>
                    <span
                        class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="width: 280px;">
                    <li>
                        <h6 class="dropdown-header">Notificaciones de Intermediación</h6>
                    </li>
                    <li><a class="dropdown-header small text-primary" href="#">El Ministerio aprobó la solicitud
                            #MITRAD-2026-X9</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item text-center small text-muted" href="#">Ver todas las alertas</a></li>
                </ul>
            </div>

            <div class="d-flex align-items-center gap-2">
                <div class="avatar-company">GE</div>
                <div class="d-none d-md-block">
                    <p class="mb-0 fw-semibold fs-7">GETESA S.A.</p>
                    <small class="text-muted d-block" style="font-size: 0.75rem;">Telecomunicaciones</small>
                </div>
            </div>
        </div>
    </header>
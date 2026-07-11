<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bolsa de Trabajo - Portal de Empleo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        /* ===== PALETA INSTITUCIONAL UNIFICADA ===== */
        :root {
            --gov-blue: #0B3A60;
            --gov-blue-light: #165285;
            --gov-gold: #C9A84C;
            --gov-gold-light: #E8D5A3;
            --gov-green: #1E7E34;
            --gov-green-light: #2E9B4A;
            --gov-dark: #0A192F;
            --gov-bg: #F8FAFC;
            --gov-border: #E2E8F0;
            --gov-radius: 8px;
            --gov-radius-sm: 4px;
        }

        body,
        html {
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

        /* ===== NAVBAR CON BORDE AZUL OSCURO ===== */
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

        /* ===== TARJETAS ===== */
        .dashboard-card {
            background: #ffffff;
            border: 1px solid var(--gov-border);
            border-radius: var(--gov-radius);
            box-shadow: 0 4px 12px rgba(11, 58, 96, 0.015);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .dashboard-card:hover {
            border-color: var(--gov-gold);
            box-shadow: 0 10px 25px rgba(11, 58, 96, 0.04);
            transform: translateY(-1px);
        }

        /* ===== TARJETAS DE OFERTA ===== */
        .job-card {
            background: #ffffff;
            border: 1px solid var(--gov-border);
            border-radius: var(--gov-radius);
            padding: 1.25rem 1.5rem;
            transition: all 0.25s;
            position: relative;
            overflow: hidden;
        }

        .job-card:hover {
            border-color: var(--gov-blue);
            box-shadow: 0 6px 20px rgba(11, 58, 96, 0.08);
            transform: translateY(-2px);
        }

        .job-card .card-accent {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
        }

        .job-card .card-accent.blue {
            background: var(--gov-blue);
        }
        .job-card .card-accent.gold {
            background: var(--gov-gold);
        }
        .job-card .card-accent.green {
            background: var(--gov-green);
        }

        .job-card .job-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--gov-dark);
        }

        .job-card .company-name {
            font-weight: 600;
            color: var(--gov-blue);
        }

        .job-card .job-meta {
            font-size: 0.85rem;
            color: #6b7a8a;
        }

        .job-card .job-meta i {
            width: 1.2rem;
            color: var(--gov-gold);
        }

        .job-card .salary-badge {
            background: rgba(11, 58, 96, 0.08);
            color: var(--gov-blue);
            font-weight: 600;
            padding: 0.25rem 0.9rem;
            border-radius: 20px;
            font-size: 0.85rem;
            border: 1px solid rgba(11, 58, 96, 0.1);
        }

        .badge-blue {
            background: var(--gov-blue);
            color: white;
            font-weight: 600;
            padding: 0.3rem 0.9rem;
            border-radius: 20px;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .badge-gold {
            background: var(--gov-gold);
            color: white;
            font-weight: 600;
            padding: 0.3rem 0.9rem;
            border-radius: 20px;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .badge-green {
            background: var(--gov-green);
            color: white;
            font-weight: 600;
            padding: 0.3rem 0.9rem;
            border-radius: 20px;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .badge-urgent {
            background: #DC3545;
            color: white;
            font-weight: 600;
            padding: 0.3rem 0.9rem;
            border-radius: 20px;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .badge-soft-blue {
            background: rgba(11, 58, 96, 0.08);
            color: var(--gov-blue);
            font-weight: 500;
            padding: 0.3rem 0.9rem;
            border-radius: 20px;
            font-size: 0.75rem;
        }

        /* ===== BOTONES ===== */
        .btn-blue {
            background: var(--gov-blue);
            border: none;
            color: white;
            border-radius: var(--gov-radius-sm);
            padding: 0.6rem 1.8rem;
            font-weight: 600;
            transition: all 0.25s;
            box-shadow: 0 4px 12px rgba(11, 58, 96, 0.15);
        }

        .btn-blue:hover {
            background: var(--gov-blue-light);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(11, 58, 96, 0.20);
            color: white;
        }

        .btn-gold {
            background: var(--gov-gold);
            border: none;
            color: white;
            border-radius: var(--gov-radius-sm);
            padding: 0.6rem 1.8rem;
            font-weight: 600;
            transition: all 0.25s;
            box-shadow: 0 4px 12px rgba(201, 168, 76, 0.15);
        }

        .btn-gold:hover {
            background: #B8953A;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(201, 168, 76, 0.20);
            color: white;
        }

        .btn-green {
            background: var(--gov-green);
            border: none;
            color: white;
            border-radius: var(--gov-radius-sm);
            padding: 0.6rem 1.8rem;
            font-weight: 600;
            transition: all 0.25s;
            box-shadow: 0 4px 12px rgba(30, 126, 52, 0.15);
        }

        .btn-green:hover {
            background: var(--gov-green-light);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(30, 126, 52, 0.20);
            color: white;
        }

        .btn-outline-blue {
            border: 2px solid var(--gov-blue);
            color: var(--gov-blue);
            background: transparent;
            border-radius: var(--gov-radius-sm);
            font-weight: 600;
            transition: all 0.25s;
        }

        .btn-outline-blue:hover {
            background: var(--gov-blue);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(11, 58, 96, 0.20);
        }

        .btn-outline-gold {
            border: 2px solid var(--gov-gold);
            color: var(--gov-gold);
            background: transparent;
            border-radius: var(--gov-radius-sm);
            font-weight: 600;
            transition: all 0.25s;
        }

        .btn-outline-gold:hover {
            background: var(--gov-gold);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(201, 168, 76, 0.30);
        }

        .btn-outline-secondary {
            border: 2px solid var(--gov-border);
            color: var(--gov-blue);
            background: transparent;
            border-radius: var(--gov-radius-sm);
            font-weight: 500;
            transition: all 0.25s;
        }

        .btn-outline-secondary:hover {
            background: var(--gov-blue);
            border-color: #cbd5e1;
        }

        /* ===== FILTROS ===== */
        .form-control-search {
            border-radius: var(--gov-radius-sm) !important;
            border: 1.5px solid var(--gov-border);
            padding: 0.7rem 1.2rem;
            font-size: 0.95rem;
            transition: border-color 0.3s, box-shadow 0.3s;
            width: 100%;
        }

        .form-control-search:focus {
            border-color: var(--gov-blue);
            box-shadow: 0 0 0 4px rgba(11, 58, 96, 0.12);
        }

        .form-select-custom {
            border-radius: var(--gov-radius-sm);
            border: 1.5px solid var(--gov-border);
            padding: 0.7rem 1.2rem;
            font-size: 0.95rem;
            transition: border-color 0.3s;
        }

        .form-select-custom:focus {
            border-color: var(--gov-gold);
            box-shadow: 0 0 0 4px rgba(201, 168, 76, 0.12);
        }

        /* ===== PAGINACIÓN ===== */
        .pagination-custom .page-link {
            border-radius: var(--gov-radius-sm) !important;
            margin: 0 4px;
            border: 1px solid var(--gov-border);
            color: var(--gov-blue);
            transition: all 0.2s;
        }

        .pagination-custom .page-link:hover {
            background: var(--gov-gold);
            color: white;
            border-color: var(--gov-gold);
        }

        .pagination-custom .page-item.active .page-link {
            background: var(--gov-blue);
            border-color: var(--gov-blue);
            color: white;
        }

        /* ===== MODAL ===== */
        .modal-content {
            border-radius: var(--gov-radius);
            border: none;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }

        .modal-header {
            border-bottom: 2px solid var(--gov-gold-light);
            background: rgba(255, 255, 255, 0.95);
            padding: 1.5rem 2rem;
        }

        .modal-header .modal-title {
            font-weight: 700;
            color: var(--gov-blue);
        }

        .modal-body {
            padding: 2rem;
            background: #FAFBFC;
        }

        .modal-body .detail-row {
            display: flex;
            align-items: baseline;
            padding: 0.6rem 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.04);
        }

        .modal-body .detail-row:last-child {
            border-bottom: none;
        }

        .modal-body .detail-label {
            font-weight: 600;
            color: var(--gov-dark);
            width: 130px;
            flex-shrink: 0;
            font-size: 0.9rem;
        }

        .modal-body .detail-value {
            color: #2D3A4A;
            font-size: 0.95rem;
        }

        .modal-body .detail-value i {
            color: var(--gov-gold);
            margin-right: 6px;
            width: 1.2rem;
        }

        .modal-body .salary-highlight {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--gov-gold);
        }

        .modal-body .requirements-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .modal-body .requirements-list li {
            padding: 0.3rem 0;
            padding-left: 1.6rem;
            position: relative;
        }

        .modal-body .requirements-list li::before {
            content: "▸";
            color: var(--gov-gold);
            font-weight: 700;
            position: absolute;
            left: 0;
        }

        .modal-footer {
            border-top: 1px solid rgba(0, 0, 0, 0.05);
            background: white;
            padding: 1.2rem 2rem;
        }

        /* ===== PERFIL DESPLEGABLE ===== */
        .profile-menu-img {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            border: 2.5px solid var(--gov-gold);
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
            border: 1px solid rgba(201, 168, 76, 0.25) !important;
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

        .custom-profile-menu .dropdown-item i {
            color: var(--gov-gold);
        }

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

        /* ===== FOOTER ===== */
        footer {
            background: rgba(255, 255, 255, 0.9) !important;
            backdrop-filter: blur(8px);
            border-top: 1px solid rgba(201, 168, 76, 0.15);
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 576px) {
            .dashboard-card {
                padding: 1.5rem !important;
            }
            .job-card {
                padding: 1rem !important;
            }
            .modal-body {
                padding: 1.2rem;
            }
            .modal-header {
                padding: 1.2rem;
            }
            .modal-body .detail-label {
                width: 100%;
                margin-bottom: 2px;
            }
            .modal-body .detail-row {
                flex-wrap: wrap;
            }
        }
    </style>
</head>

<body>

    <canvas id="canvas-background"></canvas>

    <div class="main-wrapper">

        <!-- ===== NAVBAR ===== -->
        <nav class="navbar navbar-expand-lg navbar-light navbar-portal py-2">
            <div class="container">
                <a class="navbar-brand fw-bold d-flex align-items-center gap-3" href="index.php">
                    <img src="../img/logo_,ministerio.png" alt="Escudo" style="height: 45px; width: auto; object-fit: contain;" onerror="this.src='https://placehold.co/45x50?text=Logo'">
                    <div class="d-flex flex-column lh-1 border-start ps-3 border-secondary border-opacity-25">
                        <span class="text-dark" style="font-size: 1.1rem; letter-spacing: 0.5px; font-weight: 700;">PortalEmpleo</span>
                        <span class="text-muted fw-semibold" style="font-size: 0.62rem; text-transform: uppercase; letter-spacing: 0.3px;">Min. de Trabajo, Fomento del Empleo y Seguridad Social</span>
                    </div>
                </a>

                <div class="d-flex align-items-center order-lg-last gap-3">
                    <a href="#" class="text-muted position-relative" title="Notificaciones">
                        <i class="bi bi-bell fs-5"></i>
                        <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-white rounded-circle"></span>
                    </a>

                    <div class="nav-item dropdown profile-dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="profileMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=150&q=80" class="profile-menu-img" alt="Foto">
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end custom-profile-menu" aria-labelledby="profileMenu">
                            <li>
                                <div class="px-3 py-2 border-bottom mb-2 bg-light rounded-top">
                                    <p class="m-0 small fw-bold text-dark">Juan Carlos Nsue</p>
                                    <p class="m-0 text-success fw-bold" style="font-size: 0.7rem;"><i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i> Buscador Activo</p>
                                </div>
                            </li>
                            <li><a class="dropdown-item small" href="#"><i class="bi bi-person-gear me-2 text-primary"></i> Modificar mi Perfil</a></li>
                            <li><a class="dropdown-item small" href="#"><i class="bi bi-shield-lock me-2 text-primary"></i> Seguridad y Acceso</a></li>
                            <li><a class="dropdown-item small" href="#"><i class="bi bi-file-earmark-arrow-down me-2 text-primary"></i> Mi Historial Laboral</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item small text-danger fw-bold" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión</a></li>
                        </ul>
                    </div>
                </div>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto align-items-lg-center ms-lg-4 gap-2">
                        <li class="nav-item"><a class="nav-link" href="index.php">Panel General</a></li>
                        <li class="nav-item"><a class="nav-link active fw-bold text-primary" href="bolsa_trabajo.php">Bolsa de Trabajo</a></li>
                        <li class="nav-item"><a class="nav-link" href="cursos_publicos.php">Cursos Públicos</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- ===== CONTENIDO PRINCIPAL ===== -->
        <main class="container py-5 flex-grow-1">

            <div class="row g-4">
                <!-- ===== FILTROS (sin botón buscar) ===== -->
                <div class="col-12">
                    <div class="card dashboard-card p-4">
                        <h5 class="fw-bold mb-3 h6 text-uppercase tracking-wider text-muted"><i class="bi bi-funnel me-2"></i>Filtros de Búsqueda</h5>
                        <form id="filterForm">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <input type="text" id="searchInput" class="form-control form-control-search" placeholder="Buscar por título o empresa... (búsqueda en tiempo real)">
                                </div>
                                <div class="col-md-3">
                                    <select id="sectorSelect" class="form-select form-select-custom">
                                        <option value="">Todos los sectores</option>
                                        <option value="tecnologia">Tecnología</option>
                                        <option value="construccion">Construcción</option>
                                        <option value="salud">Salud</option>
                                        <option value="educacion">Educación</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select id="citySelect" class="form-select form-select-custom">
                                        <option value="">Todas las ciudades</option>
                                        <option value="malabo">Malabo</option>
                                        <option value="bata">Bata</option>
                                        <option value="ebebiyin">Ebebiyín</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-outline-secondary w-100" id="clearFiltersBtn" title="Limpiar todos los filtros">
                                        <i class="bi bi-arrow-counterclockwise me-1"></i> Limpiar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- ===== RESULTADOS EN TARJETAS ===== -->
                <div class="col-12">
                    <div class="card dashboard-card p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold m-0 h6 text-uppercase tracking-wider text-muted"><i class="bi bi-briefcase me-2"></i>Ofertas de Empleo (<span id="jobCount">12</span>)</h5>
                            <span class="badge-soft-blue">Últimas 30 días</span>
                        </div>

                        <div id="jobList" class="row g-3">
                            <!-- Las tarjetas se generarán dinámicamente con JS -->
                        </div>

                        <!-- ===== PAGINACIÓN ===== -->
                        <nav class="mt-4">
                            <ul class="pagination justify-content-center pagination-custom" id="paginationControls">
                                <!-- Generado con JS -->
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>

        </main>

        <!-- ===== FOOTER ===== -->
        <footer class="bg-white text-center py-4 text-muted small mt-auto border-top">
            <div class="container">
                <p class="m-0">&copy; 2026 Ministerio de Trabajo, Fomento del Empleo y Seguridad Social.</p>
                <p class="m-0 text-uppercase tracking-wider" style="font-size: 0.65rem; color: var(--gov-gold); font-weight: 700;">Unidad • Paz • Justicia</p>
            </div>
        </footer>
    </div>

    <!-- ===== MODALES (SIN CAMBIOS) ===== -->
    <!-- MODAL OFERTA 1 -->
    <div class="modal fade" id="jobModal1" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-briefcase-fill me-2" style="color: var(--gov-gold);"></i>Técnico de Soporte TI</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="detail-row">
                        <span class="detail-label"><i class="bi bi-building"></i> Empresa</span>
                        <span class="detail-value">GETESA (Guinea Ecuatorial de Telecomunicaciones)</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label"><i class="bi bi-geo-alt"></i> Ubicación</span>
                        <span class="detail-value">Malabo, Bioko Norte</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label"><i class="bi bi-cash-coin"></i> Salario</span>
                        <span class="detail-value salary-highlight">450.000 FCFA / mes</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label"><i class="bi bi-clock"></i> Jornada</span>
                        <span class="detail-value">Completa (40h/semana)</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label"><i class="bi bi-file-check"></i> Contrato</span>
                        <span class="detail-value">Indefinido (periodo de prueba 3 meses)</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label"><i class="bi bi-calendar-event"></i> Publicado</span>
                        <span class="detail-value">08 de julio de 2026</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label"><i class="bi bi-list-check"></i> Requisitos</span>
                        <div class="detail-value">
                            <ul class="requirements-list">
                                <li>Título en Informática, Telecomunicaciones o afín.</li>
                                <li>Experiencia mínima de 2 años en soporte técnico.</li>
                                <li>Conocimientos en redes (TCP/IP, routers, switches).</li>
                                <li>Manejo de sistemas operativos Windows y Linux.</li>
                                <li>Capacidad para trabajar en equipo y bajo presión.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label"><i class="bi bi-gift"></i> Beneficios</span>
                        <div class="detail-value">
                            <ul class="requirements-list">
                                <li>Seguro médico privado.</li>
                                <li>Bonificación por desempeño trimestral.</li>
                                <li>Formación continua subvencionada.</li>
                                <li>Ticket de alimentación.</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-gold" onclick="postular('Técnico de Soporte TI')"><i class="bi bi-send me-2"></i>Postular ahora</button>
                    <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL OFERTA 2 -->
    <div class="modal fade" id="jobModal2" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-briefcase-fill me-2" style="color: var(--gov-gold);"></i>Ingeniero Civil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="detail-row">
                        <span class="detail-label"><i class="bi bi-building"></i> Empresa</span>
                        <span class="detail-value">SOMAGEC (Sociedad de Obras y Montajes)</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label"><i class="bi bi-geo-alt"></i> Ubicación</span>
                        <span class="detail-value">Bata, Litoral</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label"><i class="bi bi-cash-coin"></i> Salario</span>
                        <span class="detail-value salary-highlight">600.000 FCFA / mes</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label"><i class="bi bi-clock"></i> Jornada</span>
                        <span class="detail-value">Completa (40h/semana)</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label"><i class="bi bi-file-check"></i> Contrato</span>
                        <span class="detail-value">Indefinido (proyectos de infraestructura)</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label"><i class="bi bi-calendar-event"></i> Publicado</span>
                        <span class="detail-value">05 de julio de 2026</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label"><i class="bi bi-list-check"></i> Requisitos</span>
                        <div class="detail-value">
                            <ul class="requirements-list">
                                <li>Título en Ingeniería Civil o similar.</li>
                                <li>Mínimo 3 años de experiencia en obras públicas.</li>
                                <li>Manejo de AutoCAD y software de cálculo estructural.</li>
                                <li>Conocimientos en normativa de construcción local.</li>
                                <li>Disponibilidad para viajar dentro del país.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label"><i class="bi bi-gift"></i> Beneficios</span>
                        <div class="detail-value">
                            <ul class="requirements-list">
                                <li>Vehículo de empresa para desplazamientos.</li>
                                <li>Seguro de vida y accidentes.</li>
                                <li>Bono por productividad.</li>
                                <li>Oportunidad de crecimiento interno.</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-gold" onclick="postular('Ingeniero Civil')"><i class="bi bi-send me-2"></i>Postular ahora</button>
                    <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL OFERTA 3 -->
    <div class="modal fade" id="jobModal3" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-briefcase-fill me-2" style="color: var(--gov-gold);"></i>Enfermero/a</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="detail-row">
                        <span class="detail-label"><i class="bi bi-building"></i> Empresa</span>
                        <span class="detail-value">Hospital Regional de Ebebiyín</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label"><i class="bi bi-geo-alt"></i> Ubicación</span>
                        <span class="detail-value">Ebebiyín, Kié-Ntem</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label"><i class="bi bi-cash-coin"></i> Salario</span>
                        <span class="detail-value salary-highlight">350.000 FCFA / mes</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label"><i class="bi bi-clock"></i> Jornada</span>
                        <span class="detail-value">Parcial (20h/semana) · Turnos rotativos</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label"><i class="bi bi-file-check"></i> Contrato</span>
                        <span class="detail-value">Temporal (6 meses, renovable)</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label"><i class="bi bi-calendar-event"></i> Publicado</span>
                        <span class="detail-value">01 de julio de 2026</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label"><i class="bi bi-list-check"></i> Requisitos</span>
                        <div class="detail-value">
                            <ul class="requirements-list">
                                <li>Título de Enfermería (Diplomatura o Grado).</li>
                                <li>Colegiación vigente.</li>
                                <li>Experiencia mínima de 1 año en hospital.</li>
                                <li>Capacidad para atención primaria y urgencias.</li>
                                <li>Disponibilidad para turnos de fin de semana.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label"><i class="bi bi-gift"></i> Beneficios</span>
                        <div class="detail-value">
                            <ul class="requirements-list">
                                <li>Seguro médico complementario.</li>
                                <li>Bonificación por nocturnidad.</li>
                                <li>Formación continuada en salud.</li>
                                <li>Transporte gratuito desde Malabo los fines de semana.</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-gold" onclick="postular('Enfermero/a')"><i class="bi bi-send me-2"></i>Postular ahora</button>
                    <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== SCRIPTS ===== -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // ===== DATOS DE OFERTAS =====
        const jobs = [{
            id: 1,
            title: 'Técnico de Soporte TI',
            company: 'GETESA',
            city: 'Malabo',
            sector: 'tecnologia',
            salary: '450.000 FCFA',
            date: '08/07/2026',
            contract: 'Indefinido',
            jornada: 'Completa',
            badge: 'Nuevo',
            badgeColor: 'gold',
            accent: 'gold',
            modalId: 'jobModal1'
        }, {
            id: 2,
            title: 'Ingeniero Civil',
            company: 'SOMAGEC',
            city: 'Bata',
            sector: 'construccion',
            salary: '600.000 FCFA',
            date: '05/07/2026',
            contract: 'Indefinido',
            jornada: 'Completa',
            badge: 'Destacado',
            badgeColor: 'green',
            accent: 'green',
            modalId: 'jobModal2'
        }, {
            id: 3,
            title: 'Enfermero/a',
            company: 'Hospital Regional',
            city: 'Ebebiyín',
            sector: 'salud',
            salary: '350.000 FCFA',
            date: '01/07/2026',
            contract: 'Temporal',
            jornada: 'Parcial',
            badge: 'Urgente',
            badgeColor: 'urgent',
            accent: 'blue',
            modalId: 'jobModal3'
        }, {
            id: 4,
            title: 'Profesor de Matemáticas',
            company: 'Instituto Nacional',
            city: 'Malabo',
            sector: 'educacion',
            salary: '400.000 FCFA',
            date: '10/07/2026',
            contract: 'Indefinido',
            jornada: 'Completa',
            badge: 'Oferta Pública',
            badgeColor: 'blue',
            accent: 'blue',
            modalId: null
        }, {
            id: 5,
            title: 'Desarrollador Web',
            company: 'TechSolutions',
            city: 'Bata',
            sector: 'tecnologia',
            salary: '550.000 FCFA',
            date: '09/07/2026',
            contract: 'Indefinido',
            jornada: 'Completa',
            badge: 'Nuevo',
            badgeColor: 'gold',
            accent: 'gold',
            modalId: null
        }, {
            id: 6,
            title: 'Arquitecto',
            company: 'Estudio de Arquitectura',
            city: 'Malabo',
            sector: 'construccion',
            salary: '700.000 FCFA',
            date: '06/07/2026',
            contract: 'Indefinido',
            jornada: 'Completa',
            badge: null,
            badgeColor: null,
            accent: 'green',
            modalId: null
        }, {
            id: 7,
            title: 'Médico General',
            company: 'Centro de Salud',
            city: 'Ebebiyín',
            sector: 'salud',
            salary: '800.000 FCFA',
            date: '04/07/2026',
            contract: 'Indefinido',
            jornada: 'Completa',
            badge: 'Urgente',
            badgeColor: 'urgent',
            accent: 'blue',
            modalId: null
        }, {
            id: 8,
            title: 'Profesor de Inglés',
            company: 'Academia Linguística',
            city: 'Bata',
            sector: 'educacion',
            salary: '350.000 FCFA',
            date: '02/07/2026',
            contract: 'Temporal',
            jornada: 'Parcial',
            badge: null,
            badgeColor: null,
            accent: 'gold',
            modalId: null
        }];

        let currentPage = 1;
        const itemsPerPage = 3;
        let filteredJobs = [...jobs];

        // ===== FUNCIÓN POSTULAR =====
        function postular(titulo) {
            alert(`✅ Has postulado exitosamente al puesto: "${titulo}".\nPronto recibirás notificaciones sobre el estado de tu candidatura.`);
        }

        // ===== RENDERIZAR TARJETAS =====
        function renderJobs() {
            const start = (currentPage - 1) * itemsPerPage;
            const end = start + itemsPerPage;
            const pageItems = filteredJobs.slice(start, end);

            const container = document.getElementById('jobList');
            document.getElementById('jobCount').textContent = filteredJobs.length;

            if (filteredJobs.length === 0) {
                container.innerHTML =
                    `<div class="col-12 text-center py-5 text-muted"><i class="bi bi-inbox fs-1 d-block mb-2"></i>No se encontraron ofertas que coincidan con los filtros.</div>`;
                document.getElementById('paginationControls').innerHTML = '';
                return;
            }

            let html = '';
            pageItems.forEach((job) => {
                const accentClass = job.accent || 'blue';
                let badgeHtml = '';
                if (job.badge) {
                    const badgeClass = job.badgeColor === 'gold' ? 'badge-gold' :
                        job.badgeColor === 'green' ? 'badge-green' :
                        job.badgeColor === 'blue' ? 'badge-blue' :
                        job.badgeColor === 'urgent' ? 'badge-urgent' : 'badge-blue';
                    badgeHtml = `<span class="${badgeClass} small me-2">${job.badge}</span>`;
                }

                const modalBtn = job.modalId ?
                    `<button class="btn btn-outline-gold btn-sm px-3" data-bs-toggle="modal" data-bs-target="#${job.modalId}">Ver más</button>` :
                    '';

                html += `
                        <div class="col-md-6 col-lg-4">
                            <div class="job-card h-100">
                                <div class="card-accent ${accentClass}"></div>
                                <div class="pt-2">
                                    ${badgeHtml}
                                    <h6 class="fw-bold m-0 job-title">${job.title}</h6>
                                    <p class="m-0 job-meta mt-1"><i class="bi bi-building"></i><span class="company-name">${job.company}</span></p>
                                    <p class="m-0 job-meta"><i class="bi bi-geo-alt"></i>${job.city}</p>
                                    <p class="m-0 job-meta"><i class="bi bi-calendar-event"></i>${job.date} · ${job.jornada}</p>
                                    <div class="mt-2 d-flex flex-wrap gap-2 align-items-center">
                                        <span class="salary-badge">${job.salary}</span>
                                    </div>
                                    <div class="mt-3 d-flex flex-wrap gap-2">
                                        ${modalBtn}
                                        <button class="btn btn-blue btn-sm px-3" onclick="postular('${job.title}')">Postular</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
            });
            container.innerHTML = html;

            renderPagination();
        }

        // ===== PAGINACIÓN =====
        function renderPagination() {
            const totalPages = Math.ceil(filteredJobs.length / itemsPerPage);
            const controls = document.getElementById('paginationControls');
            if (totalPages <= 1) {
                controls.innerHTML = '';
                return;
            }

            let html = '';
            html +=
                `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${currentPage - 1}">Anterior</a></li>`;

            for (let i = 1; i <= totalPages; i++) {
                html +=
                    `<li class="page-item ${i === currentPage ? 'active' : ''}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
            }

            html +=
                `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${currentPage + 1}">Siguiente</a></li>`;

            controls.innerHTML = html;

            controls.querySelectorAll('a.page-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const page = parseInt(this.dataset.page);
                    if (page >= 1 && page <= totalPages) {
                        currentPage = page;
                        renderJobs();
                    }
                });
            });
        }

        // ===== FILTRADO EN TIEMPO REAL =====
        function filterJobs() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase().trim();
            const sector = document.getElementById('sectorSelect').value;
            const city = document.getElementById('citySelect').value;

            filteredJobs = jobs.filter(job => {
                const matchSearch = job.title.toLowerCase().includes(searchTerm) ||
                    job.company.toLowerCase().includes(searchTerm);
                const matchSector = sector === '' || job.sector === sector;
                const matchCity = city === '' || job.city.toLowerCase() === city;
                return matchSearch && matchSector && matchCity;
            });

            currentPage = 1;
            renderJobs();
        }

        // ===== LIMPIAR FILTROS =====
        document.getElementById('clearFiltersBtn').addEventListener('click', function() {
            document.getElementById('searchInput').value = '';
            document.getElementById('sectorSelect').value = '';
            document.getElementById('citySelect').value = '';
            filterJobs();
        });

        // ===== EVENTOS EN TIEMPO REAL =====
        document.getElementById('searchInput').addEventListener('input', filterJobs);
        document.getElementById('sectorSelect').addEventListener('change', filterJobs);
        document.getElementById('citySelect').addEventListener('change', filterJobs);

        // ===== INICIALIZAR =====
        renderJobs();

        // ===== CANVAS ANIMADO =====
        const canvas = document.getElementById('canvas-background');
        const ctx = canvas.getContext('2d');
        let points = [];
        const maxPoints = 50;
        const maxDistance = 140;

        function resizeCanvas() {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        }
        window.addEventListener('resize', resizeCanvas);
        resizeCanvas();

        class Point {
            constructor() {
                this.x = Math.random() * canvas.width;
                this.y = Math.random() * canvas.height;
                this.vx = (Math.random() - 0.5) * 0.3;
                this.vy = (Math.random() - 0.5) * 0.3;
                this.radius = Math.random() * 2.5 + 1;
            }
            update() {
                this.x += this.vx;
                this.y += this.vy;
                if (this.x < 0 || this.x > canvas.width) this.vx *= -1;
                if (this.y < 0 || this.y > canvas.height) this.vy *= -1;
            }
            draw() {
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2);
                ctx.fillStyle = 'rgba(201, 168, 76, 0.15)';
                ctx.fill();
            }
        }

        for (let i = 0; i < maxPoints; i++) {
            points.push(new Point());
        }

        function animate() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            for (let i = 0; i < points.length; i++) {
                points[i].update();
                points[i].draw();
                for (let j = i + 1; j < points.length; j++) {
                    const dx = points[i].x - points[j].x;
                    const dy = points[i].y - points[j].y;
                    const dist = Math.sqrt(dx * dx + dy * dy);
                    if (dist < maxDistance) {
                        ctx.beginPath();
                        ctx.moveTo(points[i].x, points[i].y);
                        ctx.lineTo(points[j].x, points[j].y);
                        ctx.strokeStyle = `rgba(201, 168, 76, ${0.08 * (1 - dist / maxDistance)})`;
                        ctx.lineWidth = 0.7;
                        ctx.stroke();
                    }
                }
            }
            requestAnimationFrame(animate);
        }
        animate();
    </script>
</body>

</html>
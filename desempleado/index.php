<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control - Portal de Empleo</title>
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

        /* ===== NAVBAR ===== */
        .navbar-portal {
            background-color: rgba(255, 255, 255, 0.96) !important;
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
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid var(--gov-green);
            object-fit: cover;
            cursor: pointer;
            transition: transform 0.2s ease;
        }
        .profile-menu-img:hover { transform: scale(1.05); }
        .custom-profile-menu {
            background-color: #ffffff !important;
            border: 1px solid var(--gov-border) !important;
            border-radius: var(--gov-radius) !important;
            box-shadow: 0 12px 30px rgba(10, 25, 47, 0.1) !important;
            padding: 10px !important;
            z-index: 1060 !important;
        }
        @media (min-width: 992px) {
            .custom-profile-menu {
                position: absolute !important;
                right: 0 !important;
                left: auto !important;
                min-width: 260px;
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
                transform: translate(-85%, 12px) !important;
                width: 260px !important;
                max-width: calc(100vw - 30px) !important;
            }
            .profile-dropdown:hover .custom-profile-menu {
                display: block !important;
                opacity: 1 !important;
                visibility: visible !important;
            }
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

        /* ===== ESTADÍSTICAS RÁPIDAS ===== */
        .stat-card {
            background: #ffffff;
            border: 1px solid var(--gov-border);
            border-radius: var(--gov-radius);
            padding: 1.2rem 1rem;
            transition: all 0.3s;
            text-align: center;
        }
        .stat-card:hover {
            border-color: var(--gov-gold);
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(11, 58, 96, 0.06);
        }
        .stat-card .stat-icon {
            font-size: 2rem;
            color: var(--gov-blue);
            opacity: 0.7;
            margin-bottom: 0.4rem;
        }
        .stat-card .stat-number {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--gov-dark);
            line-height: 1.2;
        }
        .stat-card .stat-label {
            font-size: 0.8rem;
            color: #6b7a8a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 500;
        }

        /* ===== BOTONES ===== */
        .btn-gov {
            background-color: var(--gov-blue);
            color: #ffffff;
            border: none;
            border-radius: var(--gov-radius-sm);
            padding: 0.6rem 1.2rem;
            font-weight: 500;
            transition: background-color 0.2s ease, transform 0.15s;
        }
        .btn-gov:hover {
            background-color: var(--gov-blue-light);
            color: #ffffff;
            transform: translateY(-1px);
        }
        .btn-gov-outline {
            background: transparent;
            border: 2px solid var(--gov-blue);
            color: var(--gov-blue);
            border-radius: var(--gov-radius-sm);
            padding: 0.5rem 1.2rem;
            font-weight: 500;
            transition: all 0.2s;
        }
        .btn-gov-outline:hover {
            background: var(--gov-blue);
            color: white;
        }
        .btn-gold {
            background-color: var(--gov-gold);
            color: #ffffff;
            border: none;
            border-radius: var(--gov-radius-sm);
            padding: 0.6rem 1.2rem;
            font-weight: 500;
            transition: all 0.2s;
        }
        .btn-gold:hover {
            background-color: #B8953A;
            color: white;
            transform: translateY(-1px);
        }
        .btn-green {
            background-color: var(--gov-green);
            color: #ffffff;
            border: none;
            border-radius: var(--gov-radius-sm);
            padding: 0.6rem 1.2rem;
            font-weight: 500;
            transition: all 0.2s;
        }
        .btn-green:hover {
            background-color: var(--gov-green-light);
            color: white;
            transform: translateY(-1px);
        }
        .btn-pill-custom {
            border-radius: 20px;
            padding: 0.4rem 1.2rem;
            font-size: 0.85rem;
            font-weight: 500;
        }

        /* ===== FILTROS RÁPIDOS ===== */
        .filter-tag {
            display: inline-block;
            background: #f1f5f9;
            color: var(--gov-dark);
            border-radius: 30px;
            padding: 0.25rem 0.9rem;
            font-size: 0.75rem;
            font-weight: 500;
            margin-right: 0.4rem;
            margin-bottom: 0.4rem;
            cursor: pointer;
            transition: all 0.2s;
            border: 1px solid transparent;
        }
        .filter-tag:hover {
            background: var(--gov-gold-light);
            border-color: var(--gov-gold);
        }
        .filter-tag.active {
            background: var(--gov-blue);
            color: white;
            border-color: var(--gov-blue);
        }

        /* ===== NOTICIAS ===== */
        .news-item {
            padding: 0.8rem 0;
            border-bottom: 1px solid var(--gov-border);
            transition: background 0.2s;
            border-radius: var(--gov-radius-sm);
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }
        .news-item:last-child { border-bottom: none; }
        .news-item:hover { background: #f8fafc; }
        .news-item .news-title {
            font-weight: 600;
            color: var(--gov-dark);
            font-size: 0.95rem;
        }
        .news-item .news-meta {
            font-size: 0.75rem;
            color: #6b7a8a;
        }
        .news-item .news-badge {
            background: var(--gov-gold);
            color: white;
            padding: 0.1rem 0.6rem;
            border-radius: 30px;
            font-size: 0.6rem;
            text-transform: uppercase;
            font-weight: 600;
        }

        /* ===== CALENDARIO ===== */
        .event-item {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            padding: 0.5rem 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .event-item:last-child { border-bottom: none; }
        .event-date {
            background: var(--gov-blue);
            color: white;
            border-radius: var(--gov-radius-sm);
            padding: 0.2rem 0.7rem;
            font-size: 0.75rem;
            font-weight: 600;
            min-width: 70px;
            text-align: center;
        }
        .event-date.gold { background: var(--gov-gold); }
        .event-date.green { background: var(--gov-green); }
        .event-info { flex: 1; font-size: 0.9rem; }
        .event-info .event-title {
            font-weight: 600;
            color: var(--gov-dark);
        }
        .event-info .event-desc {
            font-size: 0.8rem;
            color: #6b7a8a;
        }

        /* ===== PERFIL ===== */
        .profile-detail-item {
            display: flex;
            justify-content: space-between;
            padding: 0.3rem 0;
            font-size: 0.9rem;
            border-bottom: 1px dashed #e9edf2;
        }
        .profile-detail-item:last-child { border-bottom: none; }
        .profile-detail-item .label { color: #6b7a8a; }
        .profile-detail-item .value {
            font-weight: 500;
            color: var(--gov-dark);
        }

        /* ===== LISTAS ===== */
        .list-item-custom {
            background-color: #F8FAFC;
            border: 1px solid #F1F5F9;
            border-radius: var(--gov-radius);
            transition: all 0.2s ease;
        }
        .list-item-custom:hover {
            background-color: #F1F5F9;
            border-color: var(--gov-gold);
        }

        /* ===== TIMELINE ===== */
        .timeline-item {
            position: relative;
            padding-left: 24px;
            border-left: 2px solid var(--gov-border);
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -6px;
            top: 6px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: var(--gov-gold);
        }

        /* ===== ALERTA ===== */
        .alert-profile-incomplete {
            background-color: #FFF5F5;
            border: 1px solid #FEE2E2;
            border-left: 5px solid #DC3545;
            border-radius: var(--gov-radius);
            color: #7F1D1D;
        }
        .tracking-wider { letter-spacing: 0.05em; }

        /* ===== INPUTS ===== */
        .form-control-custom {
            border-radius: var(--gov-radius-sm) !important;
            border: 1px solid var(--gov-border);
            padding: 0.6rem 1rem;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }
        .form-control-custom:focus {
            border-color: var(--gov-blue);
            box-shadow: 0 0 0 3px rgba(11, 58, 96, 0.1);
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .stat-card .stat-number { font-size: 1.4rem; }
            .event-date { min-width: 60px; font-size: 0.65rem; }
        }
        @media (max-width: 576px) {
            .dashboard-card { padding: 1.5rem !important; }
        }
    </style>
</head>
<body>

    <canvas id="canvas-background"></canvas>

    <div class="main-wrapper">

        <?php include 'header_desempleado.php'; ?>

        <!-- CONTENIDO PRINCIPAL -->
        <main class="container py-5 flex-grow-1">

            <!-- ALERTA EXPEDIENTE INCOMPLETO -->
            <div id="incompleteProfileBanner" class="alert alert-profile-incomplete p-4 mb-4 d-none">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <div class="d-flex align-items-start gap-3">
                        <i class="bi bi-exclamation-octagon-fill text-danger fs-3 mt-1"></i>
                        <div>
                            <h4 class="fw-bold m-0 h5 text-dark">Expediente Digital Incompleto</h4>
                            <p class="m-0 text-muted small mt-1">Conforme a la normativa del Sistema Nacional de Empleo, debe completar su perfil adjuntando su Documento de Identidad Personal (DIP) y su Currículum Vitae para acceder a la visualización de ofertas laborales vigentes.</p>
                        </div>
                    </div>
                    <a href="#" class="btn btn-danger btn-sm text-nowrap px-4 py-2 rounded-pill fw-bold shadow-sm">
                        <i class="bi bi-pencil-square me-1"></i> Completar Perfil Ahora
                    </a>
                </div>
            </div>

            <!-- ESTADÍSTICAS RÁPIDAS -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon"><i class="bi bi-send"></i></div>
                        <div class="stat-number">12</div>
                        <div class="stat-label">Postulaciones</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon"><i class="bi bi-eye"></i></div>
                        <div class="stat-number">34</div>
                        <div class="stat-label">Ofertas Vistas</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon"><i class="bi bi-journal-bookmark"></i></div>
                        <div class="stat-number">5</div>
                        <div class="stat-label">Cursos Inscritos</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon"><i class="bi bi-bell"></i></div>
                        <div class="stat-number">3</div>
                        <div class="stat-label">Notificaciones</div>
                    </div>
                </div>
            </div>

            <div class="row g-4">

                <!-- COLUMNA IZQUIERDA (PERFIL + NOTICIAS) -->
                <div class="col-xl-4 col-lg-5">
                    <!-- PERFIL -->
                    <div class="card dashboard-card p-4 text-center mb-4">
                        <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=300&q=80"
                            class="rounded-circle border border-3 border-light mx-auto mb-3 shadow-sm"
                            style="width: 100px; height: 100px; object-fit: cover;" alt="Foto">
                        <h4 class="fw-bold m-0 h5 text-dark">Ana Trini Maye</h4>
                        <p class="text-muted small mb-3">ID Expediente: <strong style="color: var(--gov-blue);">#EG-94821</strong></p>

                        <div class="text-start small mb-3">
                            <div class="profile-detail-item">
                                <span class="label">Antigüedad</span>
                                <span class="value">2 años 3 meses</span>
                            </div>
                            <div class="profile-detail-item">
                                <span class="label">Última actividad</span>
                                <span class="value">Hace 2 días</span>
                            </div>
                            <div class="profile-detail-item">
                                <span class="label">Nivel de perfil</span>
                                <span class="value"><span class="badge bg-gold" style="background: var(--gov-gold);">Avanzado</span></span>
                            </div>
                        </div>

                        <div class="p-3 bg-light bg-opacity-70 rounded border-0 text-start small mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Residencia:</span>
                                <span class="fw-semibold text-dark">Malabo, Bioko Norte</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Progreso:</span>
                                <span id="profilePercentage" class="fw-bold text-danger">40%</span>
                            </div>
                            <div class="progress mt-2" style="height: 6px; background-color: #E2E8F0;">
                                <div id="profileProgressBar" class="progress-bar bg-danger" role="progressbar" style="width: 40%"></div>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button class="btn btn-gov btn-sm fw-semibold"><i class="bi bi-qr-code me-2"></i> Descargar Tarjeta Demandante</button>
                            <button class="btn btn-gov-outline btn-sm fw-semibold"><i class="bi bi-pencil-square me-2"></i> Editar Perfil</button>
                        </div>
                    </div>

                    <!-- NOTIFICACIONES DE OFICINA -->
                    <div class="card dashboard-card p-4 mb-4">
                        <h5 class="fw-bold mb-3 h6 text-uppercase tracking-wider text-muted"><i class="bi bi-chat-left-text text-primary me-2" style="color: var(--gov-blue) !important;"></i>Notificaciones de Oficina</h5>
                        <div class="d-flex flex-column gap-2">
                            <div class="p-2 rounded bg-warning bg-opacity-10 border-0 small">
                                <strong class="text-warning-emphasis d-block" style="font-weight: 600;">Revisión de DIP Pendiente</strong>
                                <span class="text-muted" style="font-size: 0.75rem;">Su documento escaneado debe ser completamente legible para su validación estatal.</span>
                            </div>
                            <div class="p-2 rounded bg-info bg-opacity-10 border-0 small">
                                <strong class="text-info-emphasis d-block" style="font-weight: 600;">Nueva oferta en tu sector</strong>
                                <span class="text-muted" style="font-size: 0.75rem;">Se ha publicado una vacante para Técnico de Soporte TI en GETESA.</span>
                            </div>
                        </div>
                    </div>

                    <!-- NOTICIAS Y EVENTOS -->
                    <div class="card dashboard-card p-4">
                        <h5 class="fw-bold mb-3 h6 text-uppercase tracking-wider text-muted"><i class="bi bi-newspaper me-2" style="color: var(--gov-gold);"></i>Noticias y Eventos</h5>
                        <div class="news-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="news-title">Nueva convocatoria de empleo público</span>
                                <span class="news-badge">Nuevo</span>
                            </div>
                            <div class="news-meta"><i class="bi bi-calendar-event me-1"></i> 15/07/2026 · <i class="bi bi-clock me-1"></i> Plazo: 30 días</div>
                        </div>
                        <div class="news-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="news-title">Curso gratuito de ciberseguridad</span>
                                <span class="news-badge" style="background: var(--gov-green);">Inscripción abierta</span>
                            </div>
                            <div class="news-meta"><i class="bi bi-calendar-event me-1"></i> Inicio: 01/08/2026 · <i class="bi bi-clock me-1"></i> 6 semanas</div>
                        </div>
                        <div class="news-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="news-title">Reforma del Sistema Nacional de Empleo</span>
                                <span class="news-badge" style="background: var(--gov-blue);">Información</span>
                            </div>
                            <div class="news-meta"><i class="bi bi-calendar-event me-1"></i> Publicado: 10/07/2026</div>
                        </div>
                    </div>
                </div>

                <!-- COLUMNA DERECHA (CONTENIDO / ACCIONES) -->
                <div class="col-xl-8 col-lg-7">

                    <div id="jobOffersSection">
                   

                        <!-- OFERTAS RECOMENDADAS -->
                        <div class="card dashboard-card p-4 mb-4">
                            <h5 class="fw-bold mb-3 h6 text-uppercase tracking-wider text-muted">Ofertas Recomendadas</h5>
                            <div class="d-flex flex-column gap-3">
                                <div class="list-item-custom p-3 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                                    <div>
                                        <span class="badge bg-gold" style="background: var(--gov-gold); color: white;">Nuevo</span>
                                        <h6 class="fw-bold m-0 text-dark mt-1">Técnico de Soporte TI</h6>
                                        <p class="m-0 small text-muted"><i class="bi bi-building me-1"></i>GETESA · <i class="bi bi-geo-alt me-1"></i>Malabo</p>
                                        <p class="m-0 small text-muted"><i class="bi bi-cash-coin me-1"></i>450.000 FCFA · <i class="bi bi-clock me-1"></i>Jornada completa</p>
                                    </div>
                                    <button class="btn btn-sm btn-gov btn-pill-custom">Postularme</button>
                                </div>
                                <div class="list-item-custom p-3 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                                    <div>
                                        <span class="badge bg-green" style="background: var(--gov-green); color: white;">Destacado</span>
                                        <h6 class="fw-bold m-0 text-dark mt-1">Ingeniero Civil</h6>
                                        <p class="m-0 small text-muted"><i class="bi bi-building me-1"></i>SOMAGEC · <i class="bi bi-geo-alt me-1"></i>Bata</p>
                                        <p class="m-0 small text-muted"><i class="bi bi-cash-coin me-1"></i>600.000 FCFA · <i class="bi bi-clock me-1"></i>Indefinido</p>
                                    </div>
                                    <button class="btn btn-sm btn-gov btn-pill-custom">Postularme</button>
                                </div>
                                <div class="list-item-custom p-3 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                                    <div>
                                        <span class="badge bg-danger">Urgente</span>
                                        <h6 class="fw-bold m-0 text-dark mt-1">Enfermero/a</h6>
                                        <p class="m-0 small text-muted"><i class="bi bi-building me-1"></i>Hospital Regional · <i class="bi bi-geo-alt me-1"></i>Ebebiyín</p>
                                        <p class="m-0 small text-muted"><i class="bi bi-cash-coin me-1"></i>350.000 FCFA · <i class="bi bi-clock me-1"></i>Parcial</p>
                                    </div>
                                    <button class="btn btn-sm btn-gov btn-pill-custom">Postularme</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ESTADO CANDIDATURAS -->
                    <div class="card dashboard-card p-4 mb-4">
                        <h5 class="fw-bold mb-3 h6 text-uppercase tracking-wider text-muted">Estado de mis Candidaturas</h5>
                        <div class="d-flex flex-column gap-3 mt-2">
                            <div class="timeline-item">
                                <span class="badge bg-light text-secondary border mb-1" style="font-size: 0.65rem; font-weight: 600;">05/07/2026</span>
                                <h6 class="fw-bold text-dark m-0 small">Postulación enviada a SOMAGEC</h6>
                                <p class="text-muted m-0 small mt-1" style="font-size: 0.8rem;">Puesto: Auxiliar Técnico de Obras. Estado: <span class="text-warning fw-bold" style="color: #B45309 !important;">En Revisión Ministerial</span></p>
                            </div>
                            <div class="timeline-item">
                                <span class="badge bg-light text-secondary border mb-1" style="font-size: 0.65rem; font-weight: 600;">28/06/2026</span>
                                <h6 class="fw-bold text-dark m-0 small">Postulación enviada a GETESA</h6>
                                <p class="text-muted m-0 small mt-1" style="font-size: 0.8rem;">Puesto: Técnico de Soporte TI. Estado: <span class="text-success fw-bold">Preseleccionado</span></p>
                            </div>
                        </div>
                    </div>

                    <!-- PLANES ESTATALES + CALENDARIO -->
                    <div class="card dashboard-card p-4 mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold m-0 h6 text-uppercase tracking-wider text-muted"><i class="bi bi-journal-bookmark text-success me-2" style="color: var(--gov-green) !important;"></i>Planes Estatales de Capacitación</h5>
                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill small px-2 py-1" style="font-weight: 600; font-size: 0.7rem;">Cupos Abiertos</span>
                        </div>
                        <div class="p-3 rounded list-item-custom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
                            <div>
                                <span class="badge bg-light text-dark mb-1 border" style="font-size: 0.65rem; font-weight: 500;">Presencial</span>
                                <h6 class="fw-bold m-0 text-dark">Administración de Redes y Ciberseguridad</h6>
                                <p class="text-muted small m-0 mt-1">Programa oficial subvencionado enfocado al sector de telecomunicaciones nacional.</p>
                            </div>
                            <a href="#" class="btn btn-sm btn-green btn-pill-custom text-nowrap">Solicitar Plaza</a>
                        </div>
                        <div class="p-3 rounded list-item-custom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                            <div>
                                <span class="badge bg-light text-dark mb-1 border" style="font-size: 0.65rem; font-weight: 500;">Online</span>
                                <h6 class="fw-bold m-0 text-dark">Gestión de Proyectos con Metodología Ágil</h6>
                                <p class="text-muted small m-0 mt-1">Formación en Scrum y Kanban para el sector público y privado.</p>
                            </div>
                            <a href="#" class="btn btn-sm btn-green btn-pill-custom text-nowrap">Solicitar Plaza</a>
                        </div>
                    </div>

                    <!-- CALENDARIO / PRÓXIMAS FECHAS -->
                    <div class="card dashboard-card p-4">
                        <h5 class="fw-bold mb-3 h6 text-uppercase tracking-wider text-muted"><i class="bi bi-calendar2-week me-2" style="color: var(--gov-blue);"></i>Próximas Fechas Importantes</h5>
                        <div class="event-item">
                            <span class="event-date">15 Jul</span>
                            <div class="event-info">
                                <div class="event-title">Cierre de inscripción - Curso de Ciberseguridad</div>
                                <div class="event-desc">Último día para solicitar plaza</div>
                            </div>
                        </div>
                        <div class="event-item">
                            <span class="event-date gold">20 Jul</span>
                            <div class="event-info">
                                <div class="event-title">Publicación de resultados - SOMAGEC</div>
                                <div class="event-desc">Lista de preseleccionados para el puesto de Ingeniero Civil</div>
                            </div>
                        </div>
                        <div class="event-item">
                            <span class="event-date green">01 Ago</span>
                            <div class="event-info">
                                <div class="event-title">Inicio del curso de Administración de Redes</div>
                                <div class="event-desc">Primera sesión presencial en el Centro de Formación de Malabo</div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </main>

        <?php include 'footer_desempleado.php'; ?>
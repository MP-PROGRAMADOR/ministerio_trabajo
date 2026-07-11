<?php include '../componentes/header_desempleado.php'; ?>
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

        /* ===== MENSAJE DE REGISTRO PENDIENTE ===== */
        .registro-pendiente {
            background: #f0f7ff;
            border: 1px solid #cfe2ff;
            border-radius: var(--gov-radius);
            padding: 1.2rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 1.5rem;
        }
        .registro-pendiente .icono {
            font-size: 2rem;
            color: var(--gov-blue);
        }
        .registro-pendiente .texto {
            flex: 1;
            font-size: 0.95rem;
            color: var(--gov-dark);
        }
        .registro-pendiente .texto strong {
            color: var(--gov-blue);
        }
        .registro-pendiente .btn-link {
            color: var(--gov-blue);
            font-weight: 600;
            text-decoration: none;
            border-bottom: 2px solid transparent;
            transition: all 0.2s;
        }
        .registro-pendiente .btn-link:hover {
            border-bottom-color: var(--gov-blue);
        }
    </style>
</head>
<body>

    <canvas id="canvas-background"></canvas>

    <div class="main-wrapper">

        <?php include '../componentes/menu_desempleado.php'; ?>





        <?php


$perfil_completo = false;

try {
    // 1. Verificar si tiene el registro de datos personales
    $stmt_busc = $pdo->prepare("SELECT id FROM buscadores_empleo WHERE usuario_id = :uid LIMIT 1");
    $stmt_busc->execute([':uid' => $id_usuario]);
    $has_buscador = $stmt_busc->fetch();

    // 2. Verificar si tiene los documentos obligatorios (DIP y CV)
    $stmt_doc = $pdo->prepare("SELECT id FROM documentos WHERE usuario_id = :uid LIMIT 1");
    $stmt_doc->execute([':uid' => $id_usuario]);
    $has_documentos = $stmt_doc->fetch();

    // Si tiene ambos registros obligatorios creados, el perfil se considera completado
    if ($has_buscador && $has_documentos) {
        $perfil_completo = true;
    }

} catch (PDOException $e) {
    error_log("Error al verificar estado del perfil: " . $e->getMessage());
}




?>



      <main class="container py-5 flex-grow-1">

    <?php if (!$perfil_completo): ?>
        <div id="incompleteProfileBanner" class="alert alert-profile-incomplete p-4 mb-4">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div class="d-flex align-items-start gap-3">
                    <i class="bi bi-exclamation-octagon-fill text-danger fs-3 mt-1"></i>
                    <div>
                        <h4 class="fw-bold m-0 h5 text-dark">Expediente Digital Incompleto</h4>
                        <p class="m-0 text-muted small mt-1">Conforme a la normativa del Sistema Nacional de Empleo, debe completar su perfil adjuntando su Documento de Identidad Personal (DIP) y su Currículum Vitae para acceder a la visualización de ofertas laborales vigentes.</p>
                    </div>
                </div>
                <a href="completar_perfil.php" class="btn btn-danger btn-sm text-nowrap px-4 py-2 rounded-pill fw-bold shadow-sm">
                    <i class="bi bi-pencil-square me-1"></i> Completar Perfil Ahora
                </a>
            </div>
        </div>

        <div class="registro-pendiente mb-4">
            <div class="icono"><i class="bi bi-info-circle-fill"></i></div>
            <div class="texto">
                <strong>¡Atención!</strong> Para acceder a todas las funcionalidades del portal (postulaciones, ofertas personalizadas, cursos, etc.) debe completar su registro en el <a href="completar_perfil.php" class="btn-link">Sistema Nacional de Empleo</a>.
            </div>
            <a href="completar_perfil.php" class="btn btn-gov btn-sm rounded-pill px-4">Completar registro</a>
        </div>
    <?php endif; ?>

    <?php if ($perfil_completo): ?>

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

            <div class="col-xl-4 col-lg-5">
                <div class="card dashboard-card p-4 text-center mb-4">
                    <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=300&q=80"
                        class="rounded-circle border border-3 border-light mx-auto mb-3 shadow-sm"
                        style="width: 100px; height: 100px; object-fit: cover;" alt="Foto">
                    <h4 class="fw-bold m-0 h5 text-dark"><?php echo htmlspecialchars($_SESSION['nombre_completo']); ?></h4>
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
                            <span id="profilePercentage" class="fw-bold text-success">100%</span>
                        </div>
                        <div class="progress mt-2" style="height: 6px; background-color: #E2E8F0;">
                            <div id="profileProgressBar" class="progress-bar bg-success" role="progressbar" style="width: 100%"></div>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button class="btn btn-gov btn-sm fw-semibold"><i class="bi bi-qr-code me-2"></i> Descargar Tarjeta Demandante</button>
                        <button class="btn btn-gov-outline btn-sm fw-semibold"><i class="bi bi-pencil-square me-2"></i> Editar Perfil</button>
                    </div>
                </div>

                <div class="card dashboard-card p-4 mb-4">
                    <h5 class="fw-bold mb-3 h6 text-uppercase tracking-wider text-muted"><i class="bi bi-chat-left-text text-primary me-2" style="color: var(--gov-blue) !important;"></i>Notificaciones de Oficina</h5>
                    <div class="d-flex flex-column gap-2">
                        <div class="p-2 rounded bg-info bg-opacity-10 border-0 small">
                            <strong class="text-info-emphasis d-block" style="font-weight: 600;">Nueva oferta en tu sector</strong>
                            <span class="text-muted" style="font-size: 0.75rem;">Se ha publicado una vacante para Técnico de Soporte TI en GETESA.</span>
                        </div>
                    </div>
                </div>

                <div class="card dashboard-card p-4">
                    <h5 class="fw-bold mb-3 h6 text-uppercase tracking-wider text-muted"><i class="bi bi-newspaper me-2" style="color: var(--gov-gold);"></i>Noticias y Eventos</h5>
                    <div class="news-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="news-title">Nueva convocatoria de empleo público</span>
                            <span class="news-badge">Nuevo</span>
                        </div>
                        <div class="news-meta"><i class="bi bi-calendar-event me-1"></i> 15/07/2026</div>
                    </div>
                </div>
            </div>

            <div class="col-xl-8 col-lg-7">
                <div id="jobOffersSection">
                    <div class="card dashboard-card p-4 mb-4">
                        <h5 class="fw-bold mb-3 h6 text-uppercase tracking-wider text-muted">Ofertas Recomendadas</h5>
                        <div class="d-flex flex-column gap-3">
                            <div class="list-item-custom p-3 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                                <div>
                                    <span class="badge bg-gold" style="background: var(--gov-gold); color: white;">Nuevo</span>
                                    <h6 class="fw-bold m-0 text-dark mt-1">Técnico de Soporte TI</h6>
                                    <p class="m-0 small text-muted"><i class="bi bi-building me-1"></i>GETESA · <i class="bi bi-geo-alt me-1"></i>Malabo</p>
                                </div>
                                <button class="btn btn-sm btn-gov btn-pill-custom">Postularme</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card dashboard-card p-4 mb-4">
                    <h5 class="fw-bold mb-3 h6 text-uppercase tracking-wider text-muted">Estado de mis Candidaturas</h5>
                    <div class="d-flex flex-column gap-3 mt-2">
                        <div class="timeline-item">
                            <span class="badge bg-light text-secondary border mb-1" style="font-size: 0.65rem; font-weight: 600;">28/06/2026</span>
                            <h6 class="fw-bold text-dark m-0 small">Postulación enviada a GETESA</h6>
                        </div>
                    </div>
                </div>

                <div class="card dashboard-card p-4 mb-4">
                    <h5 class="fw-bold mb-3 h6 text-uppercase tracking-wider text-muted">Planes Estatales de Capacitación</h5>
                    <div class="p-3 rounded list-item-custom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
                        <div>
                            <h6 class="fw-bold m-0 text-dark">Administración de Redes y Ciberseguridad</h6>
                        </div>
                        <a href="#" class="btn btn-sm btn-green btn-pill-custom text-nowrap">Solicitar Plaza</a>
                    </div>
                </div>
            </div>

        </div>

    <?php endif; ?>
</main>

        <script>
            const perfilCompleto = false; // Cambia a true cuando el perfil esté completo

function evaluarEstadoPerfil() {
    const banner = document.getElementById('incompleteProfileBanner');
    if (perfilCompleto) {
        banner.classList.add('d-none');
    } else {
        banner.classList.remove('d-none');
    }
}
evaluarEstadoPerfil();
        </script>
        <?php include '../componentes/footer_desempleado.php'; ?>
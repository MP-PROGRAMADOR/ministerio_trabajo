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
        .custom-profile-menu .dropdown-item i { color: var(--gov-gold); }
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

        /* ===== LISTA DE CURSOS ===== */
        .list-item-custom {
            background: #F8FAFC;
            border: 1px solid #F1F5F9;
            border-radius: var(--gov-radius);
            transition: all 0.2s;
            padding: 1rem 1.25rem;
            position: relative;
            overflow: hidden;
        }
        .list-item-custom:hover {
            background: #ffffff;
            border-color: var(--gov-gold);
            transform: translateX(4px);
            box-shadow: 0 4px 12px rgba(11, 58, 96, 0.04);
        }
        .list-item-custom .accent-bar {
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
        }
        .list-item-custom .accent-bar.blue { background: var(--gov-blue); }
        .list-item-custom .accent-bar.gold { background: var(--gov-gold); }
        .list-item-custom .accent-bar.green { background: var(--gov-green); }

        .list-item-custom .course-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--gov-dark);
        }
        .list-item-custom .institution-name {
            font-weight: 600;
            color: var(--gov-blue);
        }
        .list-item-custom .course-meta {
            font-size: 0.85rem;
            color: #6b7a8a;
        }
        .list-item-custom .course-meta i {
            width: 1.2rem;
            color: var(--gov-gold);
        }
        .list-item-custom .spots-badge {
            background: rgba(11, 58, 96, 0.08);
            color: var(--gov-blue);
            font-weight: 600;
            padding: 0.25rem 0.9rem;
            border-radius: 20px;
            font-size: 0.8rem;
            border: 1px solid rgba(11, 58, 96, 0.1);
        }

        /* Badges */
        .badge-blue {
            background: var(--gov-blue);
            color: white;
            font-weight: 600;
            padding: 0.2rem 0.8rem;
            border-radius: 20px;
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .badge-gold {
            background: var(--gov-gold);
            color: white;
            font-weight: 600;
            padding: 0.2rem 0.8rem;
            border-radius: 20px;
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .badge-green {
            background: var(--gov-green);
            color: white;
            font-weight: 600;
            padding: 0.2rem 0.8rem;
            border-radius: 20px;
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .badge-urgent {
            background: #DC3545;
            color: white;
            font-weight: 600;
            padding: 0.2rem 0.8rem;
            border-radius: 20px;
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .badge-soft-blue {
            background: rgba(11, 58, 96, 0.08);
            color: var(--gov-blue);
            font-weight: 500;
            padding: 0.2rem 0.8rem;
            border-radius: 20px;
            font-size: 0.7rem;
        }

        /* ===== BOTONES ===== */
        .btn-blue {
            background: var(--gov-blue);
            border: none;
            color: white;
            border-radius: var(--gov-radius-sm);
            padding: 0.5rem 1.2rem;
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
            padding: 0.5rem 1.2rem;
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
            padding: 0.5rem 1.2rem;
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
        .btn-outline-gold {
            border: 2px solid var(--gov-gold);
            color: var(--gov-gold);
            background: transparent;
            border-radius: var(--gov-radius-sm);
            font-weight: 600;
            transition: all 0.25s;
            padding: 0.4rem 1rem;
            font-size: 0.85rem;
        }
        .btn-outline-gold:hover {
            background: var(--gov-gold);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(201, 168, 76, 0.30);
        }
        .btn-outline-secondary {
            border: 2px solid var(--gov-border);
            color: var(--gov-dark);
            background: transparent;
            border-radius: var(--gov-radius-sm);
            font-weight: 500;
            transition: all 0.25s;
        }
        .btn-outline-secondary:hover {
            background: #e9ecef;
            border-color: #cbd5e1;
        }
        .btn-secondary-custom {
            background: #e9ecef;
            color: #6c757d;
            border: none;
            border-radius: var(--gov-radius-sm);
            padding: 0.5rem 1.2rem;
            font-weight: 500;
            cursor: default;
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

        /* ===== MODALES ===== */
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
        .modal-body .detail-row:last-child { border-bottom: none; }
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

        .form-control-custom {
            border-radius: var(--gov-radius-sm);
            border: 1.5px solid var(--gov-border);
            padding: 0.6rem 1rem;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        .form-control-custom:focus {
            border-color: var(--gov-blue);
            box-shadow: 0 0 0 4px rgba(11, 58, 96, 0.10);
        }

        /* ===== FOOTER ===== */
        footer {
            background: rgba(255, 255, 255, 0.9) !important;
            backdrop-filter: blur(8px);
            border-top: 1px solid rgba(201, 168, 76, 0.15);
        }

        @media (max-width: 576px) {
            .dashboard-card { padding: 1.5rem !important; }
            .list-item-custom { padding: 1rem !important; }
            .modal-body { padding: 1.2rem; }
            .modal-header { padding: 1.2rem; }
            .modal-body .detail-label {
                width: 100%;
                margin-bottom: 2px;
            }
            .modal-body .detail-row { flex-wrap: wrap; }
        }
    </style>
</head>
<body>

    <canvas id="canvas-background"></canvas>

    <div class="main-wrapper">

         <?php include '../componentes/menu_desempleado.php'; ?>

        <main class="container py-5 flex-grow-1">
            <div class="row g-4">
                <!-- FILTROS -->
                <div class="col-12">
                    <div class="card dashboard-card p-4">
                        <h5 class="fw-bold mb-3 h6 text-uppercase tracking-wider text-muted"><i class="bi bi-funnel me-2"></i>Filtrar Cursos</h5>
                        <form id="filterForm">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <input type="text" id="searchInput" class="form-control form-control-search" placeholder="Buscar curso... (búsqueda en tiempo real)">
                                </div>
                                <div class="col-md-3">
                                    <select id="areaSelect" class="form-select form-select-custom">
                                        <option value="">Todas las áreas</option>
                                        <option value="tecnologia">Tecnología</option>
                                        <option value="administracion">Administración</option>
                                        <option value="salud">Salud</option>
                                        <option value="educacion">Educación</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select id="modalidadSelect" class="form-select form-select-custom">
                                        <option value="">Modalidad</option>
                                        <option value="presencial">Presencial</option>
                                        <option value="online">Online</option>
                                        <option value="semipresencial">Semipresencial</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select id="estadoSelect" class="form-select form-select-custom">
                                        <option value="">Estado</option>
                                        <option value="abierto">Abiertos</option>
                                        <option value="cerrado">Cerrados</option>
                                        <option value="proximo">Próximamente</option>
                                    </select>
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-outline-secondary w-100" id="clearFiltersBtn" title="Limpiar todos los filtros">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- LISTADO DE CURSOS -->
                <div class="col-12">
                    <div class="card dashboard-card p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold m-0 h6 text-uppercase tracking-wider text-muted"><i class="bi bi-journal-bookmark me-2"></i>Planes Estatales de Capacitación (<span id="courseCount">6</span>)</h5>
                            <span class="badge-gold">Cupos disponibles</span>
                        </div>

                        <div id="courseList" class="d-flex flex-column gap-3">
                            <!-- Generado por JS -->
                        </div>

                        <nav class="mt-4">
                            <ul class="pagination justify-content-center pagination-custom" id="paginationControls">
                                <!-- Generado por JS -->
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </main>

        <!-- MODALES -->
        <div class="modal fade" id="courseModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-journal-bookmark-fill me-2" style="color: var(--gov-gold);"></i><span id="modalCourseTitle">Curso</span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" id="modalCourseBody"></div>
                    <div class="modal-footer">
                        <button class="btn btn-green" id="modalSolicitarBtn" onclick="openSolicitudModal()"><i class="bi bi-send me-2"></i>Solicitar plaza</button>
                        <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="solicitudModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-pencil-square me-2" style="color: var(--gov-green);"></i>Solicitar plaza - <span id="solicitudCursoNombre">Curso</span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" id="solicitudModalBody">
                        <form id="solicitudForm">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nombre *</label>
                                <input type="text" id="solicitudNombre" class="form-control form-control-custom" placeholder="Ej: Juan Carlos" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Apellidos *</label>
                                <input type="text" id="solicitudApellidos" class="form-control form-control-custom" placeholder="Ej: Nsue Obama" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Documento de Identidad (DIP) *</label>
                                <input type="text" id="solicitudDIP" class="form-control form-control-custom" placeholder="Ej: 123456789" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Teléfono *</label>
                                <input type="tel" id="solicitudTelefono" class="form-control form-control-custom" placeholder="Ej: 555-123456" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Correo electrónico *</label>
                                <input type="email" id="solicitudEmail" class="form-control form-control-custom" placeholder="ejemplo@correo.gq" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Motivo de interés (opcional)</label>
                                <textarea id="solicitudMotivo" class="form-control form-control-custom" rows="2" placeholder="Breve descripción de su motivación..."></textarea>
                            </div>
                            <p class="text-muted small">* Campos obligatorios</p>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-green" id="enviarSolicitudBtn"><i class="bi bi-check2-circle me-2"></i>Enviar solicitud</button>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="confirmacionModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header" style="border-bottom-color: var(--gov-green);">
                        <h5 class="modal-title"><i class="bi bi-check-circle-fill me-2" style="color: var(--gov-green);"></i>Solicitud enviada</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center py-4">
                        <i class="bi bi-hourglass-split" style="font-size: 4rem; color: var(--gov-gold);"></i>
                        <h5 class="mt-3 fw-bold">¡Su solicitud ha sido recibida!</h5>
                        <p class="text-muted">Queda <strong>pendiente de confirmación</strong> por parte del Ministerio. Recibirá notificaciones sobre el estado de su inscripción.</p>
                        <div class="alert alert-info mt-3" role="alert">
                            <i class="bi bi-info-circle me-2"></i> El proceso de validación puede tomar hasta 48 horas hábiles.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-blue" data-bs-dismiss="modal">Entendido</button>
                    </div>
                </div>
            </div>
        </div>

         <?php include '../componentes/footer_desempleado.php'; ?>

        <!-- ===== SCRIPTS (dentro de la página) ===== -->
        <script>
            // ===== DATOS DE CURSOS =====
            const courses = [
                {
                    id: 1,
                    title: 'Administración de Redes y Ciberseguridad',
                    institution: 'Centro de Formación Profesional de Malabo',
                    area: 'tecnologia',
                    modalidad: 'presencial',
                    estado: 'abierto',
                    spots: 20,
                    start_date: '15/08/2026',
                    duration: '6 semanas',
                    badge: 'Cupos disponibles',
                    badgeColor: 'green',
                    accent: 'blue',
                    description: 'Curso intensivo de formación en redes y seguridad informática, orientado a cubrir la demanda de profesionales en el sector telecomunicaciones nacional.',
                    requisitos: [
                        'Conocimientos básicos de informática',
                        'Título de Bachillerato o equivalente',
                        'Disponibilidad para clases presenciales'
                    ],
                    beneficios: [
                        'Certificado oficial del Ministerio',
                        'Bolsa de trabajo asociada al curso',
                        'Material didáctico incluido'
                    ]
                },
                {
                    id: 2,
                    title: 'Gestión de Proyectos con Metodología Ágil',
                    institution: 'Plataforma E-learning del Ministerio',
                    area: 'administracion',
                    modalidad: 'online',
                    estado: 'abierto',
                    spots: 50,
                    start_date: '01/09/2026',
                    duration: '4 semanas',
                    badge: 'Nuevo',
                    badgeColor: 'gold',
                    accent: 'gold',
                    description: 'Formación en metodologías ágiles para la gestión de proyectos, con enfoque en Scrum y Kanban, aplicado a entornos laborales públicos y privados.',
                    requisitos: [
                        'Experiencia previa en gestión de proyectos (deseable)',
                        'Acceso a internet y computadora',
                        'Disponibilidad de 10 horas semanales'
                    ],
                    beneficios: [
                        'Certificación internacional avalada',
                        'Tutoría personalizada',
                        'Acceso a la comunidad de alumni'
                    ]
                },
                {
                    id: 3,
                    title: 'Atención Sanitaria Primaria',
                    institution: 'Centro de Salud de Bata',
                    area: 'salud',
                    modalidad: 'semipresencial',
                    estado: 'abierto',
                    spots: 15,
                    start_date: '20/09/2026',
                    duration: '8 semanas',
                    badge: 'Plazas limitadas',
                    badgeColor: 'urgent',
                    accent: 'green',
                    description: 'Programa integral para la atención primaria de salud, con prácticas en centros de salud y formación teórica online.',
                    requisitos: [
                        'Título de Técnico en Cuidados Auxiliares de Enfermería o similar',
                        'Disponibilidad para prácticas presenciales',
                        'Certificado de antecedentes penales'
                    ],
                    beneficios: [
                        'Seguro de salud durante el curso',
                        'Bolsa de empleo en el sistema público',
                        'Material y uniforme incluido'
                    ]
                },
                {
                    id: 4,
                    title: 'Formación de Formadores en Educación Superior',
                    institution: 'Instituto Nacional de Educación',
                    area: 'educacion',
                    modalidad: 'presencial',
                    estado: 'cerrado',
                    spots: 0,
                    start_date: '10/07/2026',
                    duration: '12 semanas',
                    badge: 'Cerrado',
                    badgeColor: 'blue',
                    accent: 'blue',
                    description: 'Curso de especialización para docentes universitarios en metodologías activas y evaluación por competencias.',
                    requisitos: [
                        'Título universitario de grado o máster',
                        'Experiencia docente mínima de 2 años',
                        'Carta de recomendación'
                    ],
                    beneficios: [
                        'Acreditación oficial para impartición de másteres',
                        'Red de contactos académicos'
                    ]
                },
                {
                    id: 5,
                    title: 'Administración Pública y Gestión de Fondos Europeos',
                    institution: 'Escuela de Administración Pública',
                    area: 'administracion',
                    modalidad: 'online',
                    estado: 'proximo',
                    spots: 30,
                    start_date: 'Próxima convocatoria',
                    duration: 'TBD',
                    badge: 'Próximamente',
                    badgeColor: 'gold',
                    accent: 'gold',
                    description: 'Curso sobre la gestión de fondos estructurales europeos y su aplicación en la administración pública local.',
                    requisitos: [
                        'Funcionario o personal de administración',
                        'Conocimientos básicos de contabilidad'
                    ],
                    beneficios: [
                        'Certificación en gestión de fondos',
                        'Asesoramiento personalizado'
                    ]
                },
                {
                    id: 6,
                    title: 'Desarrollo Web Full Stack',
                    institution: 'Centro Tecnológico Nacional',
                    area: 'tecnologia',
                    modalidad: 'semipresencial',
                    estado: 'abierto',
                    spots: 25,
                    start_date: '01/10/2026',
                    duration: '16 semanas',
                    badge: 'En demanda',
                    badgeColor: 'green',
                    accent: 'green',
                    description: 'Formación intensiva en desarrollo web frontend y backend, con proyecto final real para empresas colaboradoras.',
                    requisitos: [
                        'Conocimientos previos de programación (mínimo 6 meses)',
                        'Ordenador propio con conexión a internet',
                        'Disponibilidad de 15 horas semanales'
                    ],
                    beneficios: [
                        'Prácticas en empresas tecnológicas',
                        'Certificado Full Stack',
                        'Bolsa de empleo exclusiva'
                    ]
                }
            ];

            let currentPage = 1;
            const itemsPerPage = 3;
            let filteredCourses = [...courses];
            let selectedCourseId = null;

            // ===== FUNCIONES =====

            function renderCourses() {
                const start = (currentPage - 1) * itemsPerPage;
                const end = start + itemsPerPage;
                const pageItems = filteredCourses.slice(start, end);

                const container = document.getElementById('courseList');
                document.getElementById('courseCount').textContent = filteredCourses.length;

                if (filteredCourses.length === 0) {
                    container.innerHTML =
                        `<div class="text-center py-5 text-muted"><i class="bi bi-inbox fs-1 d-block mb-2"></i>No se encontraron cursos que coincidan con los filtros.</div>`;
                    document.getElementById('paginationControls').innerHTML = '';
                    return;
                }

                let html = '';
                pageItems.forEach(course => {
                    const accentClass = course.accent || 'blue';
                    let badgeHtml = '';
                    if (course.badge) {
                        const badgeClass = course.badgeColor === 'gold' ? 'badge-gold' :
                            course.badgeColor === 'green' ? 'badge-green' :
                            course.badgeColor === 'blue' ? 'badge-blue' :
                            course.badgeColor === 'urgent' ? 'badge-urgent' : 'badge-blue';
                        badgeHtml = `<span class="${badgeClass} me-2">${course.badge}</span>`;
                    }

                    const spotsHtml = course.spots > 0 ?
                        `<span class="spots-badge">${course.spots} cupos</span>` :
                        `<span class="spots-badge" style="background:#dc3545; color:white; border:none;">Completo</span>`;

                    let btnHtml = '';
                    if (course.estado === 'abierto') {
                        btnHtml =
                            `<button class="btn btn-green btn-sm px-3" onclick="openSolicitudModal(${course.id})">Solicitar plaza</button>`;
                    } else if (course.estado === 'proximo') {
                        btnHtml =
                            `<button class="btn btn-secondary-custom btn-sm px-3" disabled>Próximamente</button>`;
                    } else {
                        btnHtml =
                            `<button class="btn btn-secondary-custom btn-sm px-3" disabled>Cerrado</button>`;
                    }

                    html += `
                            <div class="list-item-custom">
                                <div class="accent-bar ${accentClass}"></div>
                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-3 ps-3">
                                    <div>
                                        ${badgeHtml}
                                        <h6 class="fw-bold m-0 course-title">${course.title}</h6>
                                        <p class="m-0 course-meta"><i class="bi bi-building"></i><span class="institution-name">${course.institution}</span></p>
                                        <p class="m-0 course-meta"><i class="bi bi-grid"></i>${course.modalidad.charAt(0).toUpperCase() + course.modalidad.slice(1)} · <i class="bi bi-calendar-event"></i>Inicio: ${course.start_date} · <i class="bi bi-clock"></i>${course.duration}</p>
                                        <div class="mt-1 d-flex flex-wrap gap-2 align-items-center">
                                            ${spotsHtml}
                                        </div>
                                    </div>
                                    <div class="d-flex flex-wrap gap-2 align-items-center">
                                        <button class="btn btn-outline-gold btn-sm px-3" onclick="openCourseModal(${course.id})">Ver más</button>
                                        ${btnHtml}
                                    </div>
                                </div>
                            </div>
                        `;
                });
                container.innerHTML = html;

                renderPagination();
            }

            function renderPagination() {
                const totalPages = Math.ceil(filteredCourses.length / itemsPerPage);
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
                            renderCourses();
                        }
                    });
                });
            }

            function filterCourses() {
                const searchTerm = document.getElementById('searchInput').value.toLowerCase().trim();
                const area = document.getElementById('areaSelect').value;
                const modalidad = document.getElementById('modalidadSelect').value;
                const estado = document.getElementById('estadoSelect').value;

                filteredCourses = courses.filter(course => {
                    const matchSearch = course.title.toLowerCase().includes(searchTerm) ||
                        course.institution.toLowerCase().includes(searchTerm);
                    const matchArea = area === '' || course.area === area;
                    const matchModalidad = modalidad === '' || course.modalidad === modalidad;
                    const matchEstado = estado === '' || course.estado === estado;
                    return matchSearch && matchArea && matchModalidad && matchEstado;
                });

                currentPage = 1;
                renderCourses();
            }

            // Limpiar filtros
            document.getElementById('clearFiltersBtn').addEventListener('click', function() {
                document.getElementById('searchInput').value = '';
                document.getElementById('areaSelect').value = '';
                document.getElementById('modalidadSelect').value = '';
                document.getElementById('estadoSelect').value = '';
                filterCourses();
            });

            // Búsqueda en tiempo real
            document.getElementById('searchInput').addEventListener('input', filterCourses);
            document.getElementById('areaSelect').addEventListener('change', filterCourses);
            document.getElementById('modalidadSelect').addEventListener('change', filterCourses);
            document.getElementById('estadoSelect').addEventListener('change', filterCourses);

            // ===== MODALES =====
            function openCourseModal(id) {
                const course = courses.find(c => c.id === id);
                if (!course) return;

                selectedCourseId = id;
                document.getElementById('modalCourseTitle').textContent = course.title;

                let requisitosHtml = '';
                if (course.requisitos) {
                    requisitosHtml = course.requisitos.map(req => `<li>${req}</li>`).join('');
                }

                let beneficiosHtml = '';
                if (course.beneficios) {
                    beneficiosHtml = course.beneficios.map(b => `<li>${b}</li>`).join('');
                }

                const body = document.getElementById('modalCourseBody');
                body.innerHTML = `
                        <div class="detail-row">
                            <span class="detail-label"><i class="bi bi-building"></i> Institución</span>
                            <span class="detail-value">${course.institution}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label"><i class="bi bi-grid"></i> Modalidad</span>
                            <span class="detail-value">${course.modalidad.charAt(0).toUpperCase() + course.modalidad.slice(1)}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label"><i class="bi bi-people"></i> Cupos</span>
                            <span class="detail-value">${course.spots > 0 ? course.spots : 'Completo'}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label"><i class="bi bi-calendar-event"></i> Inicio</span>
                            <span class="detail-value">${course.start_date}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label"><i class="bi bi-clock"></i> Duración</span>
                            <span class="detail-value">${course.duration}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label"><i class="bi bi-info-circle"></i> Descripción</span>
                            <span class="detail-value">${course.description}</span>
                        </div>
                        ${requisitosHtml ? `<div class="detail-row">
                            <span class="detail-label"><i class="bi bi-list-check"></i> Requisitos</span>
                            <div class="detail-value"><ul class="requirements-list">${requisitosHtml}</ul></div>
                        </div>` : ''}
                        ${beneficiosHtml ? `<div class="detail-row">
                            <span class="detail-label"><i class="bi bi-gift"></i> Beneficios</span>
                            <div class="detail-value"><ul class="requirements-list">${beneficiosHtml}</ul></div>
                        </div>` : ''}
                    `;

                const btnSolicitar = document.getElementById('modalSolicitarBtn');
                if (course.estado === 'abierto') {
                    btnSolicitar.style.display = 'inline-block';
                    btnSolicitar.textContent = 'Solicitar plaza';
                    btnSolicitar.className = 'btn btn-green';
                    btnSolicitar.onclick = function() { openSolicitudModal(id); };
                } else {
                    btnSolicitar.style.display = 'inline-block';
                    btnSolicitar.textContent = course.estado === 'proximo' ? 'Próximamente' : 'No disponible';
                    btnSolicitar.className = 'btn btn-secondary';
                    btnSolicitar.onclick = function() { alert('Este curso no está actualmente abierto para inscripciones.'); };
                }

                const modal = new bootstrap.Modal(document.getElementById('courseModal'));
                modal.show();
            }

            function openSolicitudModal(courseId) {
                const id = courseId || selectedCourseId;
                const course = courses.find(c => c.id === id);
                if (!course) {
                    alert('Curso no encontrado');
                    return;
                }

                document.getElementById('solicitudCursoNombre').textContent = course.title;
                document.getElementById('solicitudNombre').value = '';
                document.getElementById('solicitudApellidos').value = '';
                document.getElementById('solicitudDIP').value = '';
                document.getElementById('solicitudTelefono').value = '';
                document.getElementById('solicitudEmail').value = '';
                document.getElementById('solicitudMotivo').value = '';

                document.getElementById('enviarSolicitudBtn').dataset.courseId = id;

                const modal = new bootstrap.Modal(document.getElementById('solicitudModal'));
                modal.show();
            }

            document.getElementById('enviarSolicitudBtn').addEventListener('click', function() {
                const nombre = document.getElementById('solicitudNombre').value.trim();
                const apellidos = document.getElementById('solicitudApellidos').value.trim();
                const dip = document.getElementById('solicitudDIP').value.trim();
                const telefono = document.getElementById('solicitudTelefono').value.trim();
                const email = document.getElementById('solicitudEmail').value.trim();

                if (!nombre || !apellidos || !dip || !telefono || !email) {
                    alert('Por favor, complete todos los campos obligatorios (*).');
                    return;
                }

                const solicitudModal = bootstrap.Modal.getInstance(document.getElementById('solicitudModal'));
                solicitudModal.hide();

                const confirmacionModal = new bootstrap.Modal(document.getElementById('confirmacionModal'));
                confirmacionModal.show();

                console.log('Solicitud enviada para el curso ID:', this.dataset.courseId);
                console.log('Datos:', { nombre, apellidos, dip, telefono, email });
            });

            // ===== INICIALIZAR =====
            renderCourses();

            // ===== CANVAS ANIMADO (ya está en el footer, pero lo dejamos por si acaso) =====
        </script>

    </body>
    </html>
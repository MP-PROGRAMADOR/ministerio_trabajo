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
            border-bottom: 3px solid var(--gov-gold);
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

        /* ===== NOTIFICACIONES ===== */
        .notificacion-item {
            padding: 1rem 1.25rem;
            border-left: 4px solid transparent;
            border-radius: var(--gov-radius);
            background: #ffffff;
            margin-bottom: 0.75rem;
            transition: all 0.2s;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }
        .notificacion-item:hover {
            background: #f8fafc;
            border-color: var(--gov-gold);
            transform: translateX(4px);
        }
        .notificacion-item.no-leida {
            background: #f0f7ff;
            border-left-color: var(--gov-blue);
        }
        .notificacion-item .noti-icon {
            font-size: 1.3rem;
            color: var(--gov-blue);
            width: 2.5rem;
            text-align: center;
        }
        .notificacion-item .noti-titulo {
            font-weight: 600;
            color: var(--gov-dark);
            font-size: 0.95rem;
        }
        .notificacion-item .noti-desc {
            font-size: 0.85rem;
            color: #6b7a8a;
            margin: 0;
        }
        .notificacion-item .noti-fecha {
            font-size: 0.7rem;
            color: #9aa5b5;
            white-space: nowrap;
        }
        .badge-no-leida {
            background: var(--gov-blue);
            color: white;
            font-size: 0.6rem;
            padding: 0.2rem 0.6rem;
            border-radius: 30px;
        }

        /* ===== BOTONES ===== */
        .btn-outline-secondary {
            border: 2px solid var(--gov-border);
            color: var(--gov-dark);
            background: transparent;
            border-radius: var(--gov-radius-sm);
            font-weight: 500;
            transition: all 0.25s;
        }
        .btn-outline-secondary:hover {
            background: var(--gov-blue);
            color: white;
            border-color: var(--gov-blue);
        }
        .badge-soft-blue {
            background: rgba(11, 58, 96, 0.08);
            color: var(--gov-blue);
            font-weight: 500;
            padding: 0.3rem 0.9rem;
            border-radius: 20px;
            font-size: 0.75rem;
        }

        /* ===== FOOTER ===== */
        footer {
            background: rgba(255, 255, 255, 0.9) !important;
            backdrop-filter: blur(8px);
            border-top: 1px solid rgba(201, 168, 76, 0.15);
        }

        @media (max-width: 576px) {
            .dashboard-card { padding: 1.5rem !important; }
        }
    </style>
</head>
<body>

    <canvas id="canvas-background"></canvas>

    <div class="main-wrapper">

         <?php include '../componentes/menu_desempleado.php'; ?>

        <main class="container py-5 flex-grow-1">
            <div class="row g-4">

                <!-- TÍTULO DE LA PÁGINA -->
                <div class="col-12">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <i class="bi bi-bell fs-1" style="color: var(--gov-gold);"></i>
                        <div>
                            <h1 class="h3 fw-bold m-0" style="color: var(--gov-dark);">Notificaciones</h1>
                            <p class="text-muted m-0">Revisa los últimos avisos y actualizaciones de tu cuenta</p>
                        </div>
                    </div>
                    <hr class="mb-4">
                </div>

                <!-- LISTA DE NOTIFICACIONES -->
                <div class="col-12">
                    <div class="card dashboard-card p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold m-0 h6 text-uppercase tracking-wider text-muted">
                                <i class="bi bi-list-ul me-2"></i>Últimas notificaciones
                            </h5>
                            <span class="badge-soft-blue" id="notiCount">3 no leídas</span>
                        </div>

                        <div id="notificacionesList">
                            <!-- Generado por JS -->
                        </div>

                        <!-- BOTÓN MARCAR TODAS COMO LEÍDAS -->
                        <div class="mt-3 text-end">
                            <button class="btn btn-sm btn-outline-secondary" id="marcarLeidasBtn">
                                <i class="bi bi-check2-all me-1"></i>Marcar todas como leídas
                            </button>
                        </div>
                    </div>
                </div>

             

            </div>
        </main>

        <?php include '../componentes/footer_desempleado.php'; ?>

        <script>
            // ===== DATOS DE EJEMPLO =====
            const notificaciones = [
                { id: 1, titulo: 'Nueva oferta de empleo en tu sector', descripcion: 'Se ha publicado una vacante para Técnico de Soporte TI en GETESA, Malabo.', fecha: 'Hace 2 horas', leida: false, icono: 'bi-briefcase' },
                { id: 2, titulo: 'Actualización de estado de candidatura', descripcion: 'Tu postulación a SOMAGEC ha pasado a estado "En Revisión Ministerial".', fecha: 'Hace 1 día', leida: false, icono: 'bi-clock-history' },
                { id: 3, titulo: 'Nuevo curso disponible', descripcion: 'Se ha abierto la inscripción para el curso de "Administración de Redes y Ciberseguridad".', fecha: 'Hace 3 días', leida: false, icono: 'bi-journal-bookmark' },
                { id: 4, titulo: 'Recordatorio: completar tu perfil', descripcion: 'Tu expediente digital está incompleto. Adjunta tu DIP y CV para acceder a más ofertas.', fecha: 'Hace 5 días', leida: true, icono: 'bi-exclamation-triangle' },
                { id: 5, titulo: 'Entrevista programada', descripcion: 'Has sido preseleccionado para el puesto de Ingeniero Civil en SOMAGEC. Pronto recibirás la fecha de la entrevista.', fecha: 'Hace 1 semana', leida: true, icono: 'bi-calendar-check' },
            ];

            // Función para renderizar las notificaciones
            function renderNotificaciones() {
                const container = document.getElementById('notificacionesList');
                const noLeidas = notificaciones.filter(n => !n.leida).length;
                document.getElementById('notiCount').textContent = `${noLeidas} no leídas`;

                if (notificaciones.length === 0) {
                    container.innerHTML = `
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                            No tienes notificaciones en este momento.
                        </div>
                    `;
                    return;
                }

                let html = '';
                notificaciones.forEach(noti => {
                    const claseLeida = noti.leida ? '' : 'no-leida';
                    const icono = noti.icono || 'bi-bell';
                    html += `
                        <div class="notificacion-item ${claseLeida} d-flex align-items-start gap-3">
                            <div class="noti-icon"><i class="bi ${icono}"></i></div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="noti-titulo">${noti.titulo}</div>
                                        <p class="noti-desc">${noti.descripcion}</p>
                                    </div>
                                    <span class="noti-fecha">${noti.fecha}</span>
                                </div>
                                ${!noti.leida ? '<span class="badge-no-leida">Nueva</span>' : ''}
                            </div>
                        </div>
                    `;
                });
                container.innerHTML = html;
            }

            // Marcar todas como leídas
            document.getElementById('marcarLeidasBtn').addEventListener('click', function() {
                notificaciones.forEach(n => n.leida = true);
                renderNotificaciones();
                alert('✅ Todas las notificaciones han sido marcadas como leídas.');
            });

            // Inicializar
            renderNotificaciones();
        </script>

    </body>
    </html>
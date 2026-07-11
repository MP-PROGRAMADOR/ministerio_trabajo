<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control - Portal de Empleo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --gov-blue: #007bff;
            --gov-green: #28a745;
            --gov-dark-blue: #0a2540;
            --gov-bg: #f4f7fa;
            --gov-gold: #ffc107;
        }

        body,
        html {
            min-height: 100vh;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--gov-bg);
            color: var(--gov-dark-blue);
            position: relative;
        }

        /* CANVAS EN MODO CLARO PROFESIONAL (VISIBLE) */
        #canvas-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            pointer-events: none;
            opacity: 0.6;
        }

        .main-wrapper {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* NAVBAR (Prioridad Z-Index para evitar superposiciones) */
        .navbar-portal {
            background-color: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
            border-bottom: 3px solid var(--gov-blue);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            position: relative;
            z-index: 1050;
        }

        /* TARJETAS CON EFECTO GLASSMORPHISM CLARO */
        .dashboard-card {
            background-color: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
            backdrop-filter: blur(8px);
            transition: all 0.25s ease;
        }

        .dashboard-card:hover {
            border-color: var(--gov-blue);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
        }

        /* DROPDOWN DE PERFIL */
        .profile-dropdown {
            position: relative !important; /* Asegura el contexto de origen absoluto */
        }

        .profile-menu-img {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            border: 2px solid var(--gov-green);
            object-fit: cover;
            cursor: pointer;
        }

        /* Regla base del menú desplegable */
        .custom-profile-menu {
            background-color: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 10px !important;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
            padding: 8px !important;
            z-index: 1060 !important;
        }

        /* 1. COMPORTAMIENTO PARA ESCRITORIO (>= 992px) */
        @media (min-width: 992px) {
            .custom-profile-menu {
                position: absolute !important;
                right: 0 !important;
                left: auto !important;
                min-width: 250px;
                transform: translateY(5px) !important;
            }

            .profile-dropdown:hover .custom-profile-menu {
                display: block !important;
                opacity: 1 !important;
                visibility: visible !important;
            }
        }

        /* 2. REESTRUCTURACIÓN COMPLETA PARA MÓVILES (< 992px) */
        @media (max-width: 991.98px) {
            .custom-profile-menu {
                position: absolute !important;
                /* Centramos el menú con respecto al botón del avatar */
                left: 50% !important;
                right: auto !important;
                /* El primer valor (-82%) desplaza el menú a la izquierda lo suficiente para que no se corte a la derecha */
                transform: translate(-82%, 10px) !important; 
                width: 260px !important;
                max-width: calc(100vw - 30px) !important;
                box-sizing: border-box !important;
            }

            .profile-dropdown:hover .custom-profile-menu {
                display: block !important;
                opacity: 1 !important;
                visibility: visible !important;
            }
        }

        /* TIMELINE DE POSTULACIONES */
        .timeline-item {
            position: relative;
            padding-left: 25px;
            border-left: 2px solid #e2e8f0;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -6px;
            top: 5px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: var(--gov-blue);
        }

        /* ELEMENTOS DE LISTAS */
        .list-item-custom {
            background-color: rgba(248, 250, 252, 0.8);
            border: 1px solid #edf2f7;
            border-radius: 10px;
            transition: all 0.2s ease;
        }

        .list-item-custom:hover {
            background-color: #f1f5f9;
            transform: translateX(3px);
        }

        /* ALERTA PERFIL */
        .alert-profile-incomplete {
            background-color: rgba(255, 245, 245, 0.95);
            border-left: 5px solid #dc3545;
            border-radius: 8px;
            color: #7a1c1c;
        }
    </style>
</head>

<body>

    <canvas id="canvas-background"></canvas>

    <div class="main-wrapper">

        <nav class="navbar navbar-expand-lg navbar-light navbar-portal py-2">
            <div class="container">
                
                <a class="navbar-brand fw-bold d-flex align-items-center gap-3" href="#">
                    <img src="../img/logo_,ministerio.png" alt="Escudo de Guinea Ecuatorial" style="height: 45px; width: auto; object-fit: contain;" onerror="this.src='https://placehold.co/45x50?text=Logo'">
                    
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
                            <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=150&q=80" class="profile-menu-img" alt="Foto de Perfil">
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
                        <li class="nav-item"><a class="nav-link active fw-bold text-primary" href="#">Panel General</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Bolsa de Trabajo</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Cursos Públicos</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <main class="container py-5 flex-grow-1">

            <div id="incompleteProfileBanner" class="alert alert-profile-incomplete p-4 mb-4 d-none">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <div class="d-flex align-items-start gap-3">
                        <i class="bi bi-exclamation-octagon-fill text-danger fs-3 mt-1"></i>
                        <div>
                            <h4 class="fw-bold m-0 h5 text-dark">Expediente Digital Incompleto</h4>
                            <p class="m-0 text-muted small mt-1">Conforme a la normativa del Sistema Nacional de Empleo,
                                debe completar su perfil adjuntando su Documento de Identidad Personal (DIP) y su
                                Currículum Vitae para acceder a la visualización de ofertas laborales vigentes.</p>
                        </div>
                    </div>
                    <a href="#" class="btn btn-danger btn-sm text-nowrap px-4 py-2 rounded-pill fw-bold shadow-sm">
                        <i class="bi bi-pencil-square me-1"></i> Completar Perfil Ahora
                    </a>
                </div>
            </div>

            <div class="row g-4">

                <div class="col-xl-4 col-lg-5">
                    <div class="card dashboard-card p-4 text-center mb-4">
                        <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=300&q=80"
                            class="rounded-circle border border-3 border-light mx-auto mb-3 shadow-sm"
                            style="width: 100px; height: 100px; object-fit: cover;" alt="Foto">
                        <h4 class="fw-bold m-0 h5 text-dark">Juan Carlos Nsue</h4>
                        <p class="text-muted small mb-3">ID Expediente: <strong class="text-primary">#EG-94821</strong>
                        </p>

                        <div class="p-3 bg-light bg-opacity-50 rounded border text-start small mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Residencia:</span>
                                <span class="fw-semibold text-dark">Malabo, Bioko Norte</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Progreso:</span>
                                <span id="profilePercentage" class="fw-bold text-danger">40%</span>
                            </div>
                            <div class="progress mt-2" style="height: 6px; background-color: #e2e8f0;">
                                <div id="profileProgressBar" class="progress-bar bg-danger" role="progressbar"
                                    style="width: 40%"></div>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button class="btn btn-primary btn-sm rounded-pill fw-semibold"><i
                                    class="bi bi-qr-code me-2"></i> Descargar Tarjeta Demandante</button>
                        </div>
                    </div>

                    <div class="card dashboard-card p-4">
                        <h5 class="fw-bold mb-3 h6 text-uppercase tracking-wider text-muted"><i
                                class="bi bi-chat-left-text text-primary me-2"></i>Notificaciones de Oficina</h5>
                        <div class="d-flex flex-column gap-2">
                            <div
                                class="p-2 rounded bg-warning bg-opacity-10 border border-warning border-opacity-20 small">
                                <strong class="text-warning-emphasis d-block">Revisión de DIP Pendiente</strong>
                                <span class="text-muted" style="font-size: 0.75rem;">Su documento escaneado debe ser
                                    legible.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-8 col-lg-7">

                    <div id="jobOffersSection">
                        <div class="card dashboard-card p-4 mb-4">
                            <h5 class="fw-bold mb-3 h6 text-uppercase tracking-wider text-muted">Buscador del Sistema
                            </h5>
                            <form class="input-group">
                                <input type="text" class="form-control"
                                    style="border-radius: 30px 0 0 30px; border-color: #cbd5e1;"
                                    placeholder="Buscar cargos o sectores...">
                                <button class="btn btn-primary text-white px-4" type="button"><i
                                        class="bi bi-search"></i></button>
                            </form>
                        </div>

                        <div class="card dashboard-card p-4 mb-4">
                            <h5 class="fw-bold mb-3 h6 text-uppercase tracking-wider text-muted">Ofertas Recomendadas
                            </h5>
                            <div class="d-flex flex-column gap-3">
                                <div
                                    class="list-item-custom p-3 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                                    <div>
                                        <h6 class="fw-bold m-0 text-dark">Técnico de Soporte TI</h6>
                                        <p class="m-0 small text-muted">GETESA — Malabo</p>
                                    </div>
                                    <button class="btn btn-sm btn-primary rounded-pill px-4">Postularme</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card dashboard-card p-4 mb-4">
                        <h5 class="fw-bold mb-3 h6 text-uppercase tracking-wider text-muted">Estado de mis Candidaturas
                        </h5>
                        <div class="d-flex flex-column gap-3 mt-2">
                            <div class="timeline-item">
                                <span class="badge bg-light text-primary border border-primary mb-1"
                                    style="font-size: 0.65rem;">05/07/2026</span>
                                <h6 class="fw-bold text-dark m-0 small">Postulación enviada a SOMAGEC</h6>
                                <p class="text-muted m-0 small" style="font-size: 0.8rem;">Puesto: Auxiliar Técnico de
                                    Obras. Estado: <span class="text-warning fw-bold">En Revisión Ministerial</span></p>
                            </div>
                        </div>
                    </div>

                    <div class="card dashboard-card p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold m-0 h6 text-uppercase tracking-wider text-muted"><i
                                    class="bi bi-journal-bookmark text-success me-2"></i>Planes Estatales de
                                Capacitación</h5>
                            <span class="badge bg-success rounded-pill small">Cupos Abiertos</span>
                        </div>
                        <div
                            class="p-3 rounded list-item-custom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                            <div>
                                <span class="badge bg-primary mb-1" style="font-size: 0.65rem;">Presencial</span>
                                <h6 class="fw-bold m-0 text-dark">Administración de Redes y Ciberseguridad</h6>
                                <p class="text-muted small m-0 mt-1">Programa oficial subvencionado enfocado al sector
                                    telecomunicaciones nacional.</p>
                            </div>
                            <a href="#"
                                class="btn btn-sm btn-outline-success rounded-pill px-3 fw-bold text-nowrap">Solicitar
                                Plaza</a>
                        </div>
                    </div>

                </div>
            </div>
        </main>

        <footer class="bg-white text-center py-4 text-muted small mt-auto border-top">
            <div class="container">
                <p class="m-0">&copy; 2026 Ministerio de Trabajo, Fomento del Empleo y Seguridad Social.</p>
                <p class="m-0 text-uppercase tracking-wider"
                    style="font-size: 0.65rem; color: var(--gov-green); font-weight: 700;">Unidad • Paz • Justicia</p>
            </div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // CONFIGURACIÓN DE VISTA
        const perfilCompleto = false;

        function evaluarEstadoPerfil() {
            const banner = document.getElementById('incompleteProfileBanner');
            const ofertas = document.getElementById('jobOffersSection');
            const badgePct = document.getElementById('profilePercentage');
            const progressBar = document.getElementById('profileProgressBar');

            if (perfilCompleto) {
                banner.classList.add('d-none');
                ofertas.classList.remove('d-none');
                badgePct.innerText = "100%";
                badgePct.className = "fw-bold text-success";
                progressBar.style.width = "100%";
                progressBar.className = "progress-bar bg-success";
            } else {
                banner.classList.remove('d-none');
                ofertas.classList.add('d-none');
                badgePct.innerText = "40%";
                badgePct.className = "fw-bold text-danger";
                progressBar.style.width = "40%";
                progressBar.className = "progress-bar bg-danger";
            }
        }
        evaluarEstadoPerfil();

        // CANVAS ANIMADO
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
                ctx.fillStyle = 'rgba(0, 123, 255, 0.25)';
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
                        ctx.strokeStyle = `rgba(0, 123, 255, ${0.15 * (1 - dist / maxDistance)})`;
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
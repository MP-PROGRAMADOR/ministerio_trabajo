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

        /* ===== NAVBAR (incluido desde el header) ===== */
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

        /* ===== FORMULARIOS ===== */
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

        /* ===== ESTILOS ADICIONALES PARA PERFIL ===== */
        .border-gold {
            border-color: var(--gov-gold) !important;
        }
        .position-relative .rounded-circle {
            transition: transform 0.3s ease;
        }
        .position-relative .rounded-circle:hover {
            transform: scale(1.02);
        }
        .position-relative .bg-white {
            cursor: pointer;
            transition: all 0.2s;
        }
        .position-relative .bg-white:hover {
            background-color: var(--gov-gold-light) !important;
            border-color: var(--gov-gold) !important;
        }
        .position-relative .bg-white:hover i {
            color: var(--gov-gold) !important;
        }
        hr {
            opacity: 0.5;
        }
        #passwordHelp {
            font-size: 0.8rem;
            margin-top: 0.25rem;
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
                        <i class="bi bi-person-gear fs-1" style="color: var(--gov-blue);"></i>
                        <div>
                            <h1 class="h3 fw-bold m-0" style="color: var(--gov-dark);">Mi Perfil</h1>
                            <p class="text-muted m-0">Gestiona tus datos personales y configuración de seguridad</p>
                        </div>
                    </div>
                    <hr class="mb-4">
                </div>

                <!-- COLUMNA IZQUIERDA: DATOS PERSONALES -->
                <div class="col-lg-6">
                    <div class="card dashboard-card p-4 h-100">
                        <h5 class="fw-bold mb-3 h6 text-uppercase tracking-wider text-muted">
                            <i class="bi bi-person me-2" style="color: var(--gov-blue);"></i>Datos Personales
                        </h5>
                        <form id="perfilForm" onsubmit="guardarPerfil(event)">
                            <div class="row g-3">
                                <div class="col-12 text-center mb-3">
                                    <div class="position-relative d-inline-block">
                                        <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=300&q=80"
                                            class="rounded-circle border border-3 border-gold shadow-sm"
                                            style="width: 120px; height: 120px; object-fit: cover;" alt="Foto de perfil">
                                        <div class="position-absolute bottom-0 end-0 bg-white rounded-circle p-1 shadow-sm" style="border: 2px solid var(--gov-gold);">
                                            <i class="bi bi-camera-fill" style="color: var(--gov-blue); font-size: 1.2rem;"></i>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <label class="form-label fw-semibold">Cambiar foto de perfil</label>
                                        <input type="file" class="form-control form-control-custom" accept="image/*">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"><i class="bi bi-person me-1"></i>Nombre completo *</label>
                                    <input type="text" class="form-control form-control-custom" value="Ana Trini Maye" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"><i class="bi bi-card-text me-1"></i>DIP *</label>
                                    <input type="text" class="form-control form-control-custom" value="123456789" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"><i class="bi bi-telephone me-1"></i>Teléfono *</label>
                                    <input type="tel" class="form-control form-control-custom" value="555-123456" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"><i class="bi bi-envelope me-1"></i>Correo electrónico *</label>
                                    <input type="email" class="form-control form-control-custom" value="ana@correo.gq" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold"><i class="bi bi-geo-alt me-1"></i>Residencia</label>
                                    <select class="form-select form-select-custom">
                                        <option>Malabo, Bioko Norte</option>
                                        <option>Bata, Litoral</option>
                                        <option>Ebebiyín, Kié-Ntem</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-gov w-100">
                                        <i class="bi bi-check2-circle me-2"></i>Actualizar datos
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- COLUMNA DERECHA: SEGURIDAD -->
                <div class="col-lg-6">
                    <div class="card dashboard-card p-4 h-100">
                        <h5 class="fw-bold mb-3 h6 text-uppercase tracking-wider text-muted">
                            <i class="bi bi-shield-lock me-2" style="color: var(--gov-green);"></i>Seguridad y Acceso
                        </h5>
                        <form id="seguridadForm" onsubmit="guardarSeguridad(event)">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-semibold"><i class="bi bi-person-badge me-1"></i>Nombre de usuario</label>
                                    <input type="text" class="form-control form-control-custom" value="anamaye" required>
                                    <div class="form-text">Este nombre lo usarás para iniciar sesión.</div>
                                </div>
                                <div class="col-12">
                                    <hr>
                                    <h6 class="fw-semibold text-muted"><i class="bi bi-key me-1"></i>Cambiar contraseña</h6>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Contraseña actual *</label>
                                    <input type="password" class="form-control form-control-custom" placeholder="Introduce tu contraseña actual" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Nueva contraseña *</label>
                                    <input type="password" id="newPassword" class="form-control form-control-custom" placeholder="Mínimo 8 caracteres" pattern=".{8,}" title="La contraseña debe tener al menos 8 caracteres" required>
                                    <div class="form-text">Mínimo 8 caracteres.</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Confirmar nueva contraseña *</label>
                                    <input type="password" id="confirmPassword" class="form-control form-control-custom" placeholder="Repite la nueva contraseña" required>
                                    <div id="passwordHelp" class="form-text text-danger d-none">Las contraseñas no coinciden.</div>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-green w-100">
                                        <i class="bi bi-shield-check me-2"></i>Actualizar seguridad
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                

            </div>
        </main>

        <?php include '../componentes/footer_desempleado.php'; ?>

        <script>
            // Validar confirmación de contraseña en tiempo real
            document.getElementById('confirmPassword').addEventListener('input', function() {
                const newPw = document.getElementById('newPassword').value;
                const confirmPw = this.value;
                const help = document.getElementById('passwordHelp');
                if (confirmPw.length > 0 && newPw !== confirmPw) {
                    help.classList.remove('d-none');
                    this.classList.add('is-invalid');
                } else {
                    help.classList.add('d-none');
                    this.classList.remove('is-invalid');
                }
            });

            document.getElementById('newPassword').addEventListener('input', function() {
                const confirmPw = document.getElementById('confirmPassword').value;
                const help = document.getElementById('passwordHelp');
                if (confirmPw.length > 0 && this.value !== confirmPw) {
                    help.classList.remove('d-none');
                    document.getElementById('confirmPassword').classList.add('is-invalid');
                } else {
                    help.classList.add('d-none');
                    document.getElementById('confirmPassword').classList.remove('is-invalid');
                }
            });

            function guardarPerfil(e) {
                e.preventDefault();
                alert('✅ Datos personales actualizados correctamente.');
            }

            function guardarSeguridad(e) {
                e.preventDefault();
                const newPw = document.getElementById('newPassword').value;
                const confirmPw = document.getElementById('confirmPassword').value;
                if (newPw !== confirmPw) {
                    alert('❌ Las contraseñas no coinciden. Por favor, verifícalas.');
                    return;
                }
                if (newPw.length < 8) {
                    alert('❌ La nueva contraseña debe tener al menos 8 caracteres.');
                    return;
                }
                alert('✅ Configuración de seguridad actualizada correctamente.');
            }
        </script>

    </body>
    </html>
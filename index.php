<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal de Empleo Nacional</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        /* Colores institucionales ajustados de la portada */
        :root {
            --azul-marino: #112d6e;
            --dorado: #d4a017;
            --rojo-bandera: #ce1126;
            --gris-premium: #4a5568;
        }

        body,
        html {
            height: 100%;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .bg-portal {
            background: linear-gradient(rgba(26, 66, 106, 0.85), rgba(26, 66, 106, 0.85)),
                url('https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?auto=format&fit=crop&w=1920&q=80') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            color: #ffffff;
        }

        /* Tarjetas de acceso profesionales y pulidas */
        .access-card {
            background: rgba(255, 255, 255, 0.96);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            cursor: pointer;
            min-height: 220px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            padding: 2rem;
        }

        .access-card:hover {
            transform: translateY(-8px);
            background-color: #ffffff;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            border-color: var(--dorado);
        }

        .access-card .icon-box {
            font-size: 3.5rem;
            color: var(--azul-marino);
            margin-bottom: 16px;
            transition: transform 0.3s ease;
        }

        .access-card:hover .icon-box {
            transform: scale(1.08);
        }

        .access-card .card-title {
            color: var(--azul-marino);
            font-weight: 700;
            font-size: 1.4rem;
            letter-spacing: -0.02em;
        }

        /* ===== NUEVO SEPARADOR SOBRIO Y PROFESIONAL ===== */
        .divider-line {
            width: 3px;
            background: linear-gradient(to bottom, rgba(212, 160, 23, 0) 0%, rgba(212, 160, 23, 0.7) 50%, rgba(212, 160, 23, 0) 100%);
            height: 140px;
            margin: 0 auto;
            position: relative;
        }

        /* Un sutil rombo corporativo en lugar del círculo brillante anterior */
        .divider-dot {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(45deg);
            width: 10px;
            height: 10px;
            background-color: var(--dorado);
            border: 1px solid rgba(255, 255, 255, 0.6);
        }

        /* Logo escudo */
        .logo-escudo img {
            max-height: 85px;
            width: auto;
            filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.3));
        }

        .tracking-wide {
            letter-spacing: 2px;
        }

        /* Estilo para los Toasts de notificación */
        .toast-container {
            z-index: 1060;
        }

        .custom-toast {
            background: rgba(255, 255, 255, 0.98) !important;
            border-radius: 12px !important;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2) !important;
            border-left: 4px solid var(--azul-marino) !important;
        }

        .custom-toast.toast-admin {
            border-left-color: var(--rojo-bandera) !important;
        }
    </style>
</head>

<body>

    <div class="bg-portal">
        <!-- Cabecera simple -->
        <header class="text-center py-3 bg-dark bg-opacity-25 border-bottom border-secondary">
            <p class="m-0 small text-uppercase tracking-wider">
                Bienvenido al Portal de Empleo
            </p>
        </header>

        <main class="container my-auto py-5">
            <!-- Logo e identidad -->
            <div class="text-center mb-5">
                <div class="mb-3">
                    <div class="logo-escudo">
                        <img src="./img/logo.png" alt="Escudo del Ministerio">
                    </div>
                </div>
                <h1 class="h2 text-uppercase fw-extrabold m-0 tracking-wide text-white">Ministerio de Trabajo</h1>
                <p class="text-white-50 small text-uppercase tracking-wider mt-1">Plataforma de Intermediación Laboral
                </p>
            </div>

            <!-- Tarjetas de acceso -->
            <div class="row justify-content-center align-items-center g-4 position-relative">

                <!-- Acceso Corporativo (Empresas) -->
                <div class="col-md-5">
                    <div class="card access-card text-center" onclick="redireccionar('empleador')">
                        <div class="icon-box">
                            <i class="bi bi-building-fill-check text-success"></i>
                        </div>
                        <h2 class="card-title">Acceso Corporativo</h2>
                        <p class="text-muted small m-0 mt-1">Empresas, PyMEs y Autónomos autorizados</p>
                    </div>
                </div>

                <!-- ===== SEPARADOR MEJORADO SIN BRILLOS ===== -->
                <div class="col-md-1 d-none d-md-block text-center h-100">
                    <div class="divider-line">
                        <div class="divider-dot"></div>
                    </div>
                </div>

                <!-- Acceso Particular (Desempleados) -->
                <div class="col-md-5">
                    <div class="card access-card text-center" onclick="redireccionar('ciudadano')">
                        <div class="icon-box">
                            <i class="bi bi-person-bounding-box text-success"></i>
                        </div>
                        <h2 class="card-title">Acceso Particular</h2>
                        <p class="text-muted small m-0 mt-1">Buscadores de Empleo y Ciudadanos</p>
                    </div>
                </div>
            </div>

            <!-- Tercer acceso: Administración Pública -->
            <div class="row justify-content-center mt-5">
                <div class="col-md-4">
                    <div class="card access-card p-3 text-center" style="min-height: auto;"
                        onclick="redireccionar('ministerio')">
                        <div class="d-flex align-items-center justify-content-center gap-3">
                            <i class="bi bi-shield-lock-fill fs-4 text-success"></i>
                            <span class="fw-bold text-dark text-uppercase small tracking-wide">Administración
                                Pública</span>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-dark bg-opacity-50 text-center py-3 text-white-50 small">
            <div class="container d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
                <div>&copy; 2026 Ministerio de Trabajo y Empleo. Todos los derechos reservados.</div>
                <div class="d-flex gap-3 fs-5">
                    <a href="#" class="text-white-50"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="text-white-50"><i class="bi bi-twitter-x"></i></a>
                    <a href="#" class="text-white-50"><i class="bi bi-linkedin"></i></a>
                </div>
            </div>
        </footer>
    </div>

    <!-- CONTENEDOR DE TOASTS PARA REEMPLAZAR LOS ALERTS INVASIVOS -->
    <div class="toast-container position-fixed bottom-0 end-0 p-4">
        <div id="portalToast" class="toast custom-toast border-0" role="alert" aria-live="assertive" aria-atomic="true"
            data-bs-delay="4000">
            <div class="toast-header bg-transparent border-0 text-dark pt-3 px-3">
                <i id="toastIcon" class="bi me-2 fs-5"></i>
                <strong class="me-auto text-uppercase tracking-wide small" id="toastTitle">Sistema</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body text-secondary pb-3 px-3 pt-1" id="toastMessage">
                Procesando solicitud...
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function redireccionar(perfil) {
            const toastEl = document.getElementById('portalToast');
            const toast = new bootstrap.Toast(toastEl);

            const title = document.getElementById('toastTitle');
            const message = document.getElementById('toastMessage');
            const icon = document.getElementById('toastIcon');

            // Resetear clases de administrador
            toastEl.classList.remove('toast-admin');
            icon.className = 'bi me-2 fs-5';

            switch (perfil) {
                case 'empleador':
                    title.innerText = 'Portal Corporativo';
                    // Insertamos el texto y un spinner de carga de Bootstrap justo debajo
                            message.innerHTML = `
                        Redirigiendo de forma segura al entorno de Gestión de Empresas y Ofertas...
                        <div class="d-flex justify-content-center mt-3">
                            <div class="spinner-border text-success" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                        </div>
                    `;

                    // Configuramos el icono y mostramos el Toast
                    icon.className = 'bi bi-building-fill-check text-success fs-3 me-2';
                    toast.show();

                    // Temporizador de 2.5 segundos para dar tiempo a ver el spinner antes de cambiar de página
                    setTimeout(() => {
                        window.location.href = 'login_empleadores.php';
                    }, 2500);
                    break;
                case 'ciudadano':
                    title.innerText = 'Portal Ciudadano';
                    // Mensaje institucional con spinner en color azul (text-primary)
                            message.innerHTML = `
                    Conectando con la base de datos nacional de empleo y carga de CV...
                    <div class="d-flex justify-content-center mt-3">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                    </div>
                `;

                    icon.className = 'bi bi-person-bounding-box text-primary fs-3 me-2';
                    toast.show();

                    // Redirección segura tras 2.5 segundos
                    setTimeout(() => {
                        window.location.href = 'login_desempleados.php';
                    }, 2500);
                    break;

                case 'ministerio':
                    toastEl.classList.add('toast-admin');
                    title.innerText = 'Acceso Restringido';
                    // Mensaje de advertencia de seguridad con spinner en color rojo institucional (text-danger)
                    message.innerHTML = `
                    Alerta: El acceso seleccionado está estrictamente limitado a personal y funcionarios del Ministerio.
                    <div class="d-flex justify-content-center mt-3">
                        <div class="spinner-border text-danger" role="status">
                            <span class="visually-hidden">Validando credenciales...</span>
                        </div>
                    </div>
                `;

                    icon.className = 'bi bi-shield-slash-fill text-danger fs-3 me-2';
                    toast.show();

                    // Redirección segura tras 2.5 segundos al entorno de administración
                    setTimeout(() => {
                        window.location.href = 'login_admin.php';
                    }, 2500);
                    break;
                default:
                    console.log('Perfil no reconocido');
            }
        }
    </script>
</body>

</html>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Ciudadanos - Portal de Empleo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        /* ===== COLORES INSTITUCIONALES ===== */
        :root {
            --azul-marino: rgba(26, 66, 106, 0.85);
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

        /* Fondo institucional con capa azul traslúcida */
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

        /* Carrusel */
        #citizenCarousel,
        .carousel-inner,
        .carousel-item {
            min-height: 400px;
            height: 100%;
        }

        .carousel-bg-img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .carousel-caption {
            z-index: 2;
            left: 8% !important;
            right: 8% !important;
            background: linear-gradient(transparent, rgba(0, 0, 0, 0.7));
            padding: 20px;
            border-radius: 8px;
        }

        .carousel-indicators [data-bs-target] {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin: 0 5px;
            z-index: 3;
        }

        /* ===== SEPARADOR CON ROMBO ===== */
        .divider-line {
            width: 3px;
            background: linear-gradient(to bottom, rgba(212, 160, 23, 0) 0%, rgba(212, 160, 23, 0.7) 50%, rgba(212, 160, 23, 0) 100%);
            height: 200px;
            margin: 0 auto;
            position: relative;
        }

        .divider-dot {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(45deg);
            width: 12px;
            height: 12px;
            background-color: var(--dorado);
            border: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 0 15px rgba(212, 160, 23, 0.3);
        }

        /* ===== INPUTS OSCUROS CON TRANSPARENCIA ===== */
        .custom-input-group {
            background-color: rgba(17, 45, 110, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 50px;
            padding: 4px 15px;
            margin-bottom: 20px;
            backdrop-filter: blur(4px);
            transition: border 0.3s;
        }

        .custom-input-group:hover,
        .custom-input-group:focus-within {
            border-color: var(--dorado);
        }

        .custom-input-group input {
            background: transparent !important;
            border: none !important;
            color: #ffffff !important;
            box-shadow: none !important;
        }

        .custom-input-group input::placeholder {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.9rem;
        }

        .custom-input-group .input-group-text {
            background: transparent !important;
            border: none !important;
            color: rgba(255, 255, 255, 0.6);
        }

        /* ===== BOTONES ===== */
        .btn-connect {
            background-color: #ffffff;
            color: var(--azul-marino);
            font-weight: 700;
            border-radius: 50px;
            padding: 10px 40px;
            border: 2px solid #ffffff;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-connect:hover {
            background-color: transparent;
            color: #ffffff;
            border-color: var(--dorado);
        }

        .btn-gov-id {
            background-color: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            border: 1px solid rgba(212, 160, 23, 0.5);
            border-radius: 50px;
            padding: 8px 20px;
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }

        .btn-gov-id:hover {
            background-color: var(--dorado);
            color: var(--azul-marino);
            border-color: var(--dorado);
        }

        .btn-register-outline {
            color: #ffffff;
            text-decoration: none;
            font-size: 0.9rem;
            border-bottom: 1px dashed rgba(212, 160, 23, 0.6);
            transition: opacity 0.2s;
        }

        .btn-register-outline:hover {
            opacity: 0.8;
            color: var(--dorado);
            border-bottom-color: var(--dorado);
        }

        /* ===== BOTÓN VOLVER ===== */
        .btn-back {
            color: #ffffff;
            text-decoration: none;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.25s ease;
            padding: 4px 8px;
            border: 1px solid transparent;
        }

        .btn-back:hover {
            color: var(--dorado);
        }




        .text-gold {
            color: var(--dorado);
        }

        .bg-azul-soft {
            background-color: rgba(17, 45, 110, 0.9);
        }

        /* Personalización para que el Toast se adapte a tu diseño oscuro */
        .custom-toast {
            background-color: rgba(10, 28, 72, 0.95) !important;
            border: 1px solid rgba(212, 160, 23, 0.4) !important;
            color: #ffffff !important;
            backdrop-filter: blur(8px);
        }

        .custom-toast .toast-header {
            background-color: rgba(17, 45, 110, 0.85) !important;
            color: #ffffff !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .custom-toast .btn-close {
            filter: invert(1);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .divider-line {
                height: 60px;
                width: 60%;
                margin: 20px auto;
                background: linear-gradient(to right, rgba(212, 160, 23, 0) 0%, rgba(212, 160, 23, 0.7) 50%, rgba(212, 160, 23, 0) 100%);
                min-height: auto;
            }

            .divider-dot {
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%) rotate(45deg);
            }
        }
    </style>
</head>

<body>

    <div class="bg-portal">
        <header class="text-center py-3 bg-dark bg-opacity-25 border-bottom border-secondary">
            <p class="m-0 small text-uppercase tracking-wider">
                Portal de Empleo - Área de Ciudadano desempleado
            </p>
        </header>

        <main class="container my-auto py-5">
            <div class="row align-items-center justify-content-center g-5">

                <!-- CARRUSEL PARA CIUDADANOS -->
                <div class="col-lg-4 col-md-5">
                    <div id="citizenCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel"
                        style="box-shadow: 0 10px 30px rgba(0,0,0,0.3); border-radius: 16px; overflow: hidden;">
                        <div class="carousel-indicators">
                            <button type="button" data-bs-target="#citizenCarousel" data-bs-slide-to="0" class="active"
                                aria-current="true" aria-label="Slide 1"></button>
                            <button type="button" data-bs-target="#citizenCarousel" data-bs-slide-to="1"
                                aria-label="Slide 2"></button>
                            <button type="button" data-bs-target="#citizenCarousel" data-bs-slide-to="2"
                                aria-label="Slide 3"></button>
                        </div>
                        <div class="carousel-inner">
                            <!-- Slide 1: Currículum -->
                            <div class="carousel-item active">
                                <div class="carousel-bg-img"
                                    style="background: linear-gradient(rgba(0, 0, 0, 0.2), rgba(17, 45, 110, 0.9)), url('img/img7.jpg');">
                                </div>
                                <div class="carousel-caption d-flex flex-column justify-content-end h-100 pb-5">
                                    <div class="mb-3 text-gold"><i class="bi bi-file-earmark-person fs-1"></i></div>
                                    <h3 class="h4 fw-bold text-white mb-2">Tu Currículum Profesional</h3>
                                    <p class="small text-white-50 m-0">Crea y actualiza tu hoja de vida con nuestro
                                        asistente guiado, destacando tus competencias ante las empresas.</p>
                                </div>
                            </div>
                            <!-- Slide 2: Capacitación -->
                            <div class="carousel-item">
                                <div class="carousel-bg-img"
                                    style="background: linear-gradient(rgba(0, 0, 0, 0.2), rgba(17, 45, 110, 0.9)), url('img/img5.jpeg');">
                                </div>
                                <div class="carousel-caption d-flex flex-column justify-content-end h-100 pb-5">
                                    <div class="mb-3 text-gold"><i class="bi bi-journal-bookmark-fill fs-1"></i></div>
                                    <h3 class="h4 fw-bold text-white mb-2">Capacitación y Formación</h3>
                                    <p class="small text-white-50 m-0">Accede a cursos certificados por el Ministerio,
                                        diseñados para mejorar tu empleabilidad sin coste alguno.</p>
                                </div>
                            </div>
                            <!-- Slide 3: Oportunidades -->
                            <div class="carousel-item">
                                <div class="carousel-bg-img"
                                    style="background: linear-gradient(rgba(0, 0, 0, 0.2), rgba(17, 45, 110, 0.9)), url('img/ima6.jpeg');">
                                </div>
                                <div class="carousel-caption d-flex flex-column justify-content-end h-100 pb-5">
                                    <div class="mb-3 text-gold"><i class="bi bi-award-fill fs-1"></i></div>
                                    <h3 class="h4 fw-bold text-white mb-2">Conectamos Oportunidades</h3>
                                    <p class="small text-white-50 m-0">Más de 50.000 ciudadanos han encontrado empleo
                                        formal a través de nuestra red de intermediación laboral.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-1 d-none d-md-block text-center">
                    <div class="divider-line">
                        <div class="divider-dot"></div>
                    </div>
                </div>

                <!-- FORMULARIO DE LOGIN -->
                <div class="col-lg-4 col-md-5 text-center text-md-start">
                    <div class="mb-4 text-center">
                        <i class="bi bi-person fs-1 text-gold mb-2 d-block"></i>
                        <h1 class="h pipeline h5 text-uppercase m-0 tracking-wide text-white">Ministerio de Trabajo</h1>
                        <p class="small text-white-50 mt-1">Acceso Ciudadano</p>
                    </div>

                    <h3 class="h4 fw-normal mb-4 text-center text-md-start text-white">Iniciar sesión</h3>

                    <form id="loginForm" onsubmit="handleLogin(event)">
    <div class="input-group custom-input-group">
        <input type="email" id="emailInput" class="form-control" placeholder="Correo electrónico institucional o personal" required>
        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
    </div>
    
    <div class="input-group custom-input-group">
        <input type="password" id="passwordInput" class="form-control" placeholder="Contraseña" required>
        <span class="input-group-text" style="cursor: pointer;" onclick="togglePassword()">
            <i class="bi bi-eye" id="toggleIcon"></i>
        </span>
    </div>

    <div class="text-center pt-2">
        <button type="submit" id="btnIngresar" class="btn btn-connect w-100">Ingresar a mi cuenta</button>
    </div>

    <div id="loginStatus" class="mt-3 text-center" style="min-height: 40px;"></div>

    <div class="position-relative text-center my-4">
        <hr class="text-white-50">
        <span class="position-absolute top-50 start-50 translate-middle px-3 small text-white-50" style="background-color: rgba(17,45,110,0.9); z-index: 2;">O también</span>
    </div>

    <div class="text-center mb-4">
        <button type="button" class="btn btn-gov-id w-100" onclick="handleGovLogin()">
            <i class="bi bi-shield-check me-2"></i> Acceder con Identidad Digital Estatal
        </button>
    </div>

    <div class="text-center">
        <p class="mb-2">¿Primera vez aquí?</p>
        <a href="#" class="btn-register-outline" onclick="goToRegister(event)">Crea tu cuenta de buscador de empleo</a>
    </div>

    <div class="d-flex justify-content-center mt-4 mb-3">
        <a href="#" class="btn-back" onclick="goBack(event)">
            <i class="bi bi-arrow-left"></i> Volver al portal
        </a>
    </div>
</form>


                </div>

            </div>
        </main>

        <footer class="bg-dark bg-opacity-50 text-center py-3 text-white-50 small">
            <div class="container d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
                <div>&copy; 2026 Ministerio de Trabajo y Empleo. Sección Empresas.</div>
                <div class="d-flex gap-3 fs-5">
                    <a href="#" class="text-white-50"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="text-white-50"><i class="bi bi-twitter-x"></i></a>
                    <a href="#" class="text-white-50"><i class="bi bi-linkedin"></i></a>
                </div>
            </div>
        </footer>
    </div>

    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1055;">
        <div id="notificationToast" class="toast custom-toast hide" role="alert" aria-live="assertive"
            aria-atomic="true" data-bs-delay="4000">
            <div class="toast-header">
                <i id="toastIcon"></i>
                <strong id="toastTitle" class="me-auto">Notificación</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div id="toastMessage" class="toast-body">
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // 1. CONTROL DE VISIBILIDAD DE CONTRASEÑA
        function togglePassword() {
            const pass = document.getElementById('passwordInput');
            const icon = document.getElementById('toggleIcon');
            if (pass.type === 'password') {
                pass.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                pass.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        }

        // =======================================================
// 1. VALIDACIÓN DE CREDENCIALES REAL (AJAX + SPINNER INFERIOR)
// =======================================================
function handleLogin(e) {
    e.preventDefault();

    // Capturar los valores ingresados por el usuario
    const email = document.getElementById('emailInput').value.trim();
    const password = document.getElementById('passwordInput').value;
    const statusContainer = document.getElementById('loginStatus');
    const btnIngresar = document.getElementById('btnIngresar');

    // Elementos del Toast institucional en caso de que existan en el DOM
    const toastEl = document.getElementById('notificationToast');
    const title = document.getElementById('toastTitle');
    const message = document.getElementById('toastMessage');
    const icon = document.getElementById('toastIcon');

    // Deshabilitar botón y activar Spinner en la parte inferior del login
    if (btnIngresar) btnIngresar.disabled = true;
    if (statusContainer) {
        statusContainer.innerHTML = `
            <div class="d-flex align-items-center justify-content-center text-white">
                <div class="spinner-border spinner-border-sm text-info me-2" role="status"></div>
                <span>Verificando...</span>
            </div>
        `;
    }

    // Opcional: Si el Toast existe, también le damos feedback institucional concurrente
    if (toastEl && title && message && icon) {
        const toast = bootstrap.Toast.getOrCreateInstance(toastEl);
        title.innerText = 'Validación de Acceso';
        message.innerHTML = `
            Comprobando las credenciales de <strong>${email}</strong> en el sistema centralizado del Ministerio...
            <div class="d-flex justify-content-center mt-3">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Procesando...</span>
                </div>
            </div>
        `;
        icon.className = 'bi bi-shield-lock-fill text-primary fs-3 me-2';
        toast.show();
    }

    // Petición asíncrona al Servidor PHP
    fetch('php/procesar_loginD.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ correo: email, password: password })
    })
    .then(response => response.json())
    .then(data => {
        // Retraso controlado para que el flujo visual sea apreciable
        setTimeout(() => {
            if (data.status === 'success') {
                if (statusContainer) {
                    statusContainer.innerHTML = `<span class="text-success fw-bold"><i class="bi bi-check-circle-fill me-1"></i> ${data.message}</span>`;
                }
                
                setTimeout(() => {
                    // Redirección definitiva al panel de desempleados
                    window.location.href = 'desempleado/index.php';
                }, 800);
            } else {
                // Si hay error en la base de datos o en la clave
                if (btnIngresar) btnIngresar.disabled = false;
                if (statusContainer) {
                    statusContainer.innerHTML = `<span class="text-danger fw-bold"><i class="bi bi-exclamation-triangle-fill me-1"></i> ${data.message}</span>`;
                }
                
                // Si el toast está activo, actualizamos el error visualmente ahí también
                if (toastEl && title && message && icon) {
                    title.innerText = 'Error de Autenticación';
                    message.innerHTML = `<span class="text-danger">${data.message}</span>`;
                    icon.className = 'bi bi-exclamation-triangle-fill text-danger fs-3 me-2';
                }
            }
        }, 1000);
    })
    .catch(error => {
        console.error('Error:', error);
        if (btnIngresar) btnIngresar.disabled = false;
        if (statusContainer) {
            statusContainer.innerHTML = `<span class="text-danger fw-bold"><i class="bi bi-wifi-off me-1"></i> Error de conexión con el servidor.</span>`;
        }
    });
}

// =======================================================
// 2. PASARELA UNICA DEL ESTADO CON SPINNER
// =======================================================
function handleGovLogin() {
    const toastEl = document.getElementById('notificationToast');
    const title = document.getElementById('toastTitle');
    const message = document.getElementById('toastMessage');
    const icon = document.getElementById('toastIcon');

    if (toastEl && title && message && icon) {
        const toast = bootstrap.Toast.getOrCreateInstance(toastEl);
        title.innerText = 'Autenticación del Estado';
        message.innerHTML = `
            Conectando de forma segura con la pasarela única de identidad nacional...
            <div class="d-flex justify-content-center mt-3">
                <div class="spinner-border text-warning" role="status">
                    <span class="visually-hidden">Redirigiendo...</span>
                </div>
            </div>
        `;
        icon.className = 'bi bi-building-fill text-warning fs-3 me-2';
        toast.show();

        setTimeout(() => {
            window.location.href = 'autenticacion_gob.php';
        }, 2500);
    } else {
        alert('Redirigiendo a la pasarela única de autenticación oficial del Estado...');
    }
}

// =======================================================
// 3. ASISTENTE DE REGISTRO
// =======================================================
function goToRegister(event) {
    event.preventDefault();

    const toastEl = document.getElementById('notificationToast');
    const title = document.getElementById('toastTitle');
    const message = document.getElementById('toastMessage');
    const icon = document.getElementById('toastIcon');

    if (!toastEl || !title || !message || !icon) {
        console.error("Error: No se encontraron los IDs del Toast en el HTML. Revisa que coincidan.");
        window.location.href = 'registro_desempleados.php';
        return;
    }

    const toast = bootstrap.Toast.getOrCreateInstance(toastEl);

    title.innerText = 'Registro Oficial';
    message.innerHTML = `
        Abriendo el asistente de alta para demandantes de empleo. Por favor, prepare su DIP y documentación...
        <div class="d-flex justify-content-center mt-3">
            <div class="spinner-border text-info" role="status">
                <span class="visually-hidden">Preparando entorno...</span>
            </div>
        </div>
    `;

    icon.className = 'bi bi-file-earmark-person-fill text-info fs-3 me-2';
    toast.show();

    setTimeout(() => {
        window.location.href = 'registro_usuario.php';
    }, 2500);
}

// =======================================================
// 4. FUNCIÓN VOLVER
// =======================================================
function goBack(event) {
    event.preventDefault();
    window.location.href = './index.php';
}
    </script>
</body>

</html>
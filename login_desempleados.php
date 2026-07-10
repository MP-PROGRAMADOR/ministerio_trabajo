<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Ciudadanos - Portal de Empleo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #1a426a;
            --dark-input: #0f2d4a;
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
                url('https://images.unsplash.com/photo-1521791136368-1a46827d3ad4?auto=format&fit=crop&w=1920&q=80') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            color: #ffffff;
        }

        /* Tarjeta de Servicios / Información (Izquierda) */
        .info-card {
            background-color: rgba(255, 255, 255, 0.92);
            border: none;
            border-radius: 6px;
            color: var(--primary-blue);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        .info-card h2 {
            font-weight: 700;
            letter-spacing: 1px;
            font-size: 1.4rem;
        }

        .service-list-item {
            padding: 8px 0;
            font-weight: 500;
            border-bottom: 1px solid rgba(26, 66, 106, 0.1);
        }

        /* Línea divisoria central con estrella */
        .divider-line {
            width: 3px;
            background-color: #ffffff;
            height: 100%;
            min-height: 250px;
            margin: 0 auto;
            position: relative;
        }

        .divider-star {
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            color: #ffffff;
            font-size: 1.8rem;
        }

        /* Estilo moderno para los Inputs del Login (Derecha) */
        .custom-input-group {
            background-color: var(--dark-input);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 50px;
            padding: 4px 15px;
            margin-bottom: 20px;
        }

        .custom-input-group input {
            background: transparent;
            border: none;
            color: #ffffff;
            box-shadow: none !important;
        }

        .custom-input-group input::placeholder {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.9rem;
        }

        .custom-input-group .input-group-text {
            background: transparent;
            border: none;
            color: rgba(255, 255, 255, 0.6);
        }

        /* Botón de conexión estilizado en óvalo blanco */
        .btn-connect {
            background-color: #ffffff;
            color: var(--primary-blue);
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
        }

        /* Botón alternativo para Identidad Digital Estatal */
        .btn-gov-id {
            background-color: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 50px;
            padding: 8px 20px;
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }

        .btn-gov-id:hover {
            background-color: #ffffff;
            color: var(--primary-blue);
        }

        /* Botón de Registro */
        .btn-register-outline {
            color: #ffffff;
            text-decoration: none;
            font-size: 0.9rem;
            border-bottom: 1px dashed #ffffff;
            transition: opacity 0.2s;
        }

        .btn-register-outline:hover {
            opacity: 0.8;
            color: #ffffff;
        }

        /* CORRECCIÓN DE ALTURAS PARA EL CARRUSEL */
        #citizenCarousel, 
        .carousel-inner, 
        .carousel-item {
            min-height: 450px;
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
    </style>
</head>

<body>

    <div class="bg-portal">
        <header class="text-center py-3 bg-dark bg-opacity-25 border-bottom border-secondary">
            <p class="m-0 small text-uppercase tracking-wider">
                Portal de Empleo - Canal de Atención al Ciudadano y Buscadores de Empleo
            </p>
        </header>

        <main class="container my-auto py-5">
            <div class="row align-items-center justify-content-center g-5">

                <!-- BLOQUE IZQUIERDO: Carrusel de Servicios y Herramientas Ciudadanas -->
                <div class="col-lg-4 col-md-5">
                    <div id="citizenCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" style="box-shadow: 0 4px 20px rgba(0,0,0,0.3); border-radius: 6px; overflow: hidden;">
                        
                        <div class="carousel-indicators">
                            <button type="button" data-bs-target="#citizenCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                            <button type="button" data-bs-target="#citizenCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                            <button type="button" data-bs-target="#citizenCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
                        </div>

                        <div class="carousel-inner">
                            
                            <!-- Diapositiva 1: CV -->
                            <div class="carousel-item active">
                                <div class="carousel-bg-img" style="background: linear-gradient(rgba(0, 0, 0, 0.2), rgba(26, 66, 106, 0.9)), url('img/img4.png');"></div>
                                <div class="carousel-caption d-flex flex-column justify-content-end h-100 pb-5">
                                    <div class="mb-3 text-warning"><i class="bi bi-file-earmark-person fs-1"></i></div>
                                    <h3 class="h4 fw-bold text-white mb-2">Tu Currículum Perfecto</h3>
                                    <p class="small text-white-50 m-0">Usa nuestro constructor guiado para destacar tus habilidades ante las empresas más importantes.</p>
                                </div>
                            </div>

                            <!-- Diapositiva 2: Formación -->
                            <div class="carousel-item">
                                <div class="carousel-bg-img" style="background: linear-gradient(rgba(0, 0, 0, 0.2), rgba(26, 66, 106, 0.9)), url('img/img5.png');"></div>
                                <div class="carousel-caption d-flex flex-column justify-content-end h-100 pb-5">
                                    <div class="mb-3 text-warning"><i class="bi bi-journal-bookmark-fill fs-1"></i></div>
                                    <h3 class="h4 fw-bold text-white mb-2">Capacitación Gratuita</h3>
                                    <p class="small text-white-50 m-0">Accede a cientos de cursos certificados por el Ministerio para mejorar tu perfil profesional sin costo.</p>
                                </div>
                            </div>

                            <!-- Diapositiva 3: Conectividad -->
                            <div class="carousel-item">
                                <div class="carousel-bg-img" style="background: linear-gradient(rgba(0, 0, 0, 0.2), rgba(26, 66, 106, 0.9)), url('img/img6.png');"></div>
                                <div class="carousel-caption d-flex flex-column justify-content-end h-100 pb-5">
                                    <div class="mb-3 text-warning"><i class="bi bi-award-fill fs-1"></i></div>
                                    <h3 class="h4 fw-bold text-white mb-2">Conectamos Oportunidades</h3>
                                    <p class="small text-white-50 m-0">Más de 50.000 ciudadanos ya consiguieron empleo formal este año a través de nuestra red pública.</p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- DIVISOR -->
                <div class="col-md-1 d-none d-md-block text-center">
                    <div class="divider-line">
                        <i class="bi bi-brightness-high-fill divider-star"></i>
                    </div>
                </div>

                <!-- BLOQUE DERECHO: Login Ciudadano -->
                <div class="col-lg-4 col-md-5 text-center text-md-start">
                    <div class="mb-4 text-center">
                        <i class="bi bi-person-workspace fs-1 mb-2"></i>
                        <h1 class="h5 text-uppercase m-0 tracking-wide">Ministerio de Trabajo</h1>
                    </div>

                    <h3 class="h4 fw-normal mb-4 text-center text-md-start">Área del Trabajador</h3>

                    <form id="loginForm" onsubmit="handleLogin(event)">
                        <!-- Input Usuario o Identificación de Ciudadano -->
                        <div class="input-group custom-input-group">
                            <input type="text" class="form-control" placeholder="Documento de Identidad (DNI / NIE / Pasaporte)" required>
                            <span class="input-group-text"><i class="bi bi-card-text"></i></span>
                        </div>

                        <!-- Input Contraseña -->
                        <div class="input-group custom-input-group">
                            <input type="password" id="passwordInput" class="form-control" placeholder="Contraseña" required>
                            <span class="input-group-text" style="cursor: pointer;" onclick="togglePassword()">
                                <i class="bi bi-eye" id="toggleIcon"></i>
                            </span>
                        </div>

                        <div class="text-center pt-2 mb-4">
                            <button type="submit" class="btn btn-connect w-100">Ingresar a mi cuenta</button>
                        </div>

                        <div class="position-relative text-center my-4">
                            <hr class="text-white-50">
                            <span class="position-absolute top-50 start-50 translate-middle px-3 small text-white-50" style="background-color: #173b5e; z-index: 2;">O también</span>
                        </div>

                        <div class="text-center mb-4">
                            <button type="button" class="btn btn-gov-id w-100" onclick="handleGovLogin()">
                                <i class="bi bi-shield-check me-2"></i> Acceder con Identidad Digital Gobierno
                            </button>
                        </div>

                        <div class="text-center">
                            <p class="mb-2">¿Primera vez aquí?</p>
                            <a href="registro_desempleados" class="btn-register-outline" onclick="goToRegister(event)">Crea tu cuenta de buscador de empleo</a>
                        </div>
                    </form>
                </div>

            </div>
        </main>

        <footer class="bg-dark bg-opacity-50 text-center py-3 text-white-50 small">
            <div class="container d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
                <div>&copy; 2026 Ministerio de Trabajo y Empleo. Atención Ciudadana.</div>
                <div class="d-flex gap-3 fs-5">
                    <a href="#" class="text-white-50"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="text-white-50"><i class="bi bi-twitter-x"></i></a>
                    <a href="#" class="text-white-50"><i class="bi bi-linkedin"></i></a>
                </div>
            </div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('passwordInput');
            const toggleIcon = document.getElementById('toggleIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('bi-eye');
                toggleIcon.classList.add('bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('bi-eye-slash');
                toggleIcon.classList.add('bi-eye');
            }
        }

        function handleLogin(event) {
            event.preventDefault();
            alert('Validando credenciales de usuario en el sistema del Ministerio...');
        }

        function handleGovLogin() {
            alert('Redirigiendo a la pasarela única de autenticación oficial del Estado...');
        }

      function goToRegister(event) {
    event.preventDefault();
    // Redirecciona al archivo HTML del formulario de registro
    window.location.href = 'registro_desempleados.php'; 
}
    </script>
</body>

</html>
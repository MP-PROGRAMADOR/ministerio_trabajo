<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pasarela de Identidad Digital Estatal - Portal de Empleo</title>
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
            background: linear-gradient(rgba(15, 32, 67, 0.90), rgba(15, 32, 67, 0.90)),
                url('https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?auto=format&fit=crop&w=1920&q=80') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            color: #ffffff;
        }

        /* Card translúcida de aviso */
        .notice-card {
            background-color: rgba(17, 45, 110, 0.65);
            border: 1px solid rgba(212, 160, 23, 0.4);
            border-radius: 20px;
            padding: 40px 30px;
            backdrop-filter: blur(10px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
        }

        /* Botón principal de regreso */
        .btn-connect {
            background-color: #ffffff;
            color: var(--azul-marino);
            font-weight: 700;
            border-radius: 50px;
            padding: 12px 35px;
            border: 2px solid #ffffff;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-connect:hover {
            background-color: transparent;
            color: #ffffff;
            border-color: var(--dorado);
        }

        .text-gold {
            color: var(--dorado);
        }

        /* Animación suave para el ícono de engranaje/construcción */
        .gear-spin {
            animation: spin 8s linear infinite;
        }

        @keyframes spin {
            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>

    <div class="bg-portal">
        <header class="text-center py-3 bg-dark bg-opacity-25 border-bottom border-secondary">
            <p class="m-0 small text-uppercase tracking-wider fw-semibold">
                <i class="bi bi-shield-lock-fill text-gold me-1"></i> Sistema Único de Identificación Gubernamental
            </p>
        </header>

        <main class="container my-auto py-5">
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8 text-center">
                    
                    <div class="notice-card">
                        <div class="mb-4 position-relative d-inline-block">
                            <i class="bi bi-building-lock text-gold" style="font-size: 4rem;"></i>
                            <i class="bi bi-gear-fill text-white position-absolute bottom-0 end-0 gear-spin" style="font-size: 1.8rem;"></i>
                        </div>

                        <h1 class="h5 text-uppercase tracking-wide text-gold mb-2">Ministerio de Trabajo</h1>
                        <h2 class="h3 fw-bold text-white mb-3">Identidad Digital Estatal</h2>

                        <hr class="border-secondary my-4" style="opacity: 0.3;">

                        <div class="px-md-3 mb-4">
                            <p class="fs-5 text-white fw-semibold mb-2">
                                <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i> Servicio en Desarrollo
                            </p>
                            <p class="text-white-50 small leading-relaxed">
                                Este método de autenticación mediante la **Pasarela Única de Identidad Nacional** aún no se encuentra disponible. Nos encontramos trabajando en la integración con los servidores centrales de verificación gubernamental.
                            </p>
                            <div class="p-3 bg-dark bg-opacity-25 rounded-3 border border-secondary mt-3">
                                <small class="text-white-50 d-block">
                                    <i class="bi bi-info-circle text-gold me-1"></i> Por favor, acceda temporalmente utilizando su **Correo Electrónico** y **Contraseña** registrada en el portal.
                                </small>
                            </div>
                        </div>

                        <div class="pt-2">
                            <a href="./index.php" class="btn btn-connect w-100 w-sm-auto">
                                <i class="bi bi-arrow-left-circle-fill"></i> Volver al Portal
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </main>

        <footer class="bg-dark bg-opacity-50 text-center py-3 text-white-50 small">
            <div class="container d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
                <div>&copy; 2026 Ministerio de Trabajo y Fomento de Empleo. Pasarela Única.</div>
                <div class="d-flex gap-3 fs-5">
                    <a href="#" class="text-white-50"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="text-white-50"><i class="bi bi-twitter-x"></i></a>
                    <a href="#" class="text-white-50"><i class="bi bi-linkedin"></i></a>
                </div>
            </div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
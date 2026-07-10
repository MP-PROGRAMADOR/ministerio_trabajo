<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal de Empleo Nacional</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #1a426a;
            --accent-gold: #c9a0dc; /* Un tono dorado/amarillo sutil similar al de la línea central */
        }

        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Fondo emulando la imagen: imagen conceptual + capa azul traslúcida */
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

        /* Tarjetas de acceso copiando el estilo limpio de la captura */
        .access-card {
            background-color: rgba(255, 255, 255, 0.9);
            border: none;
            border-radius: 4px;
            transition: all 0.3s ease;
            cursor: pointer;
            min-height: 200px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .access-card:hover {
            transform: translateY(-5px);
            background-color: rgba(255, 255, 255, 1);
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
        }

        .access-card .icon-box {
            font-size: 3rem;
            color: #1a426a;
            margin-bottom: 15px;
        }

        .access-card .card-title {
            color: #1a426a;
            font-weight: 600;
            font-size: 1.25rem;
        }

        /* Línea divisoria central inspirada en la imagen */
        .divider-line {
            width: 3px;
            background-color: #d4af37;
            height: 100%;
            min-height: 150px;
            margin: 0 auto;
            position: relative;
        }

        .divider-star {
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            color: #ffffff;
            font-size: 1.5rem;
        }
    </style>
</head>
<body>

    <div class="bg-portal">
        <header class="text-center py-3 bg-dark bg-opacity-25 border-bottom border-secondary">
            <p class="m-0 small text-uppercase tracking-wider">
                Bienvenido al Portal de Empleo / Bienvenido ao Portal de Emprego
            </p>
        </header>

        <main class="container my-auto py-5">
            <div class="text-center mb-5">
                <div class="mb-2">
                    <i class="bi bi-person-workspace fs-1 text-white"></i>
                </div>
                <h1 class="h4 text-uppercase fw-bold m-0 tracking-wide">Ministerio de Trabajo</h1>
                <p class="text-white-50 small">Plataforma de Intermediación Laboral</p>
            </div>

            <div class="row justify-content-center align-items-center g-4 position-relative">
                
                <div class="col-md-5">
                    <div class="card access-card p-4 text-center" onclick="redireccionar('empleador')">
                        <div class="icon-box">
                            <i class="bi bi-building-fill-check"></i>
                        </div>
                        <h2 class="card-title">Acceso Corporativo</h2>
                        <p class="text-muted small m-0">Empresas, PyMEs y Autónomos</p>
                    </div>
                </div>

                <div class="col-md-1 d-none d-md-block text-center h-100">
                    <div class="divider-line">
                        <i class="bi bi-brightness-high-fill divider-star"></i>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="card access-card p-4 text-center" onclick="redireccionar('ciudadano')">
                        <div class="icon-box">
                            <i class="bi bi-person-search"></i>
                        </div>
                        <h2 class="card-title">Acceso Particular</h2>
                        <p class="text-muted small m-0">Buscadores de Empleo y Ciudadanos</p>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center mt-5">
                <div class="col-md-4">
                    <div class="card access-card p-3 text-center" style="min-height: auto;" onclick="redireccionar('ministerio')">
                        <div class="d-flex align-items-center justify-content-center gap-3">
                            <i class="bi bi-shield-lock-fill text-dark fs-4"></i>
                            <span class="fw-bold text-dark text-uppercase small tracking-wider">Administración Pública</span>
                        </div>
                    </div>
                </div>
            </div>
        </main>

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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function redireccionar(perfil) {
            switch(perfil) {
                case 'empleador':
                    alert('Redirigiendo al portal de Empresas y Ofertas de Trabajo...');
                    // window.location.href = '/login-empleador';
                    break;
                case 'ciudadano':
                    alert('Redirigiendo al buscador de empleo y carga de CV...');
                    // window.location.href = '/login-ciudadano';
                    break;
                case 'ministerio':
                    alert('Acceso restringido para funcionarios del Ministerio...');
                    // window.location.href = '/admin';
                    break;
                default:
                    console.log('Perfil no reconocido');
            }
        }
    </script>
</body>
</html>
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Empleador - Portal de Empleo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #1a426a;
            --dark-input: #0f2d4a;
            --accent-teal: #17a2b8;
        }

        body,
        html {
            height: 100%;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .bg-portal {
            background: linear-gradient(rgba(26, 66, 106, 0.9), rgba(26, 66, 106, 0.9)),
                url('https://images.unsplash.com/photo-1521791136368-1a46827d3ad4?auto=format&fit=crop&w=1920&q=80') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            color: #ffffff;
        }

        .main-content {
            flex-grow: 1;
        }

        .form-card {
            background-color: rgba(15, 45, 74, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            padding: 30px;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
        }

        .custom-form-group .form-control,
        .custom-form-group .form-select {
            background-color: var(--dark-input);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            color: #ffffff;
            padding: 10px 15px;
        }

        .custom-form-group .form-control:focus,
        .custom-form-group .form-select:focus {
            background-color: var(--dark-input);
            border-color: var(--accent-teal);
            color: #ffffff;
            box-shadow: 0 0 0 0.25rem rgba(23, 162, 184, 0.25);
        }

        .custom-form-group .form-control::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .form-label {
            font-weight: 500;
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 6px;
        }

        .section-title {
            border-left: 4px solid var(--accent-teal);
            padding-left: 10px;
            font-size: 1.15rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #ffffff;
        }

        .btn-submit {
            background-color: #ffffff;
            color: var(--primary-blue);
            font-weight: 700;
            border-radius: 50px;
            padding: 12px 40px;
            border: 2px solid #ffffff;
            transition: all 0.3s ease;
            text-transform: uppercase;
        }

        .btn-submit:hover {
            background-color: transparent;
            color: #ffffff;
        }
    </style>
</head>

<body>

    <div class="bg-portal">
        <header class="text-center py-3 bg-dark bg-opacity-25 border-bottom border-secondary">
            <p class="m-0 small text-uppercase tracking-wider">
                Sistema Nacional de Empleo - Formulario de Alta de Trabajadores
            </p>
        </header>

        <main class="container main-content py-5">
            <div class="row justify-content-center">
                <div class="col-xl-8 col-lg-9">

                    <div class="mb-4">
                        <a href="login_desempleados.php" class="text-white text-decoration-none small">
                            <i class="bi bi-arrow-left me-2"></i> Volver al Inicio de Sesión
                        </a>
                    </div>

                    <div id="session-alert-container">
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert" id="customAlert" style="border-left: 5px solid #dc3545;">
                                <i class="bi bi-exclamation-octagon-fill me-2"></i>
                                <?= htmlspecialchars($_SESSION['error']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php unset($_SESSION['error']); ?>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['exito'])): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert" id="customAlert" style="border-left: 5px solid #198754;">
                                <i class="bi bi-check-circle-fill me-2"></i>
                                <?= htmlspecialchars($_SESSION['exito']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php unset($_SESSION['exito']); ?>
                        <?php endif; ?>
                    </div>

                   <div class="form-card">
    <form id="registrationForm" action="php/procesar_empleador.php" method="POST" class="needs-validation" novalidate>

        <input type="hidden" name="rol" value="empleador">

        <div class="text-center mb-5">
            <div class="mb-3">
                <i class="bi bi-building-check text-primary" style="font-size: 3.5rem;"></i>
            </div>
            <h2 class="fw-bold m-0">Registro de Empresa / Empleador</h2>
            <p class="text-white-50 small">Sistema Nacional de Empleo — Portal del Ministerio de Trabajo</p>
        </div>

        <h3 class="section-title mb-4">1. Datos del Representante Legal / Contacto</h3>
        <div class="row g-3 custom-form-group mb-5">
            <div class="col-md-6">
                <label class="form-label">Nombre del Representante</label>
                <input type="text" name="nombre" class="form-control" placeholder="Ej. Juan Carlos" required minlength="2" maxlength="50" pattern="^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$">
                <div class="invalid-feedback">Por favor, introduce un nombre válido.</div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Apellidos del Representante</label>
                <input type="text" name="apellidos" class="form-control" placeholder="Ej. Nsue Nguema" required minlength="2" maxlength="50" pattern="^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$">
                <div class="invalid-feedback">Por favor, introduce tus apellidos.</div>
            </div>
            <div class="col-md-12">
                <label class="form-label">Documento de Identidad (DIP / Pasaporte)</label>
                <input type="text" name="documento_identidad" class="form-control" placeholder="Número de documento oficial" required minlength="5" maxlength="30" pattern="^[a-zA-Z0-9\-]+$">
                <div class="invalid-feedback">Introduce un documento de identidad válido.</div>
            </div>
        </div>

        <h3 class="section-title mb-4">2. Datos Corporativos de la Empresa</h3>
        <div class="row g-3 custom-form-group mb-5">
            <div class="col-md-8">
                <label class="form-label">Nombre / Razón Social de la Empresa</label>
                <input type="text" name="nombre_empresa" class="form-control" placeholder="Ej. Empresa de Servicios S.L." required maxlength="150">
                <div class="invalid-feedback">Introduce el nombre oficial de la empresa.</div>
            </div>
            <div class="col-md-4">
                <label class="form-label">RNC / RUC / NIF (Opcional)</label>
                <input type="text" name="rnc_ruc" class="form-control" placeholder="Ej. RNC-123456" maxlength="50">
            </div>
            <div class="col-md-6">
                <label class="form-label">Sector Industrial / Económico</label>
                <select name="sector_industrial" class="form-select" required>
                    <option value="" selected disabled>Selecciona un sector...</option>
                    <option value="Petróleo y Gas">Petróleo y Gas</option>
                    <option value="Telecomunicaciones y Tecnología">Telecomunicaciones y Tecnología</option>
                    <option value="Construcción e Infraestructura">Construcción e Infraestructura</option>
                    <option value="Comercio y Servicios">Comercio y Servicios</option>
                    <option value="Banca y Finanzas">Banca y Finanzas</option>
                    <option value="Transporte y Logística">Transporte y Logística</option>
                    <option value="Otros">Otros</option>
                </select>
                <div class="invalid-feedback">Selecciona el sector industrial de la empresa.</div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Teléfono Corporativo</label>
                <input type="tel" name="telefono_corporativo" class="form-control" placeholder="Ej. +240 222 000 000" required maxlength="20">
                <div class="invalid-feedback">Introduce un teléfono de contacto corporativo.</div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Dirección Física / Sede</label>
                <input type="text" name="direccion" class="form-control" placeholder="Ej. Barrio Ela Nguema, Malabo" required maxlength="255">
                <div class="invalid-feedback">Introduce la dirección de la sede.</div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Sitio Web (Opcional)</label>
                <input type="url" name="sitio_web" class="form-control" placeholder="Ej. https://www.miempresa.gq" maxlength="150">
            </div>
        </div>

        <h3 class="section-title mb-4">3. Credenciales de Acceso</h3>
        <div class="row g-3 custom-form-group mb-5">
            <div class="col-md-6">
                <label class="form-label">Nombre de Usuario</label>
                <input type="text" name="nombre_usuario" class="form-control" placeholder="Ej. miempresa_admin" required minlength="4" maxlength="30" pattern="^[a-zA-Z0-9_.]+$">
                <div class="invalid-feedback">Mínimo 4 caracteres (solo letras, números, puntos o guiones bajos).</div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Correo Electrónico Corporativo</label>
                <input type="email" name="correo_electronico" class="form-control" placeholder="contacto@empresa.com" required>
                <div class="invalid-feedback">Introduce un correo electrónico válido.</div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Contraseña</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Mínimo 8 caracteres" required minlength="8" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d\W_]{8,}$">
                <div class="invalid-feedback">Mínimo 8 caracteres, incluyendo una mayúscula, una minúscula y un número.</div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Confirmar Contraseña</label>
                <input type="password" id="confirm_password" class="form-control" placeholder="Repite tu contraseña" required>
                <div id="passwordError" class="text-danger small mt-1 d-none">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> Las contraseñas no coinciden.
                </div>
                <div id="passwordSuccess" class="text-success small mt-1 d-none">
                    <i class="bi bi-check-circle-fill me-1"></i> Las contraseñas coinciden.
                </div>
            </div>
        </div>

        <div class="text-center pt-4 border-top border-secondary mt-5">
            <button type="submit" class="btn btn-submit px-5 btn-primary">Registrar Empresa y Solicitar Alta</button>
        </div>

    </form>
</div>
                </div>
            </div>
        </main>

        <footer class="bg-dark bg-opacity-50 text-center py-3 text-white-50 small mt-auto">
            &copy; 2026 Ministerio de Trabajo y Empleo. Sección de Registro Estatal.
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const form = document.getElementById('registrationForm');
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirm_password');
            const errorDiv = document.getElementById('passwordError');
            const successDiv = document.getElementById('passwordSuccess');
            const alerta = document.getElementById('customAlert');

            // 1. Temporizador para ocultar la alerta de sesión en 6 segundos
            if (alerta) {
                setTimeout(function () {
                    if (typeof bootstrap !== 'undefined' && bootstrap.Alert) {
                        const bsAlert = new bootstrap.Alert(alerta);
                        bsAlert.close();
                    } else {
                        alerta.style.transition = "opacity 0.5s ease";
                        alerta.style.opacity = "0";
                        setTimeout(() => alerta.remove(), 500);
                    }
                }, 6000);
            }

            // 2. Función para verificar si las contraseñas coinciden en tiempo real
            function verificarContrasenas() {
                const val1 = password.value;
                const val2 = confirmPassword.value;

                if (val2 === "") {
                    errorDiv.classList.add('d-none');
                    successDiv.classList.add('d-none');
                    confirmPassword.setCustomValidity("Debes confirmar la contraseña");
                    return;
                }

                if (val1 === val2) {
                    errorDiv.classList.add('d-none');
                    successDiv.classList.remove('d-none');
                    confirmPassword.setCustomValidity("");
                } else {
                    successDiv.classList.add('d-none');
                    errorDiv.classList.remove('d-none');
                    confirmPassword.setCustomValidity("Las contraseñas no coinciden");
                }
            }

            password.addEventListener('input', verificarContrasenas);
            confirmPassword.addEventListener('input', verificarContrasenas);

            // 3. Validación al intentar enviar el formulario
            form.addEventListener('submit', function (event) {
                verificarContrasenas();

                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }

                form.classList.add('was-validated');
            }, false);
        });
    </script>
</body>

</html>
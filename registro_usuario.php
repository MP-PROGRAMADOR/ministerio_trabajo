<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Buscador de Empleo - Portal de Empleo</title>
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

        /* Avatar de Previsualización */
        .avatar-preview-container {
            width: 140px;
            height: 140px;
            margin: 0 auto 15px auto;
            position: relative;
        }

        .avatar-preview {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 4px solid var(--accent-teal);
            object-fit: cover;
            background-color: var(--dark-input);
        }

        .btn-upload-avatar {
            position: absolute;
            bottom: 0;
            right: 0;
            background-color: var(--accent-teal);
            color: white;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: 2px solid #0f2d4a;
            transition: transform 0.2s;
        }

        .btn-upload-avatar:hover {
            transform: scale(1.1);
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

        .custom-form-group .form-select option {
            background-color: var(--dark-input);
            color: #fff;
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

        .file-upload-wrapper {
            background-color: rgba(26, 66, 106, 0.5);
            border: 1px dashed rgba(255, 255, 255, 0.3);
            border-radius: 8px;
            padding: 12px;
            text-align: center;
        }

        .experience-block {
            background-color: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .form-switch .form-check-input {
            width: 2.5em;
            height: 1.25em;
            cursor: pointer;
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

            <div class="form-card">
                <form id="registrationForm" action="procesar_registro.php" method="POST" class="needs-validation" novalidate>

                    <div class="text-center mb-5">
                        <div class="mb-3">
                            <i class="bi bi-shield-lock-fill text-primary" style="font-size: 3.5rem;"></i>
                        </div>
                        <h2 class="fw-bold m-0">Crear Cuenta de Acceso</h2>
                        <p class="text-white-50 small">Sistema Nacional de Empleo — Registro de Credenciales Únicas</p>
                    </div>

                    <!-- SECCIÓN: DATOS DE IDENTIDAD -->
                    <h3 class="section-title mb-4">1. Datos de Identidad</h3>
                    <div class="row g-3 custom-form-group mb-5">
                        <div class="col-md-6">
                            <label class="form-label">Nombre</label>
                            <input type="text" name="nombre" class="form-control" placeholder="Ej. Juan Carlos" 
                                   required minlength="2" maxlength="50" pattern="^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$">
                            <div class="invalid-feedback">Por favor, introduce un nombre válido (solo letras, mín. 2 caracteres).</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Apellidos</label>
                            <input type="text" name="apellidos" class="form-control" placeholder="Ej. Nsue Nguema" 
                                   required minlength="2" maxlength="50" pattern="^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$">
                            <div class="invalid-feedback">Por favor, introduce tus apellidos (solo letras, mín. 2 caracteres).</div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Documento Nacional de Identidad (DIP / Pasaporte)</label>
                            <input type="text" name="documento_identidad" class="form-control" placeholder="Número de documento oficial" 
                                   required minlength="5" maxlength="20" pattern="^[a-zA-Z0-9\-]+$">
                            <div class="invalid-feedback">Introduce un documento de identidad válido (letras, números y guiones).</div>
                        </div>
                    </div>

                    <!-- SECCIÓN: CREDENCIALES Y SEGURIDAD -->
                    <h3 class="section-title mb-4">2. Credenciales y Seguridad</h3>
                    <div class="row g-3 custom-form-group mb-5">
                        <div class="col-md-6">
                            <label class="form-label">Nombre de Usuario</label>
                            <input type="text" name="nombre_usuario" class="form-control" placeholder="Ej. jc_nsue" 
                                   required minlength="4" maxlength="30" pattern="^[a-zA-Z0-9_.]+$">
                            <div class="invalid-feedback">El usuario debe tener al menos 4 caracteres (solo letras, números, puntos o guiones bajos).</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Correo Electrónico</label>
                            <input type="email" name="correo_electronico" class="form-control" placeholder="ejemplo@dominio.com" required>
                            <div class="invalid-feedback">Por favor, introduce una dirección de correo electrónico válida.</div>
                            <div class="form-text text-white-50" style="font-size: 0.75rem;">
                                <i class="bi bi-info-circle me-1"></i> Se enviará un enlace de confirmación a esta bandeja.
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contraseña</label>
                            <input type="password" name="password" id="password" class="form-control" 
                                   placeholder="Mínimo 8 caracteres" required minlength="8" 
                                   pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d\W_]{8,}$">
                            <div class="invalid-feedback">La contraseña debe tener al menos 8 caracteres, incluyendo una mayúscula, una minúscula y un número.</div>
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
                        <button type="submit" class="btn btn-submit px-5 btn-primary">Solicitar Alta y Verificar Correo</button>
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

 <script>
document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById('registrationForm');
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');
    const errorDiv = document.getElementById('passwordError');
    const successDiv = document.getElementById('passwordSuccess');

    // Función para verificar si las contraseñas coinciden en tiempo real
    function verificarContrasenas() {
        const val1 = password.value;
        const val2 = confirmPassword.value;

        // Si el campo de confirmar está vacío, ocultamos los avisos de coincidencia
        if (val2 === "") {
            errorDiv.classList.add('d-none');
            successDiv.classList.add('d-none');
            confirmPassword.setCustomValidity("Debes confirmar la contraseña");
            return;
        }

        if (val1 === val2) {
            errorDiv.classList.add('d-none');
            successDiv.classList.remove('d-none');
            confirmPassword.setCustomValidity(""); // Informa al navegador que el campo es válido
        } else {
            successDiv.classList.add('d-none');
            errorDiv.classList.remove('d-none');
            confirmPassword.setCustomValidity("Las contraseñas no coinciden"); // Forza la invalidez en HTML5
        }
    }

    // Escuchar los eventos input mientras el usuario escribe en ambos campos
    password.addEventListener('input', verificarContrasenas);
    confirmPassword.addEventListener('input', verificarContrasenas);

    // Validación general al intentar enviar (Bootstrap / HTML5 Native)
    form.addEventListener('submit', function (event) {
        verificarContrasenas(); // Validación final antes del envío

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
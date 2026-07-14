<?php
session_start();

// Verificar sesión y rol
if (!isset($_SESSION['id_usuario']) || ($_SESSION['rol'] ?? '') !== 'buscador') {
    header('Location: ../login_desempleados.php');
    exit();
}

require_once '../conexion/conexion.php';

$id_usuario = $_SESSION['id_usuario'];
$nombre_completo = $_SESSION['nombre_completo'] ?? '';

// Obtener datos del usuario (DIP y hash de contraseña)
try {
    $stmt = $pdo->prepare("SELECT u.documento_identidad, u.password, b.id AS buscador_id 
                           FROM usuarios u 
                           JOIN buscadores_empleo b ON u.id = b.usuario_id 
                           WHERE u.id = ?");
    $stmt->execute([$id_usuario]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$usuario) {
        header('Location: completar_perfil.php');
        exit();
    }
    $documento_identidad = $usuario['documento_identidad'];
    $password_hash = $usuario['password'];
    $buscador_id = $usuario['buscador_id'];
} catch (PDOException $e) {
    error_log("Error al obtener datos del usuario: " . $e->getMessage());
    $_SESSION['mensaje'] = 'Error al cargar tus datos. Intenta más tarde.';
    $_SESSION['mensaje_tipo'] = 'danger';
    header('Location: bolsa_trabajo.php');
    exit();
}

// Verificar oferta_id
if (!isset($_GET['oferta_id']) || empty($_GET['oferta_id'])) {
    $_SESSION['mensaje'] = 'No se especificó la oferta.';
    $_SESSION['mensaje_tipo'] = 'danger';
    header('Location: bolsa_trabajo.php');
    exit();
}
$oferta_id = (int)$_GET['oferta_id'];

// Obtener datos de la oferta
try {
    $stmt = $pdo->prepare("SELECT o.titulo_puesto, e.nombre_empresa 
                           FROM ofertas_empleo o 
                           JOIN empleadores e ON o.empleador_id = e.id 
                           WHERE o.id = ? AND o.estado = 'abierta'");
    $stmt->execute([$oferta_id]);
    $oferta = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$oferta) {
        $_SESSION['mensaje'] = 'La oferta no existe o ya no está disponible.';
        $_SESSION['mensaje_tipo'] = 'danger';
        header('Location: bolsa_trabajo.php');
        exit();
    }
} catch (PDOException $e) {
    error_log("Error al obtener oferta: " . $e->getMessage());
    $_SESSION['mensaje'] = 'Error al cargar la oferta.';
    $_SESSION['mensaje_tipo'] = 'danger';
    header('Location: bolsa_trabajo.php');
    exit();
}

// Verificar si ya postuló
try {
    $stmt = $pdo->prepare("SELECT id FROM favoritos WHERE buscador_id = ? AND oferta_id = ?");
    $stmt->execute([$buscador_id, $oferta_id]);
    if ($stmt->fetch()) {
        $_SESSION['mensaje'] = 'Ya has postulado a esta oferta anteriormente.';
        $_SESSION['mensaje_tipo'] = 'info';
        header('Location: bolsa_trabajo.php');
        exit();
    }
} catch (PDOException $e) {
    error_log("Error al verificar duplicado: " . $e->getMessage());
}

// Procesar POST
$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dip_ingresado = trim($_POST['dip'] ?? '');
    $password_ingresada = $_POST['password'] ?? '';

    if (empty($dip_ingresado)) {
        $error = 'Debes introducir tu Documento de Identidad (DIP).';
    } elseif (empty($password_ingresada)) {
        $error = 'Debes introducir tu contraseña.';
    } elseif ($dip_ingresado !== $documento_identidad) {
        $error = 'El DIP ingresado no coincide con el registrado.';
    } elseif (!password_verify($password_ingresada, $password_hash)) {
        $error = 'La contraseña ingresada no es correcta.';
    } else {
        // Insertar postulación
        try {
            $stmt = $pdo->prepare("INSERT INTO favoritos (buscador_id, oferta_id) VALUES (?, ?)");
            $stmt->execute([$buscador_id, $oferta_id]);
            $_SESSION['mensaje'] = '¡Postulación exitosa! Has aplicado a la oferta correctamente.';
            $_SESSION['mensaje_tipo'] = 'success';
            header('Location: bolsa_trabajo.php');
            exit();
        } catch (PDOException $e) {
            error_log("Error al insertar postulación: " . $e->getMessage());
            $error = 'Ocurrió un error al procesar tu postulación. Intenta más tarde.';
        }
    }
}

$titulo = 'Confirmar Postulación - Portal de Empleo';
include '../componentes/header_desempleado.php';
include '../componentes/menu_desempleado.php';
?>

<style>
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
        background: rgba(255, 255, 255, 0.96) !important;
        backdrop-filter: blur(12px);
        border-bottom: 3px solid var(--gov-blue);
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
    .profile-dropdown {
        position: relative !important;
    }

    .profile-menu-img {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        object-fit: cover;
        cursor: pointer;
        transition: border-color 0.3s, transform 0.2s;
    }

    /* ===== ESTILOS PROPIOS DE LA PÁGINA (coherentes con el portal) ===== */
    .confirm-box {
        max-width: 640px;
        margin: 0 auto;
        background: #ffffff;
        border-radius: var(--gov-radius);
        border: 1px solid var(--gov-border);
        box-shadow: 0 4px 20px rgba(11, 58, 96, 0.06);
        padding: 2.5rem 2rem;
        transition: box-shadow 0.2s;
    }

    .confirm-box:hover {
        box-shadow: 0 8px 30px rgba(11, 58, 96, 0.10);
    }

    .confirm-box .icono {
        font-size: 3.5rem;
        color: var(--gov-gold);
        margin-bottom: 0.5rem;
    }

    .confirm-box .titulo-oferta {
        font-weight: 700;
        color: var(--gov-dark);
        font-size: 1.25rem;
        margin-bottom: 0.25rem;
    }

    .confirm-box .empresa {
        color: var(--gov-blue);
        font-weight: 500;
        font-size: 1rem;
    }

    .confirm-box .form-control {
        border: 1.5px solid var(--gov-border);
        border-radius: var(--gov-radius-sm);
        padding: 0.7rem 1rem;
        font-size: 0.95rem;
        transition: border-color 0.2s, box-shadow 0.2s;
        background-color: #fff;
        color: var(--gov-dark);
    }

    .confirm-box .form-control:focus {
        border-color: var(--gov-blue);
        box-shadow: 0 0 0 3px rgba(9, 51, 107, 0.2);
        outline: none;
    }

    .confirm-box .form-label {
        font-weight: 500;
        color: var(--gov-dark);
        margin-bottom: 0.25rem;
        font-size: 0.9rem;
    }

    .confirm-box .input-group-text {
        background: transparent;
        border: 1.5px solid var(--gov-border);
        border-right: none;
        border-radius: var(--gov-radius-sm) 0 0 var(--gov-radius-sm);
        color: #6b7a8a;
    }

    .confirm-box .input-group .form-control {
        border-left: none;
        border-radius: 0 var(--gov-radius-sm) var(--gov-radius-sm) 0;
    }

    .confirm-box .input-group .form-control:focus {
        border-left: none;
    }

    /* BOTÓN CONFIRMAR (CORREGIDO) */
    .confirm-box .btn-confirmar {
        color: var(--gov-green);
                border-color: var(--gov-border);

        border-radius: var(--gov-radius-sm);
        padding: 0.75rem 2.5rem;
        font-weight: 600;
        font-size: 0.95rem;
        transition: all 0.2s;
    }

    .confirm-box .btn-confirmar:hover {
        background: var(--gov-blue);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(30, 126, 52, 0.25);
        color: #fff;
    }

    .confirm-box .btn-outline-secondary {
        color: #c20615da;
        border-color: var(--gov-border);
        border-radius: var(--gov-radius-sm);
        padding: 0.75rem 2rem;
        font-weight: 500;
        transition: all 0.2s;
    }

    .confirm-box .btn-outline-secondary:hover {
        background: var(--gov-blue);
        color: #fff;
        border-color: var(--gov-blue);
    }

    .confirm-box .error-message {
        color: #dc3545;
        font-size: 0.9rem;
        background: #fff5f5;
        border: 1px solid #fee2e2;
        border-radius: var(--gov-radius-sm);
        padding: 0.6rem 1rem;
        margin-bottom: 1rem;
        transition: opacity 0.5s ease;
    }
</style>

<main class="container py-5 flex-grow-1">
    <div class="row justify-content-center">
        <!-- TÍTULO DE LA PÁGINA -->
        <div class="col-12">
            <div class="d-flex align-items-center gap-3 mb-3">
                <i class="bi bi-shield-check fs-1" style="color: var(--gov-blue);"></i>
                <div>
                    <h1 class="h4 fw-bold" style="color: var(--gov-dark);">Confirmar Postulación</h1>
                    <p class="text-muted m-0">Gestiona tus datos personales y configuración de seguridad</p>
                </div>
            </div>
            <hr class="mb-4">
        </div>

        <div class="col-lg-10 col-xl-8">
            <div class="confirm-box mt-5">
                <div class="text-center">
                    <h4 class="text-muted mb-5">Estás a punto de postularte a:</h4>
                    <p class="titulo-oferta"><?php echo htmlspecialchars($oferta['titulo_puesto']); ?></p>
                    <p class="empresa"><i class="bi bi-building me-1"></i> <?php echo htmlspecialchars($oferta['nombre_empresa']); ?></p>
                </div>
                <hr class="my-4">
                <p class="text-muted small text-center">Para confirmar tu identidad, introduce tu <strong>Documento de Identidad (DIP)</strong> y tu <strong>contraseña</strong>.</p>

                <?php if (isset($error)): ?>
                    <div id="errorMessage" class="error-message">
                        <i class="bi bi-exclamation-circle me-1"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="postular.php?oferta_id=<?php echo $oferta_id; ?>" class="mt-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="dip" class="form-label">Documento de Identidad (DIP)</label>
                            <input type="text" name="dip" id="dip" class="form-control" placeholder="Ej: 123456789" maxlength="30" required autofocus>
                        </div>
                        <div class="col-md-6">
                            <label for="password" class="form-label">Contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" name="password" id="password" class="form-control" placeholder="Introduce tu contraseña" required>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 d-flex justify-content-center gap-3 flex-wrap">
                        <a href="bolsa_trabajo.php" class="btn btn-outline-secondary px-4">❌ Cancelar</a>
                        <button type="submit" class="btn btn-confirmar px-5">✅ Confirmar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<?php include '../componentes/footer_desempleado.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const errorDiv = document.getElementById('errorMessage');
        if (errorDiv) {
            // Desaparecer automáticamente después de 5 segundos
            setTimeout(() => {
                errorDiv.style.opacity = '0';
                setTimeout(() => {
                    errorDiv.style.display = 'none';
                }, 500);
            }, 5000);
        }
    });
</script>
<?php
session_start();

// ===== VERIFICAR SESIÓN =====
if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../login_desempleados.php');
    exit();
}

$titulo = 'Mi Perfil - Portal de Empleo';
include '../componentes/header_desempleado.php';
include '../conexion/conexion.php';

$id_usuario = $_SESSION['id_usuario'];
$nombre_completo = $_SESSION['nombre_completo'] ?? '';

// ===== OBTENER DATOS DEL USUARIO DESDE BBDD =====
try {
    // Obtener datos del usuario
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
    $stmt->execute([$id_usuario]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // Obtener datos del buscador
    $stmt = $pdo->prepare("SELECT * FROM buscadores_empleo WHERE usuario_id = ?");
    $stmt->execute([$id_usuario]);
    $buscador = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar si tiene documentos
    $stmt_doc = $pdo->prepare("SELECT * FROM documentos WHERE usuario_id = ?");
    $stmt_doc->execute([$id_usuario]);
    $documentos = $stmt_doc->fetch();

    $perfil_completo = ($buscador && $documentos);

} catch (PDOException $e) {
    error_log("Error en perfil: " . $e->getMessage());
    $usuario = null;
    $buscador = null;
    $documentos = null;
    $perfil_completo = false;
}

// ===== INCLUIR MENÚ =====
include '../componentes/menu_desempleado.php';
?>

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

    /* ===== NAVBAR ===== */
    .navbar-portal {
        background-color: rgba(255, 255, 255, 0.96) !important;
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
        border-color: var(--gov-blue);
        box-shadow: 0 0 0 4px rgba(201, 168, 76, 0.12);
    }

    /* ===== ESTILOS ADICIONALES PARA PERFIL ===== */
    .perfil-completo {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
        padding: 0.5rem 1rem;
        border-radius: var(--gov-radius-sm);
        font-weight: 600;
        font-size: 0.85rem;
    }
    .perfil-incompleto {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
        padding: 0.5rem 1rem;
        border-radius: var(--gov-radius-sm);
        font-weight: 600;
        font-size: 0.85rem;
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

    /* ===== CONTENEDOR DE ALERTA EN PÁGINA (debajo del título) ===== */
    #alertContainer {
        margin-bottom: 1.5rem;
    }
    #alertContainer .alert {
        border-radius: var(--gov-radius);
        border-left: 6px solid;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        padding: 0.75rem 1.25rem;
    }
    #alertContainer .alert-success {
        border-left-color: var(--gov-green);
        background-color: #d4edda;
        color: #155724;
    }
    #alertContainer .alert-danger {
        border-left-color: #dc3545;
        background-color: #f8d7da;
        color: #721c24;
    }
</style>

<body>
    <canvas id="canvas-background"></canvas>
    <div class="main-wrapper">

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
                    <hr class="mb-3">

                    <!-- === CONTENEDOR PARA MENSAJES DE CONFIRMACIÓN (éxito/error) === -->
                    <div id="alertContainer"></div>
                </div>

                <!-- COLUMNA IZQUIERDA: DATOS PERSONALES -->
                <div class="col-lg-6">
                    <div class="card dashboard-card p-4 h-100">
                        <h5 class="fw-bold mb-3 h6 text-uppercase tracking-wider text-muted">
                            <i class="bi bi-person me-2" style="color: var(--gov-blue);"></i>Datos Personales
                        </h5>

                        <?php if ($buscador): ?>
                            <div class="mb-3">
                                <span class="<?php echo $perfil_completo ? 'perfil-completo' : 'perfil-incompleto'; ?>">
                                    <i class="bi <?php echo $perfil_completo ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill'; ?> me-1"></i>
                                    <?php echo $perfil_completo ? '✅ Perfil Completado' : '⚠️ Perfil Incompleto'; ?>
                                </span>
                            </div>
                        <?php endif; ?>

                        <form id="perfilForm" action="../php/procesar_actualizar_perfil.php" method="POST" enctype="multipart/form-data">
                            <div class="row g-3">
                                <div class="col-12 text-center mb-3">
                                    <div class="position-relative d-inline-block">
                                        <?php 
                                            $foto = $buscador['foto_carnet'] ?? '';
                                            if (!empty($foto) && strpos($foto, 'http') === false) {
                                                $foto = '../' . $foto;
                                            } elseif (empty($foto)) {
                                                $foto = 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/icons/person-bounding-box.svg';
                                            }
                                        ?>
                                        <img src="<?php echo htmlspecialchars($foto); ?>"
                                            class="rounded-circle border border-3 border-gold shadow-sm"
                                            style="width: 120px; height: 120px; object-fit: cover;" 
                                            alt="Foto de perfil"
                                            id="fotoPreview"
                                            onerror="this.src='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/icons/person-bounding-box.svg'">
                                        <label class="position-absolute bottom-0 end-0 bg-white rounded-circle p-1 shadow-sm" 
                                               style="border: 2px solid var(--gov-blue); cursor: pointer;">
                                            <i class="bi bi-camera-fill" style="color: var(--gov-blue); font-size: 1.2rem;"></i>
                                            <input type="file" name="foto_carnet" accept="image/*" class="d-none" onchange="previewImage(event)">
                                        </label>
                                    </div>
                                    <div class="mt-2">
                                        <small class="text-muted">Haz clic en la cámara para cambiar la foto (opcional)</small>
                                    </div>
                                </div>

                                <input type="hidden" name="usuario_id" value="<?php echo htmlspecialchars($usuario['id']); ?>">

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"><i class="bi bi-person me-1"></i>Nombre</label>
                                    <input type="text" name="nombre" class="form-control form-control-custom" 
                                           value="<?php echo htmlspecialchars($usuario['nombre'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"><i class="bi bi-person me-1"></i>Apellidos</label>
                                    <input type="text" name="apellidos" class="form-control form-control-custom" 
                                           value="<?php echo htmlspecialchars($usuario['apellidos'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"><i class="bi bi-card-text me-1"></i>DIP</label>
                                    <input type="text" name="documento_identidad" class="form-control form-control-custom" 
                                           value="<?php echo htmlspecialchars($usuario['documento_identidad'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"><i class="bi bi-telephone me-1"></i>Teléfono</label>
                                    <input type="tel" name="telefono" class="form-control form-control-custom" 
                                           value="<?php echo htmlspecialchars($buscador['telefono'] ?? ''); ?>">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold"><i class="bi bi-envelope me-1"></i>Correo electrónico</label>
                                    <input type="email" class="form-control form-control-custom" 
                                           value="<?php echo htmlspecialchars($usuario['correo_electronico'] ?? ''); ?>" disabled>
                                    <small class="text-muted">El correo electrónico no se puede modificar</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"><i class="bi bi-people me-1"></i>Estado Civil</label>
                                    <select name="estado_civil" class="form-select form-select-custom">
                                        <option value="soltero" <?php echo ($buscador['estado_civil'] ?? '') == 'soltero' ? 'selected' : ''; ?>>Soltero/a</option>
                                        <option value="casado" <?php echo ($buscador['estado_civil'] ?? '') == 'casado' ? 'selected' : ''; ?>>Casado/a</option>
                                        <option value="divorciado" <?php echo ($buscador['estado_civil'] ?? '') == 'divorciado' ? 'selected' : ''; ?>>Divorciado/a</option>
                                        <option value="viudo" <?php echo ($buscador['estado_civil'] ?? '') == 'viudo' ? 'selected' : ''; ?>>Viudo/a</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"><i class="bi bi-briefcase me-1"></i>Estado Laboral</label>
                                    <select name="estado_laboral" class="form-select form-select-custom">
                                        <option value="desempleado" <?php echo ($buscador['estado_laboral'] ?? '') == 'desempleado' ? 'selected' : ''; ?>>Desempleado</option>
                                        <option value="contratado" <?php echo ($buscador['estado_laboral'] ?? '') == 'contratado' ? 'selected' : ''; ?>>Contratado</option>
                                        <option value="suspendido" <?php echo ($buscador['estado_laboral'] ?? '') == 'suspendido' ? 'selected' : ''; ?>>Suspendido</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"><i class="bi bi-geo-alt me-1"></i>Provincia</label>
                                    <select name="provincia" class="form-select form-select-custom">
                                        <option value="bioko_norte" <?php echo ($buscador['provincia'] ?? '') == 'bioko_norte' ? 'selected' : ''; ?>>Bioko Norte</option>
                                        <option value="bioko_sur" <?php echo ($buscador['provincia'] ?? '') == 'bioko_sur' ? 'selected' : ''; ?>>Bioko Sur</option>
                                        <option value="litoral" <?php echo ($buscador['provincia'] ?? '') == 'litoral' ? 'selected' : ''; ?>>Litoral</option>
                                        <option value="centro_sur" <?php echo ($buscador['provincia'] ?? '') == 'centro_sur' ? 'selected' : ''; ?>>Centro Sur</option>
                                        <option value="kie_ntem" <?php echo ($buscador['provincia'] ?? '') == 'kie_ntem' ? 'selected' : ''; ?>>Kié-Ntem</option>
                                        <option value="wele_nzas" <?php echo ($buscador['provincia'] ?? '') == 'wele_nzas' ? 'selected' : ''; ?>>Wele-Nzas</option>
                                        <option value="annobon" <?php echo ($buscador['provincia'] ?? '') == 'annobon' ? 'selected' : ''; ?>>Annobón</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"><i class="bi bi-building me-1"></i>Ciudad</label>
                                    <select name="ciudad_municipio" class="form-select form-select-custom">
                                        <option value="malabo" <?php echo ($buscador['ciudad_municipio'] ?? '') == 'malabo' ? 'selected' : ''; ?>>Malabo</option>
                                        <option value="bata" <?php echo ($buscador['ciudad_municipio'] ?? '') == 'bata' ? 'selected' : ''; ?>>Bata</option>
                                        <option value="ebebiyin" <?php echo ($buscador['ciudad_municipio'] ?? '') == 'ebebiyin' ? 'selected' : ''; ?>>Ebebiyín</option>
                                        <option value="bidjabidján" <?php echo ($buscador['ciudad_municipio'] ?? '') == 'bidjabidján' ? 'selected' : ''; ?>>Bidjabidján</option>
                                        <option value="mongomo" <?php echo ($buscador['ciudad_municipio'] ?? '') == 'mongomo' ? 'selected' : ''; ?>>Mongomo</option>
                                        <option value="luba" <?php echo ($buscador['ciudad_municipio'] ?? '') == 'luba' ? 'selected' : ''; ?>>Luba</option>
                                        <option value="riaba" <?php echo ($buscador['ciudad_municipio'] ?? '') == 'riaba' ? 'selected' : ''; ?>>Riaba</option>
                                        <option value="evinayong" <?php echo ($buscador['ciudad_municipio'] ?? '') == 'evinayong' ? 'selected' : ''; ?>>Evinayong</option>
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

                <!-- COLUMNA DERECHA: SEGURIDAD Y DOCUMENTOS -->
                <div class="col-lg-6">
                    <div class="card dashboard-card p-4 h-100">
                        <h5 class="fw-bold mb-3 h6 text-uppercase tracking-wider text-muted">
                            <i class="bi bi-shield-lock me-2" style="color: var(--gov-green);"></i>Seguridad y Documentos
                        </h5>

                        <!-- Cambiar contraseña -->
                        <form id="seguridadForm" action="../php/procesar_cambiar_password.php" method="POST">
                            <div class="row g-3">
                                <div class="col-12">
                                    <hr>
                                    <h6 class="fw-semibold text-muted"><i class="bi bi-key me-1"></i>Cambiar contraseña</h6>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Contraseña actual *</label>
                                    <input type="password" name="password_actual" class="form-control form-control-custom" placeholder="Introduce tu contraseña actual" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Nueva contraseña *</label>
                                    <input type="password" name="password_nueva" id="newPassword" class="form-control form-control-custom" placeholder="Mínimo 8 caracteres" pattern=".{8,}" title="La contraseña debe tener al menos 8 caracteres" required>
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

                        <!-- Documentos -->
                        <div class="mt-4 pt-3 border-top">
                            <h6 class="fw-semibold text-muted"><i class="bi bi-file-earmark me-1"></i>Documentos adjuntos</h6>
                            <?php if ($documentos): ?>
                                <div class="row g-2 mt-2">
                                    <?php if (!empty($documentos['copia_dip'])): ?>
                                        <div class="col-6">
                                            <a href="../<?php echo htmlspecialchars($documentos['copia_dip']); ?>" target="_blank" class="btn btn-outline-secondary w-100 btn-sm">
                                                <i class="bi bi-file-earmark-pdf me-1"></i> DIP
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($documentos['cv'])): ?>
                                        <div class="col-6">
                                            <a href="../<?php echo htmlspecialchars($documentos['cv']); ?>" target="_blank" class="btn btn-outline-secondary w-100 btn-sm">
                                                <i class="bi bi-file-earmark-pdf me-1"></i> CV
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($documentos['titulos']) && $documentos['titulos'] !== ''): ?>
                                        <div class="col-6">
                                            <a href="../<?php echo htmlspecialchars($documentos['titulos']); ?>" target="_blank" class="btn btn-outline-secondary w-100 btn-sm">
                                                <i class="bi bi-file-earmark me-1"></i> Títulos
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($documentos['otros_documentos']) && $documentos['otros_documentos'] !== ''): ?>
                                        <div class="col-6">
                                            <a href="../<?php echo htmlspecialchars($documentos['otros_documentos']); ?>" target="_blank" class="btn btn-outline-secondary w-100 btn-sm">
                                                <i class="bi bi-file-earmark me-1"></i> Otros
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (empty($documentos['copia_dip']) && empty($documentos['cv'])): ?>
                                        <p class="text-muted small mt-2">No tienes documentos adjuntos.</p>
                                        <a href="completar_perfil.php" class="btn btn-gov btn-sm">
                                            <i class="bi bi-upload me-1"></i> Subir documentos
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted small mt-2">No tienes documentos adjuntos.</p>
                                <a href="completar_perfil.php" class="btn btn-gov btn-sm">
                                    <i class="bi bi-upload me-1"></i> Subir documentos
                                </a>
                            <?php endif; ?>
                        </div>

                        <!-- Información del expediente -->
                        <div class="mt-4 pt-3 border-top">
                            <h6 class="fw-semibold text-muted"><i class="bi bi-info-circle me-1"></i>Información del expediente</h6>
                            <div class="row g-2 mt-2">
                                <div class="col-6">
                                    <small class="text-muted d-block">Nº Expediente</small>
                                    <strong><?php echo htmlspecialchars($usuario['numero_expediente'] ?? 'No asignado'); ?></strong>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Fecha de registro</small>
                                    <strong><?php echo $usuario['fecha_registro'] ? date('d/m/Y', strtotime($usuario['fecha_registro'])) : 'No registrada'; ?></strong>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Rol</small>
                                    <strong><?php echo ucfirst($usuario['rol'] ?? 'No definido'); ?></strong>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Correo verificado</small>
                                    <strong>
                                        <?php echo ($usuario['correo_verificado'] ?? 0) ? '✅ Sí' : '❌ No'; ?>
                                    </strong>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </main>

        <?php include '../componentes/footer_desempleado.php'; ?>

        <script>
            // Previsualización de foto
            function previewImage(event) {
                const reader = new FileReader();
                reader.onload = function() {
                    const img = document.getElementById('fotoPreview');
                    if (img) {
                        img.src = reader.result;
                    }
                }
                if (event.target.files[0]) {
                    reader.readAsDataURL(event.target.files[0]);
                }
            }

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

            // ===== MOSTRAR MENSAJES DE CONFIRMACIÓN EN EL CONTENEDOR DE LA PÁGINA =====
            (function() {
                const container = document.getElementById('alertContainer');
                let mensaje = null;

                // Recoger mensaje de sesión (si existe)
                <?php if (isset($_SESSION['mensaje_exito'])): ?>
                    mensaje = {
                        texto: "<?php echo htmlspecialchars($_SESSION['mensaje_exito']); ?>",
                        tipo: 'success'
                    };
                    <?php unset($_SESSION['mensaje_exito']); ?>
                <?php elseif (isset($_SESSION['mensaje_error'])): ?>
                    mensaje = {
                        texto: "<?php echo htmlspecialchars($_SESSION['mensaje_error']); ?>",
                        tipo: 'danger'
                    };
                    <?php unset($_SESSION['mensaje_error']); ?>
                <?php endif; ?>

                if (mensaje && container) {
                    // Crear el div de alerta
                    const alertDiv = document.createElement('div');
                    alertDiv.className = `alert alert-${mensaje.tipo} alert-dismissible fade show`;
                    alertDiv.role = 'alert';
                    alertDiv.innerHTML = `
                        <i class="bi bi-${mensaje.tipo === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
                        ${mensaje.texto}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    `;
                    container.appendChild(alertDiv);

                    // Desaparecer automáticamente después de 8 segundos
                    setTimeout(() => {
                        if (alertDiv) {
                            alertDiv.classList.remove('show');
                            setTimeout(() => alertDiv.remove(), 300);
                        }
                    }, 8000);
                }
            })();
        </script>

    </div>
</body>
</html>
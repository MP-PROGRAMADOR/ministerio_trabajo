<?php
session_start();

// ===== VERIFICAR SESIÓN =====
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'empleador') {
    header('Location: ../login_empleador.php');
    exit();
}

$titulo = 'Datos de la Empresa - Portal de Empleo';
include_once '../componentes/header_empleador.php';
include_once '../conexion/conexion.php';

$id_usuario = $_SESSION['id_usuario'];
$nombre_empresa = $_SESSION['nombre_empresa'] ?? 'Mi Empresa';

// ===== OBTENER DATOS DE LA EMPRESA =====
$empresa = null;
$mensaje_error = '';

try {
    $stmt = $pdo->prepare("
        SELECT 
            e.*,
            u.nombre,
            u.apellidos,
            u.correo_electronico,
            u.nombre_usuario,
            u.documento_identidad,
            u.fecha_registro
        FROM empleadores e
        JOIN usuarios u ON e.usuario_id = u.id
        WHERE e.usuario_id = ?
    ");
    $stmt->execute([$id_usuario]);
    $empresa = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$empresa) {
        $mensaje_error = 'No se encontraron datos de la empresa. Complete su perfil de empleador.';
    }

} catch (PDOException $e) {
    error_log("Error en datos_empresas: " . $e->getMessage());
    $mensaje_error = 'Error al cargar los datos de la empresa.';
}

$_SESSION['nombre_empresa'] = $empresa['nombre_empresa'] ?? $nombre_empresa;

include_once '../componentes/menu_empleador.php';
?>

<!-- ===== CONTENIDO ===== -->
<style>
    /* ===== ESTILOS MEJORADOS ===== */
    :root {
        --gov-blue: #0B3A60;
        --gov-blue-light: #1A4F7A;
        --gov-green: #1E7E34;
        --gov-green-light: #2E9B4A;
        --gov-gold: #C9A84C;
        --gov-gold-light: #E8D5A3;
        --gov-dark: #0A192F;
        --gov-bg: #F8FAFC;
        --gov-border: #E2E8F0;
        --gov-radius: 12px;
        --gov-radius-sm: 8px;
        --gov-shadow: rgba(11, 58, 96, 0.08);
    }

    body {
        background: var(--gov-bg);
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }

    .custom-card {
        background: #ffffff;
        border: none;
        border-radius: var(--gov-radius);
        padding: 1.5rem;
        box-shadow: 0 2px 8px var(--gov-shadow);
        transition: all 0.3s ease;
        height: 100%;
    }
    .custom-card:hover {
        box-shadow: 0 8px 25px rgba(11, 58, 96, 0.10);
    }

    .profile-header {
        background: linear-gradient(135deg, var(--gov-blue) 0%, var(--gov-blue-light) 100%);
        border-radius: var(--gov-radius) var(--gov-radius) 0 0;
        padding: 2rem 2rem 1.5rem 2rem;
        color: white;
        margin: -1.5rem -1.5rem 1.5rem -1.5rem;
    }
    .profile-header .empresa-nombre {
        font-size: 1.8rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }
    .profile-header .empresa-ubicacion {
        font-size: 0.95rem;
        opacity: 0.85;
    }

    .detail-row {
        display: flex;
        padding: 0.6rem 0;
        border-bottom: 1px solid var(--gov-border);
    }
    .detail-row:last-child {
        border-bottom: none;
    }
    .detail-row .label {
        font-weight: 600;
        color: #6b7a8a;
        width: 180px;
        flex-shrink: 0;
        font-size: 0.9rem;
    }
    .detail-row .value {
        color: var(--gov-dark);
        font-size: 0.95rem;
        word-break: break-word;
    }
    .detail-row .value i {
        color: var(--gov-gold);
        margin-right: 6px;
        width: 1.2rem;
    }

    .status-badge {
        display: inline-block;
        padding: 0.35rem 1.2rem;
        border-radius: 30px;
        font-weight: 600;
        font-size: 0.85rem;
    }
    .status-badge.activo {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    .status-badge.inactivo {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    .status-badge.suspendido {
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffe69c;
    }

    .btn-primary {
        background: var(--gov-blue);
        border: none;
        border-radius: var(--gov-radius-sm);
        padding: 0.5rem 1.2rem;
        font-weight: 500;
        transition: all 0.3s;
    }
    .btn-primary:hover {
        background: var(--gov-blue-light);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(11, 58, 96, 0.2);
    }
    .btn-outline-secondary {
        border: 1px solid var(--gov-border);
        color: var(--gov-dark);
        border-radius: var(--gov-radius-sm);
        padding: 0.5rem 1.2rem;
        font-weight: 500;
        transition: all 0.2s;
    }
    .btn-outline-secondary:hover {
        background: var(--gov-bg);
        border-color: var(--gov-blue);
        color: var(--gov-blue);
    }

    .form-control, .form-select {
        border-radius: var(--gov-radius-sm);
        border: 1.5px solid var(--gov-border);
        padding: 0.6rem 1rem;
        transition: all 0.3s;
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--gov-blue);
        box-shadow: 0 0 0 3px rgba(11, 58, 96, 0.10);
    }
    .form-label {
        font-weight: 600;
        font-size: 0.85rem;
        color: var(--gov-dark);
    }
    .form-text {
        font-size: 0.75rem;
        color: #6b7a8a;
    }

    .info-box {
        background: var(--gov-bg);
        border-radius: var(--gov-radius-sm);
        padding: 1rem 1.25rem;
        border-left: 4px solid var(--gov-gold);
    }
    .info-box i {
        color: var(--gov-gold);
        margin-right: 8px;
    }

    /* ===== ALERTAS CON ANIMACIÓN ===== */
    .alert-auto-dismiss {
        animation: slideDown 0.5s ease forwards;
    }
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* ===== MODAL FOOTER CON ESPACIO ===== */
    .modal-footer .btn {
        min-width: 100px;
    }
    .modal-footer .btn-light {
        margin-right: 0.5rem;
    }

    @media (max-width: 768px) {
        .profile-header .empresa-nombre {
            font-size: 1.3rem;
        }
        .detail-row {
            flex-wrap: wrap;
        }
        .detail-row .label {
            width: 100%;
            margin-bottom: 2px;
            font-size: 0.8rem;
        }
        .detail-row .value {
            font-size: 0.9rem;
        }
        .custom-card {
            padding: 1rem;
        }
        .profile-header {
            margin: -1rem -1rem 1rem -1rem;
            padding: 1.5rem;
        }
        .modal-footer {
            flex-wrap: wrap;
            justify-content: center;
        }
        .modal-footer .btn {
            min-width: 80px;
            margin-bottom: 0.3rem;
        }
    }
</style>

<!-- ===== TÍTULO ===== -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex align-items-center gap-3">
            <i class="bi bi-building fs-1" style="color: var(--gov-blue);"></i>
            <div>
                <h1 class="h3 fw-bold m-0" style="color: var(--gov-dark);">Datos de la Empresa</h1>
                <p class="text-muted m-0">Información corporativa y datos de acceso a tu cuenta</p>
            </div>
        </div>
        <hr class="mb-0 mt-3">
    </div>
</div>

<!-- ===== ALERTAS CON AUTO CIERRE ===== -->
<?php if (isset($_SESSION['mensaje_exito'])): ?>
    <div class="alert alert-success alert-dismissible fade show alert-auto-dismiss" role="alert" id="alertExito">
        <i class="bi bi-check-circle-fill me-2"></i>
        <?php echo htmlspecialchars($_SESSION['mensaje_exito']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['mensaje_exito']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['mensaje_error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show alert-auto-dismiss" role="alert" id="alertError">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <?php echo htmlspecialchars($_SESSION['mensaje_error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['mensaje_error']); ?>
<?php endif; ?>

<?php if ($mensaje_error): ?>
    <div class="alert alert-warning alert-dismissible fade show alert-auto-dismiss" role="alert" id="alertWarning">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <?php echo htmlspecialchars($mensaje_error); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if ($empresa): ?>
    <div class="row g-4">

        <!-- ===== INFORMACIÓN DE LA EMPRESA ===== -->
        <div class="col-12 col-xl-7">
            <div class="custom-card">
                <div class="profile-header">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="empresa-nombre"><?php echo htmlspecialchars($empresa['nombre_empresa']); ?></div>
                            <div class="empresa-ubicacion">
                                <i class="bi bi-geo-alt me-1"></i>
                                <?php echo htmlspecialchars($empresa['direccion'] ?? 'Dirección no registrada'); ?>
                            </div>
                        </div>
                        <span class="status-badge activo">
                            <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i>
                            Activo
                        </span>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="detail-row">
                            <span class="label"><i class="bi bi-hash"></i> RUC / RNC</span>
                            <span class="value"><?php echo htmlspecialchars($empresa['rnc_ruc'] ?? 'No registrado'); ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="label"><i class="bi bi-tag"></i> Sector</span>
                            <span class="value"><?php echo htmlspecialchars($empresa['sector_industrial'] ?? 'No especificado'); ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="label"><i class="bi bi-telephone"></i> Teléfono</span>
                            <span class="value"><?php echo htmlspecialchars($empresa['telefono_corporativo'] ?? 'No registrado'); ?></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-row">
                            <span class="label"><i class="bi bi-globe"></i> Sitio Web</span>
                            <span class="value">
                                <?php if ($empresa['sitio_web']): ?>
                                    <a href="<?php echo htmlspecialchars($empresa['sitio_web']); ?>" target="_blank" style="color: var(--gov-blue);">
                                        <?php echo htmlspecialchars($empresa['sitio_web']); ?>
                                        <i class="bi bi-box-arrow-up-right ms-1" style="font-size: 0.7rem;"></i>
                                    </a>
                                <?php else: ?>
                                    No registrado
                                <?php endif; ?>
                            </span>
                        </div>
                        <div class="detail-row">
                            <span class="label"><i class="bi bi-calendar3"></i> Registro</span>
                            <span class="value"><?php echo date('d/m/Y', strtotime($empresa['fecha_registro'])); ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="label"><i class="bi bi-envelope"></i> Correo</span>
                            <span class="value"><?php echo htmlspecialchars($empresa['correo_electronico']); ?></span>
                        </div>
                    </div>
                </div>

                <div class="mt-3 d-flex flex-wrap gap-2">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalEditarEmpresa">
                        <i class="bi bi-pencil-square me-1"></i> Editar Datos
                    </button>
                    <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalEditarResponsable">
                        <i class="bi bi-person-gear me-1"></i> Editar Responsable
                    </button>
                    <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalCambiarCuenta">
                        <i class="bi bi-key me-1"></i> Cambiar Cuenta de Acceso
                    </button>
                    <button class="btn btn-outline-secondary" onclick="window.print()">
                        <i class="bi bi-download me-1"></i> Descargar Información
                    </button>
                </div>
            </div>
        </div>

        <!-- ===== INFORMACIÓN DEL RESPONSABLE ===== -->
        <div class="col-12 col-xl-5">
            <div class="custom-card">
                <h5 class="fw-bold mb-3">
                    <i class="bi bi-person-circle me-2" style="color: var(--gov-blue);"></i>
                    Responsable / Contacto
                </h5>

                <div class="text-center mb-3">
                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto" 
                         style="width: 80px; height: 80px; border: 3px solid var(--gov-green);">
                        <i class="bi bi-person" style="font-size: 2.5rem; color: var(--gov-blue);"></i>
                    </div>
                </div>

                <div class="detail-row">
                    <span class="label"><i class="bi bi-person"></i> Nombre</span>
                    <span class="value"><?php echo htmlspecialchars($empresa['nombre'] . ' ' . $empresa['apellidos']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="label"><i class="bi bi-person-badge"></i> Usuario</span>
                    <span class="value"><?php echo htmlspecialchars($empresa['nombre_usuario']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="label"><i class="bi bi-card-text"></i> Documento</span>
                    <span class="value"><?php echo htmlspecialchars($empresa['documento_identidad']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="label"><i class="bi bi-envelope"></i> Correo</span>
                    <span class="value"><?php echo htmlspecialchars($empresa['correo_electronico']); ?></span>
                </div>

                <div class="info-box mt-3">
                    <i class="bi bi-info-circle"></i>
                    <small class="text-muted">
                        Este es el contacto principal autorizado para gestionar las ofertas de empleo y las intermediaciones con el Ministerio.
                    </small>
                </div>
            </div>
        </div>

    </div>
<?php endif; ?>

<!-- ===== MODAL EDITAR EMPRESA ===== -->
<div class="modal fade" id="modalEditarEmpresa" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-pencil-square me-2" style="color: var(--gov-gold);"></i>
                    Editar Datos de la Empresa
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="../php/actualizar_perfil_empresa.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="empresa_id" value="<?php echo htmlspecialchars($empresa['id'] ?? ''); ?>">
                    <input type="hidden" name="accion" value="datos_empresa">
                    
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Nombre de la Empresa *</label>
                            <input type="text" name="nombre_empresa" class="form-control" 
                                   value="<?php echo htmlspecialchars($empresa['nombre_empresa'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">RUC / RNC</label>
                            <input type="text" name="rnc_ruc" class="form-control" 
                                   value="<?php echo htmlspecialchars($empresa['rnc_ruc'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Sector Industrial *</label>
                            <input type="text" name="sector_industrial" class="form-control" 
                                   value="<?php echo htmlspecialchars($empresa['sector_industrial'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Teléfono Corporativo *</label>
                            <input type="tel" name="telefono_corporativo" class="form-control" 
                                   value="<?php echo htmlspecialchars($empresa['telefono_corporativo'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Sitio Web</label>
                            <input type="url" name="sitio_web" class="form-control" 
                                   value="<?php echo htmlspecialchars($empresa['sitio_web'] ?? ''); ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Dirección *</label>
                            <input type="text" name="direccion" class="form-control" 
                                   value="<?php echo htmlspecialchars($empresa['direccion'] ?? ''); ?>" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check2-circle me-1"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ===== MODAL EDITAR RESPONSABLE ===== -->
<div class="modal fade" id="modalEditarResponsable" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-person-gear me-2" style="color: var(--gov-blue);"></i>
                    Editar Datos del Responsable
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="../php/actualizar_perfil_empresa.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="usuario_id" value="<?php echo htmlspecialchars($id_usuario); ?>">
                    <input type="hidden" name="accion" value="responsable">
                    
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Datos del Responsable</label>
                            <hr class="mt-1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nombre *</label>
                            <input type="text" name="nombre" class="form-control" 
                                   value="<?php echo htmlspecialchars($empresa['nombre'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Apellidos *</label>
                            <input type="text" name="apellidos" class="form-control" 
                                   value="<?php echo htmlspecialchars($empresa['apellidos'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Documento de Identidad *</label>
                            <input type="text" name="documento_identidad" class="form-control" 
                                   value="<?php echo htmlspecialchars($empresa['documento_identidad'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Correo Electrónico *</label>
                            <input type="email" name="correo_electronico" class="form-control" 
                                   value="<?php echo htmlspecialchars($empresa['correo_electronico'] ?? ''); ?>" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check2-circle me-1"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ===== MODAL CAMBIAR CUENTA DE ACCESO ===== -->
<div class="modal fade" id="modalCambiarCuenta" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-key me-2" style="color: var(--gov-green);"></i>
                    Cambiar Cuenta de Acceso
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="../php/actualizar_perfil_empresa.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="usuario_id" value="<?php echo htmlspecialchars($id_usuario); ?>">
                    <input type="hidden" name="accion" value="credenciales">
                    
                    <p class="text-muted small mb-3">
                        <i class="bi bi-info-circle me-1"></i>
                        Puedes cambiar tu nombre de usuario y contraseña de acceso al sistema.
                    </p>
                    
                    <div class="mb-3">
                        <label class="form-label">Nombre de Usuario</label>
                        <input type="text" name="nombre_usuario" class="form-control" 
                               value="<?php echo htmlspecialchars($empresa['nombre_usuario'] ?? ''); ?>" required>
                        <div class="form-text">El nombre de usuario que usarás para iniciar sesión.</div>
                    </div>

                    <hr>

                    <h6 class="fw-bold mb-3 text-muted">Cambiar Contraseña</h6>

                    <div class="mb-3">
                        <label class="form-label">Contraseña Actual</label>
                        <input type="password" name="password_actual" class="form-control" placeholder="Introduce tu contraseña actual">
                        <div class="form-text">Obligatorio si deseas cambiar la contraseña.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nueva Contraseña</label>
                        <input type="password" name="password_nueva" id="newPassword" class="form-control" placeholder="Mínimo 8 caracteres" pattern=".{8,}" title="La contraseña debe tener al menos 8 caracteres">
                        <div class="form-text">Mínimo 8 caracteres, incluyendo letras y números.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Confirmar Nueva Contraseña</label>
                        <input type="password" id="confirmPassword" class="form-control" placeholder="Repite la nueva contraseña">
                        <div id="passwordHelp" class="form-text text-danger d-none">Las contraseñas no coinciden.</div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check2-circle me-1"></i> Actualizar Cuenta
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // ===== AUTO-CERRAR ALERTAS DESPUÉS DE 5 SEGUNDOS =====
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alert-auto-dismiss');
        alerts.forEach(function(alert) {
            setTimeout(function() {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        });
    });

    // ===== VALIDAR CONFIRMACIÓN DE CONTRASEÑA =====
    document.addEventListener('DOMContentLoaded', function() {
        const newPw = document.getElementById('newPassword');
        const confirmPw = document.getElementById('confirmPassword');
        const help = document.getElementById('passwordHelp');

        function validatePassword() {
            if (confirmPw && confirmPw.value.length > 0 && newPw && newPw.value !== confirmPw.value) {
                help.classList.remove('d-none');
                confirmPw.classList.add('is-invalid');
            } else if (help) {
                help.classList.add('d-none');
                if (confirmPw) confirmPw.classList.remove('is-invalid');
            }
        }

        if (newPw) {
            newPw.addEventListener('input', validatePassword);
        }
        if (confirmPw) {
            confirmPw.addEventListener('input', validatePassword);
        }
    });
</script>

<?php
echo '</div>'; // Cierra page-content
echo '</main>'; // Cierra main-content
echo '</div>'; // Cierra wrapper

include_once '../componentes/footer_empleador.php';
?>
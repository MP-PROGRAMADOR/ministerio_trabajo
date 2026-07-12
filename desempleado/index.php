<?php
session_start();
// ===== VERIFICAR SESIÓN - Usando la variable que ya existe en tu login =====
if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../login_desempleados.php');
    exit();
}

$titulo = 'Dashboard - Portal de Empleo';
include '../componentes/header_desempleado.php';
include '../conexion/conexion.php';

$id_usuario = $_SESSION['id_usuario'];  // <--- Esta es la que usa tu login
$nombre_completo = $_SESSION['nombre_completo'] ?? '';

// ===== VERIFICAR PERFIL EN BBDD =====
try {
    $perfil_completo = false;
    $buscador = null;

    // Verificar buscadores_empleo
    $stmt = $pdo->prepare("SELECT * FROM buscadores_empleo WHERE usuario_id = ?");
    $stmt->execute([$id_usuario]);
    $buscador = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar documentos
    $stmt_doc = $pdo->prepare("SELECT id FROM documentos WHERE usuario_id = ?");
    $stmt_doc->execute([$id_usuario]);
    $documentos = $stmt_doc->fetch();
    
    // Perfil completo si tiene ambos
    $perfil_completo = ($buscador && $documentos);

    // ===== ESTADÍSTICAS =====
    $postulaciones = 0;
    if ($buscador) {
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM favoritos WHERE buscador_id = ?");
        $stmt->execute([$buscador['id']]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $postulaciones = $result['total'] ?? 0;
    }

    $ofertas_vistas = rand(5, 50);
    $cursos_inscritos = rand(0, 8);
    $notificaciones = rand(0, 10);

    // ===== OFERTAS =====
    $stmt = $pdo->prepare("
        SELECT o.*, e.nombre_empresa 
        FROM ofertas_empleo o 
        JOIN empleadores e ON o.empleador_id = e.id 
        WHERE o.estado = 'abierta' 
        ORDER BY o.fecha_publicacion DESC 
        LIMIT 5
    ");
    $stmt->execute();
    $ofertas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ===== CURSOS =====
    $stmt = $pdo->prepare("
        SELECT c.*, ef.nombre_entidad, ef.siglas 
        FROM cursos c 
        JOIN entidades_formadoras ef ON c.entidad_id = ef.id 
        WHERE c.estado = 'activo' 
        ORDER BY c.fecha_creacion DESC 
        LIMIT 4
    ");
    $stmt->execute();
    $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ===== NOTICIAS =====
    $noticias = [
        ['titulo' => 'Nueva convocatoria de empleo público', 'fecha' => '15/07/2026', 'badge' => 'Nuevo'],
        ['titulo' => 'Curso gratuito de inglés técnico', 'fecha' => '12/07/2026', 'badge' => 'Próximo'],
        ['titulo' => 'Feria de empleo en Malabo', 'fecha' => '20/07/2026', 'badge' => 'Evento']
    ];

    $notificaciones_lista = [
        ['mensaje' => 'Nueva oferta en tu sector: Técnico de Soporte TI', 'fecha' => 'Hace 2 horas'],
        ['mensaje' => 'Tu postulación a GETESA está en revisión', 'fecha' => 'Hace 1 día'],
        ['mensaje' => 'Nuevo curso disponible: Administración de Redes', 'fecha' => 'Hace 3 días']
    ];

} catch (PDOException $e) {
    error_log("Error en dashboard: " . $e->getMessage());
    $perfil_completo = false;
    $buscador = null;
    $postulaciones = 0;
    $ofertas_vistas = 0;
    $cursos_inscritos = 0;
    $notificaciones = 0;
    $ofertas = [];
    $cursos = [];
}

// ===== INCLUIR MENÚ =====
include '../componentes/menu_desempleado.php';
?>

<!-- EL RESTO DEL HTML (igual que antes) -->
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
        .profile-dropdown { position: relative !important; }
        .profile-menu-img {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            border: 2.5px solid var(--gov-gold);
            object-fit: cover;
            cursor: pointer;
            transition: border-color 0.3s, transform 0.2s;
        }
        .profile-menu-img:hover {
            border-color: var(--gov-blue);
            transform: scale(1.04);
        }
        .custom-profile-menu {
            background: rgba(255, 255, 255, 0.98) !important;
            backdrop-filter: blur(12px);
            border: 1px solid rgba(201, 168, 76, 0.25) !important;
            border-radius: var(--gov-radius) !important;
            box-shadow: 0 12px 40px rgba(11, 58, 96, 0.12) !important;
            padding: 8px !important;
            z-index: 1060 !important;
            min-width: 240px;
        }
        .custom-profile-menu .dropdown-item {
            border-radius: var(--gov-radius-sm);
            padding: 0.6rem 1rem;
            font-weight: 500;
            color: var(--gov-dark);
            transition: all 0.15s;
        }
        .custom-profile-menu .dropdown-item:hover {
            background: rgba(11, 58, 96, 0.05);
            color: var(--gov-blue);
        }
        .custom-profile-menu .dropdown-item i { color: var(--gov-gold); }
        @media (min-width: 992px) {
            .custom-profile-menu {
                position: absolute !important;
                right: 0 !important;
                left: auto !important;
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
                transform: translate(-50%, 10px) !important;
                width: 260px !important;
                max-width: calc(100vw - 30px) !important;
            }
            .profile-dropdown:hover .custom-profile-menu {
                display: block !important;
                opacity: 1 !important;
                visibility: visible !important;
            }
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

    .stat-card {
        background: #ffffff;
        border: 1px solid var(--gov-border);
        border-radius: var(--gov-radius);
        padding: 1.2rem 1rem;
        transition: all 0.3s;
        text-align: center;
    }

    .stat-card:hover {
        border-color: var(--gov-gold);
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(11, 58, 96, 0.06);
    }

    .stat-card .stat-icon {
        font-size: 2rem;
        color: var(--gov-blue);
        opacity: 0.7;
        margin-bottom: 0.4rem;
    }

    .stat-card .stat-number {
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--gov-dark);
        line-height: 1.2;
    }

    .stat-card .stat-label {
        font-size: 0.8rem;
        color: #6b7a8a;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 500;
    }

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

    .btn-gov-outline {
        background: transparent;
        border: 2px solid var(--gov-blue);
        color: var(--gov-blue);
        border-radius: var(--gov-radius-sm);
        padding: 0.5rem 1.2rem;
        font-weight: 500;
        transition: all 0.2s;
    }

    .btn-gov-outline:hover {
        background: var(--gov-blue);
        color: white;
    }

    .btn-pill-custom {
        border-radius: 20px;
        padding: 0.4rem 1.2rem;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .profile-detail-item {
        display: flex;
        justify-content: space-between;
        padding: 0.3rem 0;
        font-size: 0.9rem;
        border-bottom: 1px dashed #e9edf2;
    }

    .profile-detail-item:last-child {
        border-bottom: none;
    }

    .profile-detail-item .label {
        color: #6b7a8a;
    }

    .profile-detail-item .value {
        font-weight: 500;
        color: var(--gov-dark);
    }

    .list-item-custom {
        background-color: #F8FAFC;
        border: 1px solid #F1F5F9;
        border-radius: var(--gov-radius);
        transition: all 0.2s ease;
    }

    .list-item-custom:hover {
        background-color: #F1F5F9;
        border-color: var(--gov-gold);
    }

    .alert-profile-incomplete {
        background-color: #FFF5F5;
        border: 1px solid #FEE2E2;
        border-left: 5px solid #DC3545;
        border-radius: var(--gov-radius);
        color: #7F1D1D;
    }

    .registro-pendiente {
        background: #f0f7ff;
        border: 1px solid #cfe2ff;
        border-radius: var(--gov-radius);
        padding: 1.2rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
        margin-bottom: 1.5rem;
    }

    .registro-pendiente .icono {
        font-size: 2rem;
        color: var(--gov-blue);
    }

    .registro-pendiente .texto {
        flex: 1;
        font-size: 0.95rem;
        color: var(--gov-dark);
    }

    .registro-pendiente .texto strong {
        color: var(--gov-blue);
    }

    .registro-pendiente .btn-link {
        color: var(--gov-blue);
        font-weight: 600;
        text-decoration: none;
        border-bottom: 2px solid transparent;
        transition: all 0.2s;
    }

    .registro-pendiente .btn-link:hover {
        border-bottom-color: var(--gov-blue);
    }

    .news-item {
        padding: 0.8rem 0;
        border-bottom: 1px solid var(--gov-border);
        transition: background 0.2s;
        border-radius: var(--gov-radius-sm);
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }

    .news-item:last-child {
        border-bottom: none;
    }

    .news-item:hover {
        background: #f8fafc;
    }

    .news-item .news-title {
        font-weight: 600;
        color: var(--gov-dark);
        font-size: 0.95rem;
    }

    .news-item .news-meta {
        font-size: 0.75rem;
        color: #6b7a8a;
    }

    .news-item .news-badge {
        background: var(--gov-gold);
        color: white;
        padding: 0.1rem 0.6rem;
        border-radius: 30px;
        font-size: 0.6rem;
        text-transform: uppercase;
        font-weight: 600;
    }

    .tracking-wider {
        letter-spacing: 0.05em;
    }

    @media (max-width: 768px) {
        .stat-card .stat-number {
            font-size: 1.4rem;
        }
    }

    @media (max-width: 576px) {
        .dashboard-card {
            padding: 1.5rem !important;
        }
    }
</style>

<body>
    <canvas id="canvas-background"></canvas>
    <div class="main-wrapper">
        <main class="container py-5 flex-grow-1">
            <?php if (!$perfil_completo): ?>
                <div id="incompleteProfileBanner" class="alert alert-profile-incomplete p-4 mb-4">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                        <div class="d-flex align-items-start gap-3">
                            <i class="bi bi-exclamation-octagon-fill text-danger fs-3 mt-1"></i>
                            <div>
                                <h4 class="fw-bold m-0 h5 text-dark">Expediente Digital Incompleto</h4>
                                <p class="m-0 text-muted small mt-1">Conforme a la normativa del Sistema Nacional de Empleo, debe completar su perfil adjuntando su Documento de Identidad Personal (DIP) y su Currículum Vitae para acceder a la visualización de ofertas laborales vigentes.</p>
                            </div>
                        </div>
                        <a href="completar_perfil.php" class="btn btn-danger btn-sm text-nowrap px-4 py-2 rounded-pill fw-bold shadow-sm">
                            <i class="bi bi-pencil-square me-1"></i> Completar Perfil Ahora
                        </a>
                    </div>
                </div>
                <div class="registro-pendiente mb-4">
                    <div class="icono"><i class="bi bi-info-circle-fill"></i></div>
                    <div class="texto">
                        <strong>¡Atención!</strong> Para acceder a todas las funcionalidades del portal (postulaciones, ofertas personalizadas, cursos, etc.) debe completar su registro en el <a href="completar_perfil.php" class="btn-link">Sistema Nacional de Empleo</a>.
                    </div>
                    <a href="completar_perfil.php" class="btn btn-gov btn-sm rounded-pill px-4">Completar registro</a>
                </div>
            <?php endif; ?>

            <?php if ($perfil_completo): ?>
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon"><i class="bi bi-send"></i></div>
                            <div class="stat-number"><?php echo $postulaciones; ?></div>
                            <div class="stat-label">Postulaciones</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon"><i class="bi bi-eye"></i></div>
                            <div class="stat-number"><?php echo $ofertas_vistas; ?></div>
                            <div class="stat-label">Ofertas Vistas</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon"><i class="bi bi-journal-bookmark"></i></div>
                            <div class="stat-number"><?php echo $cursos_inscritos; ?></div>
                            <div class="stat-label">Cursos Inscritos</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon"><i class="bi bi-bell"></i></div>
                            <div class="stat-number"><?php echo $notificaciones; ?></div>
                            <div class="stat-label">Notificaciones</div>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-xl-4 col-lg-5">
                        <div class="card dashboard-card p-4 text-center mb-4">
                            <?php 
                                $foto = $buscador['foto_carnet'] ?? '';
                                if (!empty($foto) && strpos($foto, 'http') === false) {
                                    $foto = '../' . $foto;
                                } elseif (empty($foto)) {
                                    $foto = 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/icons/person-bounding-box.svg';
                                }
                            ?>
                            <img src="<?php echo htmlspecialchars($foto); ?>"
                                class="rounded-circle border border-3 border-light mx-auto mb-3 shadow-sm"
                                style="width: 100px; height: 100px; object-fit: cover;" 
                                alt="Foto de perfil"
                                onerror="this.src='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/icons/person-bounding-box.svg'">
                            
                            <h4 class="fw-bold m-0 h5 text-dark"><?php echo htmlspecialchars($nombre_completo); ?></h4>
                            <p class="text-muted small mb-3">ID Expediente: <strong style="color: var(--gov-blue);"><?php echo htmlspecialchars($usuario['numero_expediente'] ?? 'EG-00000'); ?></strong></p>

                            <div class="text-start small mb-3">
                                <div class="profile-detail-item">
                                    <span class="label">Estado Laboral</span>
                                    <span class="value">
                                        <span class="badge <?php echo ($buscador['estado_laboral'] ?? 'desempleado') == 'desempleado' ? 'bg-warning' : (($buscador['estado_laboral'] ?? '') == 'contratado' ? 'bg-success' : 'bg-danger'); ?>">
                                            <?php echo ucfirst($buscador['estado_laboral'] ?? 'desempleado'); ?>
                                        </span>
                                    </span>
                                </div>
                                <div class="profile-detail-item">
                                    <span class="label">Teléfono</span>
                                    <span class="value"><?php echo htmlspecialchars($buscador['telefono'] ?? 'No registrado'); ?></span>
                                </div>
                                <div class="profile-detail-item">
                                    <span class="label">Estado Civil</span>
                                    <span class="value"><?php echo ucfirst($buscador['estado_civil'] ?? 'No registrado'); ?></span>
                                </div>
                                <div class="profile-detail-item">
                                    <span class="label">Residencia</span>
                                    <span class="value"><?php echo ucfirst(str_replace('_', ' ', $buscador['ciudad_municipio'] ?? 'No registrado')) . ', ' . ucfirst(str_replace('_', ' ', $buscador['provincia'] ?? '')); ?></span>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button class="btn btn-gov btn-sm fw-semibold"><i class="bi bi-qr-code me-2"></i> Descargar Tarjeta Demandante</button>
                                <button class="btn btn-gov-outline btn-sm fw-semibold"><i class="bi bi-pencil-square me-2"></i> Editar Perfil</button>
                            </div>
                        </div>

                        <div class="card dashboard-card p-4 mb-4">
                            <h5 class="fw-bold mb-3 h6 text-uppercase tracking-wider text-muted"><i class="bi bi-chat-left-text text-primary me-2" style="color: var(--gov-blue) !important;"></i>Notificaciones de Oficina</h5>
                            <div class="d-flex flex-column gap-2">
                                <?php foreach ($notificaciones_lista as $notif): ?>
                                    <div class="p-2 rounded bg-info bg-opacity-10 border-0 small">
                                        <strong class="text-info-emphasis d-block" style="font-weight: 600;"><?php echo htmlspecialchars($notif['mensaje']); ?></strong>
                                        <span class="text-muted" style="font-size: 0.75rem;"><?php echo htmlspecialchars($notif['fecha']); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="card dashboard-card p-4">
                            <h5 class="fw-bold mb-3 h6 text-uppercase tracking-wider text-muted"><i class="bi bi-newspaper me-2" style="color: var(--gov-gold);"></i>Noticias y Eventos</h5>
                            <?php foreach ($noticias as $noticia): ?>
                                <div class="news-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="news-title"><?php echo htmlspecialchars($noticia['titulo']); ?></span>
                                        <span class="news-badge"><?php echo htmlspecialchars($noticia['badge']); ?></span>
                                    </div>
                                    <div class="news-meta"><i class="bi bi-calendar-event me-1"></i> <?php echo htmlspecialchars($noticia['fecha']); ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="col-xl-8 col-lg-7">
                        <div id="jobOffersSection">
                            <div class="card dashboard-card p-4 mb-4">
                                <h5 class="fw-bold mb-3 h6 text-uppercase tracking-wider text-muted">Ofertas Recomendadas</h5>
                                <div class="d-flex flex-column gap-3">
                                    <?php if (empty($ofertas)): ?>
                                        <p class="text-muted text-center">No hay ofertas disponibles en este momento.</p>
                                    <?php else: ?>
                                        <?php foreach ($ofertas as $oferta): ?>
                                            <div class="list-item-custom p-3 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                                                <div>
                                                    <span class="badge bg-gold" style="background: var(--gov-gold); color: white;"><?php echo ($oferta['estado'] ?? 'abierta') == 'abierta' ? 'Disponible' : 'Cerrada'; ?></span>
                                                    <h6 class="fw-bold m-0 text-dark mt-1"><?php echo htmlspecialchars($oferta['titulo_puesto']); ?></h6>
                                                    <p class="m-0 small text-muted">
                                                        <i class="bi bi-building me-1"></i><?php echo htmlspecialchars($oferta['nombre_empresa']); ?> · 
                                                        <i class="bi bi-geo-alt me-1"></i><?php echo ucfirst(str_replace('_', ' ', $oferta['provincia'] ?? 'No especificada')); ?>
                                                        <?php if ($oferta['salario_ofrecido']): ?>
                                                            · <i class="bi bi-coin me-1"></i><?php echo number_format($oferta['salario_ofrecido'], 0, ',', '.'); ?> FCFA
                                                        <?php endif; ?>
                                                    </p>
                                                </div>
                                                <button class="btn btn-sm btn-gov btn-pill-custom">Postularme</button>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="card dashboard-card p-4 mb-4">
                            <h5 class="fw-bold mb-3 h6 text-uppercase tracking-wider text-muted"><i class="bi bi-journal-bookmark me-2" style="color: var(--gov-green);"></i>Planes Estatales de Capacitación</h5>
                            <?php if (empty($cursos)): ?>
                                <p class="text-muted text-center">No hay cursos disponibles en este momento.</p>
                            <?php else: ?>
                                <?php foreach ($cursos as $curso): ?>
                                    <div class="p-3 rounded list-item-custom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
                                        <div>
                                            <span class="badge bg-green" style="background: var(--gov-green); color: white;"><?php echo htmlspecialchars($curso['siglas'] ?? $curso['nombre_entidad']); ?></span>
                                            <h6 class="fw-bold m-0 text-dark mt-1"><?php echo htmlspecialchars($curso['titulo_curso']); ?></h6>
                                            <p class="m-0 small text-muted">
                                                <i class="bi bi-clock me-1"></i><?php echo $curso['duracion_horas']; ?> horas · 
                                                <i class="bi bi-people me-1"></i><?php echo ucfirst($curso['modalidad']); ?> · 
                                                <i class="bi bi-calendar me-1"></i><?php echo $curso['fecha_inicio'] ? date('d/m/Y', strtotime($curso['fecha_inicio'])) : 'Por definir'; ?>
                                            </p>
                                        </div>
                                        <a href="#" class="btn btn-sm btn-green btn-pill-custom text-nowrap">Solicitar Plaza</a>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <div class="card dashboard-card p-4 mb-4">
                            <h5 class="fw-bold mb-3 h6 text-uppercase tracking-wider text-muted">Estado de mis Candidaturas</h5>
                            <div class="d-flex flex-column gap-3 mt-2">
                                <?php if ($postulaciones > 0): ?>
                                    <div class="timeline-item">
                                        <span class="badge bg-light text-secondary border mb-1" style="font-size: 0.65rem; font-weight: 600;"><?php echo date('d/m/Y'); ?></span>
                                        <h6 class="fw-bold text-dark m-0 small">Tienes <?php echo $postulaciones; ?> postulaciones activas</h6>
                                        <p class="text-muted small m-0">Revisa el estado de tus aplicaciones en el panel de seguimiento.</p>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted text-center">No tienes candidaturas activas.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </main>
        <?php include '../componentes/footer_desempleado.php'; ?>
    </div>
    <script>
        const perfilCompleto = <?php echo $perfil_completo ? 'true' : 'false'; ?>;
    </script>
</body>
</html>
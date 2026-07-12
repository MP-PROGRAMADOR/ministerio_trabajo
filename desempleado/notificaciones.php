
<?php
session_start();

// ===== VERIFICAR SESIÓN =====
if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../login_desempleados.php');
    exit();
}

$titulo = 'Notificaciones - Portal de Empleo';
include '../componentes/header_desempleado.php';
include '../conexion/conexion.php';

$id_usuario = $_SESSION['id_usuario'];
$nombre_completo = $_SESSION['nombre_completo'] ?? '';

// ===== DIAGNÓSTICO: VER QUÉ HAY EN LA BBDD =====
echo "<!-- DIAGNÓSTICO DE NOTIFICACIONES -->\n";
echo "<!-- ID Usuario: " . $id_usuario . " -->\n";

try {
    // Verificar buscadores_empleo
    $stmt = $pdo->prepare("SELECT * FROM buscadores_empleo WHERE usuario_id = ?");
    $stmt->execute([$id_usuario]);
    $buscador = $stmt->fetch(PDO::FETCH_ASSOC);

    echo "<!-- Buscador encontrado: " . ($buscador ? 'SI' : 'NO') . " -->\n";
    
    if ($buscador) {
        echo "<!-- Buscador ID: " . $buscador['id'] . " -->\n";
        
        // Contar notificaciones de intermediación
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM notificaciones_intermediacion WHERE buscador_id = ?");
        $stmt->execute([$buscador['id']]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<!-- Notificaciones intermediación: " . ($result['total'] ?? 0) . " -->\n";
        
        // Contar favoritos
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM favoritos WHERE buscador_id = ?");
        $stmt->execute([$buscador['id']]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<!-- Favoritos: " . ($result['total'] ?? 0) . " -->\n";
    }

    // Verificar documentos
    $stmt_doc = $pdo->prepare("SELECT id FROM documentos WHERE usuario_id = ?");
    $stmt_doc->execute([$id_usuario]);
    $documentos = $stmt_doc->fetch();
    
    $perfil_completo = ($buscador && $documentos);
    echo "<!-- Perfil completo: " . ($perfil_completo ? 'SI' : 'NO') . " -->\n";

    // ===== INICIALIZAR NOTIFICACIONES =====
    $notificaciones = [];

    // 1. Notificaciones de intermediación
    if ($buscador) {
        $stmt = $pdo->prepare("
            SELECT 
                ni.*,
                e.nombre_empresa,
                o.titulo_puesto,
                u.nombre as empleador_nombre,
                u.apellidos as empleador_apellidos
            FROM notificaciones_intermediacion ni
            LEFT JOIN empleadores e ON ni.empleador_id = e.id
            LEFT JOIN usuarios u ON e.usuario_id = u.id
            LEFT JOIN ofertas_empleo o ON ni.oferta_id = o.id
            WHERE ni.buscador_id = ?
            ORDER BY ni.fecha_creacion DESC
            LIMIT 20
        ");
        $stmt->execute([$buscador['id']]);
        $notificaciones_intermediacion = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "<!-- Notificaciones intermediación (detalle): " . count($notificaciones_intermediacion) . " -->\n";

        if (is_array($notificaciones_intermediacion) && !empty($notificaciones_intermediacion)) {
            foreach ($notificaciones_intermediacion as $noti) {
                $titulo = '';
                $descripcion = '';
                $icono = 'bi-clock-history';

                if ($noti['origen'] == 'empleador') {
                    $titulo = "Nueva oferta de interés de {$noti['nombre_empresa']}";
                    $descripcion = "La empresa {$noti['nombre_empresa']} ha mostrado interés en tu perfil para el puesto de {$noti['titulo_puesto']}. Estado: " . ucfirst($noti['estado_ministerio'] ?? 'pendiente');
                    $icono = 'bi-briefcase';
                } else {
                    $titulo = "Actualización de postulación";
                    $descripcion = "Tu postulación para {$noti['titulo_puesto']} en {$noti['nombre_empresa']} está " . ucfirst($noti['estado_ministerio'] ?? 'en proceso');
                    $icono = 'bi-clock-history';
                }

                $notificaciones[] = [
                    'id' => $noti['id'],
                    'titulo' => $titulo,
                    'descripcion' => $descripcion,
                    'fecha' => date('d/m/Y H:i', strtotime($noti['fecha_creacion'])),
                    'leida' => false,
                    'icono' => $icono,
                    'tipo' => 'intermediacion',
                    'codigo' => $noti['codigo_seguimiento'] ?? null,
                    'estado' => $noti['estado_ministerio'] ?? 'pendiente'
                ];
            }
        }
    }

    // 2. Favoritos
    if ($buscador) {
        $stmt = $pdo->prepare("
            SELECT 
                f.*,
                o.titulo_puesto,
                e.nombre_empresa
            FROM favoritos f
            JOIN ofertas_empleo o ON f.oferta_id = o.id
            JOIN empleadores e ON o.empleador_id = e.id
            WHERE f.buscador_id = ?
            ORDER BY f.fecha_guardado DESC
            LIMIT 10
        ");
        $stmt->execute([$buscador['id']]);
        $favoritos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "<!-- Favoritos (detalle): " . count($favoritos) . " -->\n";

        if (is_array($favoritos) && !empty($favoritos)) {
            foreach ($favoritos as $fav) {
                $notificaciones[] = [
                    'id' => 'fav_' . $fav['id'],
                    'titulo' => "Oferta guardada en favoritos",
                    'descripcion' => "Has guardado la oferta de {$fav['titulo_puesto']} en {$fav['nombre_empresa']} como favorita.",
                    'fecha' => date('d/m/Y H:i', strtotime($fav['fecha_guardado'])),
                    'leida' => false,
                    'icono' => 'bi-star',
                    'tipo' => 'favorito'
                ];
            }
        }
    }

    // 3. Notificaciones del sistema (si no hay otras notificaciones)
    if (empty($notificaciones)) {
        // Si no hay notificaciones de BBDD, mostrar mensaje
        // No agregamos notificaciones de sistema para que coincida con el contador
    }

    echo "<!-- Total notificaciones: " . count($notificaciones) . " -->\n";
    echo "<!-- FIN DIAGNÓSTICO -->\n";

    $total_no_leidas = count(array_filter($notificaciones, function($n) {
        return !$n['leida'];
    }));

} catch (PDOException $e) {
    error_log("Error en notificaciones: " . $e->getMessage());
    $buscador = null;
    $perfil_completo = false;
    $notificaciones = [];
    $total_no_leidas = 0;
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

    /* ===== NOTIFICACIONES ===== */
    .notificacion-item {
        padding: 1rem 1.25rem;
        border-left: 4px solid transparent;
        border-radius: var(--gov-radius);
        background: #ffffff;
        margin-bottom: 0.75rem;
        transition: all 0.2s;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }
    .notificacion-item:hover {
        background: #f8fafc;
        border-color: var(--gov-gold);
        transform: translateX(4px);
    }
    .notificacion-item.no-leida {
        background: #f0f7ff;
        border-left-color: var(--gov-blue);
    }
    .notificacion-item .noti-icon {
        font-size: 1.3rem;
        color: var(--gov-blue);
        width: 2.5rem;
        text-align: center;
        flex-shrink: 0;
    }
    .notificacion-item .noti-titulo {
        font-weight: 600;
        color: var(--gov-dark);
        font-size: 0.95rem;
    }
    .notificacion-item .noti-desc {
        font-size: 0.85rem;
        color: #6b7a8a;
        margin: 0;
    }
    .notificacion-item .noti-fecha {
        font-size: 0.7rem;
        color: #9aa5b5;
        white-space: nowrap;
        flex-shrink: 0;
    }
    .notificacion-item .noti-badge {
        display: inline-block;
        font-size: 0.6rem;
        padding: 0.2rem 0.6rem;
        border-radius: 30px;
        font-weight: 600;
        text-transform: uppercase;
    }
    .badge-no-leida {
        background: var(--gov-blue);
        color: white;
    }
    .badge-estado-pendiente {
        background: #ffc107;
        color: #212529;
    }
    .badge-estado-en_revision {
        background: #0dcaf0;
        color: #212529;
    }
    .badge-estado-aprobado {
        background: #198754;
        color: white;
    }
    .badge-estado-rechazado {
        background: #dc3545;
        color: white;
    }

    /* ===== BOTONES ===== */
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
    .badge-soft-blue {
        background: rgba(11, 58, 96, 0.08);
        color: var(--gov-blue);
        font-weight: 500;
        padding: 0.3rem 0.9rem;
        border-radius: 20px;
        font-size: 0.75rem;
    }

    /* ===== FOOTER ===== */
    footer {
        background: rgba(255, 255, 255, 0.9) !important;
        backdrop-filter: blur(8px);
        border-top: 1px solid rgba(201, 168, 76, 0.15);
    }

    /* ALERTA */
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

    @media (max-width: 576px) {
        .dashboard-card { padding: 1.5rem !important; }
        .notificacion-item { padding: 0.8rem 1rem; }
        .notificacion-item .noti-fecha {
            font-size: 0.6rem;
            white-space: normal;
        }
    }
</style>

<body>
    <canvas id="canvas-background"></canvas>
    <div class="main-wrapper">

        <main class="container py-5 flex-grow-1">

            <!-- ALERTA EXPEDIENTE INCOMPLETO -->
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

            <!-- ===== CONTENIDO PRINCIPAL ===== -->
            <div class="row g-4">

                <!-- TÍTULO DE LA PÁGINA -->
                <div class="col-12">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <i class="bi bi-bell fs-1" style="color: var(--gov-blue);"></i>
                        <div>
                            <h1 class="h3 fw-bold m-0" style="color: var(--gov-dark);">Notificaciones</h1>
                            <p class="text-muted m-0">Revisa los últimos avisos y actualizaciones de tu cuenta</p>
                        </div>
                    </div>
                    <hr class="mb-4">
                </div>

                <!-- LISTA DE NOTIFICACIONES -->
                <div class="col-12">
                    <div class="card dashboard-card p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold m-0 h6 text-uppercase tracking-wider text-muted">
                                <i class="bi bi-list-ul me-2"></i>Últimas notificaciones
                            </h5>
                            <span class="badge-soft-blue" id="notiCount"><?php echo $total_no_leidas; ?> no leídas</span>
                        </div>

                        <div id="notificacionesList">
                            <?php if (empty($notificaciones) || !is_array($notificaciones)): ?>
                                <div class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                    No tienes notificaciones en este momento.
                                </div>
                            <?php else: ?>
                                <?php foreach ($notificaciones as $noti): ?>
                                    <div class="notificacion-item <?php echo $noti['leida'] ? '' : 'no-leida'; ?> d-flex align-items-start gap-3">
                                        <div class="noti-icon"><i class="bi <?php echo htmlspecialchars($noti['icono']); ?>"></i></div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <div class="noti-titulo"><?php echo htmlspecialchars($noti['titulo']); ?></div>
                                                    <p class="noti-desc"><?php echo htmlspecialchars($noti['descripcion']); ?></p>
                                                </div>
                                                <span class="noti-fecha"><?php echo htmlspecialchars($noti['fecha']); ?></span>
                                            </div>
                                            <div class="mt-1 d-flex gap-2 align-items-center">
                                                <?php if (!$noti['leida']): ?>
                                                    <span class="noti-badge badge-no-leida">Nueva</span>
                                                <?php endif; ?>
                                                <?php if (isset($noti['estado']) && $noti['tipo'] == 'intermediacion'): ?>
                                                    <span class="noti-badge badge-estado-<?php echo htmlspecialchars($noti['estado']); ?>">
                                                        <?php 
                                                            $estados = [
                                                                'pendiente' => '⏳ Pendiente',
                                                                'en_revision' => '🔍 En revisión',
                                                                'aprobado' => '✅ Aprobado',
                                                                'rechazado' => '❌ Rechazado'
                                                            ];
                                                            echo $estados[$noti['estado']] ?? $noti['estado'];
                                                        ?>
                                                    </span>
                                                <?php endif; ?>
                                                <?php if (isset($noti['codigo'])): ?>
                                                    <span class="badge-soft-blue">
                                                        <i class="bi bi-hash me-1"></i><?php echo htmlspecialchars($noti['codigo']); ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <!-- BOTÓN MARCAR TODAS COMO LEÍDAS -->
                        <?php if (!empty($notificaciones) && is_array($notificaciones)): ?>
                            <div class="mt-3 text-end">
                                <button class="btn btn-sm btn-outline-secondary" id="marcarLeidasBtn">
                                    <i class="bi bi-check2-all me-1"></i>Marcar todas como leídas
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </main>

        <?php include '../componentes/footer_desempleado.php'; ?>

        <script>
            // ===== MARCAR TODAS COMO LEÍDAS =====
            document.getElementById('marcarLeidasBtn')?.addEventListener('click', function() {
                // Obtener todas las notificaciones no leídas
                const items = document.querySelectorAll('.notificacion-item.no-leida');
                if (items.length === 0) {
                    alert('No hay notificaciones sin leer.');
                    return;
                }

                // Marcar como leídas visualmente
                items.forEach(item => {
                    item.classList.remove('no-leida');
                    const badge = item.querySelector('.badge-no-leida');
                    if (badge) badge.remove();
                });

                // Actualizar contador
                document.getElementById('notiCount').textContent = '0 no leídas';

                // Opcional: enviar petición al servidor para marcar como leídas en BBDD
                // Aquí iría una llamada AJAX a php/marcar_notificaciones_leidas.php

                alert('✅ Todas las notificaciones han sido marcadas como leídas.');
            });
        </script>

    </div>
</body>
</html>
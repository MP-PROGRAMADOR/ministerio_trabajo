<?php
session_start();

// ===== VERIFICAR SESIÓN =====
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'empleador') {
    header('Location: ../login_empleador.php');
    exit();
}

$titulo = 'Todos los Candidatos - Portal de Empleo';
include_once '../componentes/header_empleador.php';
include_once '../conexion/conexion.php';

$id_usuario = $_SESSION['id_usuario'];
$nombre_empresa = $_SESSION['nombre_empresa'] ?? 'Mi Empresa';

// ===== INICIALIZAR VARIABLES =====
$candidatos = [];
$total_candidatos = 0;
$empleador_id = null;

try {
    // Obtener el ID del empleador
    $stmt = $pdo->prepare("SELECT id, nombre_empresa FROM empleadores WHERE usuario_id = ?");
    $stmt->execute([$id_usuario]);
    $empleador = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$empleador) {
        header('Location: completar_perfil_empleador.php');
        exit();
    }

    $empleador_id = $empleador['id'];
    $nombre_empresa = $empleador['nombre_empresa'];
    $_SESSION['nombre_empresa'] = $nombre_empresa;

    // ===== OBTENER TODOS LOS CANDIDATOS =====
    $stmt = $pdo->prepare("
        SELECT 
            p.*,
            u.id as usuario_id,
            u.nombre,
            u.apellidos,
            u.numero_expediente,
            u.documento_identidad,
            u.correo_electronico,
            b.telefono,
            b.estado_laboral,
            b.provincia,
            b.ciudad_municipio,
            b.foto_carnet,
            o.titulo_puesto,
            o.salario_ofrecido
        FROM postulaciones p
        JOIN ofertas_empleo o ON p.oferta_id = o.id
        JOIN buscadores_empleo b ON p.buscador_id = b.id
        JOIN usuarios u ON b.usuario_id = u.id
        WHERE o.empleador_id = ?
        ORDER BY p.fecha_postulacion DESC
    ");
    $stmt->execute([$empleador_id]);
    $candidatos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total_candidatos = count($candidatos);

} catch (PDOException $e) {
    error_log("Error en candidatos_lista: " . $e->getMessage());
    $candidatos = [];
    $total_candidatos = 0;
}

include_once '../componentes/menu_empleador.php';
?>

<style>
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
        --gov-blue-soft: #E8EEF3;
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

    .btn-primary {
        background: var(--gov-blue);
        border: none;
        border-radius: var(--gov-radius-sm);
        padding: 0.4rem 1rem;
        font-weight: 500;
        font-size: 0.85rem;
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
        padding: 0.3rem 0.8rem;
        font-size: 0.8rem;
        transition: all 0.2s;
    }
    .btn-outline-secondary:hover {
        background: var(--gov-bg);
        border-color: var(--gov-blue);
        color: var(--gov-blue);
    }

    .badge-estado {
        font-weight: 500;
        padding: 0.3rem 0.8rem;
        border-radius: 20px;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .badge-estado.pendiente {
        background: #fff3cd;
        color: #856404;
    }
    .badge-estado.revisado {
        background: #e2e3e5;
        color: #383d41;
    }
    .badge-estado.interesado {
        background: #d4edda;
        color: #155724;
    }
    .badge-estado.rechazado {
        background: #f8d7da;
        color: #721c24;
    }

    .badge-soft-blue {
        background: rgba(11, 58, 96, 0.08);
        color: var(--gov-blue);
        font-weight: 500;
        padding: 0.3rem 0.9rem;
        border-radius: 20px;
        font-size: 0.75rem;
    }

    .table-candidatos {
        border-radius: var(--gov-radius-sm);
        overflow: hidden;
    }
    .table-candidatos thead {
        background: var(--gov-bg);
    }
    .table-candidatos th {
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        color: #6b7a8a;
        border-bottom: 2px solid var(--gov-border);
        padding: 0.75rem 1rem;
    }
    .table-candidatos td {
        padding: 0.75rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
    }
    .table-candidatos tbody tr:hover {
        background: var(--gov-bg);
    }

    .avatar-mini {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: var(--gov-blue-soft);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.8rem;
        color: var(--gov-blue);
        flex-shrink: 0;
    }
    .avatar-mini img {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        object-fit: cover;
    }

    .btn-ver-todos {
        border-radius: var(--gov-radius-sm);
        padding: 0.2rem 0.8rem;
        font-size: 0.75rem;
        font-weight: 500;
        transition: all 0.2s;
        border: 1px solid var(--gov-border);
        color: var(--gov-blue);
        background: transparent;
        text-decoration: none;
    }
    .btn-ver-todos:hover {
        background: var(--gov-blue);
        color: white;
        border-color: var(--gov-blue);
    }

    @media (max-width: 768px) {
        .table-candidatos td, .table-candidatos th {
            padding: 0.5rem 0.75rem;
            font-size: 0.85rem;
        }
    }
</style>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex align-items-center gap-3">
            <i class="bi bi-people-fill fs-1" style="color: var(--gov-blue);"></i>
            <div>
                <h1 class="h3 fw-bold m-0" style="color: var(--gov-dark);">Todos los Candidatos</h1>
                <p class="text-muted m-0">Lista completa de todos los postulantes a tus ofertas</p>
            </div>
        </div>
        <hr class="mb-0 mt-3">
    </div>
</div>

<!-- ===== ESTADÍSTICAS RÁPIDAS ===== -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-4">
        <div class="custom-card text-center">
            <span class="text-muted small">Total Candidatos</span>
            <h3 class="fw-bold my-1"><?php echo $total_candidatos; ?></h3>
        </div>
    </div>
    <div class="col-12 col-sm-4">
        <div class="custom-card text-center">
            <span class="text-muted small">Postulaciones Activas</span>
            <h3 class="fw-bold my-1 text-primary"><?php 
                $activas = count(array_filter($candidatos, function($c) {
                    return in_array($c['estado'], ['pendiente', 'revisado', 'interesado']);
                }));
                echo $activas;
            ?></h3>
        </div>
    </div>
    <div class="col-12 col-sm-4">
        <div class="custom-card text-center">
            <span class="text-muted small">Rechazados</span>
            <h3 class="fw-bold my-1 text-danger"><?php 
                $rechazados = count(array_filter($candidatos, function($c) {
                    return $c['estado'] == 'rechazado';
                }));
                echo $rechazados;
            ?></h3>
        </div>
    </div>
</div>

<!-- ===== TABLA DE CANDIDATOS ===== -->
<div class="row">
    <div class="col-12">
        <div class="custom-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold m-0">
                    <i class="bi bi-list-ul me-2"></i>
                    Lista de Candidatos
                    <span class="badge-soft-blue ms-2"><?php echo $total_candidatos; ?> total</span>
                </h5>
                <a href="candidatos.php" class="btn-ver-todos">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>

            <?php if (empty($candidatos)): ?>
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-people fs-2 d-block mb-2"></i>
                    No has recibido postulaciones aún.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-candidatos">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Candidato</th>
                                <th>Puesto</th>
                                <th>Ubicación</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                                <th class="text-end">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $contador = 1;
                            foreach ($candidatos as $c): 
                            ?>
                                <tr>
                                    <td><?php echo $contador++; ?></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <?php 
                                                $foto = !empty($c['foto_carnet']) ? '../' . $c['foto_carnet'] : '';
                                            ?>
                                            <div class="avatar-mini">
                                                <?php if ($foto): ?>
                                                    <img src="<?php echo htmlspecialchars($foto); ?>" alt="Foto">
                                                <?php else: ?>
                                                    <?php echo strtoupper(substr($c['nombre'], 0, 1) . substr($c['apellidos'], 0, 1)); ?>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <div class="fw-semibold" style="font-size: 0.9rem;">
                                                    <?php echo htmlspecialchars($c['nombre'] . ' ' . $c['apellidos']); ?>
                                                </div>
                                                <small class="text-muted" style="font-size: 0.75rem;">
                                                    <?php echo htmlspecialchars($c['numero_expediente']); ?>
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold" style="font-size: 0.9rem;">
                                            <?php echo htmlspecialchars($c['titulo_puesto']); ?>
                                        </div>
                                        <?php if ($c['salario_ofrecido']): ?>
                                            <small class="text-muted" style="font-size: 0.7rem;">
                                                <?php echo number_format($c['salario_ofrecido'], 0, ',', '.') . ' XAF'; ?>
                                            </small>
                                        <?php endif; ?>
                                    </td>
                                    <td style="font-size: 0.85rem;">
                                        <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $c['ciudad_municipio'] ?? '')) . ', ' . ucfirst(str_replace('_', ' ', $c['provincia'] ?? ''))); ?>
                                    </td>
                                    <td>
                                        <span class="badge-estado <?php echo htmlspecialchars($c['estado']); ?>">
                                            <?php echo ucfirst($c['estado']); ?>
                                        </span>
                                    </td>
                                    <td style="font-size: 0.85rem;">
                                        <?php echo date('d/m/Y', strtotime($c['fecha_postulacion'])); ?>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalDetalleCandidato"
                                            data-codigo="<?php echo htmlspecialchars($c['numero_expediente']); ?>"
                                            data-nombre="<?php echo htmlspecialchars($c['nombre'] . ' ' . $c['apellidos']); ?>"
                                            data-expediente="<?php echo htmlspecialchars($c['numero_expediente']); ?>"
                                            data-telefono="<?php echo htmlspecialchars($c['telefono'] ?? 'No registrado'); ?>"
                                            data-estado="<?php echo htmlspecialchars($c['estado_laboral']); ?>"
                                            data-puesto="<?php echo htmlspecialchars($c['titulo_puesto']); ?>"
                                            data-salario="<?php echo $c['salario_ofrecido'] ? number_format($c['salario_ofrecido'], 0, ',', '.') . ' XAF' : 'No especificado'; ?>"
                                            data-fecha="<?php echo date('d/m/Y', strtotime($c['fecha_postulacion'])); ?>"
                                            data-email="<?php echo htmlspecialchars($c['correo_electronico']); ?>"
                                            data-dip="<?php echo htmlspecialchars($c['documento_identidad']); ?>"
                                            title="Ver detalles">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ===== MODAL DETALLE ===== -->
<div class="modal fade" id="modalDetalleCandidato" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-person-badge me-2" style="color: var(--gov-gold);"></i>
                    Detalle del Candidato
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6 border-end">
                        <h6 class="fw-bold text-muted" style="font-size: 0.8rem; text-transform: uppercase;">Datos del Candidato</h6>
                        <p class="mb-1"><strong>Nombre:</strong> <span id="modal-nombre"></span></p>
                        <p class="mb-1"><strong>Nº Expediente:</strong> <span id="modal-expediente"></span></p>
                        <p class="mb-1"><strong>DIP:</strong> <span id="modal-dip"></span></p>
                        <p class="mb-1"><strong>Teléfono:</strong> <span id="modal-telefono"></span></p>
                        <p class="mb-1"><strong>Correo:</strong> <span id="modal-email"></span></p>
                        <p class="mb-0"><strong>Estado Laboral:</strong> <span id="modal-estado-lab"></span></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold text-muted" style="font-size: 0.8rem; text-transform: uppercase;">Datos de la Postulación</h6>
                        <p class="mb-1"><strong>Puesto:</strong> <span id="modal-puesto"></span></p>
                        <p class="mb-1"><strong>Salario:</strong> <span id="modal-salario"></span></p>
                        <p class="mb-1"><strong>Fecha:</strong> <span id="modal-fecha"></span></p>
                        <p class="mb-0"><strong>Código:</strong> <span id="modal-codigo" class="font-monospace text-primary"></span></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="contactarCandidato()">
                    <i class="bi bi-envelope me-1"></i> Contactar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const buttons = document.querySelectorAll('[data-bs-toggle="modal"][data-bs-target="#modalDetalleCandidato"]');
        buttons.forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('modal-nombre').textContent = this.dataset.nombre || 'N/A';
                document.getElementById('modal-expediente').textContent = this.dataset.expediente || 'N/A';
                document.getElementById('modal-dip').textContent = this.dataset.dip || 'N/A';
                document.getElementById('modal-telefono').textContent = this.dataset.telefono || 'N/A';
                document.getElementById('modal-email').textContent = this.dataset.email || 'N/A';
                document.getElementById('modal-puesto').textContent = this.dataset.puesto || 'N/A';
                document.getElementById('modal-salario').textContent = this.dataset.salario || 'N/A';
                document.getElementById('modal-fecha').textContent = this.dataset.fecha || 'N/A';
                document.getElementById('modal-codigo').textContent = this.dataset.codigo || 'N/A';
                
                const estadoLab = document.getElementById('modal-estado-lab');
                const estado = this.dataset.estado || 'desempleado';
                const badgeMap = {
                    'desempleado': 'badge bg-warning text-dark',
                    'contratado': 'badge bg-success',
                    'suspendido': 'badge bg-danger'
                };
                estadoLab.className = badgeMap[estado] || 'badge bg-secondary';
                estadoLab.textContent = estado.charAt(0).toUpperCase() + estado.slice(1);
            });
        });
    });

    function contactarCandidato() {
        const nombre = document.getElementById('modal-nombre').textContent;
        const email = document.getElementById('modal-email').textContent;
        if (email && email !== 'N/A') {
            window.location.href = 'mailto:' + email + '?subject=Interés en su perfil profesional';
        } else {
            alert('No hay correo electrónico disponible para este candidato.');
        }
    }
</script>

<?php
echo '</div>'; // Cierra page-content
echo '</main>'; // Cierra main-content
echo '</div>'; // Cierra wrapper

include_once '../componentes/footer_empleador.php';
?>
<?php include_once '../componentes/header_empleador.php'; ?>

<style>
    /* ===== ESTILOS UNIFORMES CON LAS OTRAS PÁGINAS ===== */
    :root {
        --gov-blue: #0B3A60;
        --gov-blue-light: #1A4F7A;
        --gov-blue-soft: #E8EEF3;
        --gov-blue-bg: #F0F5FA;
        --gov-green: #1E7E34;
        --gov-green-light: #2E9B4A;
        --gov-gold: #C9A84C;
        --gov-gold-light: #E8D5A3;
        --gov-dark: #0A192F;
        --gov-bg: #F0F5FA;
        --gov-border: #D0DDE8;
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
    .btn-primary.text-dark {
        color: #fff !important;
    }

    .btn-outline-secondary {
        border: 1px solid var(--gov-border);
        color: var(--gov-dark);
        background: transparent;
        border-radius: var(--gov-radius-sm);
        padding: 0.5rem 1.2rem;
        font-weight: 500;
        transition: all 0.2s;
    }
    .btn-outline-secondary:hover {
        background: var(--gov-blue-bg);
        border-color: var(--gov-blue);
        color: var(--gov-blue);
    }

    .btn-light {
        background: var(--gov-blue-bg);
        border: 1px solid var(--gov-border);
        color: var(--gov-blue);
        border-radius: var(--gov-radius-sm);
        padding: 0.25rem 0.6rem;
        transition: all 0.2s;
    }
    .btn-light:hover {
        background: var(--gov-blue);
        color: white;
        border-color: var(--gov-blue);
    }
    .btn-light.text-danger {
        color: #dc3545;
    }
    .btn-light.text-danger:hover {
        background: #dc3545;
        color: white;
    }

    .badge.bg-success-subtle {
        background: #d4edda !important;
        color: #155724 !important;
    }
    .badge.bg-secondary-subtle {
        background: #e2e3e5 !important;
        color: #383d41 !important;
    }
    .badge.bg-light.text-dark.border {
        background: var(--gov-blue-bg) !important;
        border-color: var(--gov-border) !important;
        color: var(--gov-blue) !important;
    }

    .table th {
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        color: #6b7a8a;
        border-bottom: 2px solid var(--gov-border);
        background: var(--gov-blue-soft);
    }
    .table td {
        vertical-align: middle;
    }
    .table tbody tr:hover {
        background: rgba(11, 58, 96, 0.03);
    }

    .modal-content {
        border: none;
        border-radius: var(--gov-radius);
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    }
    .modal-header {
        border-bottom: 2px solid var(--gov-border);
        background: var(--gov-blue-bg);
    }
    .modal-header .modal-title {
        font-weight: 700;
        color: var(--gov-blue);
    }
    .modal-footer {
        border-top: 1px solid var(--gov-border);
        background: var(--gov-blue-bg);
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
    .input-group-text {
        background: var(--gov-blue-bg);
        border: 1.5px solid var(--gov-border);
        color: #6b7a8a;
    }

    .rounded-pill {
        border-radius: 50px !important;
    }

    .alert {
        border-radius: var(--gov-radius);
        border: none;
    }
    .alert-success {
        background: #d4edda;
        color: #155724;
    }
    .alert-danger {
        background: #f8d7da;
        color: #721c24;
    }

    @media (max-width: 768px) {
        .custom-card {
            padding: 1rem;
        }
        .table td, .table th {
            padding: 0.5rem 0.75rem;
            font-size: 0.85rem;
        }
    }
    @media (max-width: 576px) {
        .custom-card {
            padding: 0.8rem;
        }
        .table td, .table th {
            padding: 0.3rem 0.5rem;
            font-size: 0.75rem;
        }
        .btn-sm {
            padding: 0.15rem 0.4rem;
            font-size: 0.7rem;
        }
        .modal-body {
            padding: 1rem !important;
        }
        .modal-header {
            padding: 1rem !important;
        }
    }
</style>

<body>

    <?php include_once '../componentes/menu_empleador.php'; ?>


    <div class="row g-4">



        <?php

// Asegurar que exista el ID del empleador
$empleador_id = $_SESSION['empleador_id'] ?? 0;

try {
    $stmt = $pdo->prepare("
        SELECT id, titulo_puesto, provincia, salario_ofrecido, estado, descripcion, requisitos, fecha_publicacion 
        FROM ofertas_empleo 
        WHERE empleador_id = :empleador_id 
        ORDER BY fecha_publicacion DESC
    ");
    $stmt->execute([':empleador_id' => $empleador_id]);
    $ofertas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $ofertas = [];
    $error_db = "Error al cargar las ofertas: " . $e->getMessage();
}

        ?>



        <div class="col-12 col-xl-12">



            <?php if (isset($_SESSION['alerta'])): ?>
                <div id="alertaSesion" class="alert alert-<?= $_SESSION['alerta']['tipo']; ?> alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bi <?= $_SESSION['alerta']['tipo'] === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill'; ?> me-2 fs-5"></i>
                        <div>
                            <?= $_SESSION['alerta']['mensaje']; ?>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="cerrarAlertaManualmente()"></button>
                </div>
                <?php unset($_SESSION['alerta']); // Se destruye para que no reaparezca al recargar 
                ?>
            <?php endif; ?>



            <div class="custom-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">


                    <div>
                        <h5 class="fw-bold m-0">Ofertas de Empleo Recientes</h5>
                        <small class="text-muted">Gestiona tus vacantes de trabajo</small>
                    </div>
                    <button class="btn btn-primary btn-sm rounded-pill px-3" data-bs-toggle="modal"
                        data-bs-target="#modalNuevaOferta">
                        <i class="bi bi-plus-lg me-1"></i> Publicar Oferta
                    </button>
                </div>


                <div class="table-responsive">
    <table id="tablaOfertas" class="table table-hover align-middle w-100">
        <thead class="table-light">
            <tr>
                <th>Puesto / Vacante</th>
                <th>Ubicación</th>
                <th>Salario</th>
                <th>Estado</th>
                <th class="text-end">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($ofertas)): ?>
                <?php foreach ($ofertas as $oferta): ?>
                    <tr>
                        <td>
                            <div class="fw-semibold"><?= htmlspecialchars($oferta['titulo_puesto']) ?></div>
                            <small class="text-muted">
                                Pub: <?= date('d/m/Y', strtotime($oferta['fecha_publicacion'])) ?>
                            </small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">
                                <?= htmlspecialchars($oferta['provincia']) ?>
                            </span>
                        </td>
                        <td>
                            <?= $oferta['salario_ofrecido'] !== null 
                                ? number_format($oferta['salario_ofrecido'], 0, ',', '.') . ' XAF' 
                                : '<span class="text-muted italic">No especificado</span>' 
                            ?>
                        </td>
                        <td>
                            <?php if ($oferta['estado'] === 'abierta'): ?>
                                <span class="badge bg-success-subtle text-success border border-success-subtle">
                                    Abierta
                                </span>
                            <?php else: ?>
                                <span class="badge bg-secondary-subtle text-secondary border">
                                    Cerrada
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <button type="button" 
                                    class="btn btn-light btn-sm me-1 btn-editar-oferta" 
                                    data-id="<?= $oferta['id'] ?>"
                                    data-titulo="<?= htmlspecialchars($oferta['titulo_puesto'], ENT_QUOTES, 'UTF-8') ?>"
                                    data-provincia="<?= htmlspecialchars($oferta['provincia'], ENT_QUOTES, 'UTF-8') ?>"
                                    data-salario="<?= $oferta['salario_ofrecido'] ?>"
                                    data-estado="<?= htmlspecialchars($oferta['estado'], ENT_QUOTES, 'UTF-8') ?>"
                                    data-descripcion="<?= htmlspecialchars($oferta['descripcion'], ENT_QUOTES, 'UTF-8') ?>"
                                    data-requisitos="<?= htmlspecialchars($oferta['requisitos'], ENT_QUOTES, 'UTF-8') ?>"
                                    title="Editar oferta">
                                <i class="bi bi-pencil pe-none"></i>
                            </button>

                            <button class="btn btn-light btn-sm text-danger" 
                                    onclick="eliminarOferta(<?= $oferta['id'] ?>)" 
                                    title="Eliminar oferta">
                                <i class="bi bi-trash pe-none"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>


               
            </div>
        </div>



    </div>

    </main>

    <div class="modal fade" id="modalNuevaOferta" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

                <div class="modal-header bg-light border-0 px-4 pt-4 pb-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-briefcase-fill fs-4"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold m-0">Publicar Vacante de Empleo</h5>
                            <small class="text-muted">Completa la información para dar a conocer la oferta</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close align-self-start" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="../php/procesar_oferta.php" method="POST">
                    <div class="modal-body p-4">
                        <div class="row g-3">

                            <div class="col-md-8">
                                <label class="form-label fw-semibold text-secondary small">
                                    <i class="bi bi-person-badge me-1"></i> Título del Puesto / Vacante <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="titulo_puesto" class="form-control"
                                    placeholder="Ej. Técnico en Redes, Contable Senior" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-secondary small">
                                    <i class="bi bi-geo-alt me-1"></i> Provincia <span class="text-danger">*</span>
                                </label>
                                <select name="provincia" class="form-select" required>
                                    <option value="" selected disabled>Selecciona...</option>
                                    <option value="Bioko Norte">Bioko Norte</option>
                                    <option value="Litoral">Litoral</option>
                                    <option value="Bioko Sur">Bioko Sur</option>
                                    <option value="Centro Sur">Centro Sur</option>
                                    <option value="Kie-Ntem">Kie-Ntem</option>
                                    <option value="Wele-Nzas">Wele-Nzas</option>
                                    <option value="Annobón">Annobón</option>
                                    <option value="Djibloho">Djibloho</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-secondary small">
                                    <i class="bi bi-cash-stack me-1"></i> Salario Ofrecido <span class="text-muted fw-normal">(Opcional)</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="bi bi-currency-exchange"></i></span>
                                    <input type="number" name="salario_ofrecido" class="form-control" placeholder="Ej. 350000" min="0" step="5000">
                                    <span class="input-group-text bg-light text-muted fw-semibold">XAF</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-secondary small">
                                    <i class="bi bi-toggle-on me-1"></i> Estado Inicial de la Oferta
                                </label>
                                <select name="estado" class="form-select">
                                    <option value="abierta" selected>🟢 Abierta (Visible para candidatos)</option>
                                    <option value="cerrada">⚪ Cerrada (Borrador / Pausada)</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold text-secondary small">
                                    <i class="bi bi-file-text me-1"></i> Descripción del Puesto <span class="text-danger">*</span>
                                </label>
                                <textarea name="descripcion" class="form-control" rows="3"
                                    placeholder="Resume las funciones principales y responsabilidades diarias del rol..." required></textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold text-secondary small">
                                    <i class="bi bi-check2-square me-1"></i> Requisitos Solicitados <span class="text-danger">*</span>
                                </label>
                                <textarea name="requisitos" class="form-control" rows="3"
                                    placeholder="Formación académica, años de experiencia, idiomas, certificaciones..." required></textarea>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer bg-light border-0 px-4 py-3">
                        <button type="button" class="btn btn-outline-secondary px-4 rounded-pill" data-bs-dismiss="modal">
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary px-4 rounded-pill fw-semibold shadow-sm">
                            <i class="bi bi-send me-1"></i> Publicar Vacante
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>


<div class="modal fade" id="modalEditarOferta" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            
            <div class="modal-header bg-light border-0 px-4 pt-4 pb-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-pencil-square fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold m-0">Editar Vacante de Empleo</h5>
                        <small class="text-muted">Modifica los detalles de la oferta seleccionada</small>
                    </div>
                </div>
                <button type="button" class="btn-close align-self-start" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="../php/actualizar_oferta.php" method="POST">
                <input type="hidden" name="id" id="edit_id">

                <div class="modal-body p-4">
                    <div class="row g-3">
                        
                        <div class="col-md-8">
                            <label class="form-label fw-semibold text-secondary small">
                                <i class="bi bi-person-badge me-1"></i> Título del Puesto / Vacante <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="titulo_puesto" id="edit_titulo_puesto" class="form-control" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-secondary small">
                                <i class="bi bi-geo-alt me-1"></i> Provincia <span class="text-danger">*</span>
                            </label>
                            <select name="provincia" id="edit_provincia" class="form-select" required>
                                <option value="Bioko Norte">Bioko Norte</option>
                                <option value="Litoral">Litoral</option>
                                <option value="Bioko Sur">Bioko Sur</option>
                                <option value="Centro Sur">Centro Sur</option>
                                <option value="Kie-Ntem">Kie-Ntem</option>
                                <option value="Wele-Nzas">Wele-Nzas</option>
                                <option value="Annobón">Annobón</option>
                                <option value="Djibloho">Djibloho</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary small">
                                <i class="bi bi-cash-stack me-1"></i> Salario Ofrecido <span class="text-muted fw-normal">(Opcional)</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="bi bi-currency-exchange"></i></span>
                                <input type="number" name="salario_ofrecido" id="edit_salario_ofrecido" class="form-control" min="0" step="5000">
                                <span class="input-group-text bg-light text-muted fw-semibold">XAF</span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary small">
                                <i class="bi bi-toggle-on me-1"></i> Estado de la Oferta
                            </label>
                            <select name="estado" id="edit_estado" class="form-select">
                                <option value="abierta">🟢 Abierta (Visible para candidatos)</option>
                                <option value="cerrada">⚪ Cerrada (Pausada / Finalizada)</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold text-secondary small">
                                <i class="bi bi-file-text me-1"></i> Descripción del Puesto <span class="text-danger">*</span>
                            </label>
                            <textarea name="descripcion" id="edit_descripcion" class="form-control" rows="3" required></textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold text-secondary small">
                                <i class="bi bi-check2-square me-1"></i> Requisitos Solicitados <span class="text-danger">*</span>
                            </label>
                            <textarea name="requisitos" id="edit_requisitos" class="form-control" rows="3" required></textarea>
                        </div>

                    </div>
                </div>

                <div class="modal-footer bg-light border-0 px-4 py-3">
                    <button type="button" class="btn btn-outline-secondary px-4 rounded-pill" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary px-4 rounded-pill fw-semibold shadow-sm text-dark">
                        <i class="bi bi-save me-1"></i> Guardar Cambios
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
   
    <script src="../src/js/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>


  <script>


    // Delegación de eventos para capturar el botón de edición incluso tras cambiar de página en DataTables
    $('#tablaOfertas').on('click', '.btn-editar-oferta', function(e) {
        e.preventDefault();
        
        // Obtener el botón exacto (incluso si se hace clic en el icono i)
        const btn = $(e.target).closest('.btn-editar-oferta');

        // Extraer atributos
        const id          = btn.attr('data-id');
        const titulo      = btn.attr('data-titulo');
        const provincia   = btn.attr('data-provincia');
        const salario     = btn.attr('data-salario');
        const estado      = btn.attr('data-estado');
        const descripcion = btn.attr('data-descripcion');
        const requisitos  = btn.attr('data-requisitos');

        // Asignar a los campos del modal
        $('#edit_id').val(id);
        $('#edit_titulo_puesto').val(titulo);
        $('#edit_provincia').val(provincia);
        $('#edit_salario_ofrecido').val(salario && salario !== 'null' ? salario : '');
        $('#edit_estado').val(estado);
        $('#edit_descripcion').val(descripcion);
        $('#edit_requisitos').val(requisitos);

        // Abrir modal con Bootstrap 5
        const elModal = document.getElementById('modalEditarOferta');
        if (elModal) {
            const modalEditar = bootstrap.Modal.getOrCreateInstance(elModal);
            modalEditar.show();
        }
    });

    </script>


    <?php include_once '../componentes/footer_empleador.php'; ?>
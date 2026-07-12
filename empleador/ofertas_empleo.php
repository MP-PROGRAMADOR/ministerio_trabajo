<?php include_once '../componentes/header_empleador.php'; ?>

<body>

    <?php include_once '../componentes/menu_empleador.php'; ?>


    <div class="row g-4">



        <?php



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
                    <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-pencil-square fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold m-0">Editar Vacante de Empleo</h5>
                        <small class="text-muted">Modifica los detalles de la oferta seleccionada</small>
                    </div>
                </div>
                <button type="button" class="btn-close align-self-start" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="actualizar_oferta.php" method="POST">
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
                    <button type="submit" class="btn btn-warning px-4 rounded-pill fw-semibold shadow-sm text-dark">
                        <i class="bi bi-save me-1"></i> Guardar Cambios
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

    <script>
        $(document).ready(function() {
            // Delegación de eventos en la tabla (funciona con DataTables y paginación)
            $('#tablaOfertas').on('click', '.btn-editar-oferta', function(e) {
                e.preventDefault();

                const btn = $(this);

                // Extraer los valores desde los data-attributes
                const id = btn.attr('data-id');
                const titulo = btn.attr('data-titulo');
                const provincia = btn.attr('data-provincia');
                const salario = btn.attr('data-salario');
                const estado = btn.attr('data-estado');
                const descripcion = btn.attr('data-descripcion');
                const requisitos = btn.attr('data-requisitos');

                // Asignar los valores a los campos del modal de edición
                $('#edit_id').val(id);
                $('#edit_titulo_puesto').val(titulo);
                $('#edit_provincia').val(provincia);
                $('#edit_salario_ofrecido').val(salario ? salario : '');
                $('#edit_estado').val(estado);
                $('#edit_descripcion').val(descripcion);
                $('#edit_requisitos').val(requisitos);

                // Abrir el modal de forma segura con Bootstrap 5
                const modalElement = document.getElementById('modalEditarOferta');
                if (modalElement) {
                    const modalEditar = bootstrap.Modal.getOrCreateInstance(modalElement);
                    modalEditar.show();
                } else {
                    console.error("El elemento #modalEditarOferta no existe en el DOM.");
                }
            });
        });
    </script>


    <?php include_once '../componentes/footer_empleador.php'; ?>
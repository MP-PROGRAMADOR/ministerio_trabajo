<?php include_once '../componentes/header_empleador.php';  ?>
<body>

   <?php include_once '../componentes/menu_empleador.php';  ?>

        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card stat-card p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small fw-semibold">Ofertas Publicadas</span>
                            <h3 class="fw-bold my-1">12</h3>
                            <small class="text-success fw-semibold"><i class="bi bi-arrow-up-short"></i> Total acumulado</small>
                        </div>
                        <div class="icon-box bg-primary-subtle text-primary">
                            <i class="bi bi-file-earmark-post"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card stat-card p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small fw-semibold">Ofertas Activas</span>
                            <h3 class="fw-bold my-1 text-primary">5</h3>
                            <small class="text-muted fw-semibold">Vacantes abiertas</small>
                        </div>
                        <div class="icon-box bg-success-subtle text-success">
                            <i class="bi bi-briefcase-check"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card stat-card p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small fw-semibold">Solicitudes de Contacto</span>
                            <h3 class="fw-bold my-1 text-warning">8</h3>
                            <small class="text-warning fw-semibold"><i class="bi bi-clock-history"></i> Pendientes Ministerio</small>
                        </div>
                        <div class="icon-box bg-warning-subtle text-warning">
                            <i class="bi bi-person-lines-fill"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card stat-card p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small fw-semibold">Contrataciones</span>
                            <h3 class="fw-bold my-1 text-info">14</h3>
                            <small class="text-info fw-semibold">Gestionadas con éxito</small>
                        </div>
                        <div class="icon-box bg-info-subtle text-info">
                            <i class="bi bi-award"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">

            <div class="col-12 col-xl-8">
                <div class="custom-card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="fw-bold m-0">Ofertas de Empleo Recientes</h5>
                            <small class="text-muted">Gestiona tus vacantes de trabajo</small>
                        </div>
                        <button class="btn btn-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalNuevaOferta">
                            <i class="bi bi-plus-lg me-1"></i> Publicar Oferta
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Puesto / Vacante</th>
                                    <th>Ubicación</th>
                                    <th>Salario</th>
                                    <th>Estado</th>
                                    <th class="text-end">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="fw-semibold">Técnico de Redes Telecom</div>
                                        <small class="text-muted">Pub: 10/06/2026</small>
                                    </td>
                                    <td><span class="badge bg-light text-dark border">Bioko Norte</span></td>
                                    <td>450.000 XAF</td>
                                    <td><span class="badge bg-success-subtle text-success border border-success-subtle">Abierta</span></td>
                                    <td class="text-end">
                                        <button class="btn btn-light btn-sm"><i class="bi bi-pencil"></i></button>
                                        <button class="btn btn-light btn-sm text-danger"><i class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="fw-semibold">Administrador de Base de Datos</div>
                                        <small class="text-muted">Pub: 02/06/2026</small>
                                    </td>
                                    <td><span class="badge bg-light text-dark border">Litoral</span></td>
                                    <td>600.000 XAF</td>
                                    <td><span class="badge bg-success-subtle text-success border border-success-subtle">Abierta</span></td>
                                    <td class="text-end">
                                        <button class="btn btn-light btn-sm"><i class="bi bi-pencil"></i></button>
                                        <button class="btn btn-light btn-sm text-danger"><i class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="fw-semibold">Asistente de Atención al Cliente</div>
                                        <small class="text-muted">Pub: 15/05/2026</small>
                                    </td>
                                    <td><span class="badge bg-light text-dark border">Bioko Norte</span></td>
                                    <td>250.000 XAF</td>
                                    <td><span class="badge bg-secondary-subtle text-secondary border">Cerrada</span></td>
                                    <td class="text-end">
                                        <button class="btn btn-light btn-sm"><i class="bi bi-pencil"></i></button>
                                        <button class="btn btn-light btn-sm text-danger"><i class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-4">
                <div class="custom-card p-4">
                    <h5 class="fw-bold mb-1">Estado de Intermediación</h5>
                    <small class="text-muted d-block mb-3">Alertas y revisiones con el Ministerio</small>

                    <div class="d-flex flex-column gap-3">
                        
                        <div class="p-3 border rounded-3 bg-light-subtle">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-primary">MITRAD-2026-X9</span>
                                <span class="badge bg-warning text-dark">Pendiente</span>
                            </div>
                            <h6 class="mb-1 fw-semibold">Técnico de Redes Telecom</h6>
                            <p class="text-muted small mb-2">Solicitud de contacto directo enviada al Ministerio.</p>
                            <small class="text-muted" style="font-size: 0.75rem;"><i class="bi bi-clock me-1"></i>Hace 2 horas</small>
                        </div>

                        <div class="p-3 border rounded-3 bg-light-subtle">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-primary">MITRAD-2026-A2</span>
                                <span class="badge bg-success">Aprobado</span>
                            </div>
                            <h6 class="mb-1 fw-semibold">Administrador de Base de Datos</h6>
                            <p class="text-muted small mb-2">El Ministerio ha validado los perfiles candidatos.</p>
                            <small class="text-muted" style="font-size: 0.75rem;"><i class="bi bi-clock me-1"></i>Ayer</small>
                        </div>

                    </div>
                </div>
            </div>

        </div>

    </main>

    <div class="modal fade" id="modalNuevaOferta" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Publicar Vacante de Empleo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="guardar_oferta.php" method="POST">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label">Título del Puesto / Vacante</label>
                                <input type="text" name="titulo_puesto" class="form-control" placeholder="Ej. Ingeniero Civil, Contable" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Provincia</label>
                                <select name="provincia" class="form-select" required>
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
                                <label class="form-label">Salario Ofrecido (XAF) - Opcional</label>
                                <input type="number" name="salario_ofrecido" class="form-control" placeholder="Ej. 350000">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Estado Inicial</label>
                                <select name="estado" class="form-select">
                                    <option value="abierta" selected>Abierta</option>
                                    <option value="cerrada">Cerrada</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Descripción del Puesto</label>
                                <textarea name="descripcion" class="form-control" rows="3" placeholder="Describe las funciones principales del puesto..." required></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Requisitos Solicitados</label>
                                <textarea name="requisitos" class="form-control" rows="3" placeholder="Requisitos académicos, experiencia, idiomas..." required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary px-4">Publicar Vacante</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

   <?php include_once '../componentes/footer_empleador.php';  ?>
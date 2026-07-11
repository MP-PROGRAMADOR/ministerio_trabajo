<?php include_once '../componentes/header_admin.php'; ?>
<div class="d-flex" id="wrapper">

  
<?php include_once '../componentes/menu_admin.php'; ?>

        <div class="container-fluid p-4">

            <div class="row g-3 mb-4">
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card stat-card h-100 p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-7 fw-medium">Desempleados Registrados</span>
                                <h3 class="fw-bold my-1">1,248</h3>
                                <small class="text-success fw-semibold"><i class="bi bi-arrow-up-short"></i> +12% este mes</small>
                            </div>
                            <div class="stat-icon bg-primary-subtle text-primary">
                                <i class="bi bi-people"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card stat-card h-100 p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-7 fw-medium">Empresas Activas</span>
                                <h3 class="fw-bold my-1">154</h3>
                                <small class="text-muted fw-semibold">Región Bioko / Continental</small>
                            </div>
                            <div class="stat-icon bg-success-subtle text-success">
                                <i class="bi bi-building"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card stat-card h-100 p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-7 fw-medium">Intermediaciones Pendientes</span>
                                <h3 class="fw-bold my-1">28</h3>
                                <small class="text-warning fw-semibold"><i class="bi bi-exclamation-circle"></i> Requiere revisión</small>
                            </div>
                            <div class="stat-icon bg-warning-subtle text-warning">
                                <i class="bi bi-shield-exclamation"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card stat-card h-100 p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-7 fw-medium">Cursos en Oferta</span>
                                <h3 class="fw-bold my-1">12</h3>
                                <small class="text-info fw-semibold">INEM y Entidades Técnicas</small>
                            </div>
                            <div class="stat-icon bg-info-subtle text-info">
                                <i class="bi bi-journal-text"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="mb-0 fw-bold">Notificaciones y Regulaciones de Intermediación</h6>
                        <small class="text-muted">Control gubernamental de vinculación entre empresas y trabajadores</small>
                    </div>
                    <button class="btn btn-outline-primary btn-sm rounded-pill"><i class="bi bi-download me-1"></i> Exportar Informe</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Cód. Seguimiento</th>
                                <th>Origen</th>
                                <th>Desempleado</th>
                                <th>Empresa / Oferta</th>
                                <th>Estado Ministerio</th>
                                <th>Fecha</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="font-monospace fw-bold text-primary">MITRAD-2026-X9</span></td>
                                <td><span class="badge bg-light text-dark border">Empleador</span></td>
                                <td>
                                    <div class="fw-semibold">Salvador Mete</div>
                                    <small class="text-muted">EXP: EG-90812</small>
                                </td>
                                <td>
                                    <div>GETESA</div>
                                    <small class="text-muted">Ingeniero de Telecomunicaciones</small>
                                </td>
                                <td><span class="badge badge-pendiente">Pendiente</span></td>
                                <td>11 Jul, 2026</td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-success rounded-circle" title="Aprobar" data-bs-toggle="tooltip"><i class="bi bi-check-lg"></i></button>
                                    <button class="btn btn-sm btn-outline-danger rounded-circle" title="Rechazar" data-bs-toggle="tooltip"><i class="bi bi-x-lg"></i></button>
                                    <button class="btn btn-sm btn-light rounded-circle" title="Ver Detalles" data-bs-toggle="modal" data-bs-target="#modalDetalleNotificacion"><i class="bi bi-eye"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="font-monospace fw-bold text-primary">MITRAD-2026-A2</span></td>
                                <td><span class="badge bg-light text-dark border">Buscador</span></td>
                                <td>
                                    <div class="fw-semibold">María Ondó</div>
                                    <small class="text-muted">EXP: EG-43110</small>
                                </td>
                                <td>
                                    <div>TotalEnergies GE</div>
                                    <small class="text-muted">Técnico Logístico</small>
                                </td>
                                <td><span class="badge badge-aprobado">Aprobado</span></td>
                                <td>10 Jul, 2026</td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-light rounded-circle" title="Ver Detalles"><i class="bi bi-eye"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-12 col-xl-8">
                    <div class="card h-100">
                        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                            <h6 class="mb-0 fw-bold">Expedientes de Buscadores de Empleo</h6>
                            <a href="#" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i> Registrar Ciudadano</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Expediente</th>
                                        <th>Nombre Completo</th>
                                        <th>Ubicación</th>
                                        <th>Estado Laboral</th>
                                        <th class="text-end">Expediente Digital</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><span class="font-monospace">EG-90812</span></td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="bg-secondary-subtle rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                    <i class="bi bi-person text-secondary"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">Salvador Mete</div>
                                                    <small class="text-muted">DIP: 009281928</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>Bioko Norte, Malabo</td>
                                        <td><span class="badge badge-desempleado">Desempleado</span></td>
                                        <td class="text-end">
                                            <button class="btn btn-sm btn-outline-secondary" title="Descargar DIP"><i class="bi bi-card-heading"></i></button>
                                            <button class="btn btn-sm btn-outline-secondary" title="Ver CV"><i class="bi bi-file-earmark-person"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><span class="font-monospace">EG-11029</span></td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="bg-secondary-subtle rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                    <i class="bi bi-person text-secondary"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">Manuel Nsue</div>
                                                    <small class="text-muted">DIP: 001293812</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>Litoral, Bata</td>
                                        <td><span class="badge badge-contratado">Contratado</span></td>
                                        <td class="text-end">
                                            <button class="btn btn-sm btn-outline-secondary" title="Descargar DIP"><i class="bi bi-card-heading"></i></button>
                                            <button class="btn btn-sm btn-outline-secondary" title="Ver CV"><i class="bi bi-file-earmark-person"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-xl-4">
                    <div class="card h-100">
                        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                            <h6 class="mb-0 fw-bold">Capacitación Estatal (Cursos)</h6>
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalNuevoCurso"><i class="bi bi-plus-circle"></i> Nuevo</button>
                        </div>
                        <div class="card-body">
                            <div class="list-group list-group-flush">
                                <div class="list-group-item px-0 py-3">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1 fw-bold">Redes y Seguridad Informática</h6>
                                        <span class="badge bg-success-subtle text-success">Activo</span>
                                    </div>
                                    <p class="mb-1 text-muted fs-7">Impartido por: INEM / Centros Técnicos</p>
                                    <small class="text-secondary"><i class="bi bi-clock me-1"></i> Duración: 120 Horas</small>
                                </div>
                                <div class="list-group-item px-0 py-3">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1 fw-bold">Gestión Portuaria y Logística</h6>
                                        <span class="badge bg-warning-subtle text-warning">Próximamente</span>
                                    </div>
                                    <p class="mb-1 text-muted fs-7">Impartido por: Autoridad Portuaria</p>
                                    <small class="text-secondary"><i class="bi bi-clock me-1"></i> Duración: 80 Horas</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="modalDetalleNotificacion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold">Expediente de Intermediación: <span class="text-primary">MITRAD-2026-X9</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6 border-end">
                        <h6 class="fw-bold text-muted uppercase fs-8">Datos del Candidato</h6>
                        <p class="mb-1"><strong>Nombre:</strong> Salvador Mete</p>
                        <p class="mb-1"><strong>Nº Expediente:</strong> EG-90812</p>
                        <p class="mb-1"><strong>Estado Actual:</strong> <span class="badge badge-desempleado">Desempleado</span></p>
                        <p class="mb-0"><strong>Provincia:</strong> Bioko Norte</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold text-muted uppercase fs-8">Datos de la Empresa / Oferta</h6>
                        <p class="mb-1"><strong>Empresa:</strong> GETESA</p>
                        <p class="mb-1"><strong>Puesto Solicitado:</strong> Ingeniero de Telecomunicaciones</p>
                        <p class="mb-1"><strong>RUC:</strong> 10092811-GE</p>
                        <p class="mb-0"><strong>Salario Ofrecido:</strong> 850,000 XAF</p>
                    </div>
                    <div class="col-12">
                        <hr>
                        <label class="form-label fw-bold">Mensaje / Motivo de Intermediación</label>
                        <div class="p-3 bg-light rounded text-muted">
                            "Solicitud formal remitida para la revisión previa del perfil profesional y aprobación del contrato laboral según la normativa reguladora del Ministerio de Trabajo."
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-danger"><i class="bi bi-x-circle me-1"></i> Rechazar Solicitud</button>
                <button type="button" class="btn btn-success"><i class="bi bi-check-circle me-1"></i> Aprobar Intermediación</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalNuevoCurso" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Publicar Nuevo Curso de Capacitación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label class="form-label">Título del Curso</label>
                        <input type="text" class="form-control" placeholder="Ej. Administración de Sistemas Linux">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Entidad que Imparte</label>
                        <input type="text" class="form-control" placeholder="Ej. INEM / Universidad Nacional">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label">Duración (Horas)</label>
                            <input type="number" class="form-control" placeholder="60">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Estado Inicial</label>
                            <select class="form-select">
                                <option value="activo">Activo</option>
                                <option value="proximamente">Próximamente</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" rows="3" placeholder="Detalles de la capacitación..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary">Guardar y Publicar</button>
            </div>
        </div>
    </div>
</div>

<?php include_once '../componentes/footer_admin.php'; ?>
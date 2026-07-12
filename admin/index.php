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
                            <h3 class="fw-bold my-1"><?= number_format($total_desempleados) ?></h3>
                            <small class="text-success fw-semibold"><i class="bi bi-person-check"></i> Activos en
                                sistema</small>
                        </div>
                        <div class="stat-icon bg-primary-subtle text-primary p-3 rounded-circle">
                            <i class="bi bi-people fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card stat-card h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted fs-7 fw-medium">Empresas Activas</span>
                            <h3 class="fw-bold my-1"><?= number_format($total_empresas) ?></h3>
                            <small class="text-muted fw-semibold">Región Bioko / Continental</small>
                        </div>
                        <div class="stat-icon bg-success-subtle text-success p-3 rounded-circle">
                            <i class="bi bi-building fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>




            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card stat-card h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted fs-7 fw-medium">Intermediaciones Pendientes</span>
                            <h3 class="fw-bold my-1"><?= number_format($intermediaciones_pendientes) ?></h3>
                            <small class="text-warning fw-semibold"><i class="bi bi-exclamation-circle"></i> Requiere
                                revisión</small>
                        </div>
                        <div class="stat-icon bg-warning-subtle text-warning p-3 rounded-circle">
                            <i class="bi bi-shield-exclamation fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>




            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card stat-card h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted fs-7 fw-medium">Cursos en Oferta</span>
                            <h3 class="fw-bold my-1"><?= number_format($total_cursos_activos) ?></h3>
                            <small class="text-info fw-semibold">Entidades Formadoras</small>
                        </div>
                        <div class="stat-icon bg-info-subtle text-info p-3 rounded-circle">
                            <i class="bi bi-journal-text fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>






        <div class="row g-4 mb-4">
            <div class="col-12 col-lg-5">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold">Reporte Circular: Estado Laboral</h6>
                        <small class="text-muted">Proporción de ciudadanos desempleados, contratados y
                            suspendidos</small>
                    </div>
                    <div class="card-body d-flex justify-content-center align-items-center"
                        style="position: relative; min-height: 250px;">
                        <canvas id="chartEstadoLaboral"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-7">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold">Reporte de Barras: Intermediaciones Gubernamentales</h6>
                        <small class="text-muted">Estado de tramitación de expedientes en el Ministerio</small>
                    </div>
                    <div class="card-body" style="position: relative; min-height: 250px;">
                        <canvas id="chartIntermediaciones"></canvas>
                    </div>
                </div>
            </div>
        </div>



        <div class="card mb-4 shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="mb-0 fw-bold">Notificaciones y Regulaciones de Intermediación</h6>
                    <small class="text-muted">Control gubernamental de vinculación entre empresas y trabajadores</small>
                </div>
                <button class="btn btn-outline-primary btn-sm rounded-pill"><i class="bi bi-download me-1"></i> Exportar
                    Informe</button>
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
                        <?php if (empty($notificaciones)): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-3">No hay notificaciones registradas.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($notificaciones as $n): ?>
                                <tr>
                                    <td><span
                                            class="font-monospace fw-bold text-primary"><?= htmlspecialchars($n['codigo_seguimiento']) ?></span>
                                    </td>
                                    <td><span
                                            class="badge bg-light text-dark border"><?= ucfirst(htmlspecialchars($n['origen'])) ?></span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">
                                            <?= htmlspecialchars($n['buscador_nombre'] . ' ' . $n['buscador_apellidos']) ?>
                                        </div>
                                        <small class="text-muted">EXP:
                                            <?= htmlspecialchars($n['buscador_expediente']) ?></small>
                                    </td>
                                    <td>
                                        <div><?= htmlspecialchars($n['nombre_empresa']) ?></div>
                                        <small
                                            class="text-muted"><?= htmlspecialchars($n['titulo_puesto'] ?? 'Contacto Directo') ?></small>
                                    </td>
                                    <td>
                                        <?php
                                        $badge_class = match ($n['estado_ministerio']) {
                                            'aprobado' => 'bg-success-subtle text-success',
                                            'rechazado' => 'bg-danger-subtle text-danger',
                                            'en_revision' => 'bg-info-subtle text-info',
                                            default => 'bg-warning-subtle text-warning'
                                        };
                                        ?>
                                        <span
                                            class="badge <?= $badge_class ?>"><?= ucfirst(str_replace('_', ' ', $n['estado_ministerio'])) ?></span>
                                    </td>
                                    <td><?= date('d M, Y', strtotime($n['fecha_creacion'])) ?></td>
                                    <td class="text-end">
                                        <?php if ($n['estado_ministerio'] === 'pendiente'): ?>
                                            <button class="btn btn-sm btn-success rounded-circle" title="Aprobar"><i
                                                    class="bi bi-check-lg"></i></button>
                                            <button class="btn btn-sm btn-outline-danger rounded-circle" title="Rechazar"><i
                                                    class="bi bi-x-lg"></i></button>
                                        <?php endif; ?>
                                        <button class="btn btn-sm btn-light rounded-circle btn-ver-detalle"
                                            data-bs-toggle="modal" data-bs-target="#modalDetalleNotificacion"
                                            data-codigo="<?= htmlspecialchars($n['codigo_seguimiento']) ?>"
                                            data-candidato="<?= htmlspecialchars($n['buscador_nombre'] . ' ' . $n['buscador_apellidos']) ?>"
                                            data-expediente="<?= htmlspecialchars($n['buscador_expediente']) ?>"
                                            data-estado="<?= htmlspecialchars($n['buscador_estado']) ?>"
                                            data-provincia="<?= htmlspecialchars($n['buscador_provincia']) ?>"
                                            data-empresa="<?= htmlspecialchars($n['nombre_empresa']) ?>"
                                            data-puesto="<?= htmlspecialchars($n['titulo_puesto'] ?? 'N/A') ?>"
                                            data-ruc="<?= htmlspecialchars($n['rnc_ruc'] ?? 'N/A') ?>"
                                            data-salario="<?= $n['salario_ofrecido'] ? number_format($n['salario_ofrecido'], 0, ',', '.') . ' XAF' : 'No especificado' ?>"
                                            data-motivo="<?= htmlspecialchars($n['mensaje_motivo'] ?? 'Sin observaciones adicionales.') ?>">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>





        <div class="row g-4">
            <div class="col-12 col-xl-8">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                        <h6 class="mb-0 fw-bold">Expedientes de Buscadores de Empleo</h6>
                        <a href="#" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i> Registrar
                            Ciudadano</a>
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
                                <?php if (empty($buscadores)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-3">No hay registros de buscadores.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($buscadores as $b): ?>
                                        <tr>
                                            <td><span
                                                    class="font-monospace fw-semibold"><?= htmlspecialchars($b['numero_expediente']) ?></span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="bg-secondary-subtle rounded-circle d-flex align-items-center justify-content-center"
                                                        style="width: 32px; height: 32px;">
                                                        <i class="bi bi-person text-secondary"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">
                                                            <?= htmlspecialchars($b['nombre'] . ' ' . $b['apellidos']) ?>
                                                        </div>
                                                        <small class="text-muted">DIP:
                                                            <?= htmlspecialchars($b['documento_identidad']) ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?= htmlspecialchars($b['provincia'] . ', ' . $b['ciudad_municipio']) ?></td>
                                            <td>
                                                <?php
                                                $badge_b = match ($b['estado_laboral']) {
                                                    'contratado' => 'bg-success-subtle text-success',
                                                    'suspendido' => 'bg-danger-subtle text-danger',
                                                    default => 'bg-warning-subtle text-warning'
                                                };
                                                ?>
                                                <span
                                                    class="badge <?= $badge_b ?>"><?= ucfirst(htmlspecialchars($b['estado_laboral'])) ?></span>
                                            </td>
                                            <td class="text-end">
                                                <?php if (!empty($b['copia_dip'])): ?>
                                                    <a href="<?= htmlspecialchars($b['copia_dip']) ?>" target="_blank"
                                                        class="btn btn-sm btn-outline-secondary" title="Descargar DIP"><i
                                                            class="bi bi-card-heading"></i></a>
                                                <?php endif; ?>
                                                <?php if (!empty($b['cv'])): ?>
                                                    <a href="<?= htmlspecialchars($b['cv']) ?>" target="_blank"
                                                        class="btn btn-sm btn-outline-secondary" title="Ver CV"><i
                                                            class="bi bi-file-earmark-person"></i></a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                        <h6 class="mb-0 fw-bold">Capacitación Estatal (Cursos)</h6>
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                            data-bs-target="#modalNuevoCurso">
                            <i class="bi bi-plus-circle"></i> Nuevo
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <?php if (empty($cursos)): ?>
                                <p class="text-muted text-center py-3">No hay cursos disponibles.</p>
                            <?php else: ?>
                                <?php foreach ($cursos as $c): ?>
                                    <div class="list-group-item px-0 py-3">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1 fw-bold"><?= htmlspecialchars($c['titulo_curso']) ?></h6>
                                            <?php
                                            $badge_c = match ($c['estado']) {
                                                'activo' => 'bg-success-subtle text-success',
                                                'proximamente' => 'bg-warning-subtle text-warning',
                                                default => 'bg-secondary-subtle text-secondary'
                                            };
                                            ?>
                                            <span class="badge <?= $badge_c ?>"><?= ucfirst($c['estado']) ?></span>
                                        </div>
                                        <p class="mb-1 text-muted fs-7">Impartido por:
                                            <?= htmlspecialchars($c['entidad_mostrar']) ?>
                                        </p>
                                        <small class="text-secondary"><i class="bi bi-clock me-1"></i> Duración:
                                            <?= (int) $c['duracion_horas'] ?> Horas</small>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>




    <div class="row g-4">
        <div class="col-12 col-xl-8">
            <div class="card h-100">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                    <h6 class="mb-0 fw-bold">Expedientes de Buscadores de Empleo</h6>
                    <a href="#" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i> Registrar
                        Ciudadano</a>
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
                                        <div class="bg-secondary-subtle rounded-circle d-flex align-items-center justify-content-center"
                                            style="width: 32px; height: 32px;">
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
                                    <button class="btn btn-sm btn-outline-secondary" title="Descargar DIP"><i
                                            class="bi bi-card-heading"></i></button>
                                    <button class="btn btn-sm btn-outline-secondary" title="Ver CV"><i
                                            class="bi bi-file-earmark-person"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="font-monospace">EG-11029</span></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-secondary-subtle rounded-circle d-flex align-items-center justify-content-center"
                                            style="width: 32px; height: 32px;">
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
                                    <button class="btn btn-sm btn-outline-secondary" title="Descargar DIP"><i
                                            class="bi bi-card-heading"></i></button>
                                    <button class="btn btn-sm btn-outline-secondary" title="Ver CV"><i
                                            class="bi bi-file-earmark-person"></i></button>
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
                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                        data-bs-target="#modalNuevoCurso"><i class="bi bi-plus-circle"></i> Nuevo</button>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item px-0 py-3">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1 fw-bold">Redes y Seguridad Informática</h6>
                                <span class="badge bg-success-subtle text-success">Activo</span>
                            </div>
                            <p class="mb-1 text-muted fs-7">Impartido por: INEM / Centros Técnicos</p>
                            <small class="text-secondary"><i class="bi bi-clock me-1"></i> Duración: 120
                                Horas</small>
                        </div>
                        <div class="list-group-item px-0 py-3">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1 fw-bold">Gestión Portuaria y Logística</h6>
                                <span class="badge bg-warning-subtle text-warning">Próximamente</span>
                            </div>
                            <p class="mb-1 text-muted fs-7">Impartido por: Autoridad Portuaria</p>
                            <small class="text-secondary"><i class="bi bi-clock me-1"></i> Duración: 80
                                Horas</small>
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
                    <h5 class="modal-title fw-bold">Expediente de Intermediación: <span id="modal-codigo"
                            class="text-primary"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6 border-end">
                            <h6 class="fw-bold text-muted uppercase fs-8">Datos del Candidato</h6>
                            <p class="mb-1"><strong>Nombre:</strong> <span id="modal-candidato"></span></p>
                            <p class="mb-1"><strong>Nº Expediente:</strong> <span id="modal-expediente"></span></p>
                            <p class="mb-1"><strong>Estado Actual:</strong> <span id="modal-estado"
                                    class="badge bg-secondary"></span></p>
                            <p class="mb-0"><strong>Provincia:</strong> <span id="modal-provincia"></span></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold text-muted uppercase fs-8">Datos de la Empresa / Oferta</h6>
                            <p class="mb-1"><strong>Empresa:</strong> <span id="modal-empresa"></span></p>
                            <p class="mb-1"><strong>Puesto Solicitado:</strong> <span id="modal-puesto"></span></p>
                            <p class="mb-1"><strong>RUC / RNC:</strong> <span id="modal-ruc"></span></p>
                            <p class="mb-0"><strong>Salario Ofrecido:</strong> <span id="modal-salario"></span></p>
                        </div>
                        <div class="col-12">
                            <hr>
                            <label class="form-label fw-bold">Mensaje / Motivo de Intermediación</label>
                            <div id="modal-motivo" class="p-3 bg-light rounded text-muted"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-danger"><i class="bi bi-x-circle me-1"></i> Rechazar
                        Solicitud</button>
                    <button type="button" class="btn btn-success"><i class="bi bi-check-circle me-1"></i> Aprobar
                        Intermediación</button>
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
                    <form action="guardar_curso.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Código del Curso</label>
                            <input type="text" name="codigo_curso" class="form-control" placeholder="Ej. CUR-2026-005"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Título del Curso</label>
                            <input type="text" name="titulo_curso" class="form-control"
                                placeholder="Ej. Administración de Sistemas Linux" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Entidad Formadora (Imparte)</label>
                            <select name="entidad_id" class="form-select" required>
                                <option value="" disabled selected>Seleccione una Entidad Formadora</option>
                                <?php foreach ($entidades as $e): ?>
                                    <option value="<?= $e['id'] ?>">
                                        <?= htmlspecialchars($e['nombre_entidad']) ?>
                                        <?= $e['siglas'] ? '(' . htmlspecialchars($e['siglas']) . ')' : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-semibold">Duración (Horas)</label>
                                <input type="number" name="duracion_horas" class="form-control" placeholder="60"
                                    required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">Estado Inicial</label>
                                <select name="estado" class="form-select">
                                    <option value="activo">Activo</option>
                                    <option value="proximamente">Próximamente</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Descripción</label>
                            <textarea name="descripcion_curso" class="form-control" rows="3"
                                placeholder="Detalles de la capacitación..."></textarea>
                        </div>
                        <div class="modal-footer px-0 pb-0">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Guardar y Publicar</button>
                        </div>
                    </form>
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
                <h5 class="modal-title fw-bold">Expediente de Intermediación: <span
                        class="text-primary">MITRAD-2026-X9</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6 border-end">
                        <h6 class="fw-bold text-muted uppercase fs-8">Datos del Candidato</h6>
                        <p class="mb-1"><strong>Nombre:</strong> Salvador Mete</p>
                        <p class="mb-1"><strong>Nº Expediente:</strong> EG-90812</p>
                        <p class="mb-1"><strong>Estado Actual:</strong> <span
                                class="badge badge-desempleado">Desempleado</span></p>
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
                            "Solicitud formal remitida para la revisión previa del perfil profesional y aprobación del
                            contrato laboral según la normativa reguladora del Ministerio de Trabajo."
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-danger"><i class="bi bi-x-circle me-1"></i> Rechazar
                    Solicitud</button>
                <button type="button" class="btn btn-success"><i class="bi bi-check-circle me-1"></i> Aprobar
                    Intermediación</button>
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


<script>
document.addEventListener('DOMContentLoaded', function() {

    // 1. RENDERIZAR GRÁFICO CIRCULAR (DOUGHNUT)
    const ctxCircular = document.getElementById('chartEstadoLaboral').getContext('2d');
    new Chart(ctxCircular, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($labels_circular) ?>,
            datasets: [{
                data: <?= json_encode($data_circular) ?>,
                backgroundColor: ['#ffc107', '#198754', '#dc3545'],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // 2. RENDERIZAR GRÁFICO DE BARRAS
    const ctxBarras = document.getElementById('chartIntermediaciones').getContext('2d');
    new Chart(ctxBarras, {
        type: 'bar',
        data: {
            labels: <?= json_encode($labels_barras) ?>,
            datasets: [{
                label: 'Cantidad de Expedientes',
                data: <?= json_encode($data_barras) ?>,
                backgroundColor: ['#ffc107', '#0dcaf0', '#198754', '#dc3545'],
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // 3. EVENTO PARA PASAR DATOS AL MODAL DE DETALLE
    const modalButtons = document.querySelectorAll('.btn-ver-detalle');
    modalButtons.forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('modal-codigo').textContent = this.dataset.codigo;
            document.getElementById('modal-candidato').textContent = this.dataset.candidato;
            document.getElementById('modal-expediente').textContent = this.dataset.expediente;
            document.getElementById('modal-estado').textContent = this.dataset.estado;
            document.getElementById('modal-provincia').textContent = this.dataset.provincia;
            document.getElementById('modal-empresa').textContent = this.dataset.empresa;
            document.getElementById('modal-puesto').textContent = this.dataset.puesto;
            document.getElementById('modal-ruc').textContent = this.dataset.ruc;
            document.getElementById('modal-salario').textContent = this.dataset.salario;
            document.getElementById('modal-motivo').textContent = this.dataset.motivo;
        });
    });

});
</script>

<?php include_once '../componentes/footer_admin.php'; ?>
<?php include_once '../componentes/header_admin.php'; ?>
<div class="d-flex" id="wrapper">


    <?php include_once '../componentes/menu_admin.php'; ?>

    <div class="container-fluid p-4">

        <div class="d-flex justify-content-end mb-3">
            <button class="btn btn-primary d-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal"
                data-bs-target="#modalAñadir">
                <i class="bi bi-plus-lg"></i>
                <span>Añadir</span>
            </button>
        </div>

        <div class="card mb-4 shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between border-bottom-0">
                <div>
                    <h6 class="mb-0 fw-bold">Notificaciones y Regulaciones de Intermediación</h6>
                    <small class="text-muted">Control gubernamental de vinculación entre empresas y trabajadores</small>
                </div>
                <button class="btn btn-outline-primary btn-sm rounded-pill">
                    <i class="bi bi-download me-1"></i> Exportar Informe
                </button>
            </div>



            <div class="card-body px-0 pt-0">
                <div class="table-responsive px-3">
                    <table id="tablaNotificaciones" class="table table-hover align-middle mb-0 w-100">
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
                                    <button class="btn btn-sm btn-success rounded-circle" title="Aprobar"
                                        data-bs-toggle="tooltip">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger rounded-circle" title="Rechazar"
                                        data-bs-toggle="tooltip">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                    <button class="btn btn-sm btn-light rounded-circle" title="Ver Detalles"
                                        data-bs-toggle="modal" data-bs-target="#modalDetalleNotificacion">
                                        <i class="bi bi-eye"></i>
                                    </button>
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
                                    <button class="btn btn-sm btn-light rounded-circle" title="Ver Detalles">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>



    </div>
</div>
</div>





<?php include_once '../componentes/footer_admin.php'; ?>
<?php include_once '../componentes/header_admin.php'; ?>
<div class="d-flex" id="wrapper">


    <?php include_once '../componentes/menu_admin.php'; ?>

    <div class="container-fluid p-4">

        <div class="d-flex justify-content-end mb-3">
            <button class="btn btn-primary d-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal"
                data-bs-target="#modalAñadirEntidad">
                <i class="bi bi-plus-circle-fill"></i>
                <span class="fw-medium">Añadir Entidad</span>
            </button>
        </div>

        <?php
// Consulta para obtener todas las entidades registradas ordenadas por ID descendente
try {
    $stmt_entidades = $pdo->query("SELECT * FROM entidades_formadoras ORDER BY id DESC");
    $entidades = $stmt_entidades->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error al consultar entidades: " . $e->getMessage());
    $entidades = [];
}
?>

<div class="card mb-4 shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between border-bottom-0">
        <div>
            <h6 class="mb-0 fw-bold">Entidades Formadoras y Centros de Capacitación</h6>
            <small class="text-muted">Registro oficial de instituciones autorizadas para impartir cursos estatales</small>
        </div>
        <button class="btn btn-outline-primary btn-sm rounded-pill">
            <i class="bi bi-download me-1"></i> Exportar Registro
        </button>
    </div>

    <div class="card-body px-0 pt-0">
        <div class="table-responsive px-3">
            <table id="tablaNotificaciones" class="table table-hover align-middle mb-0 w-100">
                <thead class="table-light">
                    <tr>
                        <th>Cód. Entidad</th>
                        <th>Nombre / Siglas</th>
                        <th>Tipo</th>
                        <th>Contacto</th>
                        <th>Ubicación</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($entidades)): ?>
                        <?php foreach ($entidades as $entidad): ?>
                            <?php
                            // Mapeo dinámico de colores según tipo de entidad
                            $tipo = strtolower($entidad['tipo_entidad']);
                            $badgeTipo = match($tipo) {
                                'publica'       => 'bg-primary-subtle text-primary border border-primary-subtle',
                                'privada'       => 'bg-info-subtle text-info border border-info-subtle',
                                'ong'           => 'bg-warning-subtle text-warning border border-warning-subtle',
                                'internacional' => 'bg-purple-subtle text-purple border border-purple-subtle',
                                default         => 'bg-secondary-subtle text-secondary border'
                            };

                            // Estado de la entidad (si tu tabla incluye estado 'activo'/'inactivo')
                            $estado = $entidad['estado'] ?? 'activo';
                            $badgeEstado = ($estado === 'activo') ? 'bg-success' : 'bg-danger';
                            ?>
                            <tr>
                                <td>
                                    <span class="font-monospace fw-bold text-primary">
                                        <?= htmlspecialchars($entidad['codigo_entidad']); ?>
                                    </span>
                                </td>

                                <td>
                                    <div class="fw-semibold"><?= htmlspecialchars($entidad['nombre_entidad']); ?></div>
                                    <?php if (!empty($entidad['siglas'])): ?>
                                        <span class="badge bg-secondary-subtle text-secondary border">
                                            <?= htmlspecialchars($entidad['siglas']); ?>
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <span class="badge <?= $badgeTipo; ?> text-capitalize">
                                        <?= htmlspecialchars($entidad['tipo_entidad']); ?>
                                    </span>
                                </td>

                                <td>
                                    <?php if (!empty($entidad['correo_electronico'])): ?>
                                        <div class="small">
                                            <i class="bi bi-envelope me-1 text-muted"></i><?= htmlspecialchars($entidad['correo_electronico']); ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($entidad['telefono'])): ?>
                                        <div class="small text-muted">
                                            <i class="bi bi-telephone me-1"></i>+240 <?= htmlspecialchars($entidad['telefono']); ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (empty($entidad['correo_electronico']) && empty($entidad['telefono'])): ?>
                                        <span class="text-muted small"><em>Sin contacto</em></span>
                                    <?php endif; ?>
                                </td>

                                <td><?= htmlspecialchars($entidad['provincia']); ?></td>

                                <td>
                                    <span class="badge <?= $badgeEstado; ?> text-capitalize">
                                        <?= htmlspecialchars($estado); ?>
                                    </span>
                                </td>

                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-primary rounded-circle me-1 btn-editar-entidad" 
            title="Editar" 
            data-bs-toggle="modal" 
            data-bs-target="#modalEditarEntidad"
            data-id="<?= $entidad['id']; ?>"
            data-codigo="<?= htmlspecialchars($entidad['codigo_entidad']); ?>"
            data-nombre="<?= htmlspecialchars($entidad['nombre_entidad']); ?>"
            data-siglas="<?= htmlspecialchars($entidad['siglas'] ?? ''); ?>"
            data-tipo="<?= htmlspecialchars($entidad['tipo_entidad']); ?>"
            data-provincia="<?= htmlspecialchars($entidad['provincia']); ?>"
            data-responsable="<?= htmlspecialchars($entidad['responsable_contacto'] ?? ''); ?>"
            data-telefono="<?= htmlspecialchars($entidad['telefono'] ?? ''); ?>"
            data-correo="<?= htmlspecialchars($entidad['correo_electronico'] ?? ''); ?>"
            data-direccion="<?= htmlspecialchars($entidad['direccion'] ?? ''); ?>">
        <i class="bi bi-pencil"></i>
    </button>
   
                                    <button class="btn btn-sm btn-light rounded-circle me-1 btn-ver-cursos" 
        title="Ver Cursos Impartidos" 
        data-bs-toggle="modal" 
        data-bs-target="#modalVerCursos"
        data-id="<?= $entidad['id']; ?>"
        data-nombre="<?= htmlspecialchars($entidad['nombre_entidad']); ?>"
        data-siglas="<?= htmlspecialchars($entidad['siglas'] ?? ''); ?>">
    <i class="bi bi-journal-text"></i>
</button>
                                    <button class="btn btn-sm btn-outline-danger rounded-circle" 
                                            title="Suspender" 
                                            data-bs-toggle="tooltip">
                                        <i class="bi bi-slash-circle"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bi bi-building-exclamation fs-3 d-block mb-2"></i>
                                No hay entidades formadoras registradas actualmente.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>



    </div>
</div>
</div>



<div class="modal fade" id="modalAñadirEntidad" tabindex="-1" aria-labelledby="modalAñadirEntidadLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">


            <div id="session-alert-container">
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" id="customAlert"
                        style="border-left: 5px solid #dc3545;">
                        <i class="bi bi-exclamation-octagon-fill me-2"></i>
                        <?= $_SESSION['error']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['exito'])): ?>
                    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert" id="customAlert"
                        style="border-left: 5px solid #198754;">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <?= $_SESSION['exito']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['exito']); ?>
                <?php endif; ?>
            </div>



            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold fs-6" id="modalAñadirEntidadLabel">
                    <i class="bi bi-building-add me-2"></i>Registrar Nueva Entidad Formadora
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <form id="formNuevaEntidad" action="../php/procesar_entidad.php" method="POST">
                <div class="modal-body p-4">

                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label for="nombre_entidad" class="form-label fw-semibold fs-7">Nombre de la Entidad <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nombre_entidad" name="nombre_entidad"
                                placeholder="Ej: Instituto Técnico Superior de Formación Profesional" required>
                        </div>
                        <div class="col-md-4">
                            <label for="siglas" class="form-label fw-semibold fs-7">Siglas / Nombre Corto</label>
                            <input type="text" class="form-control" id="siglas" name="siglas" placeholder="Ej: ITSFP">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="tipo_entidad" class="form-label fw-semibold fs-7">Tipo de Entidad <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" id="tipo_entidad" name="tipo_entidad" required>
                                <option value="publica" selected>Pública (Gubernamental / Estatal)</option>
                                <option value="privada">Privada</option>
                                <option value="ong">Organización / ONG</option>
                                <option value="internacional">Cooperación Internacional</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="provincia" class="form-label fw-semibold fs-7">Provincia Principal <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" id="provincia" name="provincia" required>
                                <option value="Bioko Norte" selected>Bioko Norte (Malabo)</option>
                                <option value="Litoral">Litoral (Bata)</option>
                                <option value="Wele-Nzas">Wele-Nzas (Mongomo)</option>
                                <option value="Kie-Ntem">Kie-Ntem (Ebebiyín)</option>
                                <option value="Centro Sur">Centro Sur (Evinayong)</option>
                                <option value="Bioko Sur">Bioko Sur (Luba)</option>
                                <option value="Annobón">Annobón (San Antonio de Palé)</option>
                                <option value="Djibloho">Djibloho (Ciudad de la Paz)</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label for="responsable_contacto" class="form-label fw-semibold fs-7">Persona de
                                Contacto</label>
                            <input type="text" class="form-control" id="responsable_contacto"
                                name="responsable_contacto" placeholder="Nombre del encargado">
                        </div>
                        <div class="col-md-4">
                            <label for="telefono" class="form-label fw-semibold fs-7">Teléfono de Contacto</label>
                            <div class="input-group">
                                <span class="input-group-text fs-7 bg-light">+240</span>
                                <input type="tel" class="form-control" id="telefono" name="telefono"
                                    placeholder="222 000 000">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="correo_electronico" class="form-label fw-semibold fs-7">Correo
                                Electrónico</label>
                            <input type="email" class="form-control" id="correo_electronico" name="correo_electronico"
                                placeholder="contacto@entidad.gq">
                        </div>
                    </div>

                    <div class="mb-2">
                        <label for="direccion" class="form-label fw-semibold fs-7">Dirección Física / Sede</label>
                        <textarea class="form-control" id="direccion" name="direccion" rows="2"
                            placeholder="Barrio, Calle, Referencia de ubicación..."></textarea>
                    </div>

                </div>

                <div class="modal-footer bg-light px-4 py-3">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-floppy-fill me-1"></i>Guardar Entidad
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>



<div class="modal fade" id="modalEditarEntidad" tabindex="-1" aria-labelledby="modalEditarEntidadLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold fs-6" id="modalEditarEntidadLabel">
                    <i class="bi bi-pencil-square me-2"></i>Editar Entidad Formadora
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formEditarEntidad" action="../php/actualizar_entidad.php" method="POST">
                <input type="hidden" id="edit_id" name="id">

                <div class="modal-body p-4">
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <label for="edit_codigo_entidad" class="form-label fw-semibold fs-7">Cód. Entidad</label>
                            <input type="text" class="form-control font-monospace bg-light" id="edit_codigo_entidad" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_nombre_entidad" class="form-label fw-semibold fs-7">Nombre de la Entidad <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_nombre_entidad" name="nombre_entidad" required>
                        </div>
                        <div class="col-md-3">
                            <label for="edit_siglas" class="form-label fw-semibold fs-7">Siglas</label>
                            <input type="text" class="form-control" id="edit_siglas" name="siglas">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="edit_tipo_entidad" class="form-label fw-semibold fs-7">Tipo de Entidad <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_tipo_entidad" name="tipo_entidad" required>
                                <option value="publica">Pública (Gubernamental / Estatal)</option>
                                <option value="privada">Privada</option>
                                <option value="ong">Organización / ONG</option>
                                <option value="internacional">Cooperación Internacional</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_provincia" class="form-label fw-semibold fs-7">Provincia Principal <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_provincia" name="provincia" required>
                                <option value="Bioko Norte">Bioko Norte (Malabo)</option>
                                <option value="Litoral">Litoral (Bata)</option>
                                <option value="Wele-Nzas">Wele-Nzas (Mongomo)</option>
                                <option value="Kie-Ntem">Kie-Ntem (Ebebiyín)</option>
                                <option value="Centro Sur">Centro Sur (Evinayong)</option>
                                <option value="Bioko Sur">Bioko Sur (Luba)</option>
                                <option value="Annobón">Annobón (San Antonio de Palé)</option>
                                <option value="Djibloho">Djibloho (Ciudad de la Paz)</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label for="edit_responsable_contacto" class="form-label fw-semibold fs-7">Persona de Contacto</label>
                            <input type="text" class="form-control" id="edit_responsable_contacto" name="responsable_contacto">
                        </div>
                        <div class="col-md-4">
                            <label for="edit_telefono" class="form-label fw-semibold fs-7">Teléfono de Contacto</label>
                            <div class="input-group">
                                <span class="input-group-text fs-7 bg-light">+240</span>
                                <input type="tel" class="form-control" id="edit_telefono" name="telefono">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="edit_correo_electronico" class="form-label fw-semibold fs-7">Correo Electrónico</label>
                            <input type="email" class="form-control" id="edit_correo_electronico" name="correo_electronico">
                        </div>
                    </div>

                    <div class="mb-2">
                        <label for="edit_direccion" class="form-label fw-semibold fs-7">Dirección Física / Sede</label>
                        <textarea class="form-control" id="edit_direccion" name="direccion" rows="2"></textarea>
                    </div>

                </div>

                <div class="modal-footer bg-light px-4 py-3">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-floppy-fill me-1"></i>Guardar Cambios
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>



<div class="modal fade" id="modalVerCursos" tabindex="-1" aria-labelledby="modalVerCursosLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            
            <div class="modal-header bg-dark text-white py-3">
                <h5 class="modal-title fw-bold fs-6" id="modalVerCursosLabel">
                    <i class="bi bi-journal-bookmark-fill me-2 text-warning"></i>Cursos Impartidos por: <span id="lblNombreEntidad" class="text-warning"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                <div id="contenedorCursosEntidad">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="text-muted mt-2 mb-0">Consultando catálogo de capacitaciones...</p>
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-light py-2">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg me-1"></i>Cerrar
                </button>
            </div>

        </div>
    </div>
</div>




<script>
document.addEventListener("DOMContentLoaded", function() {
    const editButtons = document.querySelectorAll('.btn-editar-entidad');
    
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Extraer los datos del botón cliqueado
            document.getElementById('edit_id').value                   = this.dataset.id;
            document.getElementById('edit_codigo_entidad').value        = this.dataset.codigo;
            document.getElementById('edit_nombre_entidad').value        = this.dataset.nombre;
            document.getElementById('edit_siglas').value                = this.dataset.siglas;
            document.getElementById('edit_tipo_entidad').value          = this.dataset.tipo;
            document.getElementById('edit_provincia').value             = this.dataset.provincia;
            document.getElementById('edit_responsable_contacto').value  = this.dataset.responsable;
            document.getElementById('edit_telefono').value              = this.dataset.telefono;
            document.getElementById('edit_correo_electronico').value    = this.dataset.correo;
            document.getElementById('edit_direccion').value             = this.dataset.direccion;
        });
    });
});
</script>


<script>
document.addEventListener("DOMContentLoaded", function() {
    const btnsVerCursos = document.querySelectorAll('.btn-ver-cursos');
    const lblNombre = document.getElementById('lblNombreEntidad');
    const contenedor = document.getElementById('contenedorCursosEntidad');

    btnsVerCursos.forEach(button => {
        button.addEventListener('click', function() {
            const entidadId = this.dataset.id;
            const nombreEntidad = this.dataset.nombre;
            const siglas = this.dataset.siglas;

            // Actualizar el título del modal
            lblNombre.textContent = siglas ? `${nombreEntidad} (${siglas})` : nombreEntidad;

            // Estado de carga inicial
            contenedor.innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="text-muted mt-2 mb-0">Cargando cursos...</p>
                </div>`;

            // Consultar el endpoint PHP pasándole el entidad_id
            fetch(`../php/obtener_cursos_entidad.php?entidad_id=${entidadId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'error') {
                        contenedor.innerHTML = `<div class="alert alert-danger mb-0">${data.message}</div>`;
                        return;
                    }

                    if (data.cursos.length === 0) {
                        contenedor.innerHTML = `
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-journal-x fs-1 d-block mb-2 text-secondary"></i>
                                <p class="mb-0">Esta entidad no tiene cursos ni capacitaciones registradas actualmente.</p>
                            </div>`;
                        return;
                    }

                    // Construir la tabla con los cursos
                    let html = `
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr class="fs-7 text-uppercase">
                                        <th>Código</th>
                                        <th>Título del Curso</th>
                                        <th>Modalidad</th>
                                        <th>Duración</th>
                                        <th>Fechas</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>`;

                    data.cursos.forEach(curso => {
                        let badgeModalidad = curso.modalidad === 'presencial' ? 'bg-primary' : (curso.modalidad === 'online' ? 'bg-info' : 'bg-warning text-dark');
                        let badgeEstado = curso.estado === 'activo' ? 'bg-success' : (curso.estado === 'proximamente' ? 'bg-warning text-dark' : 'bg-secondary');

                        html += `
                            <tr>
                                <td><span class="font-monospace fw-bold text-primary">${curso.codigo_curso}</span></td>
                                <td class="fw-semibold">${curso.titulo_curso}</td>
                                <td><span class="badge ${badgeModalidad} text-capitalize">${curso.modalidad}</span></td>
                                <td>${curso.duracion_horas} hrs</td>
                                <td class="small text-muted">
                                    ${curso.fecha_inicio ? curso.fecha_inicio : 'Sin fecha'} - ${curso.fecha_fin ? curso.fecha_fin : 'Sin fecha'}
                                </td>
                                <td><span class="badge ${badgeEstado} text-capitalize">${curso.estado}</span></td>
                            </tr>`;
                    });

                    html += `</tbody></table></div>`;
                    contenedor.innerHTML = html;
                })
                .catch(error => {
                    contenedor.innerHTML = `
                        <div class="alert alert-danger mb-0">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>Ocurrió un error al cargar la información.
                        </div>`;
                });
        });
    });
});
</script>

<?php include_once '../componentes/footer_admin.php'; ?>
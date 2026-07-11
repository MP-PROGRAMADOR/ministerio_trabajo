<?php include_once '../componentes/header_admin.php'; ?>
<div class="d-flex" id="wrapper">


    <?php include_once '../componentes/menu_admin.php'; ?>

    <div class="container-fluid p-4">

        <div class="d-flex justify-content-end mb-3">
            <button class="btn btn-primary d-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal"
                data-bs-target="#modalCrearCurso">
                <i class="bi bi-plus-lg"></i>
                <span>Añadir</span>
            </button>
        </div>

       
        <?php
// Consulta para obtener los cursos relacionando la entidad formadora
try {
    $sql_cursos = "SELECT 
                        c.id,
                        c.codigo_curso,
                        c.titulo_curso,
                        c.descripcion_curso,
                        c.duracion_horas,
                        c.modalidad,
                        c.fecha_inicio,
                        c.fecha_fin,
                        c.cupos_maximos,
                        c.estado,
                        e.nombre_entidad,
                        e.siglas
                   FROM cursos c
                   INNER JOIN entidades_formadoras e ON c.entidad_id = e.id
                   ORDER BY c.id DESC";
                   
    $stmt_cursos = $pdo->query($sql_cursos);
    $cursos = $stmt_cursos->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error al consultar cursos: " . $e->getMessage());
    $cursos = [];
}
?>

<div class="card mb-4 shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between border-bottom-0">
        <div>
            <h6 class="mb-0 fw-bold">Catálogo Oficial de Cursos y Capacitaciones</h6>
            <small class="text-muted">Oferta formativa estatal para mejorar la empleabilidad nacional</small>
        </div>
        <button class="btn btn-outline-primary btn-sm rounded-pill">
            <i class="bi bi-download me-1"></i> Exportar Cursos
        </button>
    </div>

    <div class="card-body px-0 pt-0">
        <div class="table-responsive px-3">
            <table id="tablaNotificaciones" class="table table-hover align-middle mb-0 w-100">
                <thead class="table-light">
                    <tr>
                        <th>Cód. Curso</th>
                        <th>Título del Curso</th>
                        <th>Entidad Formadora</th>
                        <th>Modalidad / Horas</th>
                        <th>Período</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($cursos)): ?>
                        <?php foreach ($cursos as $curso): ?>
                            <?php
                            // Badges para modalidad
                            $badgeModalidad = match($curso['modalidad']) {
                                'presencial' => 'bg-primary-subtle text-primary border border-primary-subtle',
                                'online'     => 'bg-info-subtle text-info border border-info-subtle',
                                'hibrido'    => 'bg-warning-subtle text-warning border border-warning-subtle',
                                default      => 'bg-secondary-subtle text-secondary'
                            };

                            // Badges para estado del curso
                            $badgeEstado = match($curso['estado']) {
                                'activo'       => 'bg-success',
                                'proximamente' => 'bg-warning text-dark',
                                'finalizado'   => 'bg-secondary',
                                default        => 'bg-light text-dark border'
                            };
                            ?>
                            <tr>
                                <td>
                                    <span class="font-monospace fw-bold text-primary">
                                        <?= htmlspecialchars($curso['codigo_curso']); ?>
                                    </span>
                                </td>

                                <td>
                                    <div class="fw-semibold"><?= htmlspecialchars($curso['titulo_curso']); ?></div>
                                    <small class="text-muted">
                                        <i class="bi bi-people me-1"></i>Cupos: <?= (int)$curso['cupos_maximos']; ?>
                                    </small>
                                </td>

                                <td>
                                    <div><?= htmlspecialchars($curso['nombre_entidad']); ?></div>
                                    <?php if (!empty($curso['siglas'])): ?>
                                        <span class="badge bg-light text-dark border">
                                            <?= htmlspecialchars($curso['siglas']); ?>
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <span class="badge <?= $badgeModalidad; ?> text-capitalize mb-1 d-inline-block">
                                        <?= htmlspecialchars($curso['modalidad']); ?>
                                    </span>
                                    <div class="small text-muted">
                                        <i class="bi bi-clock me-1"></i><?= (int)$curso['duracion_horas']; ?> hrs
                                    </div>
                                </td>

                                <td>
                                    <div class="small fw-semibold">
                                        <?= $curso['fecha_inicio'] ? date('d/m/Y', strtotime($curso['fecha_inicio'])) : 'Por definir'; ?>
                                    </div>
                                    <div class="small text-muted">
                                        al <?= $curso['fecha_fin'] ? date('d/m/Y', strtotime($curso['fecha_fin'])) : 'Por definir'; ?>
                                    </div>
                                </td>

                                <td>
                                    <span class="badge <?= $badgeEstado; ?> text-capitalize">
                                        <?= htmlspecialchars($curso['estado']); ?>
                                    </span>
                                </td>

                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-primary rounded-circle me-1 btn-editar-curso" 
                                            title="Editar Curso" 
                                            data-bs-toggle="tooltip"
                                            data-id="<?= $curso['id']; ?>">
                                        <i class="bi bi-pencil"></i>
                                    </button>

                                    <button class="btn btn-sm btn-light rounded-circle me-1" 
                                            title="Ver Detalle / Inscritos" 
                                            data-bs-toggle="tooltip">
                                        <i class="bi bi-eye"></i>
                                    </button>

                                    <button class="btn btn-sm btn-outline-danger rounded-circle" 
                                            title="Eliminar / Finalizar" 
                                            data-bs-toggle="tooltip">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bi bi-journal-x fs-3 d-block mb-2"></i>
                                No hay cursos o capacitaciones registradas actualmente.
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




<div class="modal fade" id="modalCrearCurso" tabindex="-1" aria-labelledby="modalCrearCursoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold fs-6" id="modalCrearCursoLabel">
                    <i class="bi bi-journal-plus me-2"></i>Registrar Nuevo Curso
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="../php/procesar_curso.php" method="POST" enctype="multipart/form-data" id="formCrearCurso">
                <div class="modal-body p-4">
                    
                    <div class="row g-4">
                        <div class="col-lg-8">
                            
                            <div class="mb-3">
                                <label for="titulo_curso" class="form-label fw-semibold fs-7">Título del Curso <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="titulo_curso" name="titulo_curso" placeholder="Ej: Especialización en Redes Nube" required>
                            </div>


                            <?php
// Consulta para cargar el desplegable de Entidades Formadoras
try {
    $stmt_entidades = $pdo->query("SELECT id, nombre_entidad, siglas FROM entidades_formadoras WHERE estado = 'activo' ORDER BY nombre_entidad ASC");
    $entidades = $stmt_entidades->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error al consultar entidades formadoras: " . $e->getMessage());
    $entidades = [];
}
?>


                            <div class="mb-3">
                                <label for="entidad_id" class="form-label fw-semibold fs-7">Entidad Impartidora <span class="text-danger">*</span></label>
                                <select class="form-select" id="entidad_id" name="entidad_id" required>
                                    <option value="" selected disabled>-- Seleccionar Entidad Formadora --</option>
                                    <?php if (!empty($entidades)): ?>
                                        <?php foreach ($entidades as $entidad): ?>
                                            <option value="<?= $entidad['id']; ?>">
                                                <?= htmlspecialchars($entidad['nombre_entidad']); ?> <?= !empty($entidad['siglas']) ? '('.htmlspecialchars($entidad['siglas']).')' : ''; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="" disabled>No hay entidades registradas</option>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-4">
                                    <label for="modalidad" class="form-label fw-semibold fs-7">Modalidad <span class="text-danger">*</span></label>
                                    <select class="form-select" id="modalidad" name="modalidad" required>
                                        <option value="presencial" selected>Presencial</option>
                                        <option value="online">Online</option>
                                        <option value="hibrido">Híbrido</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="duracion_horas" class="form-label fw-semibold fs-7">Duración (Horas) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="duracion_horas" name="duracion_horas" min="1" placeholder="40" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="cupos_maximos" class="form-label fw-semibold fs-7">Cupos Máx. <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="cupos_maximos" name="cupos_maximos" value="30" min="1" required>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-4">
                                    <label for="fecha_inicio" class="form-label fw-semibold fs-7">Fecha Inicio</label>
                                    <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio">
                                </div>
                                <div class="col-md-4">
                                    <label for="fecha_fin" class="form-label fw-semibold fs-7">Fecha Fin</label>
                                    <input type="date" class="form-control" id="fecha_fin" name="fecha_fin">
                                </div>
                                <div class="col-md-4">
                                    <label for="estado" class="form-label fw-semibold fs-7">Estado</label>
                                    <select class="form-select" id="estado" name="estado">
                                        <option value="proximamente">Próximamente</option>
                                        <option value="activo" selected>Activo</option>
                                        <option value="finalizado">Finalizado</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-2">
                                <label for="descripcion_curso" class="form-label fw-semibold fs-7">Descripción del Curso <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="descripcion_curso" name="descripcion_curso" rows="3" placeholder="Detalles de los módulos o temarios..." required></textarea>
                            </div>

                        </div>

                        <div class="col-lg-4 border-start ps-lg-4 d-flex flex-column justify-content-between">
                            <div>
                                <label class="form-label fw-semibold fs-7 mb-2 d-block">Imagen de Portada</label>
                                
                                <div class="card border-dashed bg-light text-center p-2 mb-3 position-relative" style="border: 2px dashed #dee2e6; min-height: 200px;">
                                    <img id="previewPortada" src="img/cursos/default.jpg" alt="Previsualización" class="img-fluid rounded shadow-sm w-100 h-100 object-fit-cover" style="max-height: 210px; display: block;" onerror="this.src='https://placehold.co/600x400?text=Portada+Curso';">
                                </div>

                                <div class="mb-3">
                                    <input class="form-control form-control-sm" type="file" id="imagen_portada" name="imagen_portada" accept="image/png, image/jpeg, image/webp">
                                    <small class="text-muted d-block mt-1">Formatos: JPG, PNG, WEBP (Máx. 2MB)</small>
                                </div>
                            </div>

                            <div class="alert alert-info py-2 px-3 small mb-0">
                                <i class="bi bi-info-circle me-1"></i> El código del curso se autogenerará al guardar.
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer bg-light px-4 py-3">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-floppy-fill me-1"></i>Guardar Curso
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>


<script>
document.addEventListener("DOMContentLoaded", function () {
    const inputImagen = document.getElementById('imagen_portada');
    const imgPreview = document.getElementById('previewPortada');

    if (inputImagen && imgPreview) {
        inputImagen.addEventListener('change', function (e) {
            const file = e.target.files[0];

            if (file) {
                // Validar que el archivo sea una imagen
                if (!file.type.match('image.*')) {
                    alert('Por favor selecciona un archivo de imagen válido (PNG, JPG, WEBP).');
                    inputImagen.value = '';
                    return;
                }

                // Cargar imagen con FileReader
                const reader = new FileReader();
                reader.onload = function (e) {
                    imgPreview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }
});
</script>

<?php include_once '../componentes/footer_admin.php'; ?>
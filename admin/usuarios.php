<?php include_once '../componentes/header_admin.php'; ?>
<div class="d-flex" id="wrapper">


    <?php include_once '../componentes/menu_admin.php'; ?>

    <div class="container-fluid p-4">

        <div class="d-flex justify-content-end mb-3">
            <button class="btn btn-primary d-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal"
                data-bs-target="#modalAgregarUsuario">
                <i class="bi bi-plus-lg"></i>
                <span>Añadir</span>
            </button>
        </div>

       
<?php
// Consulta para obtener el listado completo de usuarios del portal
try {
    $sql_usuarios = "SELECT 
        id,
        numero_expediente,
        nombre,
        apellidos,
        nombre_usuario,
        correo_electronico,
        documento_identidad,
        rol,
        correo_verificado,
        DATE_FORMAT(fecha_registro, '%d/%m/%Y') AS fecha_registro
    FROM usuarios
    ORDER BY id DESC";

    $stmt_usuarios = $pdo->query($sql_usuarios);
    $usuarios = $stmt_usuarios->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error al consultar usuarios: " . $e->getMessage());
    $usuarios = [];
}
?>

<div class="card mb-4 shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between border-bottom-0">
        <div>
            <h6 class="mb-0 fw-bold">Gestión Centralizada de Usuarios</h6>
            <small class="text-muted">Usuarios registrados en el portal del Ministerio de Trabajo</small>
        </div>
        <button class="btn btn-outline-primary btn-sm rounded-pill">
            <i class="bi bi-download me-1"></i> Exportar Usuarios
        </button>
    </div>

    <div class="card-body px-0 pt-0">
        <div class="table-responsive px-3">
            <table id="tablaUsuarios" class="table table-hover align-middle mb-0 w-100">
                <thead class="table-light">
                    <tr>
                        <th>Expediente</th>
                        <th>Usuario / Nombre</th>
                        <th>Documento / Email</th>
                        <th>Rol</th>
                        <th>Verificación</th>
                        <th>Registro</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($usuarios)): ?>
                        <?php foreach ($usuarios as $usr): ?>
                            <?php
                            // Badges por Rol de Usuario
                            $badgeRol = match($usr['rol']) {
                                'administrador' => 'bg-danger-subtle text-danger border border-danger-subtle',
                                'ministerio'    => 'bg-primary-subtle text-primary border border-primary-subtle',
                                'empleador'     => 'bg-info-subtle text-info border border-info-subtle',
                                'buscador'      => 'bg-success-subtle text-success border border-success-subtle',
                                default         => 'bg-secondary-subtle text-secondary'
                            };
                            ?>
                            <tr>
                                <td>
                                    <span class="font-monospace fw-bold text-primary">
                                        <?= htmlspecialchars($usr['numero_expediente']); ?>
                                    </span>
                                </td>

                                <td>
                                    <div class="fw-semibold">
                                        <?= htmlspecialchars($usr['nombre'] . ' ' . $usr['apellidos']); ?>
                                    </div>
                                    <small class="text-muted">
                                        <i class="bi bi-person me-1"></i>@<?= htmlspecialchars($usr['nombre_usuario']); ?>
                                    </small>
                                </td>

                                <td>
                                    <div class="small fw-semibold text-dark">
                                        <i class="bi bi-card-heading me-1"></i><?= htmlspecialchars($usr['documento_identidad']); ?>
                                    </div>
                                    <small class="text-muted d-block">
                                        <?= htmlspecialchars($usr['correo_electronico']); ?>
                                    </small>
                                </td>

                                <td>
                                    <span class="badge <?= $badgeRol; ?> text-capitalize px-2 py-1">
                                        <?= htmlspecialchars($usr['rol']); ?>
                                    </span>
                                </td>

                                <td>
                                    <?php if ($usr['correo_verificado'] == 1): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">
                                            <i class="bi bi-check-circle me-1"></i>Verificado
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle">
                                            <i class="bi bi-clock-history me-1"></i>Pendiente
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <span class="small text-muted">
                                        <?= htmlspecialchars($usr['fecha_registro']); ?>
                                    </span>
                                </td>

                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-primary rounded-circle me-1 btn-editar-usuario" 
                                            title="Editar Usuario" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalEditarUsuario"
                                            data-id="<?= $usr['id']; ?>"
                                            data-expediente="<?= htmlspecialchars($usr['numero_expediente']); ?>"
                                            data-nombre="<?= htmlspecialchars($usr['nombre']); ?>"
                                            data-apellidos="<?= htmlspecialchars($usr['apellidos']); ?>"
                                            data-username="<?= htmlspecialchars($usr['nombre_usuario']); ?>"
                                            data-email="<?= htmlspecialchars($usr['correo_electronico']); ?>"
                                            data-documento="<?= htmlspecialchars($usr['documento_identidad']); ?>"
                                            data-rol="<?= htmlspecialchars($usr['rol']); ?>">
                                        <i class="bi bi-pencil"></i>
                                    </button>

                                    <button class="btn btn-sm btn-light rounded-circle me-1 btn-ver-usuario" 
        title="Ver Perfil Detallado" 
        data-bs-toggle="modal" 
        data-bs-target="#modalDetalleUsuario"
        data-id="<?= $usr['id']; ?>"
        data-expediente="<?= htmlspecialchars($usr['numero_expediente']); ?>"
        data-nombre="<?= htmlspecialchars($usr['nombre'] . ' ' . $usr['apellidos']); ?>"
        data-username="<?= htmlspecialchars($usr['nombre_usuario']); ?>"
        data-email="<?= htmlspecialchars($usr['correo_electronico']); ?>"
        data-documento="<?= htmlspecialchars($usr['documento_identidad']); ?>"
        data-rol="<?= htmlspecialchars($usr['rol']); ?>"
        data-verificado="<?= $usr['correo_verificado']; ?>"
        data-registro="<?= htmlspecialchars($usr['fecha_registro']); ?>">
    <i class="bi bi-eye"></i>
</button>

                                    <button class="btn btn-sm btn-outline-danger rounded-circle btn-eliminar-usuario" 
                                            title="Eliminar Usuario" 
                                            data-id="<?= $usr['id']; ?>"
                                            data-nombre="<?= htmlspecialchars($usr['nombre_usuario']); ?>">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bi bi-people-fill fs-3 d-block mb-2"></i>
                                No hay usuarios registrados actualmente en la base de datos.
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




<div class="modal fade" id="modalAgregarUsuario" tabindex="-1" aria-labelledby="modalAgregarUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold fs-6" id="modalAgregarUsuarioLabel">
                    <i class="bi bi-person-plus-fill me-2 text-primary"></i>Registrar Nuevo Usuario
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="../php/procesar_usuarios.php" method="POST" id="formAgregarUsuario">
                <div class="modal-body p-4">
                    
                    <div class="alert alert-info py-2 px-3 small mb-4">
                        <i class="bi bi-info-circle me-1"></i>
                        El <strong>número de expediente</strong> (formato <code>EG-XXXXX</code>) se generará automáticamente de forma única al guardar.
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="add_nombre" class="form-label fw-semibold fs-7">Nombre(s) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="add_nombre" name="nombre" placeholder="Ej: Salvador" required>
                        </div>
                        <div class="col-md-6">
                            <label for="add_apellidos" class="form-label fw-semibold fs-7">Apellidos <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="add_apellidos" name="apellidos" placeholder="Ej: Mete Bijeri" required>
                        </div>

                        <div class="col-md-6">
                            <label for="add_nombre_usuario" class="form-label fw-semibold fs-7">Nombre de Usuario (Username) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted">@</span>
                                <input type="text" class="form-control" id="add_nombre_usuario" name="nombre_usuario" placeholder="ej: smete" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="add_documento_identidad" class="form-label fw-semibold fs-7">Documento de Identidad (DIP/Pasaporte) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control font-monospace" id="add_documento_identidad" name="documento_identidad" placeholder="Ej: 123456789" required>
                        </div>

                        <div class="col-md-7">
                            <label for="add_correo_electronico" class="form-label fw-semibold fs-7">Correo Electrónico <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="add_correo_electronico" name="correo_electronico" placeholder="ejemplo@dominio.com" required>
                        </div>
                        <div class="col-md-5">
                            <label for="add_rol" class="form-label fw-semibold fs-7">Rol Institucional <span class="text-danger">*</span></label>
                            <select class="form-select" id="add_rol" name="rol" required>
                                <option value="buscador" selected>Buscador de Empleo</option>
                                <option value="empleador">Empleador / Empresa</option>
                                <option value="ministerio">Personal Ministerio</option>
                                <option value="administrador">Administrador del Sistema</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="add_password" class="form-label fw-semibold fs-7">Contraseña <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="add_password" name="password" minlength="6" required>
                        </div>
                        <div class="col-md-6">
                            <label for="add_password_confirm" class="form-label fw-semibold fs-7">Confirmar Contraseña <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="add_password_confirm" name="password_confirm" minlength="6" required>
                        </div>

                        <div class="col-12 mt-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="add_correo_verificado" name="correo_verificado" value="1" checked>
                                <label class="form-check-label fs-7 fw-semibold" for="add_correo_verificado">
                                    Marcar correo como verificado automáticamente
                                </label>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer bg-light px-4 py-3">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check-circle-fill me-1"></i>Guardar Usuario
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>





<div class="modal fade" id="modalEditarUsuario" tabindex="-1" aria-labelledby="modalEditarUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold fs-6" id="modalEditarUsuarioLabel">
                    <i class="bi bi-pencil-square me-2 text-warning"></i>Editar Usuario
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="../php/actualizar_usuario.php" method="POST" id="formEditarUsuario">
                <input type="hidden" id="edit_id" name="id">

                <div class="modal-body p-4">
                    
                    <div class="alert alert-secondary py-2 px-3 small mb-4 d-flex align-items-center justify-content-between">
                        <div>
                            <i class="bi bi-folder2-open me-1 text-primary"></i>
                            Expediente: <strong id="lbl_edit_expediente" class="font-monospace text-primary">EG-00000</strong>
                        </div>
                        <span class="badge bg-light text-muted border">ID Usuario: <span id="lbl_edit_id">0</span></span>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="edit_nombre" class="form-label fw-semibold fs-7">Nombre(s) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_nombre" name="nombre" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_apellidos" class="form-label fw-semibold fs-7">Apellidos <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_apellidos" name="apellidos" required>
                        </div>

                        <div class="col-md-6">
                            <label for="edit_nombre_usuario" class="form-label fw-semibold fs-7">Nombre de Usuario (Username) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted">@</span>
                                <input type="text" class="form-control" id="edit_nombre_usuario" name="nombre_usuario" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_documento_identidad" class="form-label fw-semibold fs-7">Documento de Identidad (DIP/Pasaporte) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control font-monospace" id="edit_documento_identidad" name="documento_identidad" required>
                        </div>

                        <div class="col-md-7">
                            <label for="edit_correo_electronico" class="form-label fw-semibold fs-7">Correo Electrónico <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="edit_correo_electronico" name="correo_electronico" required>
                        </div>
                        <div class="col-md-5">
                            <label for="edit_rol" class="form-label fw-semibold fs-7">Rol Institucional <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_rol" name="rol" required>
                                <option value="buscador">Buscador de Empleo</option>
                                <option value="empleador">Empleador / Empresa</option>
                                <option value="ministerio">Personal Ministerio</option>
                                <option value="administrador">Administrador del Sistema</option>
                            </select>
                        </div>

                        <div class="col-12 border-top pt-3 mt-3">
                            <p class="text-muted small mb-2">
                                <i class="bi bi-shield-lock me-1"></i><strong>Cambiar Contraseña (Opcional):</strong> Deje los campos vacíos si desea conservar la contraseña actual.
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_password" class="form-label fw-semibold fs-7">Nueva Contraseña</label>
                            <input type="password" class="form-control" id="edit_password" name="password" minlength="8" placeholder="••••••••">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_password_confirm" class="form-label fw-semibold fs-7">Confirmar Nueva Contraseña</label>
                            <input type="password" class="form-control" id="edit_password_confirm" name="password_confirm" minlength="8" placeholder="••••••••">
                        </div>

                        <div class="col-12 mt-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="edit_correo_verificado" name="correo_verificado" value="1">
                                <label class="form-check-label fs-7 fw-semibold" for="edit_correo_verificado">
                                    Cuenta con correo verificado
                                </label>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer bg-light px-4 py-3">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-warning text-dark px-4 fw-semibold">
                        <i class="bi bi-arrow-repeat me-1"></i>Actualizar Usuario
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>




<div class="modal fade" id="modalDetalleUsuario" tabindex="-1" aria-labelledby="modalDetalleUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold fs-6" id="modalDetalleUsuarioLabel">
                    <i class="bi bi-person-badge-fill me-2"></i>Ficha Informativa del Usuario
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                
                <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
                    <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold fs-3 me-3" style="width: 60px; height: 60px;">
                        <span id="view_iniciales">US</span>
                    </div>
                    <div>
                        <h5 class="mb-1 fw-bold text-dark" id="view_nombre_completo">Nombre Apellidos</h5>
                        <div class="text-muted small">
                            <span class="me-3"><i class="bi bi-at me-1"></i><span id="view_username">usuario</span></span>
                            <span><i class="bi bi-folder2-open me-1"></i>Expediente: <strong id="view_expediente" class="font-monospace text-primary">EG-00000</strong></span>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 h-100">
                            <small class="text-muted d-block mb-1"><i class="bi bi-card-heading me-1"></i>Documento de Identidad (DIP / Pasaporte)</small>
                            <span class="fw-bold font-monospace text-dark fs-6" id="view_documento">---</span>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 h-100">
                            <small class="text-muted d-block mb-1"><i class="bi bi-envelope me-1"></i>Correo Electrónico</small>
                            <span class="fw-semibold text-dark fs-6" id="view_email">---</span>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded-3 h-100">
                            <small class="text-muted d-block mb-1"><i class="bi bi-shield-check me-1"></i>Rol en el Sistema</small>
                            <span id="view_badge_rol" class="badge text-capitalize">---</span>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded-3 h-100">
                            <small class="text-muted d-block mb-1"><i class="bi bi-check-circle me-1"></i>Verificación de Cuenta</small>
                            <span id="view_badge_verificado" class="badge">---</span>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded-3 h-100">
                            <small class="text-muted d-block mb-1"><i class="bi bi-calendar-event me-1"></i>Fecha de Registro</small>
                            <span class="fw-semibold text-dark" id="view_fecha_registro">--/--/----</span>
                        </div>
                    </div>
                </div>

            </div>

            <div class="modal-footer bg-light px-4 py-3">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg me-1"></i>Cerrar
                </button>
            </div>

        </div>
    </div>
</div>




<script>
document.addEventListener("DOMContentLoaded", function () {
    const formAgregarUsuario = document.getElementById('formAgregarUsuario');

    if (formAgregarUsuario) {
        formAgregarUsuario.addEventListener('submit', function (e) {
            const pass = document.getElementById('add_password').value;
            const passConfirm = document.getElementById('add_password_confirm').value;

            if (pass !== passConfirm) {
                e.preventDefault();
                alert('Las contraseñas no coinciden. Por favor, verifícalas.');
                document.getElementById('add_password_confirm').focus();
            }
        });
    }
});
</script>




<script>
document.addEventListener("DOMContentLoaded", function () {
    // 1. Manejo del evento click en el botón Editar Usuario
    const btnsEditar = document.querySelectorAll('.btn-editar-usuario');

    btnsEditar.forEach(btn => {
        btn.addEventListener('click', function () {
            // Asignar los datos del data-* attributes al formulario del modal
            document.getElementById('edit_id').value = this.dataset.id;
            document.getElementById('lbl_edit_id').textContent = this.dataset.id;
            document.getElementById('lbl_edit_expediente').textContent = this.dataset.expediente;

            document.getElementById('edit_nombre').value = this.dataset.nombre;
            document.getElementById('edit_apellidos').value = this.dataset.apellidos;
            document.getElementById('edit_nombre_usuario').value = this.dataset.username;
            document.getElementById('edit_documento_identidad').value = this.dataset.documento;
            document.getElementById('edit_correo_electronico').value = this.dataset.email;
            document.getElementById('edit_rol').value = this.dataset.rol;

            // Limpiar los campos de clave por seguridad
            document.getElementById('edit_password').value = '';
            document.getElementById('edit_password_confirm').value = '';

            // Marcar el checkbox de verificado según corresponda
            const verificado = this.dataset.verificado == "1";
            document.getElementById('edit_correo_verificado').checked = verificado;
        });
    });

    // 2. Validación opcional de contraseñas si el usuario decide cambiarlas
    const formEditarUsuario = document.getElementById('formEditarUsuario');
    if (formEditarUsuario) {
        formEditarUsuario.addEventListener('submit', function (e) {
            const pass = document.getElementById('edit_password').value;
            const passConfirm = document.getElementById('edit_password_confirm').value;

            if (pass !== '' || passConfirm !== '') {
                if (pass !== passConfirm) {
                    e.preventDefault();
                    alert('Las contraseñas no coinciden. Por favor, compruébalas.');
                    document.getElementById('edit_password_confirm').focus();
                }
            }
        });
    }
});
</script>



<script>
document.addEventListener("DOMContentLoaded", function () {
    const btnsVerUsuario = document.querySelectorAll('.btn-ver-usuario');

    btnsVerUsuario.forEach(btn => {
        btn.addEventListener('click', function () {
            const nombre = this.dataset.nombre || 'Sin Nombre';
            const username = this.dataset.username || '---';
            const expediente = this.dataset.expediente || '---';
            const documento = this.dataset.documento || '---';
            const email = this.dataset.email || '---';
            const rol = this.dataset.rol || 'buscador';
            const verificado = this.dataset.verificado == "1";
            const registro = this.dataset.registro || '---';

            // Asignar texto
            document.getElementById('view_nombre_completo').textContent = nombre;
            document.getElementById('view_username').textContent = username;
            document.getElementById('view_expediente').textContent = expediente;
            document.getElementById('view_documento').textContent = documento;
            document.getElementById('view_email').textContent = email;
            document.getElementById('view_fecha_registro').textContent = registro;

            // Generar Iniciales para el avatar
            const iniciales = nombre.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
            document.getElementById('view_iniciales').textContent = iniciales || 'US';

            // Badge de Rol
            const badgeRol = document.getElementById('view_badge_rol');
            badgeRol.textContent = rol;
            badgeRol.className = 'badge text-capitalize px-2 py-1 ';
            if (rol === 'administrador') badgeRol.classList.add('bg-danger-subtle', 'text-danger', 'border', 'border-danger-subtle');
            else if (rol === 'ministerio') badgeRol.classList.add('bg-primary-subtle', 'text-primary', 'border', 'border-primary-subtle');
            else if (rol === 'empleador') badgeRol.classList.add('bg-info-subtle', 'text-info', 'border', 'border-info-subtle');
            else badgeRol.classList.add('bg-success-subtle', 'text-success', 'border', 'border-success-subtle');

            // Badge de Verificación
            const badgeVerificado = document.getElementById('view_badge_verificado');
            if (verificado) {
                badgeVerificado.textContent = 'Verificado';
                badgeVerificado.className = 'badge bg-success-subtle text-success border border-success-subtle';
            } else {
                badgeVerificado.textContent = 'Pendiente';
                badgeVerificado.className = 'badge bg-warning-subtle text-warning border border-warning-subtle';
            }
        });
    });
});
</script>


<?php include_once '../componentes/footer_admin.php'; ?>
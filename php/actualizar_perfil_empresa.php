<?php
session_start();

// ===== VERIFICAR SESIÓN =====
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'empleador') {
    header('Location: ../login_empleador.php');
    exit();
}

include_once '../conexion/conexion.php';

$id_usuario = $_SESSION['id_usuario'];
$accion = $_POST['accion'] ?? '';

// ===== VALIDAR ACCIÓN =====
if (empty($accion)) {
    $_SESSION['mensaje_error'] = 'Acción no válida.';
    header('Location: ../empleador/datos_empresas.php');
    exit();
}

try {
    // ============================================================
    // ACCIÓN 1: ACTUALIZAR DATOS DE LA EMPRESA
    // ============================================================
    if ($accion === 'datos_empresa') {
        $empresa_id = $_POST['empresa_id'] ?? 0;
        $nombre_empresa = trim($_POST['nombre_empresa'] ?? '');
        $rnc_ruc = trim($_POST['rnc_ruc'] ?? '');
        $sector_industrial = trim($_POST['sector_industrial'] ?? '');
        $telefono_corporativo = trim($_POST['telefono_corporativo'] ?? '');
        $sitio_web = trim($_POST['sitio_web'] ?? '');
        $direccion = trim($_POST['direccion'] ?? '');

        // Validar campos obligatorios
        if (empty($nombre_empresa) || empty($sector_industrial) || empty($telefono_corporativo) || empty($direccion)) {
            $_SESSION['mensaje_error'] = 'Todos los campos obligatorios deben estar llenos.';
            header('Location: ../empleador/datos_empresas.php');
            exit();
        }

        // Verificar que la empresa pertenece al usuario
        $stmt = $pdo->prepare("SELECT id FROM empleadores WHERE id = ? AND usuario_id = ?");
        $stmt->execute([$empresa_id, $id_usuario]);
        if (!$stmt->fetch()) {
            $_SESSION['mensaje_error'] = 'No tienes permiso para editar esta empresa.';
            header('Location: ../empleador/datos_empresas.php');
            exit();
        }

        // Actualizar datos
        $stmt = $pdo->prepare("
            UPDATE empleadores 
            SET nombre_empresa = ?, 
                rnc_ruc = ?, 
                sector_industrial = ?, 
                telefono_corporativo = ?, 
                sitio_web = ?, 
                direccion = ?
            WHERE id = ? AND usuario_id = ?
        ");
        $stmt->execute([
            $nombre_empresa, 
            $rnc_ruc, 
            $sector_industrial, 
            $telefono_corporativo, 
            $sitio_web, 
            $direccion, 
            $empresa_id, 
            $id_usuario
        ]);

        // Actualizar sesión
        $_SESSION['nombre_empresa'] = $nombre_empresa;

        $_SESSION['mensaje_exito'] = 'Datos de la empresa actualizados correctamente.';
        header('Location: ../empleador/datos_empresas.php');
        exit();
    }

    // ============================================================
    // ACCIÓN 2: ACTUALIZAR DATOS DEL RESPONSABLE
    // ============================================================
    if ($accion === 'responsable') {
        $nombre = trim($_POST['nombre'] ?? '');
        $apellidos = trim($_POST['apellidos'] ?? '');
        $documento_identidad = trim($_POST['documento_identidad'] ?? '');
        $correo_electronico = trim($_POST['correo_electronico'] ?? '');

        // Validar campos obligatorios
        if (empty($nombre) || empty($apellidos) || empty($documento_identidad) || empty($correo_electronico)) {
            $_SESSION['mensaje_error'] = 'Todos los campos del responsable son obligatorios.';
            header('Location: ../empleador/datos_empresas.php');
            exit();
        }

        // Validar formato de correo
        if (!filter_var($correo_electronico, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['mensaje_error'] = 'El correo electrónico no es válido.';
            header('Location: ../empleador/datos_empresas.php');
            exit();
        }

        // Verificar que el correo no exista ya (excepto para este usuario)
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE correo_electronico = ? AND id != ?");
        $stmt->execute([$correo_electronico, $id_usuario]);
        if ($stmt->fetch()) {
            $_SESSION['mensaje_error'] = 'El correo electrónico ya está en uso por otro usuario.';
            header('Location: ../empleador/datos_empresas.php');
            exit();
        }

        // Verificar que el documento no exista ya (excepto para este usuario)
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE documento_identidad = ? AND id != ?");
        $stmt->execute([$documento_identidad, $id_usuario]);
        if ($stmt->fetch()) {
            $_SESSION['mensaje_error'] = 'El documento de identidad ya está registrado por otro usuario.';
            header('Location: ../empleador/datos_empresas.php');
            exit();
        }

        // Actualizar datos del responsable
        $stmt = $pdo->prepare("
            UPDATE usuarios 
            SET nombre = ?, 
                apellidos = ?, 
                documento_identidad = ?, 
                correo_electronico = ?
            WHERE id = ?
        ");
        $stmt->execute([$nombre, $apellidos, $documento_identidad, $correo_electronico, $id_usuario]);

        // Actualizar sesión
        $_SESSION['nombre_completo'] = $nombre . ' ' . $apellidos;

        $_SESSION['mensaje_exito'] = 'Datos del responsable actualizados correctamente.';
        header('Location: ../empleador/datos_empresas.php');
        exit();
    }

    // ============================================================
    // ACCIÓN 3: ACTUALIZAR CREDENCIALES DE ACCESO
    // ============================================================
    if ($accion === 'credenciales') {
        $nombre_usuario = trim($_POST['nombre_usuario'] ?? '');
        $password_actual = $_POST['password_actual'] ?? '';
        $password_nueva = $_POST['password_nueva'] ?? '';

        // Validar nombre de usuario
        if (empty($nombre_usuario)) {
            $_SESSION['mensaje_error'] = 'El nombre de usuario es obligatorio.';
            header('Location: ../empleador/datos_empresas.php');
            exit();
        }

        // Verificar que el nombre de usuario no exista ya (excepto para este usuario)
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE nombre_usuario = ? AND id != ?");
        $stmt->execute([$nombre_usuario, $id_usuario]);
        if ($stmt->fetch()) {
            $_SESSION['mensaje_error'] = 'El nombre de usuario ya está en uso. Por favor, elige otro.';
            header('Location: ../empleador/datos_empresas.php');
            exit();
        }

        // Actualizar nombre de usuario
        $stmt = $pdo->prepare("UPDATE usuarios SET nombre_usuario = ? WHERE id = ?");
        $stmt->execute([$nombre_usuario, $id_usuario]);

        // Actualizar sesión
        $_SESSION['nombre_usuario'] = $nombre_usuario;

        // ===== CAMBIAR CONTRASEÑA SI SE PROPORCIONA =====
        $mensaje = 'Nombre de usuario actualizado correctamente.';
        
        if (!empty($password_actual) || !empty($password_nueva)) {
            // Si se proporciona uno, ambos son requeridos
            if (empty($password_actual) || empty($password_nueva)) {
                $_SESSION['mensaje_error'] = 'Para cambiar la contraseña, debes introducir tanto la actual como la nueva.';
                header('Location: ../empleador/datos_empresas.php');
                exit();
            }

            // Verificar contraseña actual
            $stmt = $pdo->prepare("SELECT password FROM usuarios WHERE id = ?");
            $stmt->execute([$id_usuario]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!password_verify($password_actual, $usuario['password'])) {
                $_SESSION['mensaje_error'] = 'La contraseña actual es incorrecta.';
                header('Location: ../empleador/datos_empresas.php');
                exit();
            }

            // Validar longitud de nueva contraseña
            if (strlen($password_nueva) < 8) {
                $_SESSION['mensaje_error'] = 'La nueva contraseña debe tener al menos 8 caracteres.';
                header('Location: ../empleador/datos_empresas.php');
                exit();
            }

            // Actualizar contraseña
            $password_hash = password_hash($password_nueva, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
            $stmt->execute([$password_hash, $id_usuario]);

            $mensaje = 'Nombre de usuario y contraseña actualizados correctamente.';
        }

        $_SESSION['mensaje_exito'] = $mensaje;
        header('Location: ../empleador/datos_empresas.php');
        exit();
    }

    // ============================================================
    // ACCIÓN NO VÁLIDA
    // ============================================================
    $_SESSION['mensaje_error'] = 'Acción no reconocida.';
    header('Location: ../empleador/datos_empresas.php');
    exit();

} catch (PDOException $e) {
    error_log("Error al actualizar perfil de empresa: " . $e->getMessage());
    $_SESSION['mensaje_error'] = 'Error al actualizar los datos. Por favor, intente nuevamente.';
    header('Location: ../empleador/datos_empresas.php');
    exit();
}
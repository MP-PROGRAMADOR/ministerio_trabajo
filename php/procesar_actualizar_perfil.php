<?php
session_start();

// ===== VERIFICAR SESIÓN =====
if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../login_desempleados.php');
    exit();
}

include '../conexion/conexion.php';

$id_usuario = $_SESSION['id_usuario'];

// ===== RECIBIR DATOS DEL FORMULARIO =====
$nombre          = trim($_POST['nombre'] ?? '');
$apellidos       = trim($_POST['apellidos'] ?? '');
$documento       = trim($_POST['documento_identidad'] ?? '');
$telefono        = trim($_POST['telefono'] ?? '');
$estado_civil    = $_POST['estado_civil'] ?? '';
$estado_laboral  = $_POST['estado_laboral'] ?? '';
$provincia       = $_POST['provincia'] ?? '';
$ciudad_municipio= $_POST['ciudad_municipio'] ?? '';
$foto_carnet     = $_FILES['foto_carnet'] ?? null;

// ===== OBTENER DATOS ACTUALES =====
try {
    // Obtener usuario
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
    $stmt->execute([$id_usuario]);
    $usuario_actual = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$usuario_actual) {
        $_SESSION['mensaje_error'] = "Usuario no encontrado.";
        header('Location: ../desempleado/perfil.php');
        exit();
    }

    // Obtener buscador
    $stmt = $pdo->prepare("SELECT * FROM buscadores_empleo WHERE usuario_id = ?");
    $stmt->execute([$id_usuario]);
    $buscador_actual = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$buscador_actual) {
        $_SESSION['mensaje_error'] = "Perfil de buscador no encontrado. Contacte al administrador.";
        header('Location: ../desempleado/perfil.php');
        exit();
    }

} catch (PDOException $e) {
    error_log("Error al obtener datos actuales: " . $e->getMessage());
    $_SESSION['mensaje_error'] = "Error al obtener los datos actuales.";
    header('Location: ../desempleado/perfil.php');
    exit();
}

// ===== CONSTRUIR ARRAY DE ACTUALIZACIONES (SOLO CAMPOS MODIFICADOS) =====
$updates_usuario = [];
$updates_buscador = [];

// ----- Campos de la tabla usuarios -----
if (!empty($nombre) && $nombre !== $usuario_actual['nombre']) {
    $updates_usuario['nombre'] = $nombre;
}
if (!empty($apellidos) && $apellidos !== $usuario_actual['apellidos']) {
    $updates_usuario['apellidos'] = $apellidos;
}
if (!empty($documento) && $documento !== $usuario_actual['documento_identidad']) {
    // Verificar que el nuevo DIP no esté en uso por otro usuario (excepto el propio)
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE documento_identidad = ? AND id != ?");
    $stmt->execute([$documento, $id_usuario]);
    if ($stmt->fetch()) {
        $_SESSION['mensaje_error'] = "El número de DIP ya está registrado por otro usuario.";
        header('Location: ../desempleado/perfil.php');
        exit();
    }
    $updates_usuario['documento_identidad'] = $documento;
}

// ----- Campos de la tabla buscadores_empleo -----
if (!empty($telefono) && $telefono !== $buscador_actual['telefono']) {
    $updates_buscador['telefono'] = $telefono;
}
if (!empty($estado_civil) && $estado_civil !== $buscador_actual['estado_civil']) {
    $updates_buscador['estado_civil'] = $estado_civil;
}
if (!empty($estado_laboral) && $estado_laboral !== $buscador_actual['estado_laboral']) {
    $updates_buscador['estado_laboral'] = $estado_laboral;
}
if (!empty($provincia) && $provincia !== $buscador_actual['provincia']) {
    $updates_buscador['provincia'] = $provincia;
}
if (!empty($ciudad_municipio) && $ciudad_municipio !== $buscador_actual['ciudad_municipio']) {
    $updates_buscador['ciudad_municipio'] = $ciudad_municipio;
}

// ----- Manejo de la foto de perfil -----
$foto_subida = false;
if ($foto_carnet && $foto_carnet['error'] === UPLOAD_ERR_OK) {
    $directorio = '../uploads/fotos/';
    if (!is_dir($directorio)) {
        mkdir($directorio, 0777, true);
    }
    $extension = pathinfo($foto_carnet['name'], PATHINFO_EXTENSION);
    $nombre_archivo = 'foto_' . $id_usuario . '_' . time() . '.' . $extension;
    $ruta_completa = $directorio . $nombre_archivo;

    if (move_uploaded_file($foto_carnet['tmp_name'], $ruta_completa)) {
        // Guardar ruta relativa para la base de datos
        $ruta_relativa = 'uploads/fotos/' . $nombre_archivo;
        $updates_buscador['foto_carnet'] = $ruta_relativa;
        $foto_subida = true;
    } else {
        $_SESSION['mensaje_error'] = "Error al subir la foto de perfil.";
        header('Location: ../desempleado/perfil.php');
        exit();
    }
}

// ===== EJECUTAR ACTUALIZACIONES (SOLO SI HAY CAMBIOS) =====
try {
    $pdo->beginTransaction();

    // Actualizar tabla usuarios
    if (!empty($updates_usuario)) {
        $set_parts = [];
        $params = [];
        foreach ($updates_usuario as $campo => $valor) {
            $set_parts[] = "$campo = ?";
            $params[] = $valor;
        }
        $params[] = $id_usuario;
        $sql = "UPDATE usuarios SET " . implode(', ', $set_parts) . " WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
    }

    // Actualizar tabla buscadores_empleo
    if (!empty($updates_buscador)) {
        $set_parts = [];
        $params = [];
        foreach ($updates_buscador as $campo => $valor) {
            $set_parts[] = "$campo = ?";
            $params[] = $valor;
        }
        $params[] = $id_usuario;
        $sql = "UPDATE buscadores_empleo SET " . implode(', ', $set_parts) . " WHERE usuario_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
    }

    $pdo->commit();

    // Actualizar nombre completo en sesión si se cambió nombre o apellidos
    if (isset($updates_usuario['nombre']) || isset($updates_usuario['apellidos'])) {
        $nuevo_nombre = $updates_usuario['nombre'] ?? $usuario_actual['nombre'];
        $nuevo_apellidos = $updates_usuario['apellidos'] ?? $usuario_actual['apellidos'];
        $_SESSION['nombre_completo'] = $nuevo_nombre . ' ' . $nuevo_apellidos;
    }

    $_SESSION['mensaje_exito'] = "Perfil actualizado correctamente.";
    header('Location: ../desempleado/perfil.php');
    exit();

} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Error al actualizar perfil: " . $e->getMessage());
    $_SESSION['mensaje_error'] = "Error al actualizar el perfil. Intente de nuevo.";
    header('Location: ../desempleado/perfil.php');
    exit();
}
?>
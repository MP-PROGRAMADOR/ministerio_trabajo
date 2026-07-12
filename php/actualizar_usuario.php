<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pagina_destino = '../admin/usuarios.php'; 

require_once '../conexion/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = "Acceso no autorizado.";
    header("Location: $pagina_destino");
    exit();
}

$id                  = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$nombre              = trim($_POST['nombre'] ?? '');
$apellidos           = trim($_POST['apellidos'] ?? '');
$documento_identidad = trim($_POST['documento_identidad'] ?? '');
$nombre_usuario      = trim($_POST['nombre_usuario'] ?? '');
$correo_electronico  = trim($_POST['correo_electronico'] ?? '');
$password            = $_POST['password'] ?? '';
$password_confirm    = $_POST['password_confirm'] ?? '';
$rol                 = $_POST['rol'] ?? 'buscador';
$correo_verificado   = isset($_POST['correo_verificado']) ? 1 : 0;

if (!$id) {
    $_SESSION['error'] = "Identificador de usuario no válido.";
    header("Location: $pagina_destino");
    exit();
}

// 1. Validaciones básicas de campos requeridos
if (empty($nombre) || empty($apellidos) || empty($documento_identidad) || empty($nombre_usuario) || empty($correo_electronico)) {
    $_SESSION['error'] = "Todos los campos obligatorios deben estar rellenos.";
    header("Location: $pagina_destino");
    exit();
}

// 2. Validación de formato de correo
if (!filter_var($correo_electronico, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = "El correo electrónico introducido no tiene un formato válido.";
    header("Location: $pagina_destino");
    exit();
}

// 3. Requisitos del nombre de usuario
if (strlen($nombre_usuario) < 4 || strlen($nombre_usuario) > 30 || !preg_match('/^[a-zA-Z0-9_.]+$/', $nombre_usuario)) {
    $_SESSION['error'] = "El nombre de usuario debe tener entre 4 y 30 caracteres válidos.";
    header("Location: $pagina_destino");
    exit();
}

try {
    // Verificar si el nombre_usuario, correo o documento ya pertenecen a OTRO usuario diferente (id != :id)
    $query_check = "SELECT id, nombre_usuario, correo_electronico, documento_identidad FROM usuarios 
                    WHERE (nombre_usuario = :user OR correo_electronico = :email OR documento_identidad = :dip) 
                    AND id != :id LIMIT 1";
    
    $stmt_check = $pdo->prepare($query_check);
    $stmt_check->execute([
        ':user'  => $nombre_usuario,
        ':email' => $correo_electronico,
        ':dip'   => $documento_identidad,
        ':id'    => $id
    ]);
    
    $usuario_existente = $stmt_check->fetch();
    
    if ($usuario_existente) {
        if ($usuario_existente['nombre_usuario'] === $nombre_usuario) {
            $_SESSION['error'] = "El nombre de usuario <strong>@{$nombre_usuario}</strong> ya pertenece a otro usuario.";
        } elseif ($usuario_existente['correo_electronico'] === $correo_electronico) {
            $_SESSION['error'] = "El correo electrónico ya está en uso por otra cuenta.";
        } elseif ($usuario_existente['documento_identidad'] === $documento_identidad) {
            $_SESSION['error'] = "El Documento de Identidad (DIP/Pasaporte) pertenece a otro registro.";
        }
        header("Location: $pagina_destino");
        exit();
    }

    // Comprobar si se proporcionó una nueva contraseña para actualizarla
    $actualizar_password = false;
    if (!empty($password)) {
        if ($password !== $password_confirm) {
            $_SESSION['error'] = "Las contraseñas escritas no coinciden.";
            header("Location: $pagina_destino");
            exit();
        }

        if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password)) {
            $_SESSION['error'] = "La nueva contraseña debe tener al menos 8 caracteres con mayúsculas, minúsculas y un número.";
            header("Location: $pagina_destino");
            exit();
        }

        $password_encriptada = password_hash($password, PASSWORD_DEFAULT);
        $actualizar_password = true;
    }

    // Preparar la consulta SQL según si se cambia o no la contraseña
    if ($actualizar_password) {
        $sql_update = "UPDATE usuarios SET 
                        nombre = :nombre,
                        apellidos = :apellidos,
                        nombre_usuario = :usuario,
                        correo_electronico = :email,
                        documento_identidad = :dip,
                        password = :pass,
                        rol = :rol,
                        correo_verificado = :verificado
                       WHERE id = :id";
    } else {
        $sql_update = "UPDATE usuarios SET 
                        nombre = :nombre,
                        apellidos = :apellidos,
                        nombre_usuario = :usuario,
                        correo_electronico = :email,
                        documento_identidad = :dip,
                        rol = :rol,
                        correo_verificado = :verificado
                       WHERE id = :id";
    }

    $stmt_update = $pdo->prepare($sql_update);

    $params = [
        ':nombre'     => $nombre,
        ':apellidos'  => $apellidos,
        ':usuario'    => $nombre_usuario,
        ':email'      => $correo_electronico,
        ':dip'        => $documento_identidad,
        ':rol'        => $rol,
        ':verificado' => $correo_verificado,
        ':id'         => $id
    ];

    if ($actualizar_password) {
        $params[':pass'] = $password_encriptada;
    }

    $stmt_update->execute($params);

    $_SESSION['exito'] = "El usuario <strong>@{$nombre_usuario}</strong> ha sido actualizado correctamente.";
    header("Location: $pagina_destino");
    exit();

} catch (PDOException $e) {
    error_log("Error al actualizar usuario: " . $e->getMessage());
    $_SESSION['error'] = "Ocurrió un error en el servidor al actualizar el usuario.";
    header("Location: $pagina_destino");
    exit();
}
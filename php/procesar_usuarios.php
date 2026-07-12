<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirección por defecto al panel de usuarios
$pagina_destino = '../admin/usuarios.php'; 

require_once '../conexion/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = "Acceso no autorizado.";
    header("Location: $pagina_destino");
    exit();
}

// Captura y sanitización de inputs
$nombre              = trim($_POST['nombre'] ?? '');
$apellidos           = trim($_POST['apellidos'] ?? '');
$documento_identidad = trim($_POST['documento_identidad'] ?? '');
$nombre_usuario      = trim($_POST['nombre_usuario'] ?? '');
$correo_electronico  = trim($_POST['correo_electronico'] ?? '');
$password            = $_POST['password'] ?? '';
$password_confirm    = $_POST['password_confirm'] ?? '';
$rol                 = $_POST['rol'] ?? 'buscador';

// Permite definir si el admin lo crea verificado directamente o requiere activación
$correo_verificado   = isset($_POST['correo_verificado']) ? 1 : 0; 

// 1. Validaciones básicas de campos obligatorios
if (empty($nombre) || empty($apellidos) || empty($documento_identidad) || empty($nombre_usuario) || empty($correo_electronico) || empty($password)) {
    $_SESSION['error'] = "Todos los campos obligatorios deben ser cumplimentados.";
    header("Location: $pagina_destino");
    exit();
}

// 2. Validación de coincidencia de contraseñas
if ($password !== $password_confirm) {
    $_SESSION['error'] = "Las contraseñas introducidas no coinciden.";
    header("Location: $pagina_destino");
    exit();
}

// 3. Validación de formato de correo
if (!filter_var($correo_electronico, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = "El formato del correo electrónico no es válido.";
    header("Location: $pagina_destino");
    exit();
}

// 4. Requisitos del nombre de usuario
if (strlen($nombre_usuario) < 4 || strlen($nombre_usuario) > 30 || !preg_match('/^[a-zA-Z0-9_.]+$/', $nombre_usuario)) {
    $_SESSION['error'] = "El nombre de usuario debe tener entre 4 y 30 caracteres (solo letras, números, guion bajo o punto).";
    header("Location: $pagina_destino");
    exit();
}

// 5. Directiva de seguridad de la contraseña
if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password)) {
    $_SESSION['error'] = "La contraseña debe tener al menos 8 caracteres, incluir mayúsculas, minúsculas y un número.";
    header("Location: $pagina_destino");
    exit();
}

try {
    // Verificación de duplicados previos (Usuario, Email o DIP)
    $query_check = "SELECT nombre_usuario, correo_electronico, documento_identidad FROM usuarios 
                    WHERE nombre_usuario = :user OR correo_electronico = :email OR documento_identidad = :dip LIMIT 1";
    
    $stmt_check = $pdo->prepare($query_check);
    $stmt_check->execute([
        ':user'  => $nombre_usuario,
        ':email' => $correo_electronico,
        ':dip'   => $documento_identidad
    ]);
    
    $usuario_existente = $stmt_check->fetch();
    
    if ($usuario_existente) {
        if ($usuario_existente['nombre_usuario'] === $nombre_usuario) {
            $_SESSION['error'] = "El nombre de usuario <strong>@{$nombre_usuario}</strong> ya se encuentra registrado.";
        } elseif ($usuario_existente['correo_electronico'] === $correo_electronico) {
            $_SESSION['error'] = "El correo electrónico ya está vinculado a otra cuenta.";
        } elseif ($usuario_existente['documento_identidad'] === $documento_identidad) {
            $_SESSION['error'] = "El Documento de Identidad (DIP/Pasaporte) ya está registrado.";
        }
        header("Location: $pagina_destino");
        exit();
    }

    // Hash de la contraseña y token único
    $password_encriptada = password_hash($password, PASSWORD_DEFAULT);
    $token_verificacion = bin2hex(random_bytes(32));

    // Generación del Número de Expediente (EG-XXXXX)
    $numero_expediente = "EG-" . rand(10000, 99999);

    // Verificación rápida para evitar colisión de expediente
    $stmt_check_exp = $pdo->prepare("SELECT id FROM usuarios WHERE numero_expediente = :exp LIMIT 1");
    $stmt_check_exp->execute([':exp' => $numero_expediente]);
    if ($stmt_check_exp->fetch()) {
        $numero_expediente = "EG-" . rand(10000, 99999);
    }

    // Inserción en la base de datos
    $sql_insert = "INSERT INTO usuarios (
                    numero_expediente, 
                    nombre, 
                    apellidos, 
                    nombre_usuario, 
                    correo_electronico, 
                    documento_identidad, 
                    password, 
                    rol, 
                    correo_verificado, 
                    token_verificacion
                ) VALUES (
                    :expediente, 
                    :nombre, 
                    :apellidos, 
                    :usuario, 
                    :email, 
                    :dip, 
                    :pass, 
                    :rol, 
                    :verificado, 
                    :token
                )";
    
    $stmt_insert = $pdo->prepare($sql_insert);
    
    $resultado = $stmt_insert->execute([
        ':expediente' => $numero_expediente,
        ':nombre'     => $nombre,
        ':apellidos'  => $apellidos,
        ':usuario'    => $nombre_usuario,
        ':email'      => $correo_electronico,
        ':dip'        => $documento_identidad,
        ':pass'       => $password_encriptada,
        ':rol'        => $rol,
        ':verificado' => $correo_verificado,
        ':token'      => $token_verificacion
    ]);

    // Procesamiento del correo de notificación / activación si no fue verificado automáticamente
    if ($resultado) {
        if ($correo_verificado === 0) {
            if (file_exists('enviar_correo.php')) {
                require_once 'enviar_correo.php';
                $enlace_verificacion = "http://" . $_SERVER['HTTP_HOST'] . "/ministerio_trabajo/php/verificar.php?token=" . $token_verificacion;
                $nombre_completo = $nombre . ' ' . $apellidos;
                
                enviarCorreoVerificacion($correo_electronico, $nombre_completo, $enlace_verificacion);
            }
            $_SESSION['exito'] = "Usuario <strong>@{$nombre_usuario}</strong> creado correctamente con el expediente <strong>{$numero_expediente}</strong> (Pendiente de verificación).";
        } else {
            $_SESSION['exito'] = "Usuario <strong>@{$nombre_usuario}</strong> registrado y verificado correctamente con expediente <strong>{$numero_expediente}</strong>.";
        }

        header("Location: $pagina_destino");
        exit();
    }

} catch (PDOException $e) {
    error_log("Error crítico al guardar usuario: " . $e->getMessage());
    $_SESSION['error'] = "Hubo un error interno en el servidor al procesar el registro.";
    header("Location: $pagina_destino");
    exit();
}
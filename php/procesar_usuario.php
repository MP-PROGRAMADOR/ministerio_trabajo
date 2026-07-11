<?php
session_start();

// Suponiendo que el formulario se llama registro.php, ajusta si es necesario
$pagina_formulario = '../registro_usuario.php'; 

require_once '../conexion/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = "Acceso no autorizado.";
    header("Location: $pagina_formulario");
    exit();
}

$nombre              = trim($_POST['nombre'] ?? '');
$apellidos           = trim($_POST['apellidos'] ?? '');
$documento_identidad = trim($_POST['documento_identidad'] ?? '');
$nombre_usuario      = trim($_POST['nombre_usuario'] ?? '');
$correo_electronico  = trim($_POST['correo_electronico'] ?? '');
$password            = $_POST['password'] ?? '';

$rol_por_defecto     = 'buscador'; 

// Validaciones
if (empty($nombre) || empty($apellidos) || empty($documento_identidad) || empty($nombre_usuario) || empty($correo_electronico) || empty($password)) {
    $_SESSION['error'] = "Todos los campos son estrictamente obligatorios.";
    header("Location: $pagina_formulario");
    exit();
}

if (!filter_var($correo_electronico, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = "El formato del correo electrónico no es válido.";
    header("Location: $pagina_formulario");
    exit();
}

if (strlen($nombre_usuario) < 4 || strlen($nombre_usuario) > 30 || !preg_match('/^[a-zA-Z0-9_.]+$/', $nombre_usuario)) {
    $_SESSION['error'] = "El nombre de usuario no cumple con los requisitos de seguridad.";
    header("Location: $pagina_formulario");
    exit();
}

if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password)) {
    $_SESSION['error'] = "La contraseña no cumple con la directiva de seguridad.";
    header("Location: $pagina_formulario");
    exit();
}

try {
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
            $_SESSION['error'] = "El nombre de usuario ya se encuentra registrado.";
        } elseif ($usuario_existente['correo_electronico'] === $correo_electronico) {
            $_SESSION['error'] = "El correo electrónico ya está vinculado a otra cuenta.";
        } elseif ($usuario_existente['documento_identidad'] === $documento_identidad) {
            $_SESSION['error'] = "El Documento de Identidad (DIP/Pasaporte) ya está registrado.";
        }
        header("Location: $pagina_formulario");
        exit();
    }

    $password_encriptada = password_hash($password, PASSWORD_DEFAULT);
    $token_verificacion = bin2hex(random_bytes(32));

    $sql_insert = "INSERT INTO usuarios (nombre, apellidos, nombre_usuario, correo_electronico, documento_identidad, password, rol, correo_verificado, token_verificacion) 
                   VALUES (:nombre, :apellidos, :usuario, :email, :dip, :pass, :rol, 0, :token)";
    
    $stmt_insert = $pdo->prepare($sql_insert);
    
    $resultado = $stmt_insert->execute([
        ':nombre'    => $nombre,
        ':apellidos' => $apellidos,
        ':usuario'   => $nombre_usuario,
        ':email'     => $correo_electronico,
        ':dip'       => $documento_identidad,
        ':pass'      => $password_encriptada,
        ':rol'       => $rol_por_defecto,
        ':token'     => $token_verificacion
    ]);

    // =======================================================
    // MODIFICACIÓN AQUÍ: PROCESO REAL DE ENVÍO DE CORREO
    // =======================================================
    if ($resultado) {
        // 1. Requerir el archivo
        require_once 'enviar_correo.php';

        // 2. Construir el enlace dinámico (CORREGIDO: Se añade la barra '/' antes de verificar.php)
        $enlace_verificacion = "http://" . $_SERVER['HTTP_HOST'] . "verificar.php?token=" . $token_verificacion;
        
        // 3. Llamar a la función pasándole los datos capturados
        $nombre_completo = $nombre . ' ' . $apellidos;
        
        // Ejecutamos el envío
        $correo_enviado = enviarCorreoVerificacion($correo_electronico, $nombre_completo, $enlace_verificacion);

        if ($correo_enviado) {
            $_SESSION['exito'] = "¡Cuenta creada con éxito! Por favor, revisa tu correo electrónico para verificarla.";
        } else {
            $_SESSION['error'] = "Registro completado, pero no se pudo enviar el correo de confirmación. Contacte a soporte.";
        }
        
        header("Location: $pagina_formulario");
        exit();
    }


} catch (PDOException $e) {
    error_log("Error crítico en el registro: " . $e->getMessage());
    $_SESSION['error'] = "Hubo un error interno. Por favor, inténtelo más tarde.";
    header("Location: $pagina_formulario");
    exit();
}
?>
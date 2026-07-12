<?php
session_start();

// Redirección en caso de error o éxito hacia el formulario de empresas
$pagina_formulario = '../registro_empleadores.php'; 

require_once '../conexion/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = "Acceso no autorizado.";
    header("Location: $pagina_formulario");
    exit();
}

// 1. CAPTURA Y LIMPIEZA DE DATOS DEL REPRESENTANTE / USUARIO
$nombre              = trim($_POST['nombre'] ?? '');
$apellidos           = trim($_POST['apellidos'] ?? '');
$documento_identidad = trim($_POST['documento_identidad'] ?? '');
$nombre_usuario      = trim($_POST['nombre_usuario'] ?? '');
$correo_electronico  = trim($_POST['correo_electronico'] ?? '');
$password            = $_POST['password'] ?? '';

// 2. CAPTURA Y LIMPIEZA DE DATOS DE LA EMPRESA
$nombre_empresa      = trim($_POST['nombre_empresa'] ?? '');
$rnc_ruc             = trim($_POST['rnc_ruc'] ?? '');
$sector_industrial  = trim($_POST['sector_industrial'] ?? '');
$telefono_corp       = trim($_POST['telefono_corporativo'] ?? '');
$direccion           = trim($_POST['direccion'] ?? '');
$sitio_web           = trim($_POST['sitio_web'] ?? '');

$rol                 = 'empleador'; 

// =======================================================
// VALIDACIONES PREVIAS DE CAMPOS OBLIGATORIOS
// =======================================================
if (
    empty($nombre) || empty($apellidos) || empty($documento_identidad) || 
    empty($nombre_usuario) || empty($correo_electronico) || empty($password) ||
    empty($nombre_empresa) || empty($sector_industrial) || empty($telefono_corp) || empty($direccion)
) {
    $_SESSION['error'] = "Todos los campos obligatorios deben ser completados.";
    header("Location: $pagina_formulario");
    exit();
}

// Validar formato del correo electrónico
if (!filter_var($correo_electronico, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = "El formato del correo electrónico no es válido.";
    header("Location: $pagina_formulario");
    exit();
}

// Validar requisitos del nombre de usuario
if (strlen($nombre_usuario) < 4 || strlen($nombre_usuario) > 30 || !preg_match('/^[a-zA-Z0-9_.]+$/', $nombre_usuario)) {
    $_SESSION['error'] = "El nombre de usuario no cumple con los requisitos de seguridad.";
    header("Location: $pagina_formulario");
    exit();
}

// Validar requisitos de la contraseña (Mínimo 8 caracteres, mayúscula, minúscula y número)
if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password)) {
    $_SESSION['error'] = "La contraseña no cumple con la directiva de seguridad.";
    header("Location: $pagina_formulario");
    exit();
}

try {
    // =======================================================
    // VERIFICACIÓN DE UNICIDAD (Usuario, Correo, DIP y RNC/RUC)
    // =======================================================
    $query_check = "SELECT nombre_usuario, correo_electronico, documento_identidad 
                    FROM usuarios 
                    WHERE nombre_usuario = :user OR correo_electronico = :email OR documento_identidad = :dip 
                    LIMIT 1";
    
    $stmt_check = $pdo->prepare($query_check);
    $stmt_check->execute([
        ':user'  => $nombre_usuario,
        ':email' => $correo_electronico,
        ':dip'   => $documento_identidad
    ]);
    
    $usuario_existente = $stmt_check->fetch();
    
    if ($usuario_existente) {
        if ($usuario_existente['correo_electronico'] === $correo_electronico) {
            $_SESSION['error'] = "El correo electrónico ya está vinculado a otra cuenta.";
        } elseif ($usuario_existente['nombre_usuario'] === $nombre_usuario) {
            $_SESSION['error'] = "El nombre de usuario ya se encuentra registrado.";
        } elseif ($usuario_existente['documento_identidad'] === $documento_identidad) {
            $_SESSION['error'] = "El Documento de Identidad (DIP/Pasaporte) ya está registrado.";
        }
        header("Location: $pagina_formulario");
        exit();
    }

    // Verificar si el RNC/RUC ya existe si el usuario proporcionó uno
    if (!empty($rnc_ruc)) {
        $stmt_check_rnc = $pdo->prepare("SELECT id FROM empleadores WHERE rnc_ruc = :rnc LIMIT 1");
        $stmt_check_rnc->execute([':rnc' => $rnc_ruc]);
        if ($stmt_check_rnc->fetch()) {
            $_SESSION['error'] = "El RNC/RUC ingresado ya pertenece a una empresa registrada.";
            header("Location: $pagina_formulario");
            exit();
        }
    }

    // Preparar contraseña y token de verificación
    $password_encriptada = password_hash($password, PASSWORD_DEFAULT);
    $token_verificacion = bin2hex(random_bytes(32));

    // GENERACIÓN DE NÚMERO DE EXPEDIENTE UNICO
    $numero_expediente = "EG-" . rand(10000, 99999);
    $stmt_check_exp = $pdo->prepare("SELECT id FROM usuarios WHERE numero_expediente = :exp LIMIT 1");
    $stmt_check_exp->execute([':exp' => $numero_expediente]);
    if ($stmt_check_exp->fetch()) {
        $numero_expediente = "EG-" . rand(10000, 99999);
    }

    // INICIAR TRANSACCIÓN DE BASE DE DATOS
    $pdo->beginTransaction();

    // 1. Insertar registro en la tabla USUARIOS
    $sql_usuario = "INSERT INTO usuarios (numero_expediente, nombre, apellidos, nombre_usuario, correo_electronico, documento_identidad, password, rol, correo_verificado, token_verificacion) 
                    VALUES (:expediente, :nombre, :apellidos, :usuario, :email, :dip, :pass, :rol, 0, :token)";
    
    $stmt_user = $pdo->prepare($sql_usuario);
    $stmt_user->execute([
        ':expediente' => $numero_expediente,
        ':nombre'     => $nombre,
        ':apellidos'  => $apellidos,
        ':usuario'    => $nombre_usuario,
        ':email'      => $correo_electronico,
        ':dip'        => $documento_identidad,
        ':pass'       => $password_encriptada,
        ':rol'        => $rol,
        ':token'      => $token_verificacion
    ]);

    // Obtener el ID recién insertado en usuarios
    $usuario_id = $pdo->lastInsertId();

    // 2. Insertar registro en la tabla EMPLEADORES
    $sql_empleador = "INSERT INTO empleadores (usuario_id, nombre_empresa, rnc_ruc, sector_industrial, telefono_corporativo, direccion, sitio_web) 
                      VALUES (:usuario_id, :nombre_empresa, :rnc_ruc, :sector, :telefono, :direccion, :sitio_web)";
    
    $stmt_emp = $pdo->prepare($sql_empleador);
    $stmt_emp->execute([
        ':usuario_id'       => $usuario_id,
        ':nombre_empresa'   => $nombre_empresa,
        ':rnc_ruc'          => !empty($rnc_ruc) ? $rnc_ruc : NULL,
        ':sector'           => $sector_industrial,
        ':telefono'         => $telefono_corp,
        ':direccion'        => $direccion,
        ':sitio_web'        => !empty($sitio_web) ? $sitio_web : NULL
    ]);

    // Confirmar cambios en la base de datos
    $pdo->commit();

    // =======================================================
    // PROCESO DE ENVÍO DE CORREO DE VERIFICACIÓN
    // =======================================================
    require_once 'enviar_correo.php';

    $enlace_verificacion = "http://" . $_SERVER['HTTP_HOST'] . "/ministerio_trabajo/php/verificar.php?token=" . $token_verificacion;
    $nombre_completo = $nombre . ' ' . $apellidos;
    
    $correo_enviado = enviarCorreoVerificacion($correo_electronico, $nombre_completo, $enlace_verificacion);

    if ($correo_enviado) {
        $_SESSION['exito'] = "¡Empresa registrada con éxito! Tu expediente institucional es " . $numero_expediente . ". Por favor, revisa tu correo electrónico para activar la cuenta.";
    } else {
        $_SESSION['exito'] = "¡Empresa registrada con éxito! Tu expediente es " . $numero_expediente . ". (Aviso: Correo de activación pendiente de envío).";
    }
    
    header("Location: $pagina_formulario");
    exit();

} catch (PDOException $e) {
    // Revertir cambios si algo falla durante la transacción
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Error crítico en el registro de empleador: " . $e->getMessage());
    $_SESSION['error'] = "Hubo un error interno al registrar la empresa. Por favor, inténtelo más tarde.";
    header("Location: $pagina_formulario");
    exit();
}
?>
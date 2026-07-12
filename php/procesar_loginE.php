<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once '../conexion/conexion.php';

// Validar que la petición llegue por POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Acceso no autorizado.']);
    exit();
}

// Obtener y limpiar los datos recibidos por AJAX (Soporta tanto JSON como POST estándar)
$data = json_decode(file_get_contents("php://input"), true);

$correo   = trim($data['correo'] ?? $_POST['correo'] ?? '');
$password = $data['password'] ?? $_POST['password'] ?? '';

if (empty($correo) || empty($password)) {
    echo json_encode(['status' => 'error', 'message' => 'El correo y la contraseña son obligatorios.']);
    exit();
}

try {
    // 1. Buscar al usuario por su correo electrónico en la tabla usuarios
    $query = "SELECT id, numero_expediente, nombre, apellidos, nombre_usuario, password, rol, correo_verificado 
              FROM usuarios 
              WHERE correo_electronico = :email LIMIT 1";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([':email' => $correo]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // 2. Si el usuario existe y la contraseña coincide
    if ($usuario && password_verify($password, $usuario['password'])) {
        
        // 3. Regla de Negocio: Validar activación de correo
        if ((int)$usuario['correo_verificado'] !== 1) {
            echo json_encode([
                'status' => 'error', 
                'message' => 'Su cuenta aún no ha sido activada. Por favor, revise su correo electrónico para verificarla.'
            ]);
            exit();
        }

        // 4. Regla de Negocio: Validar que el rol corresponda a un empleador
        if ($usuario['rol'] !== 'empleador') {
            echo json_encode([
                'status' => 'error', 
                'message' => 'Esta cuenta no tiene permisos de acceso al portal de empleadores.'
            ]);
            exit();
        }

        // 5. Obtener el perfil corporativo desde la tabla empleadores
        $query_emp = "SELECT id, nombre_empresa, rnc_ruc, sector_industrial, telefono_corporativo 
                      FROM empleadores 
                      WHERE usuario_id = :usuario_id LIMIT 1";
        
        $stmt_emp = $pdo->prepare($query_emp);
        $stmt_emp->execute([':usuario_id' => $usuario['id']]);
        $empleador = $stmt_emp->fetch(PDO::FETCH_ASSOC);

        if (!$empleador) {
            echo json_encode([
                'status' => 'error', 
                'message' => 'No se encontró el perfil corporativo asociado a este usuario.'
            ]);
            exit();
        }

        // 6. Registrar variables de sesión globales de la empresa
        $_SESSION['id_usuario']        = $usuario['id'];
        $_SESSION['numero_expediente'] = $usuario['numero_expediente'];
        $_SESSION['nombre_completo']   = $usuario['nombre'] . ' ' . $usuario['apellidos'];
        $_SESSION['rol']               = $usuario['rol'];
        
        // Datos específicos de la empresa
        $_SESSION['empleador_id']      = $empleador['id'];
        $_SESSION['nombre_empresa']    = $empleador['nombre_empresa'];
        $_SESSION['rnc_ruc']           = $empleador['rnc_ruc'];

        // Ruta de redirección específica para el panel de empleadores
        $redirigir = '../empleador/index.php';

        echo json_encode([
            'status'   => 'success',
            'message'  => '¡Autenticación exitosa! Accediendo al portal corporativo...',
            'redirect' => $redirigir
        ]);
        exit();

    } else {
        // Mensaje genérico por seguridad
        echo json_encode([
            'status'  => 'error', 
            'message' => 'El correo electrónico o la contraseña son incorrectos.'
        ]);
        exit();
    }

} catch (PDOException $e) {
    error_log("Error en Login Empleadores: " . $e->getMessage());
    echo json_encode([
        'status'  => 'error', 
        'message' => 'Hubo un error interno en el servidor. Inténtelo más tarde.'
    ]);
    exit();
}
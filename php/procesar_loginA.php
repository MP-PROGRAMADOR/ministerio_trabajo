<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once '../conexion/conexion.php';

// Validar que la petición llegue por POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Acceso no autorizado.']);
    exit();
}

// Obtener y limpiar los datos recibidos por AJAX
$data = json_decode(file_get_contents("php://input"), true);
$correo   = trim($data['correo'] ?? '');
$password = $data['password'] ?? '';

if (empty($correo) || empty($password)) {
    echo json_encode(['status' => 'error', 'message' => 'El correo y la contraseña son obligatorios.']);
    exit();
}

try {
    // 1. Buscar al usuario por su correo electrónico
    $query = "SELECT id, numero_expediente, nombre, apellidos, nombre_usuario, password, rol, correo_verificado 
              FROM usuarios 
              WHERE correo_electronico = :email LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':email' => $correo]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // 2. Comprobar existencia de usuario y contraseña encriptada
    if ($usuario && password_verify($password, $usuario['password'])) {
        
        // 3. REGLA DE NEGOCIO 1: Verificar privilégios (Solo 'administrador' o 'ministerio')
        $roles_permitidos = ['administrador', 'ministerio'];
        if (!in_array($usuario['rol'], $roles_permitidos)) {
            echo json_encode([
                'status' => 'error', 
                'message' => 'Acceso denegado. Esta cuenta no cuenta con privilegios administrativos o ministeriales.'
            ]);
            exit();
        }

        // 4. REGLA DE NEGOCIO 2: Validar si la cuenta está activada/verificada
        if ((int)$usuario['correo_verificado'] !== 1) {
            echo json_encode([
                'status' => 'error', 
                'message' => 'Su cuenta administrativa aún no ha sido activada por el Departamento de Informática.'
            ]);
            exit();
        }

        // 5. Credenciales y rol válidos: Iniciar la sesión global del sistema
        $_SESSION['id_usuario']      = $usuario['id'];
        $_SESSION['expediente']      = $usuario['numero_expediente'];
        $_SESSION['nombre_completo']  = $usuario['nombre'] . ' ' . $usuario['apellidos'];
        $_SESSION['nombre_usuario']  = $usuario['nombre_usuario'];
        $_SESSION['rol']             = $usuario['rol'];

        // Definir la ruta de redirección según el perfil administrativo
        $redirigir = '../admin/index.php';

        echo json_encode([
            'status' => 'success',
            'message' => '¡Autenticación exitosa! Accediendo al panel administrativo...',
            'redirect' => $redirigir
        ]);
        exit();

    } else {
        // Mensaje genérico de seguridad
        echo json_encode([
            'status' => 'error', 
            'message' => 'El correo electrónico o la contraseña son incorrectos.'
        ]);
        exit();
    }

} catch (PDOException $e) {
    error_log("Error en el Login Administrativo: " . $e->getMessage());
    echo json_encode([
        'status' => 'error', 
        'message' => 'Hubo un error interno en el servidor de identidad.'
    ]);
    exit();
}
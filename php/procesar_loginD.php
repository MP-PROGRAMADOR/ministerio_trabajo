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
    $query = "SELECT id, nombre, apellidos,nombre_usuario, password, rol, correo_verificado FROM usuarios WHERE correo_electronico = :email LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':email' => $correo]);
    $usuario = $stmt->fetch();

    // 2. Si el usuario existe, comprobar la contraseña encriptada
    if ($usuario && password_verify($password, $usuario['password'])) {
        
        // 3. REGRA DE NEGOCIO: Validar si verificó su correo
        if ((int)$usuario['correo_verificado'] !== 1) {
            echo json_encode([
                'status' => 'error', 
                'message' => 'Su cuenta aún no ha sido activada. Por favor, revise su correo electrónico para verificarla.'
            ]);
            exit();
        }

        // 4. Credenciales correctas: Iniciar la sesión global del sistema
        $_SESSION['id_usuario'] = $usuario['id'];
        $_SESSION['nombre_completo'] = $usuario['nombre'] . ' ' . $usuario['apellidos'];
        $_SESSION['rol'] = $usuario['rol'];

        // Definir a dónde enviarlo según su rol (buscador, administrador, etc.)
        $redirigir = 'dashboard_buscador.php';
        if ($usuario['rol'] === 'admin') {
            $redirigir = '../desempleado/index.php';
        }

          $redirigir = 'dashboard_buscador.php';
        if ($usuario['rol'] === 'buscador') {
            $redirigir = '../desempleado/index.php';
        }

        echo json_encode([
            'status' => 'success',
            'message' => '¡Autenticación exitosa! Iniciando entorno...',
            'redirect' => $redirigir
        ]);
        exit();

    } else {
        // Mensaje genérico por seguridad (no dar pistas de si falló el correo o la clave)
        echo json_encode(['status' => 'error', 'message' => 'El correo electrónico o la contraseña son incorrectos.']);
        exit();
    }

} catch (PDOException $e) {
    error_log("Error en el Login: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Hubo un error interno en el servidor de identidad.']);
    exit();
}
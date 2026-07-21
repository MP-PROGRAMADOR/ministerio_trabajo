<?php
session_start();
require_once '../conexion/conexion.php'; // Tu archivo de conexión PDO ($pdo)

// Solo aceptar peticiones vía POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../empleador/candidatos.php');
    exit();
}

// Validar que el empleador tenga una sesión activa
if (!isset($_SESSION['empleador_id']) || empty($_SESSION['empleador_id'])) {
    $_SESSION['alerta'] = [
        'tipo' => 'danger',
        'mensaje' => 'Sesión expirada. Inicie sesión nuevamente.'
    ];
    header('Location: ../login_empleadores.php');
    exit();
}

$empleador_id = $_SESSION['empleador_id'];

// Capturar datos
$notificacion_id = filter_input(INPUT_POST, 'notificacion_id', FILTER_VALIDATE_INT);
$estado_ministerio = isset($_POST['estado_ministerio']) ? trim($_POST['estado_ministerio']) : 'en_revision';

// -------------------------------------------------------------------------
// VALIDACIONES
// -------------------------------------------------------------------------
$errores = [];

if (!$notificacion_id) {
    $errores[] = "Identificador de notificación no válido.";
}

$estados_validos = ['pendiente', 'en_revision', 'aprobado', 'rechazado', 'contratado'];

if (!in_array($estado_ministerio, $estados_validos)) {
    $errores[] = "El estado especificado no es válido.";
}

if (!empty($errores)) {
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => implode(' ', $errores)]);
        exit();
    }

    $_SESSION['alerta'] = [
        'tipo' => 'danger',
        'mensaje' => implode('<br>', $errores)
    ];
    header('Location: ../empleador/candidatos.php');
    exit();
}

// -------------------------------------------------------------------------
// ACTUALIZACIÓN EN BASE DE DATOS
// -------------------------------------------------------------------------
try {
    // Si prefieres usar solo 'id' en el UPDATE:
    $sql = "UPDATE notificaciones_intermediacion 
            SET estado_ministerio = :estado_ministerio
            WHERE id = :id AND empleador_id = :empleador_id"; // <-- Mantenemos empleador_id por seguridad

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':estado_ministerio' => $estado_ministerio,
        ':id'                => $notificacion_id,
        ':empleador_id'      => $empleador_id
    ]);

    $es_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

    // Nota: $stmt->execute() devuelve true si la consulta fue sintácticamente correcta y se ejecutó
    if ($es_ajax) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true, 
            'message' => 'El estado se ha actualizado a en_revision correctamente.'
        ]);
        exit();
    }

    $_SESSION['alerta'] = [
        'tipo' => 'success',
        'mensaje' => 'El estado de la intermediación ha sido actualizado correctamente.'
    ];

} catch (PDOException $e) {
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
        exit();
    }

    $_SESSION['alerta'] = [
        'tipo' => 'danger',
        'mensaje' => 'Error al actualizar el estado: ' . $e->getMessage()
    ];
}

header('Location: ../empleador/candidatos.php');
exit();
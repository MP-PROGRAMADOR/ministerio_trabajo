<?php
session_start();
require_once '../conexion/conexion.php'; // Tu archivo de conexión PDO ($pdo)

// Solo aceptar peticiones vía POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../empleador/candidatos.php');
    exit();
}

// Validar sesión activa del empleador
if (!isset($_SESSION['empleador_id']) || empty($_SESSION['empleador_id'])) {
    $_SESSION['alerta'] = [
        'tipo' => 'danger',
        'mensaje' => 'Sesión expirada. Inicie sesión nuevamente.'
    ];
    header('Location: ../login_empleadores.php');
    exit();
}

$empleador_id = $_SESSION['empleador_id'];

// Capturar ambos IDs y el estado objetivo
$notificacion_id = filter_input(INPUT_POST, 'notificacion_id', FILTER_VALIDATE_INT);
$postulacion_id  = filter_input(INPUT_POST, 'postulacion_id', FILTER_VALIDATE_INT);
$estado_ministerio = isset($_POST['estado_ministerio']) ? trim($_POST['estado_ministerio']) : 'en_revision';

// -------------------------------------------------------------------------
// VALIDACIONES
// -------------------------------------------------------------------------
$errores = [];

if (!$notificacion_id) {
    $errores[] = "Identificador de notificación no válido.";
}

if (!$postulacion_id) {
    $errores[] = "Identificador de postulación no válido.";
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
// ACTUALIZACIÓN EN BASE DE DATOS CON TRANSACCIÓN
// -------------------------------------------------------------------------
try {
    // Iniciar transacción para asegurar atomicidad
    $pdo->beginTransaction();

    // 1. Actualizar estado en notificaciones_intermediacion
    $sqlNotif = "UPDATE notificaciones_intermediacion 
                 SET estado_ministerio = :estado_ministerio
                 WHERE id = :id AND empleador_id = :empleador_id";
    
    $stmtNotif = $pdo->prepare($sqlNotif);
    $stmtNotif->execute([
        ':estado_ministerio' => $estado_ministerio,
        ':id'                => $notificacion_id,
        ':empleador_id'      => $empleador_id
    ]);

    // 2. Actualizar estado a 'revisado' en postulaciones
    // Nos aseguramos de validar que la postulación pertenezca a una oferta de este empleador
    $sqlPost = "UPDATE postulaciones p
                JOIN ofertas_empleo o ON p.oferta_id = o.id
                SET p.estado = 'revisado'
                WHERE p.id = :postulacion_id AND o.empleador_id = :empleador_id";

    $stmtPost = $pdo->prepare($sqlPost);
    $stmtPost->execute([
        ':postulacion_id' => $postulacion_id,
        ':empleador_id'   => $empleador_id
    ]);

    // Confirmar los cambios
    $pdo->commit();

    $es_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

    if ($es_ajax) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true, 
            'message' => 'Estados actualizados correctamente en notificación y postulación.'
        ]);
        exit();
    }

    $_SESSION['alerta'] = [
        'tipo' => 'success',
        'mensaje' => 'La notificación y la postulación han sido actualizadas correctamente.'
    ];

} catch (PDOException $e) {
    // Revertir transacción si ocurre un error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
        exit();
    }

    $_SESSION['alerta'] = [
        'tipo' => 'danger',
        'mensaje' => 'Error al actualizar los estados: ' . $e->getMessage()
    ];
}

header('Location: ../empleador/candidatos.php');
exit();
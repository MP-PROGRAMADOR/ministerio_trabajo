<?php
session_start();
require_once '../conexion/conexion.php'; // Conexión PDO ($pdo)

header('Content-Type: application/json');

// Solo aceptar peticiones POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit();
}

// Captura de datos
$notificacion_id          = filter_input(INPUT_POST, 'notificacion_id', FILTER_VALIDATE_INT);
$buscador_id              = filter_input(INPUT_POST, 'buscador_id', FILTER_VALIDATE_INT);
$postulacion_id           = filter_input(INPUT_POST, 'postulacion_id', FILTER_VALIDATE_INT);
$estado_ministerio        = isset($_POST['estado_ministerio']) ? trim($_POST['estado_ministerio']) : '';
$observaciones_ministerio = isset($_POST['observaciones_ministerio']) ? trim($_POST['observaciones_ministerio']) : null;

// Validaciones
if (!$notificacion_id || !$postulacion_id || !$buscador_id) {
    echo json_encode(['success' => false, 'message' => 'Identificadores faltantes o no válidos.']);
    exit();
}

$estados_permitidos = ['aprobado', 'rechazado'];
if (!in_array($estado_ministerio, $estados_permitidos)) {
    echo json_encode(['success' => false, 'message' => 'Estado no permitido.']);
    exit();
}

try {
    $pdo->beginTransaction();

    // 1. Preparar campos de credencial solo si es 'aprobado'
    $numero_credencial = null;
    $fecha_emision = null;

    if ($estado_ministerio === 'aprobado') {
        // Generar un código único de credencial: CRED-AÑO-ID-RANDOM
        $anioActual = date('Y');
        $randomHex  = strtoupper(substr(bin2hex(random_bytes(2)), 0, 4));
        $numero_credencial = "CRED-{$anioActual}-{$notificacion_id}-{$randomHex}";
        
        // Fecha y hora actual de emisión
        $fecha_emision = date('Y-m-d H:i:s');
    }

    // 2. Actualizar notificaciones_intermediacion
    $sqlNotif = "UPDATE notificaciones_intermediacion 
                 SET estado_ministerio        = :estado_ministerio,
                     observaciones_ministerio = :observaciones,
                     numero_credencial        = :numero_credencial,
                     fecha_emision_credencial = :fecha_emision
                 WHERE id = :id AND buscador_id = :buscador_id";

    $stmtNotif = $pdo->prepare($sqlNotif);
    $stmtNotif->execute([
        ':estado_ministerio' => $estado_ministerio,
        ':observaciones'     => $observaciones_ministerio,
        ':numero_credencial' => $numero_credencial,
        ':fecha_emision'     => $fecha_emision,
        ':id'                => $notificacion_id,
        ':buscador_id'       => $buscador_id
    ]);

    // 3. Determinar estado para la tabla postulaciones
    // 'aprobado' -> 'interesado'
    // 'rechazado' -> 'rechazado'
    $estado_postulacion = ($estado_ministerio === 'aprobado') ? 'interesado' : 'rechazado';

    $sqlPost = "UPDATE postulaciones 
                SET estado = :estado_postulacion
                WHERE id = :id AND buscador_id = :buscador_id";

    $stmtPost = $pdo->prepare($sqlPost);
    $stmtPost->execute([
        ':estado_postulacion' => $estado_postulacion,
        ':id'                 => $postulacion_id,
        ':buscador_id'        => $buscador_id
    ]);

    $pdo->commit();

    echo json_encode([
        'success'           => true,
        'message'           => 'Resolución registrada correctamente.',
        'numero_credencial' => $numero_credencial // Lo devolvemos por si lo quieres mostrar en un Swal alert o UI
    ]);
    exit();

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode([
        'success' => false,
        'message' => 'Error en la base de datos: ' . $e->getMessage()
    ]);
    exit();
}
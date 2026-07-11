<?php
// 1. INICIAR SESIÓN Y CONEXIÓN
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');
require_once '../conexion/conexion.php'; 

$entidad_id = filter_input(INPUT_GET, 'entidad_id', FILTER_VALIDATE_INT);

if (!$entidad_id) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Identificador de entidad no válido.'
    ]);
    exit();
}

try {
    // 2. CONSULTAR CURSOS VINCULADOS MEDIANTE LA FK entidad_id
    $sql = "SELECT 
                codigo_curso, 
                titulo_curso, 
                descripcion_curso, 
                duracion_horas, 
                modalidad, 
                DATE_FORMAT(fecha_inicio, '%d/%m/%Y') AS fecha_inicio, 
                DATE_FORMAT(fecha_fin, '%d/%m/%Y') AS fecha_fin, 
                cupos_maximos, 
                estado 
            FROM cursos 
            WHERE entidad_id = :entidad_id 
            ORDER BY id DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':entidad_id' => $entidad_id]);
    $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => 'success',
        'cursos' => $cursos
    ]);

} catch (PDOException $e) {
    error_log("Error al consultar cursos por entidad: " . $e->getMessage());
    echo json_encode([
        'status'  => 'error',
        'message' => 'Error en el servidor al consultar los cursos.'
    ]);
}
exit();
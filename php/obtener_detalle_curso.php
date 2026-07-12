<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');
require_once '../conexion/conexion.php'; 

$curso_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$curso_id) {
    echo json_encode([
        'status'  => 'error', 
        'message' => 'Identificador de curso no válido.'
    ]);
    exit();
}

try {
    // Consulta basada en db.sql relacionando cursos con entidades_formadoras
    $sql = "SELECT 
                c.id,
                c.codigo_curso,
                c.titulo_curso,
                c.descripcion_curso,
                c.imagen_portada,
                c.duracion_horas,
                c.modalidad,
                DATE_FORMAT(c.fecha_inicio, '%d/%m/%Y') AS fecha_inicio,
                DATE_FORMAT(c.fecha_fin, '%d/%m/%Y') AS fecha_fin,
                c.cupos_maximos,
                c.estado,
                e.nombre_entidad,
                e.siglas,
                e.provincia
            FROM cursos c
            INNER JOIN entidades_formadoras e ON c.entidad_id = e.id
            WHERE c.id = :id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $curso_id]);
    $curso = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$curso) {
        echo json_encode([
            'status'  => 'error', 
            'message' => 'El curso solicitado no existe en la base de datos.'
        ]);
        exit();
    }

    echo json_encode([
        'status' => 'success',
        'curso'  => $curso
    ]);

} catch (PDOException $e) {
    error_log("Error al consultar detalles del curso: " . $e->getMessage());
    echo json_encode([
        'status'  => 'error', 
        'message' => 'Error de servidor al procesar la consulta.'
    ]);
}
exit();
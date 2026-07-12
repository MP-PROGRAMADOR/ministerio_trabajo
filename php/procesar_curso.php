<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../conexion/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Generación Automática del Código de Curso (Ej: CUR-2026-X89A)
    $codigo_curso = 'CUR-' . date('Y') . '-' . strtoupper(substr(uniqid(), -4));

    $titulo_curso      = trim($_POST['titulo_curso']);
    $entidad_id        = filter_input(INPUT_POST, 'entidad_id', FILTER_VALIDATE_INT);
    $duracion_horas    = filter_input(INPUT_POST, 'duracion_horas', FILTER_VALIDATE_INT);
    $modalidad         = $_POST['modalidad'] ?? 'presencial';
    $fecha_inicio      = !empty($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : NULL;
    $fecha_fin         = !empty($_POST['fecha_fin']) ? $_POST['fecha_fin'] : NULL;
    $cupos_maximos     = filter_input(INPUT_POST, 'cupos_maximos', FILTER_VALIDATE_INT) ?: 30;
    $estado            = $_POST['estado'] ?? 'activo';
    $descripcion_curso = trim($_POST['descripcion_curso']);

    // Ruta base por defecto si no suben imagen
    $imagen_portada = 'uploads/img/cursos/default.jpg';

    // Procesamiento de Imagen de Portada
    if (isset($_FILES['imagen_portada']) && $_FILES['imagen_portada']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath   = $_FILES['imagen_portada']['tmp_name'];
        $fileName      = $_FILES['imagen_portada']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($fileExtension, $allowedExtensions)) {
            $newFileName   = 'curso_' . time() . '_' . uniqid() . '.' . $fileExtension;
            
            // Directorio físico donde se guardarán los archivos
            $uploadFileDir = '../uploads/img/cursos/';

            // Crea las carpetas recursivamente (uploads/img/cursos) si no existen
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }

            $dest_path = $uploadFileDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                // Ruta relativa guardada en la BD
                $imagen_portada = 'uploads/img/cursos/' . $newFileName;
            }
        }
    }

    try {
        $sql = "INSERT INTO cursos (codigo_curso, titulo_curso, descripcion_curso, imagen_portada, entidad_id, duracion_horas, modalidad, fecha_inicio, fecha_fin, cupos_maximos, estado) 
                VALUES (:codigo, :titulo, :descripcion, :portada, :entidad_id, :duracion, :modalidad, :f_inicio, :f_fin, :cupos, :estado)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':codigo'      => $codigo_curso,
            ':titulo'      => $titulo_curso,
            ':descripcion' => $descripcion_curso,
            ':portada'     => $imagen_portada,
            ':entidad_id'  => $entidad_id,
            ':duracion'    => $duracion_horas,
            ':modalidad'   => $modalidad,
            ':f_inicio'    => $fecha_inicio,
            ':f_fin'       => $fecha_fin,
            ':cupos'       => $cupos_maximos,
            ':estado'      => $estado
        ]);

        $_SESSION['exito'] = "Curso registrado con éxito bajo el código <strong>{$codigo_curso}</strong>.";
    } catch (PDOException $e) {
        error_log("Error al guardar curso: " . $e->getMessage());
        $_SESSION['error'] = "Error al intentar registrar el curso.";
    }

    header("Location: ../admin/capacitaciones.php");
    exit();
}
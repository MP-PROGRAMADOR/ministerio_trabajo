<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../conexion/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = "Acceso no permitido.";
    header("Location: ../admin/capacitaciones.php");
    exit();
}

$id                = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$titulo_curso      = trim($_POST['titulo_curso']);
$entidad_id        = filter_input(INPUT_POST, 'entidad_id', FILTER_VALIDATE_INT);
$duracion_horas    = filter_input(INPUT_POST, 'duracion_horas', FILTER_VALIDATE_INT);
$modalidad         = $_POST['modalidad'] ?? 'presencial';
$fecha_inicio      = !empty($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : NULL;
$fecha_fin         = !empty($_POST['fecha_fin']) ? $_POST['fecha_fin'] : NULL;
$cupos_maximos     = filter_input(INPUT_POST, 'cupos_maximos', FILTER_VALIDATE_INT) ?: 30;
$estado            = $_POST['estado'] ?? 'activo';
$descripcion_curso = trim($_POST['descripcion_curso']);
$imagen_portada    = $_POST['imagen_actual']; // Por defecto mantiene la actual

if (!$id) {
    $_SESSION['error'] = "Identificador de curso no válido.";
    header("Location: ../admin/capacitaciones.php");
    exit();
}

// Procesar Subida de Nueva Imagen (Si aplica)
if (isset($_FILES['imagen_portada']) && $_FILES['imagen_portada']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath   = $_FILES['imagen_portada']['tmp_name'];
    $fileName      = $_FILES['imagen_portada']['name'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];

    if (in_array($fileExtension, $allowedExtensions)) {
        $newFileName   = 'curso_' . time() . '_' . uniqid() . '.' . $fileExtension;
        
        // Directorio físico donde se guardarán las imágenes
        $uploadFileDir = '../uploads/img/cursos/';

        // Crea la estructura de directorios recursivamente si no existe
        if (!is_dir($uploadFileDir)) {
            mkdir($uploadFileDir, 0755, true);
        }

        $dest_path = $uploadFileDir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            // Eliminar la imagen anterior si existe y no es la por defecto
            if (!empty($_POST['imagen_actual']) && $_POST['imagen_actual'] !== 'uploads/img/cursos/default.jpg' && $_POST['imagen_actual'] !== 'img/cursos/default.jpg') {
                $oldFile = '../' . $_POST['imagen_actual'];
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
            }
            
            // Nueva ruta relativa guardada en la BD
            $imagen_portada = 'uploads/img/cursos/' . $newFileName;
        }
    }
}

try {
    $sql = "UPDATE cursos SET 
                titulo_curso      = :titulo,
                descripcion_curso = :descripcion,
                imagen_portada    = :portada,
                entidad_id        = :entidad_id,
                duracion_horas    = :duracion,
                modalidad         = :modalidad,
                fecha_inicio      = :f_inicio,
                fecha_fin         = :f_fin,
                cupos_maximos     = :cupos,
                estado            = :estado
            WHERE id = :id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':titulo'      => $titulo_curso,
        ':descripcion' => $descripcion_curso,
        ':portada'     => $imagen_portada,
        ':entidad_id'  => $entidad_id,
        ':duracion'    => $duracion_horas,
        ':modalidad'   => $modalidad,
        ':f_inicio'    => $fecha_inicio,
        ':f_fin'       => $fecha_fin,
        ':cupos'       => $cupos_maximos,
        ':estado'      => $estado,
        ':id'          => $id
    ]);

    $_SESSION['exito'] = "Curso <strong>{$titulo_curso}</strong> actualizado con éxito.";
} catch (PDOException $e) {
    error_log("Error al actualizar curso: " . $e->getMessage());
    $_SESSION['error'] = "Ocurrió un error en el servidor al intentar actualizar el curso.";
}

header("Location: ../admin/capacitaciones.php");
exit();
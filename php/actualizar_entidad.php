<?php
// 1. INICIAR SESIÓN Y CONEXIÓN
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../conexion/conexion.php'; 

// 2. VERIFICAR QUE LA PETICIÓN SEA DE TIPO POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = "Acceso no permitido.";
    header("Location: ../admin/entidades.php");
    exit();
}

// 3. RECUPERAR Y SANITIZAR ENTRADAS
$id                   = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$nombre_entidad       = trim(filter_input(INPUT_POST, 'nombre_entidad', FILTER_SANITIZE_SPECIAL_CHARS));
$siglas               = trim(filter_input(INPUT_POST, 'siglas', FILTER_SANITIZE_SPECIAL_CHARS));
$tipo_entidad         = trim(filter_input(INPUT_POST, 'tipo_entidad', FILTER_SANITIZE_SPECIAL_CHARS));
$provincia            = trim(filter_input(INPUT_POST, 'provincia', FILTER_SANITIZE_SPECIAL_CHARS));
$responsable_contacto = trim(filter_input(INPUT_POST, 'responsable_contacto', FILTER_SANITIZE_SPECIAL_CHARS));
$telefono             = trim(filter_input(INPUT_POST, 'telefono', FILTER_SANITIZE_SPECIAL_CHARS));
$correo_electronico   = filter_input(INPUT_POST, 'correo_electronico', FILTER_VALIDATE_EMAIL);
$direccion            = trim(filter_input(INPUT_POST, 'direccion', FILTER_SANITIZE_SPECIAL_CHARS));

// 4. VALIDACIONES DE SERVIDOR
$errores = [];

if (!$id) {
    $errores[] = "Identificador de entidad no válido.";
}

if (empty($nombre_entidad)) {
    $errores[] = "El nombre de la entidad es obligatorio.";
}

$tipos_permitidos = ['publica', 'privada', 'ong', 'internacional'];
if (!in_array($tipo_entidad, $tipos_permitidos)) {
    $errores[] = "El tipo de entidad seleccionado no es válido.";
}

$provincias_permitidas = [
    'Bioko Norte', 'Litoral', 'Wele-Nzas', 'Kie-Ntem', 
    'Centro Sur', 'Bioko Sur', 'Annobón', 'Djibloho'
];
if (!in_array($provincia, $provincias_permitidas)) {
    $errores[] = "La provincia seleccionada no es válida.";
}

if (!empty($_POST['correo_electronico']) && !$correo_electronico) {
    $errores[] = "El formato del correo electrónico no es correcto.";
}

// SI HAY ERRORES, RETORNAR CON MENSAJE
if (!empty($errores)) {
    $_SESSION['error'] = implode('<br>', $errores);
    header("Location: ../admin/entidades.php");
    exit();
}

try {
    // 5. ACTUALIZAR EN BASE DE DATOS
    $sql = "UPDATE entidades_formadoras SET 
                nombre_entidad       = :nombre_entidad,
                siglas               = :siglas,
                tipo_entidad         = :tipo_entidad,
                provincia            = :provincia,
                responsable_contacto = :responsable_contacto,
                telefono             = :telefono,
                correo_electronico   = :correo_electronico,
                direccion            = :direccion
            WHERE id = :id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nombre_entidad'       => $nombre_entidad,
        ':siglas'               => !empty($siglas) ? $siglas : NULL,
        ':tipo_entidad'         => $tipo_entidad,
        ':provincia'            => $provincia,
        ':responsable_contacto' => !empty($responsable_contacto) ? $responsable_contacto : NULL,
        ':telefono'             => !empty($telefono) ? $telefono : NULL,
        ':correo_electronico'   => $correo_electronico ? $correo_electronico : NULL,
        ':direccion'            => !empty($direccion) ? $direccion : NULL,
        ':id'                   => $id
    ]);

    $_SESSION['exito'] = "Entidad <strong>{$nombre_entidad}</strong> actualizada con éxito.";

} catch (PDOException $e) {
    error_log("Error al actualizar entidad: " . $e->getMessage());
    $_SESSION['error'] = "Ocurrió un error en el servidor al actualizar la entidad.";
}

header("Location: ../admin/entidades.php");
exit();
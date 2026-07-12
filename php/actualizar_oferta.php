<?php
session_start();
require_once '../conexion/conexion.php'; // Tu archivo de conexión PDO ($pdo)

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ofertas.php');
    exit();
}

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
$id              = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$titulo_puesto   = isset($_POST['titulo_puesto']) ? trim($_POST['titulo_puesto']) : '';
$provincia       = isset($_POST['provincia']) ? trim($_POST['provincia']) : '';
$salario_input   = isset($_POST['salario_ofrecido']) ? trim($_POST['salario_ofrecido']) : '';
$estado          = isset($_POST['estado']) ? trim($_POST['estado']) : 'abierta';
$descripcion     = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
$requisitos      = isset($_POST['requisitos']) ? trim($_POST['requisitos']) : '';

// -------------------------------------------------------------------------
// VALIDACIONES
// -------------------------------------------------------------------------
$errores = [];

if (!$id) {
    $errores[] = "Identificador de oferta no válido.";
}

$provincias_validas = ['Bioko Norte', 'Litoral', 'Bioko Sur', 'Centro Sur', 'Kie-Ntem', 'Wele-Nzas', 'Annobón', 'Djibloho'];

if (empty($titulo_puesto) || strlen($titulo_puesto) < 3) {
    $errores[] = "El título del puesto debe tener al menos 3 caracteres.";
}

if (!in_array($provincia, $provincias_validas)) {
    $errores[] = "Seleccione una provincia válida.";
}

if (!in_array($estado, ['abierta', 'cerrada'])) {
    $estado = 'abierta';
}

$salario = null;
if ($salario_input !== '') {
    if (!is_numeric($salario_input) || floatval($salario_input) < 0) {
        $errores[] = "El salario debe ser un número positivo.";
    } else {
        $salario = floatval($salario_input);
    }
}

if (empty($descripcion) || strlen($descripcion) < 10) {
    $errores[] = "La descripción debe tener al menos 10 caracteres.";
}

if (empty($requisitos) || strlen($requisitos) < 10) {
    $errores[] = "Los requisitos deben tener al menos 10 caracteres.";
}

if (!empty($errores)) {
    $_SESSION['alerta'] = [
        'tipo' => 'danger',
        'mensaje' => implode('<br>', $errores)
    ];
    header('Location: ofertas.php');
    exit();
}

// -------------------------------------------------------------------------
// ACTUALIZACIÓN EN BASE DE DATOS
// -------------------------------------------------------------------------
try {
    // Validamos empleador_id para asegurar que solo edite SUS PROPIAS ofertas
    $sql = "UPDATE ofertas_empleo 
            SET titulo_puesto = :titulo_puesto,
                descripcion   = :descripcion,
                requisitos    = :requisitos,
                provincia     = :provincia,
                salario_ofrecido = :salario_ofrecido,
                estado        = :estado
            WHERE id = :id AND empleador_id = :empleador_id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':titulo_puesto'   => $titulo_puesto,
        ':descripcion'     => $descripcion,
        ':requisitos'      => $requisitos,
        ':provincia'       => $provincia,
        ':salario_ofrecido'=> $salario,
        ':estado'          => $estado,
        ':id'              => $id,
        ':empleador_id'    => $empleador_id
    ]);

    if ($stmt->rowCount() >= 0) {
        $_SESSION['alerta'] = [
            'tipo' => 'success',
            'mensaje' => '¡La oferta de empleo ha sido actualizada correctamente!'
        ];
    } else {
        $_SESSION['alerta'] = [
            'tipo' => 'warning',
            'mensaje' => 'No se realizaron cambios o la oferta no pertenece a su cuenta.'
        ];
    }

} catch (PDOException $e) {
    $_SESSION['alerta'] = [
        'tipo' => 'danger',
        'mensaje' => 'Error al actualizar: ' . $e->getMessage()
    ];
}

header('Location: ../empleador/ofertas_empleo.php');
exit();
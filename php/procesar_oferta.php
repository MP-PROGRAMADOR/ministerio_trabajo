<?php
session_start();
require_once '../conexion/conexion.php'; // Tu archivo de conexión PDO ($pdo)

// Verificar que la petición sea vía POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ofertas.php');
    exit();
}

// Verificar que el empleador esté autenticado
if (!isset($_SESSION['empleador_id']) || empty($_SESSION['empleador_id'])) {
    $_SESSION['alerta'] = [
        'tipo' => 'danger',
        'mensaje' => 'Sesión no válida o expirada. Por favor, inicie sesión nuevamente.'
    ];
    header('Location: ../login_empleadores.php');
    exit();
}

$empleador_id = $_SESSION['empleador_id'];

// Capturar y limpiar datos del formulario
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

// Lista de provincias válidas (según la BD de Guinea Ecuatorial)
$provincias_validas = ['Bioko Norte', 'Litoral', 'Bioko Sur', 'Centro Sur', 'Kie-Ntem', 'Wele-Nzas', 'Annobón', 'Djibloho'];

if (empty($titulo_puesto) || strlen($titulo_puesto) < 3) {
    $errores[] = "El título del puesto es obligatorio y debe tener al menos 3 caracteres.";
}

if (!in_array($provincia, $provincias_validas)) {
    $errores[] = "Debe seleccionar una provincia válida.";
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
    $errores[] = "La descripción del puesto es obligatoria (mínimo 10 caracteres).";
}

if (empty($requisitos) || strlen($requisitos) < 10) {
    $errores[] = "Los requisitos son obligatorios (mínimo 10 caracteres).";
}

// Si existen errores, guardamos el alerta y redirigimos
if (!empty($errores)) {
    $_SESSION['alerta'] = [
        'tipo' => 'danger',
        'mensaje' => implode('<br>', $errores)
    ];
    header('Location: ofertas.php');
    exit();
}

// -------------------------------------------------------------------------
// INSERCIÓN EN BASE DE DATOS
// -------------------------------------------------------------------------
try {
    $sql = "INSERT INTO ofertas_empleo 
            (empleador_id, titulo_puesto, descripcion, requisitos, provincia, salario_ofrecido, estado) 
            VALUES (:empleador_id, :titulo_puesto, :descripcion, :requisitos, :provincia, :salario_ofrecido, :estado)";

    $stmt = $pdo->prepare($sql);
    $resultado = $stmt->execute([
        ':empleador_id'    => $empleador_id,
        ':titulo_puesto'   => $titulo_puesto,
        ':descripcion'     => $descripcion,
        ':requisitos'      => $requisitos,
        ':provincia'       => $provincia,
        ':salario_ofrecido'=> $salario,
        ':estado'          => $estado
    ]);

    if ($resultado) {
        $_SESSION['alerta'] = [
            'tipo' => 'success',
            'mensaje' => '¡La oferta de empleo ha sido publicada exitosamente!'
        ];
    } else {
        $_SESSION['alerta'] = [
            'tipo' => 'danger',
            'mensaje' => 'No se pudo registrar la oferta. Inténtelo de nuevo.'
        ];
    }

} catch (PDOException $e) {
    $_SESSION['alerta'] = [
        'tipo' => 'danger',
        'mensaje' => 'Error de base de datos: ' . $e->getMessage()
    ];
}

header('Location: ../empleador/ofertas_empleo.php');
exit();
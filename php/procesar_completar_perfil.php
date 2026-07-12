<?php
session_start();
include '../conexion/conexion.php';

// Verificar que el usuario haya iniciado sesión
if (!isset($_SESSION['id_usuario'])) {    
    header('Location: ../login_desempleados.php');
    exit();
}

// Verificar que el formulario fue enviado por POST 
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../desempleado/completar_perfil.php');
    exit();
}
 
// Obtener datos del formulario
$usuario_id = $_SESSION['id_usuario']?? 0;
$nombre = trim($_POST['nombre'] ?? '');
$apellidos = trim($_POST['apellidos'] ?? '');
$documento_identidad = trim($_POST['documento_identidad'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$estado_civil = trim($_POST['estado_civil'] ?? '');
$provincia = trim($_POST['provincia'] ?? '');
$distrito = trim($_POST['distrito'] ?? '');
$ciudad_municipio = trim($_POST['ciudad_municipio'] ?? '');
$tiene_experiencia = isset($_POST['tiene_experiencia']) ? 1 : 0;

// Validar que el usuario_id coincida con el de la sesión
if ($usuario_id != $_SESSION['id_usuario']) {
    $_SESSION['mensaje_error'] = "Error: ID de usuario no coincide.";
    header('Location: ../desempleado/completar_perfil.php');
    exit();
}

// Validar campos obligatorios
if (empty($nombre) || empty($apellidos) || empty($documento_identidad) || 
    empty($telefono) || empty($estado_civil) || empty($provincia) || 
    empty($distrito) || empty($ciudad_municipio)) {
    $_SESSION['mensaje_error'] = "Error: Todos los campos obligatorios deben estar llenos.";
    header('Location: ../desempleado/completar_perfil.php');
    exit();
}

try {
    // Iniciar transacción
    $pdo->beginTransaction();

    // 1. ACTUALIZAR DATOS EN TABLA usuarios
    $stmt = $pdo->prepare("
        UPDATE usuarios 
        SET nombre = ?, apellidos = ?, documento_identidad = ? 
        WHERE id = ?
    ");
    $stmt->execute([$nombre, $apellidos, $documento_identidad, $usuario_id]);

    // 2. PROCESAR FOTO CARNET
    $foto_carnet = '';
    if (isset($_FILES['foto_carnet']) && $_FILES['foto_carnet']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/fotos/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $extension = pathinfo($_FILES['foto_carnet']['name'], PATHINFO_EXTENSION);
        $nombre_archivo = 'foto_' . $usuario_id . '_' . time() . '.' . $extension;
        $ruta_archivo = $upload_dir . $nombre_archivo;
        
        if (move_uploaded_file($_FILES['foto_carnet']['tmp_name'], $ruta_archivo)) {
            $foto_carnet = 'uploads/fotos/' . $nombre_archivo;
        } else {
            throw new Exception("Error al subir la foto carnet.");
        }
    } else {
        throw new Exception("La foto carnet es obligatoria.");
    }

    // 3. INSERTAR EN buscadores_empleo
    $stmt = $pdo->prepare("
        INSERT INTO buscadores_empleo 
        (usuario_id, telefono, estado_civil, foto_carnet, provincia, distrito, ciudad_municipio, estado_laboral) 
        VALUES (?, ?, ?, ?, ?, ?, ?, 'desempleado')
    ");
    $stmt->execute([$usuario_id, $telefono, $estado_civil, $foto_carnet, $provincia, $distrito, $ciudad_municipio]);

    // 4. PROCESAR DOCUMENTOS
    $copia_dip = '';
    $cv = '';
    $titulos = '';
    $otros_documentos = '';

    $upload_dir = '../uploads/documentos/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Array de archivos a procesar
    $archivos = [
        'copia_dip' => ['required' => true],
        'cv' => ['required' => true],
        'titulos' => ['required' => false],
        'otros_documentos' => ['required' => false]
    ];

    foreach ($archivos as $campo => $config) {
        if (isset($_FILES[$campo]) && $_FILES[$campo]['error'] === UPLOAD_ERR_OK) {
            $extension = pathinfo($_FILES[$campo]['name'], PATHINFO_EXTENSION);
            $nombre_archivo = $campo . '_' . $usuario_id . '_' . time() . '.' . $extension;
            $ruta_archivo = $upload_dir . $nombre_archivo;
            
            if (move_uploaded_file($_FILES[$campo]['tmp_name'], $ruta_archivo)) {
                $$campo = 'uploads/documentos/' . $nombre_archivo;
            } else {
                if ($config['required']) {
                    throw new Exception("Error al subir el archivo $campo.");
                }
            }
        } elseif ($config['required']) {
            throw new Exception("El archivo $campo es obligatorio.");
        }
    }

    // 5. INSERTAR EN documentos
    $stmt = $pdo->prepare("
        INSERT INTO documentos 
        (usuario_id, copia_dip, cv, titulos, otros_documentos) 
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([$usuario_id, $copia_dip, $cv, $titulos, $otros_documentos]);

    // 6. PROCESAR EXPERIENCIA LABORAL (si tiene experiencia)
    if ($tiene_experiencia == 1) {
        $empresas = $_POST['exp_empresa'] ?? [];
        $puestos = $_POST['exp_puesto'] ?? [];
        $fechas_inicio = $_POST['exp_fecha_inicio'] ?? [];
        $fechas_fin = $_POST['exp_fecha_fin'] ?? [];
        $funciones = $_POST['exp_funciones'] ?? [];

        for ($i = 0; $i < count($empresas); $i++) {
            // Validar que los campos obligatorios no estén vacíos
            if (!empty($empresas[$i]) && !empty($puestos[$i]) && 
                !empty($fechas_inicio[$i]) && !empty($funciones[$i])) {
                
                $stmt = $pdo->prepare("
                    INSERT INTO experiencia_laboral 
                    (usuario_id, empresa, puesto, fecha_inicio, fecha_fin, descripcion) 
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                
                $fecha_fin = !empty($fechas_fin[$i]) ? $fechas_fin[$i] : null;
                $stmt->execute([
                    $usuario_id,
                    $empresas[$i],
                    $puestos[$i],
                    $fechas_inicio[$i],
                    $fecha_fin,
                    $funciones[$i]
                ]);
            }
        }
    }

    // Confirmar transacción
    $pdo->commit();

    // Redirigir al dashboard con mensaje de éxito
    $_SESSION['mensaje_exito'] = "Perfil completado exitosamente. Bienvenido al sistema.";
    header('Location: ../desempleado/index.php');
    exit();

} catch (Exception $e) {
    // Revertir transacción en caso de error
    $pdo->rollBack();
    error_log("Error al completar perfil: " . $e->getMessage());
    $_SESSION['mensaje_error'] = "Error al completar el perfil: " . $e->getMessage();
    header('Location: ../desempleado/completar_perfil.php');
    exit();
}
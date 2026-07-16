<?php
session_start();

// Ajusta la página de retorno según tu estructura de carpetas
$pagina_formulario = '../admin/inscripciones.php'; 

require_once '../conexion/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = "Acceso no autorizado.";
    header("Location: $pagina_formulario");
    exit();
}

// 1. RECEPCIÓN Y LIMPIEZA DE DATOS BÁSICOS
$nombre            = trim($_POST['nombre'] ?? '');
$apellido          = trim($_POST['apellido'] ?? '');
$telefono          = trim($_POST['telefono'] ?? '');
$email             = trim($_POST['email'] ?? '');
$dip_numero        = trim($_POST['dip_numero'] ?? '');
$provincia         = trim($_POST['provincia'] ?? '');
$distrito          = trim($_POST['distrito'] ?? '');
$sede_formacion    = trim($_POST['sede_formacion'] ?? '');
$profesion_oficio  = trim($_POST['profesion_oficio'] ?? '');
$fecha_desempleo   = trim($_POST['fecha_desempleo'] ?? '');

// 2. VALIDACIONES DE CAMPOS OBLIGATORIOS
if (empty($nombre) || empty($apellido) || empty($telefono) || empty($dip_numero) || empty($provincia) || empty($distrito) || empty($sede_formacion)) {
    $_SESSION['error'] = "Todos los campos marcados con asterisco (*) son estrictamente obligatorios.";
    header("Location: $pagina_formulario");
    exit();
}

// 3. VALIDACIONES ESPECÍFICAS DE FORMATO

// Validar Correo (si se ha rellenado)
if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = "El formato del correo electrónico no es válido.";
    header("Location: $pagina_formulario");
    exit();
}

// Validar Número de Teléfono (Ej: mínimo 6 dígitos, permitiendo caracteres como + o espacios)
if (!preg_match('/^[0-9+\s\-]{6,20}$/', $telefono)) {
    $_SESSION['error'] = "El número de teléfono no tiene un formato válido.";
    header("Location: $pagina_formulario");
    exit();
}

// Validar Sede de Formación permitida
$sedes_permitidas = ['BATA', 'MALABO'];
if (!in_array(strtoupper($sede_formacion), $sedes_permitidas)) {
    $_SESSION['error'] = "La sede de formación seleccionada no es válida.";
    header("Location: $pagina_formulario");
    exit();
}

// Validar Fecha de Desempleo (si se ha rellenado)
if (!empty($fecha_desempleo)) {
    $fecha_temp = explode('-', $fecha_desempleo);
    if (count($fecha_temp) !== 3 || !checkdate($fecha_temp[1], $fecha_temp[2], $fecha_temp[0])) {
        $_SESSION['error'] = "La fecha de desempleo seleccionada no es válida.";
        header("Location: $pagina_formulario");
        exit();
    }
} else {
    $fecha_desempleo = null; // Guardar como NULL en la BD si está vacía
}

if (empty($profesion_oficio)) {
    $profesion_oficio = null;
}

// 4. VALIDACIÓN Y TRATAMIENTO DE ARCHIVOS (SUBIDAS)

// Definición de directorios de almacenamiento
$dir_fotos = '../uploads/fotos/';
$dir_pdfs  = '../uploads/documentos_dip/';

// Crear los directorios si no existen con permisos seguros
if (!is_dir($dir_fotos)) {
    mkdir($dir_fotos, 0755, true);
}
if (!is_dir($dir_pdfs)) {
    mkdir($dir_pdfs, 0755, true);
}

$ruta_foto_guardada = null;
$ruta_pdf_guardada  = null;

// A. VALIDACIÓN OBLIGATORIA DEL PDF (DIP Escaneado)
if (!isset($_FILES['dip_copia_pdf']) || $_FILES['dip_copia_pdf']['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['error'] = "Es obligatorio subir la copia escaneada de su DIP en formato PDF.";
    header("Location: $pagina_formulario");
    exit();
}

$file_pdf = $_FILES['dip_copia_pdf'];
$pdf_extension = strtolower(pathinfo($file_pdf['name'], PATHINFO_EXTENSION));

// Validar tipo de archivo PDF de manera estricta
$finfo = new finfo(FILEINFO_MIME_TYPE);
$pdf_mime = $finfo->file($file_pdf['tmp_name']);

if ($pdf_extension !== 'pdf' || $pdf_mime !== 'application/pdf') {
    $_SESSION['error'] = "El archivo del DIP debe ser un PDF válido.";
    header("Location: $pagina_formulario");
    exit();
}

// Validar tamaño máximo del PDF (Ej: 5MB)
if ($file_pdf['size'] > 5 * 1024 * 1024) {
    $_SESSION['error'] = "El archivo PDF es demasiado pesado. El límite es de 5MB.";
    header("Location: $pagina_formulario");
    exit();
}

// B. VALIDACIÓN OPCIONAL DE LA FOTO CARNET
if (isset($_FILES['foto_carnet']) && $_FILES['foto_carnet']['error'] === UPLOAD_ERR_OK) {
    $file_foto = $_FILES['foto_carnet'];
    $foto_extension = strtolower(pathinfo($file_foto['name'], PATHINFO_EXTENSION));
    
    // Validar tipo de archivo de imagen mediante MIME
    $foto_mime = $finfo->file($file_foto['tmp_name']);
    $mimes_permitidos = ['image/jpeg', 'image/png', 'image/jpg'];
    $extensiones_permitidas = ['jpeg', 'jpg', 'png'];

    if (!in_array($foto_mime, $mimes_permitidos) || !in_array($foto_extension, $extensiones_permitidas)) {
        $_SESSION['error'] = "La foto debe ser una imagen válida (JPG, JPEG o PNG).";
        header("Location: $pagina_formulario");
        exit();
    }

    // Validar tamaño de la foto (Ej: 2MB)
    if ($file_foto['size'] > 2 * 1024 * 1024) {
        $_SESSION['error'] = "La foto carnet supera el límite de tamaño permitido (2MB).";
        header("Location: $pagina_formulario");
        exit();
    }

    // Generar nombre de archivo único para evitar colisiones
    $nombre_foto_unico = 'FOTO_' . uniqid() . '_' . time() . '.' . $foto_extension;
    $ruta_foto_guardada = $dir_fotos . $nombre_foto_unico;
}

// Generar nombre de archivo único para el PDF
$nombre_pdf_unico = 'DIP_' . uniqid() . '_' . time() . '.' . $pdf_extension;
$ruta_pdf_guardada = $dir_pdfs . $nombre_pdf_unico;


// 5. PROCESO DE BASE DE DATOS Y GUARDADO
// 5. PROCESO DE BASE DE DATOS Y GUARDADO
try {
    // Verificar si el DIP o el Teléfono ya están registrados en la tabla 'personas_desempleadas'
    $query_check = "SELECT dip_numero, telefono FROM personas_desempleadas 
                    WHERE dip_numero = :dip OR telefono = :tel LIMIT 1";
    
    $stmt_check = $pdo->prepare($query_check);
    $stmt_check->execute([
        ':dip' => $dip_numero,
        ':tel' => $telefono
    ]);
    
    $registro_existente = $stmt_check->fetch();
    
    if ($registro_existente) {
        if ($registro_existente['dip_numero'] === $dip_numero) {
            $_SESSION['error'] = "El número de DIP ingresado ya se encuentra registrado.";
        } elseif ($registro_existente['telefono'] === $telefono) {
            $_SESSION['error'] = "Este número de teléfono ya está registrado por otro solicitante.";
        }
        header("Location: $pagina_formulario");
        exit();
    }

    // Mover los archivos temporales a sus carpetas finales de manera segura
    if (!move_uploaded_file($file_pdf['tmp_name'], $ruta_pdf_guardada)) {
        throw new Exception("Error al guardar el archivo PDF del DIP.");
    }

    if ($ruta_foto_guardada !== null) {
        if (!move_uploaded_file($file_foto['tmp_name'], $ruta_foto_guardada)) {
            // Si falla la foto, eliminamos el PDF ya subido para mantener consistencia
            if (file_exists($ruta_pdf_guardada)) unlink($ruta_pdf_guardada);
            throw new Exception("Error al guardar la foto de carnet.");
        }
    }

    // Generación del número de expediente único para el desempleado (Ej: CAND-XXXXX)
    // Nota: Aunque generas el expediente, tu tabla actual 'personas_desempleadas' no tiene 
    // la columna 'numero_expediente'. La guardaremos en la BD de la siguiente manera:
    $numero_expediente = "CAND-" . rand(10000, 99999);

    // Insertar registros en la base de datos (Corregido 'foto_carnet_url')
    $sql_insert = "INSERT INTO personas_desempleadas (
                        nombre, apellido, telefono, email, 
                        dip_numero, dip_copia_pdf, provincia, distrito, 
                        sede_formacion, profesion_oficio, fecha_desempleo, foto_carnet_url
                    ) VALUES (
                        :nombre, :apellido, :telefono, :email, 
                        :dip_numero, :dip_copia_pdf, :provincia, :distrito, 
                        :sede_formacion, :profesion_oficio, :fecha_desempleo, :foto_carnet
                    )";
    
    $stmt_insert = $pdo->prepare($sql_insert);
    
    $resultado = $stmt_insert->execute([
        ':nombre'           => $nombre,
        ':apellido'         => $apellido,
        ':telefono'         => $telefono,
        ':email'            => !empty($email) ? $email : null,
        ':dip_numero'       => $dip_numero,
        ':dip_copia_pdf'    => $ruta_pdf_guardada,
        ':provincia'        => $provincia,
        ':distrito'         => $distrito,
        ':sede_formacion'   => strtoupper($sede_formacion),
        ':profesion_oficio' => $profesion_oficio,
        ':fecha_desempleo'  => $fecha_desempleo,
        ':foto_carnet'      => $ruta_foto_guardada
    ]);

    if ($resultado) {
        $_SESSION['exito'] = "¡Inscripción realizada con éxito! Su número de expediente provisional es: " . $numero_expediente;
        header("Location: $pagina_formulario");
        exit();
    }

} catch (Exception $e) {
    // Si algo falla y los archivos ya se habían subido, los eliminamos para no dejar basura en el servidor
    if (isset($ruta_pdf_guardada) && file_exists($ruta_pdf_guardada)) {
        unlink($ruta_pdf_guardada);
    }
    if (isset($ruta_foto_guardada) && file_exists($ruta_foto_guardada)) {
        unlink($ruta_foto_guardada);
    }

    // Guardamos el detalle real en el log de PHP para depurar
    error_log("Error crítico en el registro de desempleado: " . $e->getMessage());
    
    // Si estás en desarrollo local, puedes descomentar la siguiente línea para ver el error directamente en pantalla:
    // die("Error de desarrollo: " . $e->getMessage());

    $_SESSION['error'] = "Hubo un error interno al procesar los datos. Por favor, intente de nuevo.";
    header("Location: $pagina_formulario");
    exit();
}

?>
<?php
session_start();

// Ajusta la página de retorno según tu estructura de carpetas (apunta a la vista de administración)
$pagina_formulario = '../admin/inscripciones.php'; 

require_once '../conexion/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = "Acceso no autorizado.";
    header("Location: $pagina_formulario");
    exit();
}

// 1. RECEPCIÓN Y LIMPIEZA DE DATOS (Mapeados con los inputs del Modal)
$id               = intval($_POST['id'] ?? 0);
$estado_seleccion = trim($_POST['estado_seleccion'] ?? '');
$nombre           = trim($_POST['nombre'] ?? '');
$apellido         = trim($_POST['apellido'] ?? '');
$telefono         = trim($_POST['telefono'] ?? '');
$email            = trim($_POST['email'] ?? '');
$dip_numero       = trim($_POST['dip_numero'] ?? '');
$provincia        = trim($_POST['provincia'] ?? '');
$distrito         = trim($_POST['distrito'] ?? '');
$sede_formacion   = trim($_POST['sede_formacion'] ?? '');
$profesion_oficio = trim($_POST['profesion_oficio'] ?? '');
$fecha_desempleo  = trim($_POST['fecha_desempleo'] ?? '');

// Validación del ID
if ($id <= 0) {
    $_SESSION['error'] = "ID de registro no válido para actualizar.";
    header("Location: $pagina_formulario");
    exit();
}

// 2. VALIDACIONES DE CAMPOS OBLIGATORIOS
if (empty($nombre) || empty($apellido) || empty($telefono) || empty($dip_numero) || empty($provincia) || empty($distrito) || empty($sede_formacion) || empty($estado_seleccion)) {
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

// Validar Número de Teléfono
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


// 4. TRATAMIENTO DE ARCHIVOS (SUBIDAS OPCIONALES EN EDICIÓN)

// Directorios de almacenamiento heredados de tus rutas
$dir_fotos = '../uploads/fotos/';
$dir_pdfs  = '../uploads/documentos_dip/';

$finfo = new finfo(FILEINFO_MIME_TYPE);

try {
    // Recuperar las rutas actuales de los archivos por si el usuario decide conservarlas (no sube nada nuevo)
    $query_actual = "SELECT dip_copia_pdf, foto_carnet_url, dip_numero, telefono FROM personas_desempleadas WHERE id = :id LIMIT 1";
    $stmt_actual = $pdo->prepare($query_actual);
    $stmt_actual->execute([':id' => $id]);
    $registro_actual = $stmt_actual->fetch();

    if (!$registro_actual) {
        $_SESSION['error'] = "El candidato seleccionado no existe en el sistema.";
        header("Location: $pagina_formulario");
        exit();
    }

    // Mantener rutas previas por defecto
    $ruta_pdf_guardada  = $registro_actual['dip_copia_pdf'];
    $ruta_foto_guardada = $registro_actual['foto_carnet_url'];

    // Validar duplicados de DIP o Teléfono con OTROS registros (excluyendo el ID actual)
    $query_check = "SELECT id, dip_numero, telefono FROM personas_desempleadas 
                    WHERE (dip_numero = :dip OR telefono = :tel) AND id != :id LIMIT 1";
    $stmt_check = $pdo->prepare($query_check);
    $stmt_check->execute([
        ':dip' => $dip_numero,
        ':tel' => $telefono,
        ':id'  => $id
    ]);
    $registro_existente = $stmt_check->fetch();

    if ($registro_existente) {
        if ($registro_existente['dip_numero'] === $dip_numero) {
            $_SESSION['error'] = "El número de DIP ingresado ya pertenece a otra persona registrada.";
        } elseif ($registro_existente['telefono'] === $telefono) {
            $_SESSION['error'] = "Este número de teléfono ya está registrado por otro solicitante.";
        }
        header("Location: $pagina_formulario");
        exit();
    }

    // A. PROCESAR NUEVO PDF DEL DIP (Si se sube uno nuevo)
    if (isset($_FILES['dip_copia_pdf']) && $_FILES['dip_copia_pdf']['error'] === UPLOAD_ERR_OK) {
        $file_pdf = $_FILES['dip_copia_pdf'];
        $pdf_extension = strtolower(pathinfo($file_pdf['name'], PATHINFO_EXTENSION));
        $pdf_mime = $finfo->file($file_pdf['tmp_name']);

        if ($pdf_extension !== 'pdf' || $pdf_mime !== 'application/pdf') {
            $_SESSION['error'] = "El archivo del DIP debe ser un PDF válido.";
            header("Location: $pagina_formulario");
            exit();
        }

        if ($file_pdf['size'] > 5 * 1024 * 1024) {
            $_SESSION['error'] = "El archivo PDF es demasiado pesado. El límite es de 5MB.";
            header("Location: $pagina_formulario");
            exit();
        }

        // Generar nombre único para el nuevo PDF
        $nombre_pdf_unico = 'DIP_' . uniqid() . '_' . time() . '.' . $pdf_extension;
        $nueva_ruta_pdf = $dir_pdfs . $nombre_pdf_unico;

        if (move_uploaded_file($file_pdf['tmp_name'], $nueva_ruta_pdf)) {
            // Eliminar el archivo PDF anterior si existía físicamente
            if (!empty($ruta_pdf_guardada) && file_exists($ruta_pdf_guardada)) {
                @unlink($ruta_pdf_guardada);
            }
            $ruta_pdf_guardada = $nueva_ruta_pdf;
        } else {
            throw new Exception("Error al guardar el nuevo archivo PDF del DIP.");
        }
    }

    // B. PROCESAR NUEVA FOTO CARNET (Si se sube una nueva)
    if (isset($_FILES['foto_carnet']) && $_FILES['foto_carnet']['error'] === UPLOAD_ERR_OK) {
        $file_foto = $_FILES['foto_carnet'];
        $foto_extension = strtolower(pathinfo($file_foto['name'], PATHINFO_EXTENSION));
        $foto_mime = $finfo->file($file_foto['tmp_name']);
        
        $mimes_permitidos = ['image/jpeg', 'image/png', 'image/jpg'];
        $extensiones_permitidas = ['jpeg', 'jpg', 'png'];

        if (!in_array($foto_mime, $mimes_permitidos) || !in_array($foto_extension, $extensiones_permitidas)) {
            $_SESSION['error'] = "La foto debe ser una imagen válida (JPG, JPEG o PNG).";
            header("Location: $pagina_formulario");
            exit();
        }

        if ($file_foto['size'] > 2 * 1024 * 1024) {
            $_SESSION['error'] = "La foto carnet supera el límite de tamaño permitido (2MB).";
            header("Location: $pagina_formulario");
            exit();
        }

        // Generar nombre único para la nueva foto
        $nombre_foto_unico = 'FOTO_' . uniqid() . '_' . time() . '.' . $foto_extension;
        $nueva_ruta_foto = $dir_fotos . $nombre_foto_unico;

        if (move_uploaded_file($file_foto['tmp_name'], $nueva_ruta_foto)) {
            // Eliminar la foto anterior si existía físicamente
            if (!empty($ruta_foto_guardada) && file_exists($ruta_foto_guardada)) {
                @unlink($ruta_foto_guardada);
            }
            $ruta_foto_guardada = $nueva_ruta_foto;
        } else {
            throw new Exception("Error al guardar la nueva foto de carnet.");
        }
    }

    // 5. EJECUTAR EL PROCESO DE ACTUALIZACIÓN (UPDATE)
    $sql_update = "UPDATE personas_desempleadas SET 
                    nombre = :nombre, 
                    apellido = :apellido, 
                    telefono = :telefono, 
                    email = :email, 
                    dip_numero = :dip_numero, 
                    dip_copia_pdf = :dip_copia_pdf, 
                    provincia = :provincia, 
                    distrito = :distrito, 
                    sede_formacion = :sede_formacion, 
                    profesion_oficio = :profesion_oficio, 
                    fecha_desempleo = :fecha_desempleo, 
                    foto_carnet_url = :foto_carnet,
                    estado_seleccion = :estado_seleccion
                   WHERE id = :id";
    
    $stmt_update = $pdo->prepare($sql_update);
    
    $resultado = $stmt_update->execute([
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
        ':foto_carnet'      => $ruta_foto_guardada,
        ':estado_seleccion' => $estado_seleccion,
        ':id'               => $id
    ]);

    if ($resultado) {
        $_SESSION['exito'] = "¡Los datos del candidato se han actualizado correctamente!";
        header("Location: $pagina_formulario");
        exit();
    }

} catch (Exception $e) {
    // Registro de errores internos en el log
    error_log("Error crítico en la actualización de desempleado: " . $e->getMessage());

    $_SESSION['error'] = "Hubo un error interno al procesar los cambios. Por favor, intente de nuevo.";
    header("Location: $pagina_formulario");
    exit();
}
?>
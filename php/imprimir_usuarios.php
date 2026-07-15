<?php
// ===== 1. INICIAR BUFFER DE SALIDA AL PRINCIPIO DEL ARCHIVO =====
ob_start(); 

session_start();

// ===== 2. VERIFICAR SESIÓN ADMINISTRATIVA =====
if (!isset($_SESSION['id_usuario']) || !in_array($_SESSION['rol'] ?? '', ['administrador', 'ministerio'])) {
    die("Acceso no autorizado.");
}

// ===== 3. INCLUIR CONEXIÓN Y FPDF =====
require_once '../conexion/conexion.php'; 
require_once '../libs/fpdf/fpdf.php'; 

// (El resto de tu código de captura de GET y la consulta SQL se mantiene exactamente igual...)
$filtro_busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';
$filtro_rol = isset($_GET['rol']) ? trim($_GET['rol']) : '';
$filtro_verificado = isset($_GET['verificado']) ? trim($_GET['verificado']) : '';

try {
    $sql = "
        SELECT 
            numero_expediente,
            CONCAT(nombre, ' ', apellidos) AS nombre_completo,
            documento_identidad,
            correo_electronico,
            rol,
            correo_verificado,
            DATE_FORMAT(fecha_registro, '%d/%m/%Y') AS fecha_registro
        FROM usuarios
        WHERE 1=1
    ";
    
    $params = [];
    if ($filtro_busqueda !== '') {
        $sql .= " AND (nombre LIKE ? OR apellidos LIKE ? OR nombre_usuario LIKE ? OR documento_identidad LIKE ? OR numero_expediente LIKE ?)";
        $buscar_val = "%$filtro_busqueda%";
        array_push($params, $buscar_val, $buscar_val, $buscar_val, $buscar_val, $buscar_val);
    }
    if ($filtro_rol !== '') {
        $sql .= " AND rol = ?";
        $params[] = $filtro_rol;
    }
    if ($filtro_verificado !== '') {
        $sql .= " AND correo_verificado = ?";
        $params[] = $filtro_verificado;
    }
    $sql .= " ORDER BY id DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al consultar datos para el PDF: " . $e->getMessage());
}

// ===== 4. CLASE PDF =====
class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 16);
        $this->SetTextColor(33, 37, 41);
        $this->Cell(0, 10, utf8_decode('REPORTE DE CONTROL DE USUARIOS'), 0, 1, 'C');
        
        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(108, 117, 125);
        $this->Cell(0, 5, utf8_decode('Panel de Administración - Ministerio'), 0, 1, 'C');
        $this->Ln(10);
        
        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor(41, 128, 185); 
        $this->SetTextColor(255, 255, 255);
        
        $this->Cell(30, 8, 'Expediente', 1, 0, 'C', true);
        $this->Cell(65, 8, 'Nombre Completo', 1, 0, 'L', true);
        $this->Cell(30, 8, 'Doc. Identidad', 1, 0, 'C', true);
        $this->Cell(65, 8, 'Email', 1, 0, 'L', true);
        $this->Cell(30, 8, 'Rol', 1, 0, 'C', true);
        $this->Cell(30, 8, 'Verif.', 1, 0, 'C', true);
        $this->Cell(27, 8, 'Registro', 1, 1, 'C', true);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(120, 120, 120);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
        $this->Cell(0, 10, date('d/m/Y H:i'), 0, 0, 'R');
    }
}

// ===== 5. GENERACIÓN DEL PDF =====
$pdf = new PDF('L', 'mm', 'A4'); 
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(50, 50, 50);

$fill = false; 

foreach ($usuarios as $usr) {
    $pdf->SetFillColor(245, 247, 250);
    $verificacion = ($usr['correo_verificado'] == 1) ? 'Verificado' : 'Pendiente';
    
    $pdf->Cell(30, 7, utf8_decode($usr['numero_expediente']), 1, 0, 'C', $fill);
    $pdf->Cell(65, 7, utf8_decode($usr['nombre_completo']), 1, 0, 'L', $fill);
    $pdf->Cell(30, 7, utf8_decode($usr['documento_identidad']), 1, 0, 'C', $fill);
    $pdf->Cell(65, 7, utf8_decode($usr['correo_electronico']), 1, 0, 'L', $fill);
    $pdf->Cell(30, 7, utf8_decode(ucfirst($usr['rol'])), 1, 0, 'C', $fill);
    $pdf->Cell(30, 7, utf8_decode($verificacion), 1, 0, 'C', $fill);
    $pdf->Cell(27, 7, utf8_decode($usr['fecha_registro']), 1, 1, 'C', $fill);
    
    $fill = !$fill; 
}

if (empty($usuarios)) {
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(277, 10, utf8_decode('No se encontraron resultados con los filtros actuales.'), 1, 1, 'C');
}

// ===== [CRÍTICO] LIMPIAR CUALQUIER SALIDA PREVIA DE PHP =====
// Esto elimina espacios en blanco que puedan romper el visor del PDF
ob_end_clean();

// Forzar visualización en navegador
$pdf->Output('I', 'Reporte_Usuarios_' . date('Ymd_His') . '.pdf');

// Omitimos la etiqueta de cierre '?>' para que no se envíe basura accidentalmente.
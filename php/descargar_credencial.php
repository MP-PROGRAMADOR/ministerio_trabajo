<?php
ob_start(); // Prevenir cualquier salida accidental de espacio en blanco
session_start();

// ===== 1. SEGURIDAD =====
if (!isset($_SESSION['id_usuario'])) {
    die("Acceso no autorizado.");
}

require_once '../conexion/conexion.php'; 
require_once '../libs/fpdf/fpdf.php'; 

// Capturar el ID de la credencial
$credencial_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($credencial_id === 0) {
    die("ID de credencial no válido.");
}

// ===== 2. CONSULTAR DATOS DE LA CREDENCIAL =====
try {
    $sql = "
        SELECT 
            c.numero_credencial,
            DATE_FORMAT(c.fecha_emision, '%d/%m/%Y') AS fecha_emision,
            DATE_FORMAT(c.fecha_vencimiento, '%d/%m/%Y') AS fecha_vencimiento,
            u.nombre,
            u.apellidos,
            u.documento_identidad,
            u.numero_expediente,
            emp.nombre_empresa,
            n.codigo_seguimiento
        FROM credenciales_empleo c
        INNER JOIN buscadores_empleo b ON c.buscador_id = b.id
        INNER JOIN usuarios u ON b.usuario_id = u.id
        INNER JOIN notificaciones_intermediacion n ON c.notificacion_id = n.id
        INNER JOIN empleadores emp ON n.empleador_id = emp.id
        WHERE c.id = ?
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$credencial_id]);
    $credencial = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$credencial) {
        die("Credencial no encontrada.");
    }

} catch (PDOException $e) {
    die("Error al consultar la credencial: " . $e->getMessage());
}

// ===== 3. CLASE FPDF PARA LA CREDENCIAL OFICIAL =====
class CredencialPDF extends FPDF {
    function Header() {
        // Marco elegante exterior de la credencial
        $this->Rect(10, 10, 190, 277, 'D');
        
        // Encabezado Ministerial
        $this->SetFont('Arial', 'B', 12);
        $this->SetTextColor(31, 58, 138); // Azul Institucional
        $this->Cell(0, 8, utf8_decode('REPÚBLICA DE GUINEA ECUATORIAL'), 0, 1, 'C');
        $this->SetFont('Arial', 'B', 10);
        $this->SetTextColor(100, 110, 120);
        $this->Cell(0, 6, utf8_decode('MINISTERIO DE TRABAJO Y FOMENTO DE EMPLEO'), 0, 1, 'C');
        $this->Cell(0, 4, utf8_decode('Dirección General de Empleo'), 0, 1, 'C');
        $this->Ln(10);
    }

    function Footer() {
        // Pie de página con validez
        $this->SetY(-25);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(120, 120, 120);
        $this->Cell(0, 5, utf8_decode('Esta credencial es personal e intransferible.'), 0, 1, 'C');
        $this->Cell(0, 5, utf8_decode('La alteración de este documento constituye un delito sancionable por la ley.'), 0, 1, 'C');
    }
}

// ===== 4. MAQUETACIÓN DE LA CREDENCIAL =====
$pdf = new CredencialPDF('P', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetFont('Arial', '', 11);

// Título Principal
$pdf->SetFont('Arial', 'B', 16);
$pdf->SetTextColor(33, 37, 41);
$pdf->Cell(0, 12, utf8_decode('CREDENCIAL DE INSERCIÓN LABORAL'), 0, 1, 'C');
$pdf->Ln(5);

// Código de Credencial destacado
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(240, 244, 248);
$pdf->Cell(0, 10, utf8_decode('Nº CREDENCIAL: ' . $credencial['numero_credencial']), 1, 1, 'C', true);
$pdf->Ln(10);

// Contenedor de Información del Beneficiario
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetTextColor(31, 58, 138);
$pdf->Cell(0, 8, utf8_decode('Datos del Ciudadano Autorizado:'), 'B', 1, 'L');
$pdf->Ln(4);

$pdf->SetFont('Arial', '', 11);
$pdf->SetTextColor(50, 50, 50);

// Tabla informativa de datos personales
$pdf->Cell(50, 8, utf8_decode('Nombre Completo:'), 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 8, utf8_decode($credencial['nombre'] . ' ' . $credencial['apellidos']), 0, 1, 'L');

$pdf->SetFont('Arial', '', 11);
$pdf->Cell(50, 8, utf8_decode('Expediente Único:'), 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 8, utf8_decode($credencial['numero_expediente']), 0, 1, 'L');

$pdf->SetFont('Arial', '', 11);
$pdf->Cell(50, 8, utf8_decode('Documento Identidad:'), 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 8, utf8_decode($credencial['documento_identidad']), 0, 1, 'L');
$pdf->Ln(6);

// Contenedor de Información de la Empresa / Trámite
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetTextColor(31, 58, 138);
$pdf->Cell(0, 8, utf8_decode('Datos de la Contratación y Vigencia:'), 'B', 1, 'L');
$pdf->Ln(4);

$pdf->SetFont('Arial', '', 11);
$pdf->SetTextColor(50, 50, 50);

$pdf->Cell(50, 8, utf8_decode('Empresa Asignada:'), 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 8, utf8_decode($credencial['nombre_empresa']), 0, 1, 'L');

$pdf->SetFont('Arial', '', 11);
$pdf->Cell(50, 8, utf8_decode('Código de Trámite:'), 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 8, utf8_decode($credencial['codigo_seguimiento']), 0, 1, 'L');

$pdf->SetFont('Arial', '', 11);
$pdf->Cell(50, 8, utf8_decode('Fecha de Emisión:'), 0, 0, 'L');
$pdf->Cell(0, 8, utf8_decode($credencial['fecha_emision']), 0, 1, 'L');

$pdf->Cell(50, 8, utf8_decode('Válido Hasta:'), 0, 0, 'L');
$pdf->SetTextColor(200, 30, 30); // Rojo para destacar fecha límite
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 8, utf8_decode($credencial['fecha_vencimiento']), 0, 1, 'L');
$pdf->Ln(25);

// Área de Firmas
$pdf->SetTextColor(50, 50, 50);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(95, 5, '___________________________', 0, 0, 'C');
$pdf->Cell(95, 5, '___________________________', 0, 1, 'C');
$pdf->Cell(95, 5, utf8_decode('Firma del Interesado'), 0, 0, 'C');
$pdf->Cell(95, 5, utf8_decode('Director General de Empleo'), 0, 1, 'C');

// Limpiar buffer previo para asegurar renderizado correcto y evitar pantallas negras
ob_end_clean();

// Salida directa al navegador
$pdf->Output('I', 'Credencial_' . $credencial['numero_credencial'] . '.pdf');
<?php
session_start();
require_once '../conexion/conexion.php'; // Tu conexión PDO a ministerio_trabajo
require_once '../libs/fpdf/fpdf.php';
require_once '../libs/phpqrcode/qrlib.php';

// 1. Obtener y validar el parámetro de la URL
$notificacion_id = filter_input(INPUT_GET, 'notificacion_id', FILTER_VALIDATE_INT);

if (!$notificacion_id) {
    die('Error: El identificador del trámite no es válido.');
}

// 2. Consulta SQL Corregida acorde a tus tablas reales
try {
    $sql = "SELECT 
                c.numero_credencial,
                c.fecha_emision,
                c.fecha_vencimiento,
                
                -- Datos del Candidato (Desde 'usuarios' y 'buscadores_empleo')
                u_b.nombre AS buscador_nombre,
                u_b.apellidos AS buscador_apellidos,
                u_b.numero_expediente AS buscador_expediente,
                u_b.documento_identidad,
                b.telefono AS candidato_telefono,
                b.provincia AS candidato_provincia,
                
                -- Datos de la Empresa Empleadora
                e.nombre_empresa,
                e.rnc_ruc,
                e.direccion AS empresa_direccion,
                e.telefono_corporativo,
                
                -- Datos de la Oferta / Trámite
                COALESCE(o.titulo_puesto, 'PUESTO A DESIGNAR / GENERAL') AS titulo_puesto,
                ni.codigo_seguimiento
            FROM credenciales_empleo c
            INNER JOIN notificaciones_intermediacion ni ON c.notificacion_id = ni.id
            INNER JOIN buscadores_empleo b ON c.buscador_id = b.id
            INNER JOIN usuarios u_b ON b.usuario_id = u_b.id
            INNER JOIN empleadores e ON ni.empleador_id = e.id
            LEFT JOIN ofertas_empleo o ON ni.oferta_id = o.id
            WHERE c.notificacion_id = :notificacion_id AND ni.estado_ministerio = 'aprobado'";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':notificacion_id' => $notificacion_id]);
    $datos = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$datos) {
        die('Error: No se encontró una credencial aprobada asociada a este número de notificación.');
    }

} catch (PDOException $e) {
    die('Error en la consulta de la base de datos: ' . $e->getMessage());
}

// 3. Generación del Código QR dinámico
$textoQR = "MINISTERIO DE TRABAJO - GUINEA ECUATORIAL\n" .
           "Credencial: " . $datos['numero_credencial'] . "\n" .
           "Titular: " . $datos['buscador_nombre'] . " " . $datos['buscador_apellidos'] . "\n" .
           "DIP: " . $datos['documento_identidad'] . "\n" .
           "Expediente: " . $datos['buscador_expediente'] . "\n" .
           "Estado: VALIDO";

$tempDir = sys_get_temp_dir() . '/';
$qrFilename = $tempDir . 'qr_' . md5($datos['numero_credencial']) . '.png';

// Generar la imagen PNG del QR
QRcode::png($textoQR, $qrFilename, QR_ECLEVEL_M, 4, 2);


// 4. Definición de la Clase FPDF con Membrete Oficial
// 4. Definición de la Clase FPDF con Membrete Oficial de la OCE
class PDF_Credencial extends FPDF {
    function Header() {
        // Logo de la Oficina Central de Empleo (OCE)
        $logo = '../src/img/logo_nuevo.png'; // Asegúrate de ubicar tu imagen en esta ruta
        if (file_exists($logo)) {
            $this->Image($logo, 15, 10, 25);
        }
        
        // Membrete Institucional
        $this->SetFont('Arial', 'B', 11);
        $this->SetTextColor(20, 40, 80);
        $this->Cell(0, 5, utf8_decode('REPÚBLICA DE GUINEA ECUATORIAL'), 0, 1, 'C');
        
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 5, utf8_decode('MINISTERIO DE TRABAJO Y FOMENTO DE EMPLEO'), 0, 1, 'C');
        
        $this->SetFont('Arial', 'B', 9);
        $this->SetTextColor(0, 102, 204); // Azul representativo
        $this->Cell(0, 4, utf8_decode('OFICINA CENTRAL DE EMPLEO (OCE)'), 0, 1, 'C');
        
        $this->Ln(4);
        
        // Línea divisora estilo bandera / corporativo
        $this->SetDrawColor(20, 40, 80);
        $this->SetLineWidth(0.6);
        $this->Line(15, 31, 195, 31);
        $this->Ln(6);
    }

    function Footer() {
        $this->SetY(-20);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(120, 120, 120);
        $this->Cell(0, 4, utf8_decode('Oficina Central de Empleo (OCE) - Sistema de Intermediación Laboral.'), 0, 1, 'C');
        $this->Cell(0, 4, utf8_decode('Página ') . $this->PageNo() . utf8_decode(' - Validez sujeta a verificación mediante el código QR impreso.'), 0, 1, 'C');
    }
}


// 5. Construcción del PDF
$pdf = new PDF_Credencial('P', 'mm', 'A4');
$pdf->SetMargins(15, 15, 15);
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 20);

// Título y Credencial
$pdf->SetFont('Arial', 'B', 13);
$pdf->SetTextColor(0, 51, 102);
$pdf->Cell(0, 7, utf8_decode('PASE OFICIAL DE INTERMEDIACIÓN LABORAL'), 0, 1, 'C');

$pdf->SetFont('Arial', 'B', 10);
$pdf->SetTextColor(180, 0, 0);
$pdf->Cell(0, 5, utf8_decode('Nº CREDENCIAL: ' . $datos['numero_credencial']), 0, 1, 'C');
$pdf->Ln(5);

// Texto explicativo legal
$pdf->SetFont('Arial', '', 9.5);
$pdf->SetTextColor(30, 30, 30);
$textoIntro = "Por la presente, el Ministerio de Trabajo y Fomento de Empleo certifica el registro e intermediación oficial del ciudadano abajo firmado. Se solicita a la entidad empleadora la recepción del candidato para los trámites correspondientes de entrevista, evaluaciones o contratación previa conforme a la legislación laboral de Guinea Ecuatorial.";
$pdf->MultiCell(0, 4.8, utf8_decode($textoIntro), 0, 'J');
$pdf->Ln(5);

// --- SECCIÓN 1: DATOS DEL CANDIDATO ---
$pdf->SetFillColor(230, 240, 250);
$pdf->SetFont('Arial', 'B', 9.5);
$pdf->SetTextColor(20, 40, 80);
$pdf->Cell(0, 6, utf8_decode(' 1. DATOS DEL BENEFICIARIO (CANDIDATO)'), 1, 1, 'L', true);

$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(0, 0, 0);

$pdf->Cell(45, 6, utf8_decode('Nombre y Apellidos:'), 'L', 0);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(135, 6, utf8_decode(strtoupper($datos['buscador_nombre'] . ' ' . $datos['buscador_apellidos'])), 'R', 1);

$pdf->SetFont('Arial', '', 9);
$pdf->Cell(45, 6, utf8_decode('Documento Identidad (DIP):'), 'L', 0);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(135, 6, utf8_decode($datos['documento_identidad']), 'R', 1);

$pdf->SetFont('Arial', '', 9);
$pdf->Cell(45, 6, utf8_decode('Nº Expediente Único:'), 'L', 0);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(135, 6, utf8_decode($datos['buscador_expediente']), 'R', 1);

$pdf->SetFont('Arial', '', 9);
$pdf->Cell(45, 6, utf8_decode('Provincia / Teléfono:'), 'LB', 0);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(135, 6, utf8_decode(strtoupper($datos['candidato_provincia']) . ' / ' . $datos['candidato_telefono']), 'RB', 1);

$pdf->Ln(5);

// --- SECCIÓN 2: DATOS DE LA EMPRESA Y OFERTA ---
$pdf->SetFillColor(230, 240, 250);
$pdf->SetFont('Arial', 'B', 9.5);
$pdf->SetTextColor(20, 40, 80);
$pdf->Cell(0, 6, utf8_decode(' 2. DATOS DE LA EMPRESA Y PUESTO ASIGNADO'), 1, 1, 'L', true);

$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(0, 0, 0);

$pdf->Cell(45, 6, utf8_decode('Empresa Destino:'), 'L', 0);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(135, 6, utf8_decode(strtoupper($datos['nombre_empresa'])), 'R', 1);

$pdf->SetFont('Arial', '', 9);
$pdf->Cell(45, 6, utf8_decode('RNC / RUC:'), 'L', 0);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(135, 6, utf8_decode($datos['rnc_ruc'] ?? 'N/A'), 'R', 1);

$pdf->SetFont('Arial', '', 9);
$pdf->Cell(45, 6, utf8_decode('Puesto / Vacante:'), 'LB', 0);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(135, 6, utf8_decode(strtoupper($datos['titulo_puesto'])), 'RB', 1);

$pdf->Ln(6);

// --- SECCIÓN 3: CONTROL DE VIGENCIA Y CÓDIGO QR ---
$pdf->SetFillColor(252, 252, 252);
// Marco contenedor
$pdf->Cell(135, 30, '', 1, 0, 'L', true);
$pdf->Cell(45, 30, '', 1, 1, 'C', true);

// Escribir contenido interno
$pdf->SetXY(18, $pdf->GetY() - 28);
$pdf->SetFont('Arial', '', 8.5);
$pdf->Cell(40, 5, utf8_decode('Fecha de Emisión:'), 0, 0);
$pdf->SetFont('Arial', 'B', 8.5);
$pdf->Cell(80, 5, date('d/m/Y H:i', strtotime($datos['fecha_emision'])), 0, 1);

$pdf->SetX(18);
$pdf->SetFont('Arial', '', 8.5);
$pdf->Cell(40, 5, utf8_decode('Fecha de Vencimiento:'), 0, 0);
$pdf->SetFont('Arial', 'B', 8.5);
$pdf->Cell(80, 5, date('d/m/Y', strtotime($datos['fecha_vencimiento'])), 0, 1);

$pdf->SetX(18);
$pdf->SetFont('Arial', '', 8.5);
$pdf->Cell(40, 5, utf8_decode('Cód. Seguimiento:'), 0, 0);
$pdf->SetFont('Arial', 'B', 8.5);
$pdf->Cell(80, 5, utf8_decode($datos['codigo_seguimiento']), 0, 1);

// Imprimir Imagen QR dentro del marco derecho
if (file_exists($qrFilename)) {
    $pdf->Image($qrFilename, 161, $pdf->GetY() - 14, 23, 23);
}

$pdf->Ln(18);

// --- SECCIÓN 4: FIRMAS Y SELLOS ---
$pdf->SetFont('Arial', 'B', 8.5);
$pdf->SetTextColor(20, 40, 80);

$pdf->Cell(85, 4, utf8_decode('POR EL MINISTERIO DE TRABAJO'), 0, 0, 'C');
$pdf->Cell(10, 4, '', 0, 0);
$pdf->Cell(85, 4, utf8_decode('POR LA EMPRESA RECEPTORA'), 0, 1, 'C');

$pdf->Ln(15); // Espacio para firma física y sello de caucho

$pdf->SetFont('Arial', '', 8);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(85, 4, utf8_decode('-----------------------------------------------------------'), 0, 0, 'C');
$pdf->Cell(10, 4, '', 0, 0);
$pdf->Cell(85, 4, utf8_decode('-----------------------------------------------------------'), 0, 1, 'C');

$pdf->Cell(85, 4, utf8_decode('Firma y Sello del Funcionario'), 0, 0, 'C');
$pdf->Cell(10, 4, '', 0, 0);
$pdf->Cell(85, 4, utf8_decode('Firma y Sello de Recursos Humanos'), 0, 1, 'C');

// 6. Limpieza del archivo QR temporal y salida en pantalla
if (file_exists($qrFilename)) {
    unlink($qrFilename);
}

// Generar visor PDF
$pdf->Output('I', 'Credencial_' . $datos['numero_credencial'] . '.pdf');
exit();
?>
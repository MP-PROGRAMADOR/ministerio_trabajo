<?php
require_once '../conexion/conexion.php';
require('../libs/fpdf/fpdf.php');

/* ==========================================================================
   PROCESAMIENTO DE FILTROS MÚLTIPLES (JSON DECODIFICADO)
   ========================================================================== */
$filtrosRaw = $_GET['filtros'] ?? '[]';
$filtros = json_decode($filtrosRaw, true);

if (!is_array($filtros)) {
    $filtros = [];
}

// Consulta SQL optimizada según la estructura real de tu tabla personas_desempleadas
$sql = "SELECT 
        id,
        nombre,
        apellido,
        telefono,
        email,
        dip_numero,
        dip_copia_pdf,
        provincia,
        distrito,
        sede_formacion,
        profesion_oficio,
        fecha_desempleo,
        estado_seleccion
        FROM personas_desempleadas 
        WHERE 1=1";

$params = [];
$cadenasTextoFiltros = []; 

// Diccionario de mapeo seguro para evitar Inyección SQL
$columnas = [
    'nombre'    => 'nombre',
    'apellido'  => 'apellido',
    'provincia' => 'provincia',
    'distrito'  => 'distrito',
    'sede'      => 'sede_formacion',
    'estado'    => 'estado_seleccion'
];

foreach ($filtros as $index => $f) {
    $tipo = $f['tipo'] ?? '';
    $valor = trim($f['valor'] ?? '');

    if ($valor !== '' && isset($columnas[$tipo])) {
        $placeholder = ":valor_" . $index;
        $sql .= " AND {$columnas[$tipo]} LIKE {$placeholder}";
        $params[$placeholder] = "%$valor%";
        $cadenasTextoFiltros[] = "{$tipo}: {$valor}";
    }
}

// Ordenamos formalmente por Apellido y Nombre
$sql .= " ORDER BY apellido ASC, nombre ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$personas = $stmt->fetchAll(PDO::FETCH_ASSOC);


/* ==========================================================================
   CLASE PDF MINISTERIAL
   ========================================================================== */
class PDF extends FPDF {

    function Header() {
        // LOGO MINISTERIO
        $logo = '../src/img/logo_nuevo.png';
        if(file_exists($logo)){
            $this->Image($logo, 10, 8, 20);
        }

        // ENCABEZADO INSTITUCIONAL
        $this->SetFont('Arial', 'B', 14);
        $this->SetTextColor(0, 51, 102);
        $this->Cell(30);
        $this->Cell(0, 6, mb_convert_encoding('REPÚBLICA DE GUINEA ECUATORIAL', 'ISO-8859-1', 'UTF-8'), 0, 1);

        $this->SetFont('Arial', 'B', 11);
        $this->Cell(30);
        $this->Cell(0, 6, mb_convert_encoding('MINISTERIO DE TRABAJO, FOMENTO DE EMPLEO Y SEGURIDAD SOCIAL', 'ISO-8859-1', 'UTF-8'), 0, 1);

        $this->SetFont('Arial', '', 10);
        $this->Cell(30);
        $this->Cell(0, 5, mb_convert_encoding('Oficina Central de Empleo', 'ISO-8859-1', 'UTF-8'), 0, 1);

        // Doble línea institucional superior
        $this->Ln(2);
        $this->SetDrawColor(0, 51, 102);
        $this->SetLineWidth(0.8);
        $this->Line(10, 34, 287, 34);
        $this->SetLineWidth(0.2);
        $this->Line(10, 36, 287, 36);

        // TÍTULO DEL REPORTE
        $this->Ln(6);
        $this->SetFont('Arial', 'B', 13);
        $this->SetTextColor(30, 30, 30);
        $this->Cell(0, 8, mb_convert_encoding('SOLICITUDES DE FORMACIÓN (CANDIDATOS DESEMPLEADOS)', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');

        // Código único de auditoría del documento
        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(90);
        $this->Cell(0, 5, 'Codigo Documento: MT-OCE-' . date('Ymd-His'), 0, 1, 'C');

        $this->Ln(3);
    }

    function Footer() {
        $this->SetY(-18);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(120);

        $this->Line(10, $this->GetY(), 287, $this->GetY());
        $this->Ln(2);

        $this->Cell(0, 5, mb_convert_encoding('Documento Oficial - Oficina Central de Empleo','ISO-8859-1','UTF-8'), 0, 1, 'L');
        $this->Cell(0, 5, 'Pagina ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
        $this->Cell(0, 5, date('d/m/Y H:i'), 0, 0, 'R');
    }
}


/* ==========================================================================
   GENERAR ESTRUCTURA DEL PDF
   ========================================================================== */
$pdf = new PDF('L', 'mm', 'A4'); // Formato horizontal (Landscape)
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(true, 20);


/* ===== CAJA INFORMATIVA ===== */
$pdf->SetFillColor(242, 244, 248);
$pdf->SetDrawColor(200, 200, 200);
$pdf->SetFont('Arial', '', 9.5);

$filtroTexto = !empty($cadenasTextoFiltros) ? "Filtros: " . implode(', ', $cadenasTextoFiltros) : "Sin filtros aplicados";

$pdf->Cell(140, 8, mb_convert_encoding("Fecha de emision: " . date('d/m/Y'), 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', true);
$pdf->Cell(140, 8, mb_convert_encoding($filtroTexto, 'ISO-8859-1', 'UTF-8'), 1, 1, 'L', true);

$pdf->Cell(140, 8, 'Total de solicitudes encontradas: ' . count($personas), 1, 0, 'L', true);
$pdf->Cell(140, 8, mb_convert_encoding('Tipo de documento: Reporte de Seleccion Laboral', 'ISO-8859-1', 'UTF-8'), 1, 1, 'L', true);

$pdf->Ln(5);


/* ===== CABECERA DE LA TABLA ===== */
$pdf->SetFillColor(30, 70, 140); // Azul Institucional
$pdf->SetTextColor(255);
$pdf->SetFont('Arial', 'B', 9);

// Medidas calculadas para cubrir los 277mm disponibles de ancho en A4 horizontal
$w = [10, 60, 28, 25, 50, 22, 50, 32];
$headers = ['N°', 'Candidato (Apellidos, Nombres)', 'DIP', 'Telefono', 'Procedencia (Prov/Dist)', 'Sede', 'Profesion / Oficio', 'Estado'];

foreach($headers as $i => $col){
    $pdf->Cell($w[$i], 9, mb_convert_encoding($col, 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
}
$pdf->Ln();


/* ===== RENDERIZADO DE LAS FILAS ===== */
$pdf->SetFont('Arial', '', 8.5);
$pdf->SetTextColor(40);
$pdf->SetFillColor(248, 250, 253); // Zebra striping sutil

$fill = false;
$contador = 1; 

foreach($personas as $per){
    
    // Formateamos Apellido, Nombre
    $candidato = $per['apellido'] . ', ' . $per['nombre'];
    
    // Ubicación geográfica concatenada
    $ubicacion = $per['provincia'] . ' / ' . $per['distrito'];
    
    // Tratamiento visual de ENUM de selección (LISTA_ESPERA -> LISTA ESPERA)
    $estadoFormateado = str_replace('_', ' ', $per['estado_seleccion']);

    $pdf->Cell($w[0], 8, $contador++, 1, 0, 'C', $fill);
    $pdf->Cell($w[1], 8, mb_convert_encoding($candidato, 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', $fill);
    $pdf->Cell($w[2], 8, $per['dip_numero'], 1, 0, 'C', $fill);
    $pdf->Cell($w[3], 8, $per['telefono'], 1, 0, 'C', $fill);
    $pdf->Cell($w[4], 8, mb_convert_encoding($ubicacion, 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', $fill);
    $pdf->Cell($w[5], 8, mb_convert_encoding($per['sede_formacion'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', $fill);
    $pdf->Cell($w[6], 8, mb_convert_encoding($per['profesion_oficio'] ?? 'Sin especificar', 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', $fill);
    $pdf->Cell($w[7], 8, mb_convert_encoding($estadoFormateado, 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', $fill);

    $pdf->Ln();
    $fill = !$fill;
}


/* ===== SECCIÓN DE FIRMAS DE LA OFICINA CENTRAL ===== */
$pdf->Ln(15);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 6, '____________________________________', 0, 1, 'R');
$pdf->Cell(0, 6, mb_convert_encoding('Oficina Central de Empleo', 'ISO-8859-1', 'UTF-8'), 0, 1, 'R');
$pdf->Cell(0, 6, mb_convert_encoding('Ministerio de Trabajo, Fomento de Empleo y Seguridad Social', 'ISO-8859-1', 'UTF-8'), 0, 1, 'R');

// Forzamos la descarga/visualización del PDF en el navegador
$pdf->Output('I', 'Reporte_Oficina_Central_Empleo.pdf');
?>
<?php
require_once '../config/conexion.php';
require('../fpdf/fpdf.php');

/* ==========================================================================
   PROCESAMIENTO DE FILTROS MÚLTIPLES (JSON DECODIFICADO)
   ========================================================================== */
// Leemos el parámetro 'filtros', si no existe inicializamos un array vacío
$filtrosRaw = $_GET['filtros'] ?? '[]';
$filtros = json_decode($filtrosRaw, true);

if (!is_array($filtros)) {
    $filtros = [];
}

// Base de la consulta SQL
$sql = "SELECT 
        e.id,
        e.nombre_completo,
        e.codigo_acceso,
        e.fecha_nacimiento,
        e.anio_inicio_carrera,
        e.anio_fin_carrera,
        p.nombre AS pais,
        c.nombre AS ciudad,
        e.telefono
        FROM estudiantes e
        INNER JOIN paises p ON e.pais_id = p.id
        LEFT JOIN ciudades c ON e.ciudad_id = c.id
        WHERE 1=1";

$params = [];
$cadenasTextoFiltros = []; // Para la caja informativa del PDF

// Diccionario de mapeo seguro para evitar Inyección SQL
$columnas = [
    'nombre'    => 'e.nombre_completo',
    'pais'      => 'p.nombre',
    'ciudad'    => 'c.nombre',
    'fecha_fin' => 'e.anio_fin_carrera'
];

// Recorremos cada filtro acumulado enviado desde la interfaz
foreach ($filtros as $index => $f) {
    $tipo = $f['tipo'] ?? '';
    $valor = trim($f['valor'] ?? '');

    if ($valor !== '' && isset($columnas[$tipo])) {
        // Creamos marcadores únicos para PDO (ej: :valor_0, :valor_1)
        $placeholder = ":valor_" . $index;
        
        $sql .= " AND {$columnas[$tipo]} LIKE {$placeholder}";
        $params[$placeholder] = "%$valor%";

        // Guardamos texto legible para mostrar en el encabezado del PDF
        $cadenasTextoFiltros[] = "{$tipo}: {$valor}";
    }
}

// Mantenemos el orden por defecto
$sql .= " ORDER BY e.nombre_completo ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$estudiantes = $stmt->fetchAll(PDO::FETCH_ASSOC);


/* ==========================================================================
   CLASE PDF MINISTERIAL
   ========================================================================== */
class PDF extends FPDF {

    function Header() {
        // LOGO
        $logo = '../config/img/logo1.png';
        if(file_exists($logo)){
            $this->Image($logo,10,8,20);
        }

        // NOMBRE INSTITUCIONAL
        $this->SetFont('Arial','B',14);
        $this->SetTextColor(0,51,102);
        $this->Cell(30);
        $this->Cell(0,6, mb_convert_encoding('REPUBLICA DE GUINEA ECUATORIAL','ISO-8859-1','UTF-8'),0,1);

        $this->SetFont('Arial','',11);
        $this->Cell(30);
        $this->Cell(0,6, mb_convert_encoding('TESORERIA Y PATRIMONIO DEL ESTADO','ISO-8859-1','UTF-8'),0,1);

        $this->Cell(30);
        $this->Cell(0,6, mb_convert_encoding('','ISO-8859-1','UTF-8'),0,1);

        // Líneas institucionales
        $this->Ln(2);
        $this->SetDrawColor(0,51,102);
        $this->SetLineWidth(0.8);
        $this->Line(10,32,287,32);
        $this->SetLineWidth(0.2);
        $this->Line(10,34,287,34);

        // TÍTULO
        $this->Ln(6);
        $this->SetFont('Arial','B',13);
        $this->SetTextColor(30,30,30);
        $this->Cell(0,8, mb_convert_encoding('REPORTE OFICIAL DE ESTUDIANTES BECADOS','ISO-8859-1','UTF-8'),0,1,'C');

        // Código del documento
        $this->SetFont('Arial','',9);
        $this->SetTextColor(90);
        $this->Cell(0,5, 'Codigo Documento: TGE-RPT-'.date('Ymd-His'),0,1,'C');

        $this->Ln(3);
    }

    function Footer() {
        $this->SetY(-18);
        $this->SetFont('Arial','I',8);
        $this->SetTextColor(120);

        $this->Line(10,$this->GetY(),287,$this->GetY());
        $this->Ln(2);

        $this->Cell(0,5, mb_convert_encoding('Documento Oficial - Uso Interno Institucional','ISO-8859-1','UTF-8'),0,1,'L');
        $this->Cell(0,5,'Pagina '.$this->PageNo().'/{nb}',0,0,'C');
        $this->Cell(0,5,date('d/m/Y H:i'),0,0,'R');
    }
}


/* ==========================================================================
   GENERAR PDF
   ========================================================================== */
$pdf = new PDF('L','mm','A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetMargins(10,10,10);
$pdf->SetAutoPageBreak(true,20);


/* ===== CAJA INFORMATIVA ===== */
$pdf->SetFillColor(240,240,240);
$pdf->SetDrawColor(200,200,200);
$pdf->SetFont('Arial','',10);

// Unimos los textos legibles por comas si hay filtros, de lo contrario indica "Sin Filtros"
$filtroTexto = !empty($cadenasTextoFiltros) ? "Filtros: " . implode(', ', $cadenasTextoFiltros) : "Sin filtros aplicados";

$pdf->Cell(140,8,mb_convert_encoding("Fecha de emision: ".date('d/m/Y'),'ISO-8859-1','UTF-8'),1,0,'L',true);
$pdf->Cell(140,8,mb_convert_encoding($filtroTexto,'ISO-8859-1','UTF-8'),1,1,'L',true);

$pdf->Cell(140,8,'Total de registros encontrados: '.count($estudiantes),1,0,'L',true);
$pdf->Cell(140,8,mb_convert_encoding('Tipo de documento: Reporte Oficial Cruzado','ISO-8859-1','UTF-8'),1,1,'L',true);

$pdf->Ln(6);


/* ===== CABECERA TABLA ===== */
$pdf->SetFillColor(30,70,140);
$pdf->SetTextColor(255);
$pdf->SetFont('Arial','B',9);

$w = [10, 65, 30, 30, 40, 35, 30, 20, 20];
$headers = ['N°','Nombre','Codigo','Nacimiento','Pais','Ciudad','Telefono','Inicio','Final'];

foreach($headers as $i=>$col){
    $pdf->Cell($w[$i],9,mb_convert_encoding($col,'ISO-8859-1','UTF-8'),1,0,'C',true);
}
$pdf->Ln();


/* ===== FILAS ===== */
$pdf->SetFont('Arial','',9);
$pdf->SetTextColor(40);
$pdf->SetFillColor(245,249,252);

$fill = false;
$contador = 1; 

foreach($estudiantes as $e){
    $pdf->Cell($w[0],8,$contador++,1,0,'C',$fill);
    $pdf->Cell($w[1],8,mb_convert_encoding($e['nombre_completo'],'ISO-8859-1','UTF-8'),1,0,'L',$fill);
    $pdf->Cell($w[2],8,$e['codigo_acceso'],1,0,'C',$fill);
    $pdf->Cell($w[3],8,date('d/m/Y',strtotime($e['fecha_nacimiento'])),1,0,'C',$fill);
    $pdf->Cell($w[4],8,mb_convert_encoding($e['pais'],'ISO-8859-1','UTF-8'),1,0,'L',$fill);
    $pdf->Cell($w[5],8,mb_convert_encoding($e['ciudad'],'ISO-8859-1','UTF-8'),1,0,'L',$fill);
    $pdf->Cell($w[6],8,$e['telefono'],1,0,'C',$fill);
    $pdf->Cell($w[7],8,$e['anio_inicio_carrera'],1,0,'C',$fill);
    $pdf->Cell($w[8],8,$e['anio_fin_carrera'],1,0,'C',$fill);

    $pdf->Ln();
    $fill = !$fill;
}


/* ===== FIRMA INSTITUCIONAL ===== */
$pdf->Ln(12);
$pdf->SetFont('Arial','',10);
$pdf->Cell(0,6,'____________________________________',0,1,'R');
$pdf->Cell(0,6,mb_convert_encoding('Negociado de Misiones Diplomaticas','ISO-8859-1','UTF-8'),0,1,'R');
$pdf->Cell(0,6,mb_convert_encoding('Tesoreria General y Patrimonio del Estado','ISO-8859-1','UTF-8'),0,1,'R');

$pdf->Output('I','Reporte_Estudiantes_Becados.pdf');
?>
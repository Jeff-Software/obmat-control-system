<?php

require_once('../fpdf/fpdf.php');
require_once('../config/conexion.php');
require_once('../config/auth.php');

$pdf = new FPDF();
$pdf->AddPage();

/* CONFIGURACIÓN */

$config = $conexion->query("
SELECT *
FROM configuracion
LIMIT 1
");

$empresa = $config->fetch_assoc();

/* LOGO */

$pdf->Image(
    '../assets/img/logo.png',
    10,
    10,
    25
);

/* ENCABEZADO */

$pdf->SetXY(40,10);

$pdf->SetFont('Arial','B',18);

$pdf->Cell(
    150,
    10,
    utf8_decode($empresa['nombre_negocio']),
    0,
    1,
    'C'
);

$pdf->SetFont('Arial','',10);

$pdf->Cell(
    190,
    6,
    utf8_decode($empresa['direccion']),
    0,
    1,
    'C'
);

$pdf->Cell(
    190,
    6,
    'Tel: '.$empresa['telefono'],
    0,
    1,
    'C'
);

$pdf->Ln(5);

$pdf->SetFont('Arial','B',15);

$pdf->Cell(
    190,
    10,
    'REPORTE DE AUDITORIA',
    0,
    1,
    'C'
);

$pdf->SetFont('Arial','',10);

$pdf->Cell(
    190,
    6,
    'Generado el: '.date('d/m/Y H:i'),
    0,
    1,
    'C'
);

$pdf->Ln(8);

$sqlResumen = "
SELECT
COUNT(*) total,
SUM(CASE WHEN accion LIKE '%Inicio de sesión%' THEN 1 ELSE 0 END) logins,
SUM(CASE WHEN accion LIKE '%Cierre de sesión%' THEN 1 ELSE 0 END) logouts,
SUM(CASE WHEN accion LIKE '%Creó usuario%' THEN 1 ELSE 0 END) creados,
SUM(CASE WHEN accion LIKE '%Editó usuario%' THEN 1 ELSE 0 END) editados,
SUM(CASE WHEN accion LIKE '%Cambió estado%' THEN 1 ELSE 0 END) estados
FROM logs
";

$resumen = $conexion
->query($sqlResumen)
->fetch_assoc();

$pdf->SetFillColor(37,99,235);
$pdf->SetTextColor(255,255,255);

$pdf->Cell(
    190,
    8,
    'RESUMEN DE ACTIVIDAD',
    1,
    1,
    'C',
    true
);

$pdf->SetTextColor(0,0,0);

$pdf->Cell(95,8,'Total Eventos',1);
$pdf->Cell(95,8,$resumen['total'],1,1);

$pdf->Cell(95,8,'Inicios de sesion',1);
$pdf->Cell(95,8,$resumen['logins'],1,1);

$pdf->Cell(95,8,'Cierres de sesion',1);
$pdf->Cell(95,8,$resumen['logouts'],1,1);

$pdf->Cell(95,8,'Usuarios creados',1);
$pdf->Cell(95,8,$resumen['creados'],1,1);

$pdf->Cell(95,8,'Usuarios editados',1);
$pdf->Cell(95,8,$resumen['editados'],1,1);

$pdf->Cell(95,8,'Cambios de estado',1);
$pdf->Cell(95,8,$resumen['estados'],1,1);

$pdf->Ln(8);

$pdf->SetFillColor(37,99,235);
$pdf->SetTextColor(255,255,255);

$pdf->Cell(
    190,
    8,
    'ULTIMOS MOVIMIENTOS',
    1,
    1,
    'C',
    true
);

$pdf->SetTextColor(0,0,0);

$pdf->SetFont('Arial','B',9);

$pdf->Cell(20,8,'ID',1);
$pdf->Cell(50,8,'Usuario',1);
$pdf->Cell(80,8,'Accion',1);
$pdf->Cell(40,8,'Fecha',1);

$pdf->Ln();

$sql = "
SELECT
l.id,
u.nombre,
l.accion,
l.fecha
FROM logs l
LEFT JOIN usuarios u
ON u.id = l.usuario_id
ORDER BY l.fecha DESC
LIMIT 20
";

$result = $conexion->query($sql);

$pdf->SetFont('Arial','',8);

while($row = $result->fetch_assoc()){

    $pdf->Cell(20,7,$row['id'],1);

    $pdf->Cell(
        50,
        7,
        utf8_decode($row['nombre'] ?? 'Eliminado'),
        1
    );

    $pdf->Cell(
        80,
        7,
        utf8_decode($row['accion']),
        1
    );

    $pdf->Cell(
        40,
        7,
        date(
            'd/m/Y H:i',
            strtotime($row['fecha'])
        ),
        1
    );

    $pdf->Ln();
}

$pdf->Ln(10);

$pdf->SetFont('Arial','I',8);

$pdf->Cell(
    190,
    5,
    'OBMAT CONTROL - Auditoria del Sistema',
    0,
    1,
    'C'
);

$pdf->Ln(5);

$pdf->SetFillColor(37,99,235);
$pdf->SetTextColor(255,255,255);

$pdf->Cell(
    190,
    8,
    'OBSERVACIONES',
    1,
    1,
    'C',
    true
);

$pdf->SetTextColor(0,0,0);

$pdf->SetFont('Arial','',10);

$pdf->MultiCell(
    190,
    7,
    utf8_decode(
        'Este reporte muestra las actividades registradas por los usuarios del sistema, incluyendo accesos, cierres de sesión, creación de usuarios, modificaciones y cambios de estado.'
    ),
    1
);

$pdf->Ln(5);

$pdf->Output(
    'I',
    'Auditoria_'.date('Ymd_His').'.pdf'
);


<?php

require('../config/conexion.php');
require('../config/config_global.php');
$config = $conexion->query(
    "SELECT simbolo_moneda FROM configuracion LIMIT 1"
);

$simboloMoneda = $config->fetch_assoc()['simbolo_moneda'] ?? 'S/';
require('../fpdf/fpdf.php');

$idVenta = $_GET['id'] ?? 0;

if (!$idVenta) {
    die(__('venta_no_encontrada'));
}

/* DATOS DE LA VENTA */

$sqlVenta = "
SELECT v.*, u.nombre AS cajero
FROM ventas v
LEFT JOIN usuarios u ON v.usuario_id = u.id
WHERE v.id = ?
";

$stmt = $conexion->prepare($sqlVenta);
$stmt->bind_param("i", $idVenta);
$stmt->execute();

$venta = $stmt->get_result()->fetch_assoc();

if (!$venta) {
    die(__('venta_inexistente'));
}

/* DETALLE */

$sqlDetalle = "
SELECT
    p.nombre,
    d.cantidad,
    d.precio_unitario
FROM detalle_ventas d
INNER JOIN productos p
    ON d.producto_id = p.id
WHERE d.id_venta = ?
";

$stmt = $conexion->prepare($sqlDetalle);
$stmt->bind_param("i", $idVenta);
$stmt->execute();

$detalle = $stmt->get_result();

/* PDF */

$pdf = new FPDF('P','mm',[80,220]);
$pdf->AddPage();

$pdf->SetMargins(5,5,5);

/* ENCABEZADO */

$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,8,'OBMAT',0,1,'C');

$pdf->SetFont('Arial','',9);
$pdf->Cell(70,5,utf8_decode(__('gracias_compra')),0,1,'C');

$pdf->Ln(3);

$pdf->Cell(70,0,'','T',1);

$pdf->Ln(4);

/* DATOS */

$pdf->SetFont('Arial','',8);

$pdf->Cell(25,5,__('fecha').':',0,0);
$pdf->Cell(45,5,date('d/m/Y', strtotime($venta['fecha'])),0,1);

$pdf->Cell(25,5,__('hora').':',0,0);
$pdf->Cell(45,5,date('H:i:s', strtotime($venta['fecha'])),0,1);

$pdf->Cell(25,5,__('factura').':',0,0);
$pdf->Cell(45,5,'#'.$venta['id'],0,1);

$pdf->Cell(25,5,__('cajero').':',0,0);
$pdf->Cell(45,5,utf8_decode($venta['cajero']),0,1);

$pdf->Ln(3);

$pdf->Cell(70,0,'','T',1);

$pdf->Ln(3);

/* CABECERA PRODUCTOS */

$pdf->SetFont('Arial','B',7);

$pdf->Cell(28,5,__('producto'),0,0);
$pdf->Cell(10,5,__('cantidad'),0,0,'C');
$pdf->Cell(14,5,__('precio'),0,0,'R');
$pdf->Cell(18,5,__('total'),0,1,'R');

$pdf->Cell(70,0,'','T',1);

$pdf->Ln(2);

/* PRODUCTOS */

$pdf->SetFont('Arial','',7);

while($row = $detalle->fetch_assoc()){

    $totalLinea =
        $row['cantidad'] *
        $row['precio_unitario'];

    $pdf->Cell(
        28,
        5,
        utf8_decode(substr($row['nombre'],0,16)),
        0,
        0
    );

    $pdf->Cell(
        10,
        5,
        $row['cantidad'],
        0,
        0,
        'C'
    );

    $pdf->Cell(
        14,
        5,
        $simboloMoneda.' '.number_format($row['precio_unitario'],2),
        0,
        0,
        'R'
    );

    $pdf->Cell(
        18,
        5,
        $simboloMoneda.' '.number_format($totalLinea,2),
        0,
        1,
        'R'
    );
}

$pdf->Ln(2);

$pdf->Cell(70,0,'','T',1);

$pdf->Ln(4);

/* TOTAL */

$pdf->SetFont('Arial','B',9);

$pdf->Cell(40,6,__('total').':',0,0);
$pdf->Cell(
    30,
    6,
    $simboloMoneda.' '.number_format($venta['total'],2),
    0,
    1,
    'R'
);

$pdf->SetFont('Arial','',8);

$pdf->Cell(40,5,__('metodo_pago').':',0,0);
$pdf->Cell(
    30,
    5,
    __(strtolower($venta['metodo_pago'])),
    0,
    1,
    'R'
);

/* CAMBIO */

$pdf->Cell(40,5,__('cambio').':',0,0);
$pdf->Cell(
    30,
    5,
    $simboloMoneda.' 0.00',
    0,
    1,
    'R'
);

$pdf->Ln(3);

$pdf->Cell(70,0,'','T',1);

$pdf->Ln(5);

$pdf->SetFont('Arial','',8);

$pdf->Cell(
    70,
    5,
    utf8_decode(__('vuelva_pronto')),
    0,
    1,
    'C'
);

$pdf->Cell(
    70,
    5,
    utf8_decode(__('conserve_comprobante')),
    0,
    1,
    'C'
);

$pdf->Output();
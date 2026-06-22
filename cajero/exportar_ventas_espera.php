<?php

require('../fpdf/fpdf.php');
require('../config/config_global.php');


if (!isset($_POST['venta'])) {
    die("No se recibió información de la venta");
}


$venta = json_decode($_POST['venta'], true);


if (!$venta || !isset($venta['carrito'])) {
    die("Venta inválida");
}



$pdf = new FPDF('P','mm',[80,250]);

$pdf->AddPage();


// TITULO

$pdf->SetFont('Arial','B',14);

$pdf->Cell(
    0,
    8,
    __('ticket_venta_espera'),
    0,
    1,
    'C'
);


$pdf->SetFont('Arial','',8);

$pdf->Cell(0,5,'------------------------------',0,1,'C');



// DATOS

$hora = $venta['hora'] ?? date('H:i');


$pdf->Cell(
    0,
    5,
    __('ticket_fecha').': '.date('d/m/Y'),
    0,
    1
);


$pdf->Cell(
    0,
    5,
    __('ticket_hora').': '.$hora,
    0,
    1
);


$pdf->Cell(0,5,'------------------------------',0,1,'C');



// PRODUCTOS

$pdf->SetFont('Arial','B',9);

$pdf->Cell(
    0,
    6,
    __('ticket_productos'),
    0,
    1
);


$pdf->SetFont('Arial','',8);



$total = 0;



foreach($venta['carrito'] as $item){


    if(!isset($item['nombre'])){
        continue;
    }


    $nombre = $item['nombre'];

    $cantidad = $item['cantidad'] ?? 1;

    $precio = $item['precio'] ?? 0;


    $subtotal = $cantidad * $precio;


    $total += $subtotal;



    // nombre

    $pdf->MultiCell(
        0,
        5,
        $nombre
    );


    // cantidad precio

    $pdf->Cell(
        0,
        5,
        $cantidad." x S/ ".number_format($precio,2).
        " = S/ ".number_format($subtotal,2),
        0,
        1
    );


    $pdf->Ln(1);

}



$pdf->Cell(
    0,
    5,
    '------------------------------',
    0,
    1,
    'C'
);



// TOTAL

$pdf->SetFont('Arial','B',10);


$pdf->Cell(
    0,
    7,
    __('ticket_total').': S/ '.number_format($total,2),
    0,
    1,
    'C'
);



$pdf->SetFont('Arial','',8);


$pdf->Cell(
    0,
    8,
    __('ticket_pendiente'),
    0,
    1,
    'C'
);


$pdf->Cell(
    0,
    5,
    __('ticket_gracias'),
    0,
    1,
    'C'
);



$pdf->Output();

?>
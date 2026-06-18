<?php
// generar_ticket.php
// Usamos 'fpdf/fpdf.php' porque está dentro de tu carpeta obmat_control
require_once('../fpdf/fpdf.php');
require_once('../config/conexion.php');

$id_venta = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Consultar datos
$stmt = $conexion->prepare("SELECT 
                            v.fecha,
                            v.total,
                            v.metodo_pago,
                            u.nombre AS cajero,
                            c.nombre_negocio,
                            c.simbolo_moneda
                           FROM ventas v 
                           JOIN usuarios u ON v.usuario_id = u.id 
                           CROSS JOIN configuracion c WHERE v.id = ?");
$stmt->bind_param("i", $id_venta);
$stmt->execute();
$venta = $stmt->get_result()->fetch_assoc();

// Consultar productos
$stmtDet = $conexion->prepare("SELECT p.nombre, dv.cantidad, dv.precio_unitario 
                               FROM detalle_ventas dv 
                               JOIN productos p ON dv.producto_id = p.id WHERE dv.id_venta = ?");
$stmtDet->bind_param("i", $id_venta);
$stmtDet->execute();
$productos = $stmtDet->get_result();

// Crear PDF
// ====================================
// CREAR PDF TICKET MEJORADO
// ====================================

$pdf = new FPDF('P', 'mm', array(80, 180));
$pdf->AddPage();

$pdf->SetMargins(5,5,5);

// Logo (opcional)
$logo = '../assets/img/logo.png';

if(file_exists($logo)){
    $pdf->Image($logo, 28, 5, 25);
    $pdf->Ln(22);
}

// Empresa
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,5, utf8_decode($venta['nombre_negocio']),0,1,'C');

$pdf->SetFont('Arial','',8);
$pdf->Cell(0,4,'Comprobante de Venta',0,1,'C');

$pdf->Ln(2);

$pdf->Cell(0,0,'',1,1);
$pdf->Ln(3);

// Datos de venta
$pdf->SetFont('Arial','',8);

$pdf->Cell(22,4,'Venta N°:',0,0);
$pdf->Cell(0,4,$id_venta,0,1);

$pdf->Cell(22,4,'Fecha:',0,0);
$pdf->Cell(0,4,date('d/m/Y H:i', strtotime($venta['fecha'])),0,1);

$pdf->Cell(22,4,'Cajero:',0,0);
$pdf->Cell(0,4,utf8_decode($venta['cajero']),0,1);

$pdf->Cell(22,4,'Pago:',0,0);
$pdf->Cell(0,4,utf8_decode($venta['metodo_pago']),0,1);

$pdf->Ln(2);

$pdf->Cell(0,0,'',1,1);
$pdf->Ln(3);

// Cabecera productos
$pdf->SetFont('Arial','B',7);

$pdf->Cell(26,5,'Producto',0,0);
$pdf->Cell(8,5,'Cant',0,0,'C');
$pdf->Cell(12,5,'P.U',0,0,'R');
$pdf->Cell(16,5,'Total',0,1,'R');

$pdf->SetFont('Arial','',7);

while($row = $productos->fetch_assoc()){

    $subtotal =
        $row['cantidad'] *
        $row['precio_unitario'];

    $pdf->Cell(
        26,
        5,
        substr(
            utf8_decode($row['nombre']),
            0,
            16
        ),
        0,
        0
    );

    $pdf->Cell(
        8,
        5,
        $row['cantidad'],
        0,
        0,
        'C'
    );

    $pdf->Cell(
        12,
        5,
        $venta['simbolo_moneda'].' '.number_format(
            $row['precio_unitario'],
            2
        ),
        0,
        0,
        'R'
    );

    $pdf->Cell(
        16,
        5,
        $venta['simbolo_moneda'].' '.number_format(
            $subtotal,
            2
        ),
        0,
        1,
        'R'
    );
}

$pdf->Ln(2);

$pdf->Cell(0,0,'',1,1);
$pdf->Ln(3);

// Total
$pdf->SetFont('Arial','B',10);

$pdf->Cell(
    40,
    6,
    'TOTAL',
    0,
    0
);

$pdf->Cell(
    30,
    6,
    $venta['simbolo_moneda'].' '.number_format($venta['total'],2),
    0,
    1,
    'R'
);

$pdf->Ln(5);

// Mensaje final
$pdf->SetFont('Arial','',8);

$pdf->MultiCell(
    0,
    4,
    utf8_decode(
        "Gracias por su compra.\nConserve este comprobante."
    ),
    0,
    'C'
);

$pdf->Ln(3);

$pdf->SetFont('Arial','I',7);

$pdf->Cell(
    0,
    4,
    utf8_decode('Sistema OBMAT CONTROL'),
    0,
    1,
    'C'
);

// Mostrar PDF
$pdf->Output(
    'I',
    'Ticket_'.$id_venta.'.pdf'
);
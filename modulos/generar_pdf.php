<?php

require_once('../fpdf/fpdf.php');
require_once('../config/conexion.php');

$fechaInicio = $_GET['fechaInicio'] ?? '';
$fechaFin    = $_GET['fechaFin'] ?? '';

$whereFecha = '';

if(!empty($fechaInicio) && !empty($fechaFin)){

    $whereFecha =
        " WHERE DATE(fecha)
          BETWEEN '$fechaInicio'
          AND '$fechaFin' ";
}

$pdf = new FPDF();
$pdf->AddPage();

/* =====================================
   CONFIGURACIÓN DEL NEGOCIO
===================================== */

$config = $conexion->query("SELECT * FROM configuracion LIMIT 1");
$empresa = $config->fetch_assoc();
$moneda = $empresa['simbolo_moneda'] ?? 'S/';
/* LOGO */

$pdf->Image(
    '../assets/img/logo.png',
    10,
    10,
    25
);

/* =====================================
   KPIs GENERALES
===================================== */

$sql = "
SELECT
    SUM(total) AS ventas,
    COUNT(*) AS transacciones,
    AVG(total) AS ticket
FROM ventas
$whereFecha
";

$res = $conexion->query($sql);
$datos = $res->fetch_assoc();

$totalVentas = $datos['ventas'] ?? 0;
$totalTransacciones = $datos['transacciones'] ?? 0;
$ticketPromedio = $datos['ticket'] ?? 0;

/* =====================================
   PRODUCTOS VENDIDOS
===================================== */

$sqlProductos = "
SELECT SUM(dv.cantidad) AS vendidos
FROM detalle_ventas dv
INNER JOIN ventas v
    ON v.id = dv.id_venta
";

if(!empty($fechaInicio) && !empty($fechaFin)){
    $sqlProductos .= "
    WHERE DATE(v.fecha)
    BETWEEN '$fechaInicio'
    AND '$fechaFin'
    ";
}

$resProd = $conexion->query($sqlProductos);
$filaProd = $resProd->fetch_assoc();

$totalProductos = (int)($filaProd['vendidos'] ?? 0);

/* =====================================
   PRODUCTO MÁS VENDIDO
===================================== */

$sqlTopProducto = "
SELECT
    p.nombre,
    SUM(d.cantidad) total
FROM detalle_ventas d
INNER JOIN productos p
    ON p.id = d.producto_id
INNER JOIN ventas v
    ON v.id = d.id_venta
";

if(!empty($fechaInicio) && !empty($fechaFin)){

    $sqlTopProducto .= "
    WHERE DATE(v.fecha)
    BETWEEN '$fechaInicio'
    AND '$fechaFin'
    ";
}

$sqlTopProducto .= "
GROUP BY p.id
ORDER BY total DESC
LIMIT 1
";

$resTop = $conexion->query($sqlTopProducto);

$topProducto = $resTop->fetch_assoc();

if(!$topProducto){
    $topProducto = [
        'nombre' => 'Sin datos',
        'total' => 0
    ];
}

/* =====================================
   MÉTODO DE PAGO MÁS USADO
===================================== */

$sqlMetodo = "
SELECT metodo_pago,
COUNT(*) total
FROM ventas
WHERE metodo_pago IS NOT NULL
";

if(!empty($fechaInicio) && !empty($fechaFin)){
    $sqlMetodo .= "
    AND DATE(fecha)
    BETWEEN '$fechaInicio'
    AND '$fechaFin'
    ";
}

$sqlMetodo .= "
GROUP BY metodo_pago
ORDER BY total DESC
LIMIT 1
";

$resMetodo = $conexion->query($sqlMetodo);

$metodo = $resMetodo->fetch_assoc();

if(!$metodo){
    $metodo = [
        'metodo_pago' => 'Sin datos'
    ];
}

/* =====================================
   MEJOR CAJERO
===================================== */
$filtroCajero = "
WHERE u.rol = 'cajero'
";

if(!empty($fechaInicio) && !empty($fechaFin)){

    $filtroCajero .= "
    AND DATE(v.fecha)
    BETWEEN '$fechaInicio'
    AND '$fechaFin'
    ";
}

$sqlCajero = "
SELECT
    u.nombre,
    SUM(v.total) AS ventas
FROM ventas v
INNER JOIN usuarios u
    ON u.id = v.usuario_id
$filtroCajero
GROUP BY u.id
ORDER BY ventas DESC
LIMIT 1
";

$resCajero = $conexion->query($sqlCajero);

$cajero = $resCajero->fetch_assoc();

if(!$cajero){
    $cajero = [
        'nombre' => 'Sin datos',
        'ventas' => 0
    ];
}

/* =====================================
   STOCK CRÍTICO
===================================== */

$sqlStock = "
SELECT COUNT(*) total
FROM productos
WHERE stock <= stock_minimo
AND stock_minimo > 0
";

$resStock = $conexion->query($sqlStock);
$stock = $resStock->fetch_assoc();

$stockCritico = $stock['total'] ?? 0;

/* =====================================
   PDF
===================================== */



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
$pdf->Cell(190,6,utf8_decode($empresa['direccion']),0,1,'C');
$pdf->Cell(190,6,'Tel: '.$empresa['telefono'],0,1,'C');

$pdf->Ln(5);

$pdf->SetFont('Arial','B',15);
$pdf->Cell(190,10,'REPORTE GENERAL DE VENTAS',0,1,'C');

$pdf->SetFont('Arial','',10);
$pdf->Cell(
    190,
    8,
    'Generado el: '.date('d/m/Y H:i'),
    0,
    1,
    'C'
);

if(!empty($fechaInicio) && !empty($fechaFin)){

    $pdf->Cell(
        190,
        6,
        'Periodo: '
        .date('d/m/Y', strtotime($fechaInicio))
        .' al '
        .date('d/m/Y', strtotime($fechaFin)),
        0,
        1,
        'C'
    );

}else{

    $pdf->Cell(
        190,
        6,
        'Periodo: Historico completo',
        0,
        1,
        'C'
    );
}

$pdf->Ln(5);

/* RESUMEN EJECUTIVO */

$pdf->SetFillColor(37,99,235);
$pdf->SetTextColor(255,255,255);

$pdf->Cell(
    190,
    8,
    'RESUMEN EJECUTIVO',
    1,
    1,
    'C',
    true
);

$pdf->SetTextColor(0,0,0);

$pdf->SetFont('Arial','',11);

$pdf->Cell(95,8,'Ventas Totales',1);
$pdf->Cell(95,8,$moneda.' '.number_format($totalVentas,2),1,1);

$pdf->Cell(95,8,'Transacciones',1);
$pdf->Cell(95,8,$totalTransacciones,1,1);

$pdf->Cell(95,8,'Ticket Promedio',1);
$pdf->Cell(95,8,$moneda.' '.number_format($ticketPromedio,2),1,1);

$pdf->Cell(95,8,'Productos Vendidos',1);
$pdf->Cell(95,8,$totalProductos,1,1);

$pdf->Ln(8);

/* INDICADORES */

$pdf->SetFillColor(37,99,235);
$pdf->SetTextColor(255,255,255);

$pdf->Cell(
    190,
    8,
    'INDICADORES CLAVE',
    1,
    1,
    'C',
    true
);

$pdf->SetTextColor(0,0,0);

$pdf->SetFont('Arial','',10);

$pdf->Cell(95,8,'Producto mas vendido',1);
$pdf->Cell(
    95,
    8,
    utf8_decode($topProducto['nombre']).' ('.$topProducto['total'].')',
    1,
    1
);

$pdf->Cell(95,8,'Metodo de pago preferido',1);
$pdf->Cell(
    95,
    8,
    ucfirst($metodo['metodo_pago']),
    1,
    1
);

$pdf->Cell(95,8,'Mejor cajero',1);
$pdf->Cell(
    95,
    8,
    utf8_decode($cajero['nombre']),
    1,
    1
);

$pdf->Cell(95,8,'Productos con stock critico',1);
$pdf->Cell(
    95,
    8,
    $stockCritico,
    1,
    1
);

$pdf->Ln(8);

/* ANÁLISIS AUTOMÁTICO */

$pdf->SetFillColor(37,99,235);
$pdf->SetTextColor(255,255,255);

$pdf->Cell(
    190,
    8,
    'ANALISIS DEL REPORTE',
    1,
    1,
    'C',
    true
);

$pdf->SetTextColor(0,0,0);

$pdf->SetFont('Arial','',10);

$analisis = '';

if($totalVentas > 1000){
    $analisis .= '* El volumen de ventas es saludable.'."\n";
}else{
    $analisis .= '* Las ventas pueden incrementarse mediante promociones.'."\n";
}

if($ticketPromedio >= 20){
    $analisis .= '* El ticket promedio es favorable.'."\n";
}else{
    $analisis .= '* Se recomienda impulsar ventas cruzadas.'."\n";
}

if($stockCritico > 0){
    $analisis .= '* Existen productos que requieren reposicion inmediata.'."\n";
}

$analisis .= '* El metodo de pago dominante es '.ucfirst($metodo['metodo_pago']).'.';

$pdf->MultiCell(
    190,
    7,
    utf8_decode($analisis),
    1
);

$pdf->Ln(8);

/* OBSERVACIONES */

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

$pdf->SetFont('Arial','I',10);

$pdf->MultiCell(
    190,
    7,
    utf8_decode(
        "Este reporte ha sido generado automáticamente por el sistema OBMAT CONTROL. ".
        "La información presentada permite evaluar el desempeño comercial, ".
        "el comportamiento de ventas y el control del inventario del negocio."
    ),
    1
);

$pdf->Ln(10);

$pdf->SetFont('Arial','I',8);
$pdf->Cell(
    190,
    5,
    'OBMAT CONTROL - Sistema de Gestion Comercial',
    0,
    1,
    'C'
);

$pdf->Output(
    'I',
    'Reporte_Ventas_'.date('Ymd_His').'.pdf'
);
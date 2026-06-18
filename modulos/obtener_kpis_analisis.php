<?php
header('Content-Type: application/json');
require_once('../config/conexion.php');

$data = [];

// Consulta única para métricas básicas para mayor velocidad
$res_v = $conexion->query("SELECT SUM(total) as ventas, COUNT(id) as transacciones FROM ventas")->fetch_assoc();
$ventas = $res_v['ventas'] ?? 0;
$transacciones = $res_v['transacciones'] ?? 0;

// Ganancia (Join detallado)
$res_g = $conexion->query("SELECT SUM((dv.precio_unitario - p.precio_compra) * dv.cantidad) as ganancia 
                           FROM detalle_ventas dv 
                           JOIN productos p ON dv.producto_id = p.id")->fetch_assoc();
$ganancia = $res_g['ganancia'] ?? 0;

// Otros KPIs con manejo de nulos
$data['ventas'] = number_format($ventas, 2);
$data['ganancia'] = number_format($ganancia, 2);
$data['margen'] = ($ventas > 0) ? number_format(($ganancia / $ventas) * 100, 1) . '%' : '0%';
$data['transacciones'] = $transacciones;
$data['ticket_promedio'] = ($transacciones > 0) ? number_format($ventas / $transacciones, 2) : '0.00';
$data['clientes'] = $conexion->query("
    SELECT COUNT(*) as total
    FROM ventas
")->fetch_assoc()['total'];
// Cambiamos SUM(cantidad) por COUNT(*) para que cuente los registros
$data['productos_vendidos'] =
$conexion->query("
    SELECT COALESCE(SUM(cantidad),0) as total
    FROM detalle_ventas
")->fetch_assoc()['total'] ?? 0;
$data['devoluciones'] = $conexion->query("SELECT COUNT(*) as total FROM devoluciones")->fetch_assoc()['total'] ?? 0;

echo json_encode($data);
?>
<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once('../config/conexion.php');

if (!isset($_SESSION['usuario'])) {
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

$id_venta = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $conexion->prepare("
    SELECT 
        dv.cantidad,
        dv.precio_unitario,
        p.nombre,
        v.fecha,
        v.total,
        v.metodo_pago,
        u.nombre AS nombre_cajero,
        c.nombre_negocio
    FROM ventas v
    INNER JOIN usuarios u
        ON v.usuario_id = u.id
    LEFT JOIN detalle_ventas dv
        ON v.id = dv.id_venta
    LEFT JOIN productos p
        ON dv.producto_id = p.id
    CROSS JOIN configuracion c
    WHERE v.id = ?
");

$stmt->bind_param("i", $id_venta);
$stmt->execute();

$result = $stmt->get_result();

$productos = [];
$info_venta = null;

while ($row = $result->fetch_assoc()) {

    if (!$info_venta) {
        $info_venta = [
            'fecha'    => $row['fecha'],
            'cajero'   => $row['nombre_cajero'],
            'metodo'   => $row['metodo_pago'] ?? 'N/A',
            'negocio'  => $row['nombre_negocio'],
            'total'    => $row['total']
        ];
    }

    if ($row['nombre'] !== null) {

        $subtotal = (float)$row['cantidad'] * (float)$row['precio_unitario'];

        $productos[] = [
            'nombre'   => $row['nombre'],
            'cantidad' => (int)$row['cantidad'],
            'precio'   => number_format((float)$row['precio_unitario'], 2),
            'subtotal' => number_format($subtotal, 2)
        ];
    }
}

echo json_encode([
    'info' => $info_venta,
    'productos' => $productos,
    'total' => number_format((float)$info_venta['total'], 2)
]);
?>
<?php

require_once('../config/conexion.php');

$sql = "
SELECT
    metodo_pago,
    SUM(total) as total_ventas
FROM ventas
WHERE metodo_pago IS NOT NULL
AND metodo_pago <> ''
GROUP BY metodo_pago
";

$resultado = $conexion->query($sql);

$metodos = [];
$montos = [];

while($fila = $resultado->fetch_assoc()){
    $metodos[] = ucfirst($fila['metodo_pago']);
    $montos[] = floatval($fila['total_ventas']);
}

echo json_encode([
    'metodos' => $metodos,
    'montos' => $montos
]);
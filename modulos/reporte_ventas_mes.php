<?php

require_once('../config/conexion.php');

$sql = "
SELECT
    DATE_FORMAT(fecha,'%m/%Y') as mes,
    SUM(total) as total
FROM ventas
GROUP BY DATE_FORMAT(fecha,'%Y-%m')
ORDER BY fecha
";

$resultado = $conexion->query($sql);

$meses = [];
$totales = [];

while($fila = $resultado->fetch_assoc()){
    $meses[] = $fila['mes'];
    $totales[] = $fila['total'];
}

echo json_encode([
    'meses' => $meses,
    'totales' => $totales
]);
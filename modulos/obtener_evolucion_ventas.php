<?php
require_once('../config/conexion.php');

// 1. Obtener datos (Asegúrate de que esta consulta devuelva resultados)
$query = "SELECT DATE_FORMAT(fecha, '%d %b') as fecha, SUM(total) as total 
          FROM ventas 
          WHERE fecha >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) 
          GROUP BY DATE(fecha) 
          ORDER BY fecha ASC";

$resultado = $conexion->query($query);

$fechas = [];
$totales = [];

while ($row = $resultado->fetch_assoc()) {
    $fechas[] = $row['fecha'];
    $totales[] = $row['total'];
}

// 2. IMPORTANTE: Limpia cualquier salida previa (espacios en blanco o ecos)
header('Content-Type: application/json');
echo json_encode([
    'fechas' => $fechas,
    'totales' => $totales
]);
exit; // Esto asegura que no se imprima nada más
?>
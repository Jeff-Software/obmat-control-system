<?php
require_once('../config/conexion.php');

$config = $conexion
    ->query("
        SELECT stock_cero
        FROM configuracion
        WHERE id = 1
    ")
    ->fetch_assoc();

$q = $_GET['q'] ?? '';
$q = "%$q%";

$filtroStock = '';

if (empty($config['stock_cero'])) {
    $filtroStock = 'AND stock > 0';
}


$sql = "
    SELECT id, nombre, precio, stock, imagen
    FROM productos
    WHERE nombre LIKE ?
    AND estado = 1
    $filtroStock
    LIMIT 10
";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $q);
$stmt->execute();
$result = $stmt->get_result();

$productos = [];
while ($row = $result->fetch_assoc()) {
    $productos[] = $row;
}

header('Content-Type: application/json');
echo json_encode($productos);
?>
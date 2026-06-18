<?php
require_once('../config/conexion.php');

/*
|--------------------------------------------------------------------------
| Limpiar alertas antiguas de stock
|--------------------------------------------------------------------------
*/

$conexion->query("
    DELETE FROM notificaciones
    WHERE tipo = 'stock'
");

/*
|--------------------------------------------------------------------------
| Generar alertas actuales
|--------------------------------------------------------------------------
*/

$query = "
SELECT nombre, stock, stock_minimo
FROM productos
WHERE estado = 1
AND stock <= stock_minimo
AND stock_minimo > 0
";

$result = $conexion->query($query);

while($producto = $result->fetch_assoc()) {

    $mensaje = "⚠️ Stock crítico: "
        . $producto['nombre']
        . " ("
        . $producto['stock']
        . "/"
        . $producto['stock_minimo']
        . ")";

    $insert = $conexion->prepare("
        INSERT INTO notificaciones
        (mensaje, tipo, leido)
        VALUES (?, 'stock', 0)
    ");

    $insert->bind_param("s", $mensaje);
    $insert->execute();
}
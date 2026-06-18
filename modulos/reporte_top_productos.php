<?php

require_once('../config/conexion.php');
require_once('../config/config_global.php');

$sql = "
SELECT
    p.nombre,
    SUM(dv.cantidad) AS unidades,
    SUM(dv.cantidad * dv.precio_unitario) AS total
FROM detalle_ventas dv
INNER JOIN productos p
ON dv.producto_id = p.id
GROUP BY p.id
ORDER BY unidades DESC
LIMIT 5
";

$resultado = $conexion->query($sql);

?>

<div class="grafico-card">

    <h3>Top 5 Productos Más Vendidos</h3>

    <table class="tabla-reporte">

        <thead>
            <tr>
                <th>Producto</th>
                <th>Unidades</th>
                <th>Ventas</th>
            </tr>
        </thead>

        <tbody>

        <?php while($fila = $resultado->fetch_assoc()): ?>

            <tr>
                <td><?php echo htmlspecialchars($fila['nombre']); ?></td>
                <td><?php echo $fila['unidades']; ?></td>
                <td>
                    <?= $simboloMoneda ?>
                    <?php echo number_format($fila['total'],2); ?>
                </td>
            </tr>

        <?php endwhile; ?>

        </tbody>

    </table>

</div>
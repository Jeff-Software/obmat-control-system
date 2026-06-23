<?php

require_once('../config/conexion.php');
require_once('../config/config_global.php');

$sql = "
SELECT
    u.nombre,
    COUNT(v.id) AS transacciones,
    SUM(v.total) AS ventas
FROM ventas v
INNER JOIN usuarios u
    ON v.usuario_id = u.id
WHERE u.rol = 'cajero'
GROUP BY u.id
ORDER BY ventas DESC
";

$resultado = $conexion->query($sql);

?>

<div class="grafico-card">

    <h3><?= __('ventas_cajero') ?></h3>

    <table class="tabla-reporte">

        <thead>
            <tr>
            <th><?= __('cajero') ?></th>
            <th><?= __('cantidad_transacciones') ?></th>
            <th><?= __('total_vendido') ?></th>
            </tr>
        </thead>

        <tbody>

        <?php while($fila = $resultado->fetch_assoc()): ?>

            <tr>
                <td><?php echo htmlspecialchars($fila['nombre']); ?></td>
                <td><?php echo $fila['transacciones']; ?></td>
                <td>
                    <?= $simboloMoneda ?>
                    <?php echo number_format($fila['ventas'],2); ?>
                </td>
            </tr>

        <?php endwhile; ?>

        </tbody>

    </table>

</div>
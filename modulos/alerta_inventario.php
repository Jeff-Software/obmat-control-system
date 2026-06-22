<?php

$query_stock = "
SELECT nombre, stock, stock_minimo
FROM productos
WHERE stock <= stock_minimo
AND estado = 1
ORDER BY stock ASC
LIMIT 5;
";

$productos_criticos = [];

try {

    $res_stock = $conexion->query($query_stock);

    while($row = $res_stock->fetch_assoc()){
        $productos_criticos[] = $row;
    }

} catch (Exception $e) {
    $productos_criticos = [];
}

$total_criticos = count($productos_criticos);

?>

<?php if ($total_criticos > 0): ?>

<div class="inventory-alert-banner">

    <div class="alert-message-box">

        <div class="alert-icon-circle">
            <span>⚠️</span>
        </div>

        <div>

        <p class="alert-text-content">
            <?= str_replace(':count', $total_criticos, __('stock_alerta')) ?>
        </p>

            <ul style="margin-top:8px; padding-left:20px;">

                <?php foreach($productos_criticos as $producto){ ?>

                <li>
                    <?= htmlspecialchars($producto['nombre']); ?>
                    (<?= str_replace(
                        [':current', ':min'],
                        [$producto['stock'], $producto['stock_minimo']],
                        __('stock_formato')
                    ) ?>)
                </li>

                <?php } ?>

            </ul>

        </div>

    </div>

    <a href="articulos.php" class="btn-alert-action">
        <?= __('ver_inventario') ?>
    </a>

</div>

<?php endif; ?>
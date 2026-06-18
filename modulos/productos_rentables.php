<?php
require_once('../config/config_global.php');
$query = "
SELECT
    p.nombre,
    SUM(
        (dv.precio_unitario - p.precio_compra) * dv.cantidad
    ) AS ganancia_total
FROM detalle_ventas dv
INNER JOIN productos p
    ON dv.producto_id = p.id
WHERE p.precio_compra IS NOT NULL
GROUP BY p.id, p.nombre
ORDER BY ganancia_total DESC
LIMIT 5
";

$result = $conexion->query($query);

if (!$result) {
    die("Error SQL: " . $conexion->error);
}
?>

<div class="card-header-clean-split">
    <h3>Productos más rentables</h3>
</div>

<div class="products-list-container">

<?php
if ($result->num_rows > 0) {
    $pos = 1;

    while ($row = $result->fetch_assoc()) {
?>

        <div class="product-item-row">

            <div class="product-rank">
                <?= $pos ?>
            </div>

            <div class="product-info-meta">
                <span class="product-name-text">
                    <?= htmlspecialchars($row['nombre']) ?>
                </span>

                <span class="product-qty-text">
                    Top rentabilidad
                </span>
            </div>

            <div class="product-price-value" style="color:#16a34a;">
                <?= $simboloMoneda ?> <?= number_format($row['ganancia_total'], 2) ?>
            </div>

        </div>

<?php
        $pos++;
    }
} else {
?>

    <div style="padding:20px;text-align:center;color:#64748b;">
        No hay datos de rentabilidad disponibles.
    </div>

<?php
}
?>

</div>
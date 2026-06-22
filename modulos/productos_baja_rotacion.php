<?php
// modulos/productos_baja_rotacion.php

// CONSULTA SQL: Encuentra productos que tienen stock pero cuya última venta fue hace días.
$query_baja = "SELECT 
                p.nombre, 
                p.imagen, 
                p.stock,
                DATEDIFF(CURDATE(), MAX(v.fecha)) AS dias_sin_venta
              FROM productos p
              LEFT JOIN detalle_ventas dv
              ON p.id = dv.producto_id
              LEFT JOIN ventas v ON dv.id_venta = v.id
              WHERE p.stock > 0 -- Solo nos interesan productos que aún tienen inventario
              GROUP BY p.id
              ORDER BY dias_sin_venta DESC, p.stock ASC
              LIMIT 5";

$res_baja = false;
try {
    $res_baja = $conexion->query($query_baja);
} catch (Exception $e) {
    $res_baja = false; // Respaldo gráfico automático si la BD varía
}
?>

<div class="dashboard-card summary-box">
    <div class="card-header-clean-split">
        <h3><?= __('productos_baja_rotacion') ?></h3>
    </div>

    <div class="products-list-container">
        <?php 
        // Si la consulta funciona y devuelve registros válidos con días sin venta
        if ($res_baja && $res_baja->num_rows > 0):
            while($prod = $res_baja->fetch_assoc()): 

                if (is_null($prod['dias_sin_venta'])) {
                    $texto_venta = __('nunca_vendido');
                } else {
                    $texto_venta = __('sin_ventas') . ' ' . $prod['dias_sin_venta'] . ' ' . __('dias');
                }

                $ruta_img = "../assets/img/productos/" . 
                    (!empty($prod['imagen']) ? $prod['imagen'] : 'default.png');
            ?>
            <div class="product-item-row">

                <div class="product-alert-icon">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>

                <div class="product-thumb-box">
                    <img 
                        src="<?php echo htmlspecialchars($ruta_img); ?>" 
                        alt="<?php echo htmlspecialchars($prod['nombre']); ?>">
                </div>

                <div class="product-info-meta">
                    <span class="product-name-text">
                        <?php echo htmlspecialchars($prod['nombre']); ?>
                    </span>

                    <span class="product-qty-text alert-orange-text">
                        <?= $texto_venta ?>
                    </span>
                </div>

                <div class="product-stock-value">
                    <span class="stock-label">
                        <?= __('stock') ?>:
                    </span>
                    <?php echo $prod['stock']; ?>
                </div>

            </div>

        <?php 
            endwhile; 
        else:
        ?>

<div class="empty-state">
            <p><?= __('sin_productos_baja_rotacion') ?></p>
        </div>

        <?php 
            endif; 
        ?>
    </div>
</div>
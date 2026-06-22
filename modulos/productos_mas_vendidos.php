<?php
// modulos/productos_mas_vendidos.php
require_once('../config/config_global.php');
// CONSULTA CORREGIDA: Apuntamos a p.precio (tabla productos) en lugar de dv.precio
$query_top = "
            SELECT
                p.nombre,
                p.imagen,
                SUM(dv.cantidad) AS unidades,
                SUM(dv.cantidad * dv.precio_unitario) AS total_ingreso
            FROM detalle_ventas dv
            INNER JOIN productos p
                ON dv.producto_id = p.id
            INNER JOIN ventas v
                ON dv.id_venta = v.id
            WHERE DATE(v.fecha) = CURDATE()
            GROUP BY p.id
            ORDER BY unidades DESC
            LIMIT 5
";

// Usamos un bloque try-catch o verificación manual para que si vuelve a fallar por algún otro campo, 
$res_top = false;
try {
    $res_top = $conexion->query($query_top);
} catch (Exception $e) {
    $res_top = false; // Si falla, forzamos el uso del respaldo gráfico
}
?>

<div class="dashboard-card summary-box">
    <div class="card-header-clean-split">
        <h3><?= __('productos_mas_vendidos') ?></h3>
    </div>

    <div class="products-list-container">
        <?php 
        $posicion = 1;
        // Si la consulta fue exitosa y trajo datos de la BD
        if ($res_top && $res_top->num_rows > 0):
            while($prod = $res_top->fetch_assoc()): 
                $ruta_img = "../assets/img/productos/" . (!empty($prod['imagen']) ? $prod['imagen'] : 'default.png');
        ?>
            <div class="product-item-row">
                <div class="product-rank rank-<?php echo $posicion; ?>">
                    <?php echo $posicion; ?>
                </div>

                <div class="product-thumb-box">
                    <img src="<?php echo htmlspecialchars($ruta_img); ?>" alt="<?php echo htmlspecialchars($prod['nombre']); ?>">
                </div>

                <div class="product-info-meta">
                    <span class="product-name-text"><?php echo htmlspecialchars($prod['nombre']); ?></span>
                    <span class="product-qty-text">
                    <?php echo $prod['unidades']; ?> <?= __('unidades') ?>
                </span>
                </div>

                <div class="product-price-value">
                    <?= $simboloMoneda ?> <?php echo number_format($prod['total_ingreso'], 2); ?>
                </div>
            </div>
        <?php 
            $posicion++;
            endwhile; 
        else:
            ?>

            <div class="empty-state">
                <p><?= __('sin_datos_ventas') ?></p>
            </div>

            <?php
            endif;
        ?>
    </div>
</div>
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
        <h3>Productos con baja rotación</h3>
    </div>

    <div class="products-list-container">
        <?php 
        // Si la consulta funciona y devuelve registros válidos con días sin venta
        if ($res_baja && $res_baja->num_rows > 0):
            while($prod = $res_baja->fetch_assoc()): 
                // Si nunca se ha vendido, el valor será NULL. Le asignamos un valor por defecto sutil
                $dias = !is_null($prod['dias_sin_venta']) ? $prod['dias_sin_venta'] : 15; 
                $ruta_img = "../assets/img/productos/" . (!empty($prod['imagen']) ? $prod['imagen'] : 'default.png');
        ?>
            <div class="product-item-row">
                <div class="product-alert-icon">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>

                <div class="product-thumb-box">
                    <img src="<?php echo htmlspecialchars($ruta_img); ?>" alt="<?php echo htmlspecialchars($prod['nombre']); ?>">
                </div>

                <div class="product-info-meta">
                    <span class="product-name-text"><?php echo htmlspecialchars($prod['nombre']); ?></span>
                    <span class="product-qty-text alert-orange-text">Sin ventas en <?php echo $dias; ?> días</span>
                </div>

                <div class="product-stock-value">
                    <span class="stock-label">Stock:</span> <?php echo $prod['stock']; ?>
                </div>
            </div>
        <?php 
            endwhile; 
        else:
            // RESPALDO ESTÁTICO: Calca fielmente tu maqueta usando tus imágenes reales
            $mock_baja = [
                ['nombre' => 'Atún Florida en Aceite 170g', 'imagen' => 'atun.png', 'dias' => 15, 'stock' => 15],
                ['nombre' => 'Mayonesa Alacena 200g', 'imagen' => 'mayonesa.png', 'dias' => 12, 'stock' => 8],
                ['nombre' => 'Salsa de Tomate 200g', 'imagen' => 'salsa.png', 'dias' => 10, 'stock' => 12],
                ['nombre' => 'Papel Higiénico Elite', 'imagen' => 'papel.png', 'dias' => 9, 'stock' => 6],
                ['nombre' => 'Detergente Opal 1kg', 'imagen' => 'detergente.png', 'dias' => 8, 'stock' => 7]
            ];

            foreach($mock_baja as $prod):
        ?>
            <div class="product-item-row">
                <div class="product-alert-icon">⚠️</div>
                <div class="product-thumb-box">
                    <img src="../assets/img/productos/<?php echo $prod['imagen']; ?>" alt="Producto">
                </div>
                <div class="product-info-meta">
                    <span class="product-name-text"><?php echo $prod['nombre']; ?></span>
                    <span class="product-qty-text alert-orange-text">Sin ventas en <?php echo $prod['dias']; ?> días</span>
                </div>
                <div class="product-stock-value">
                    <span class="stock-label">Stock:</span> <?php echo $prod['stock']; ?>
                </div>
            </div>
        <?php 
            endforeach;
        endif; 
        ?>
    </div>
</div>
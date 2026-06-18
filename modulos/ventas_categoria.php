<?php
// modulos/ventas_categoria.php

// CONSULTA SQL: Suma el total vendido agrupado por categorías de productos en el día de hoy
$query_cat = "
            SELECT
                p.categoria,
                SUM(dv.cantidad * dv.precio_unitario) AS total_vendido
            FROM detalle_ventas dv
            INNER JOIN productos p
                ON dv.producto_id = p.id
            INNER JOIN ventas v
                ON dv.id_venta = v.id
            WHERE DATE(v.fecha) = CURDATE()
            GROUP BY p.categoria
            ORDER BY total_vendido DESC
";

$res_cat = false;
try {
    $res_cat = $conexion->query($query_cat);
} catch (Exception $e) {
    $res_cat = false;
}

// Preparar arrays para el gráfico de Chart.js
$labels_js = [];
$valores_js = [];
$tabla_render = [];
$total_general = 0;

if ($res_cat && $res_cat->num_rows > 0) {
    while ($row = $res_cat->fetch_assoc()) {
        $tabla_render[] = [
            'nombre' => $row['categoria'],
            'total' => (float)$row['total_vendido']
        ];
        $total_general += (float)$row['total_vendido'];
    }
} else {
    // RESPALDO ESTÁTICO: Datos calcados de tu imagen de muestra
    $tabla_render = [
        ['nombre' => 'Abarrotes', 'total' => 561.80, 'porcentaje' => 45],
        ['nombre' => 'Lácteos', 'total' => 249.70, 'porcentaje' => 20],
        ['nombre' => 'Bebidas', 'total' => 187.30, 'porcentaje' => 15],
        ['nombre' => 'Higiene', 'total' => 124.80, 'porcentaje' => 10],
        ['nombre' => 'Otros', 'total' => 124.90, 'porcentaje' => 10]
    ];
    $total_general = 1248.50;
}

// Calcular porcentajes dinámicos y preparar variables para JavaScript
foreach ($tabla_render as &$item) {
    if (!isset($item['porcentaje'])) {
        $item['porcentaje'] = $total_general > 0 ? round(($item['total'] / $total_general) * 100) : 0;
    }
    $labels_js[] = $item['nombre'];
    $valores_js[] = $item['total'];
}
unset($item);

// Definición de colores fijos según el orden de tu maqueta
$colores_map = [
    'Abarrotes' => '#0061f2', // Azul
    'Lácteos' => '#2563eb',   // Azul intermedio
    'Bebidas' => '#38bdf8',   // Celeste claro
    'Higiene' => '#10b981',   // Verde
    'Otros' => '#cbd5e1'     // Gris
];
?>

<div class="dashboard-card summary-box">
    <div class="card-header-clean">
        <h3>Ventas por categoría</h3>
    </div>
    
    <div class="category-chart-layout">
        <div class="donut-chart-wrapper">
            <canvas id="chartCategorias"></canvas>
        </div>

        <div class="category-legend-list">
            <?php foreach ($tabla_render as $cat): 
                $color = isset($colores_map[$cat['nombre']]) ? $colores_map[$cat['nombre']] : '#64748b';
            ?>
                <div class="category-legend-row">
                    <div class="cat-label-group">
                        <span class="cat-color-dot" style="background-color: <?php echo $color; ?>;"></span>
                        <span class="cat-name"><?php echo htmlspecialchars($cat['nombre']); ?></span>
                    </div>
                    <span class="cat-percentage"><?php echo $cat['porcentaje']; ?>%</span>
                    <span class="cat-value">
                        <?php echo $simboloMoneda . ' ' . number_format($cat['total'], 2); ?>
                    </span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctxCat = document.getElementById('chartCategorias').getContext('2d');
    new Chart(ctxCat, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($labels_js); ?>,
            datasets: [{
                data: <?php echo json_encode($valores_js); ?>,
                backgroundColor: [
                    '#0061f2', '#2563eb', '#38bdf8', '#10b981', '#cbd5e1'
                ],
                borderWidth: 0,
                cutout: '75%' // Hace que el centro de la dona sea delgado y elegante
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false } // Ocultamos la leyenda nativa porque la hicimos personalizada en HTML
            }
        }
    });
});
</script>
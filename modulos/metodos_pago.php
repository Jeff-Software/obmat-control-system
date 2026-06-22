<?php
require_once('../config/config_global.php');
// modulos/metodos_pago.php

$query = "
    SELECT
        metodo_pago,
        COUNT(*) AS cantidad,
        SUM(total) AS total
    FROM ventas
    WHERE DATE(fecha) = CURDATE()
    GROUP BY metodo_pago
    ORDER BY total DESC
";

$resultado = $conexion->query($query);

$datos = [];
$total_general = 0;

while ($fila = $resultado->fetch_assoc()) {

    $datos[] = $fila;
    $total_general += $fila['total'];
}

$labels = [];
$valores = [];
$colores = [
    '#0061f2',
    '#2563eb',
    '#9333ea',
    '#10b981',
    '#f59e0b'
];

foreach ($datos as $fila) {

    $labels[] = ucfirst($fila['metodo_pago']);
    $valores[] = (float)$fila['total'];
}
?>

<div class="dashboard-card">
    <div class="card-header-clean">
        <h3><?= __('metodos_pago') ?></h3>
    </div>

    <div class="category-chart-layout">

        <div class="donut-chart-wrapper">
            <canvas id="chartMetodosPago"></canvas>
        </div>

        <div class="category-legend-list">

            <?php
            $i = 0;

            foreach($datos as $fila):

                $porcentaje = $total_general > 0
                    ? round(($fila['total'] / $total_general) * 100)
                    : 0;
            ?>

            <div class="category-legend-row">

                <div class="cat-label-group">
                    <span class="cat-color-dot"
                        style="background-color: <?php echo $colores[$i]; ?>">
                    </span>

                <span class="cat-name">
                    <?= __(strtolower($fila['metodo_pago'])) ?>
                </span>
                </div>

                <span class="cat-percentage">
                    <?php echo $porcentaje; ?>%
                </span>

                <span class="cat-value">
                    <?= $simboloMoneda ?> <?php echo number_format($fila['total'], 2); ?>
                </span>

            </div>

            <?php
            $i++;
            endforeach;
            ?>

        </div>

    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {

    const ctx = document.getElementById('chartMetodosPago');

    new Chart(ctx, {

        type: 'doughnut',

        data: {
            labels: <?php echo json_encode($labels); ?>,

            datasets: [{
                data: <?php echo json_encode($valores); ?>,

                backgroundColor: [
                    '#0061f2',
                    '#2563eb',
                    '#9333ea',
                    '#10b981',
                    '#f59e0b'
                ],

                borderWidth: 0,
                cutout: '75%'
            }]
        },

        options: {

            responsive: true,
            maintainAspectRatio: false,

            plugins: {

                legend: {
                    display: false
                },

                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label +
                                ': <?= $simboloMoneda ?> ' +
                                Number(context.raw).toFixed(2);
                        }
                    }
                }
            }
        }
    });

});
</script>
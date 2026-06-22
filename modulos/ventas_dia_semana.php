<?php
// Consulta para obtener ventas por día (forzando los 7 días de la semana)
$query_dia = "SELECT 
                dias.nombre AS dia, 
                COALESCE(SUM(v.total), 0) AS total_dia,
                dias.ord
              FROM (
                SELECT 'lunes' AS nombre, 2 AS ord UNION 
                SELECT 'martes', 3 UNION 
                SELECT 'miercoles', 4 UNION 
                SELECT 'jueves', 5 UNION 
                SELECT 'viernes', 6 UNION 
                SELECT 'sabado', 7 UNION 
                SELECT 'domingo', 1
              ) AS dias

              LEFT JOIN ventas v 
              ON ELT(
                    WEEKDAY(v.fecha) + 1,
                    'lunes',
                    'martes',
                    'miercoles',
                    'jueves',
                    'viernes',
                    'sabado',
                    'domingo'
              ) = dias.nombre

              WHERE v.fecha >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
              OR v.fecha IS NULL

              GROUP BY dias.nombre, dias.ord

              ORDER BY dias.ord ASC";

$result = $conexion->query($query_dia);
$labels = [];
$totales = [];
while($row = $result->fetch_assoc()) {

    $dia = $row['dia'];

    $labels[] = $lang['dias_semana'][$dia] ?? $dia;

    $totales[] = (float)$row['total_dia'];
}
?>

<div class="chart-container">
    <h3 style="margin-bottom:20px;">
    <?= __('ventas_dia_semana') ?>
    </h3>
    <canvas id="chartDiaSemana"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    new Chart(document.getElementById('chartDiaSemana'), {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($labels); ?>, // ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom']
            datasets: [{
                data: <?php echo json_encode($totales); ?>,
                backgroundColor: '#3b82f6',
                borderRadius: 6,
                barThickness: 20
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                datalabels: { // Etiquetas sobre las barras
                    anchor: 'end',
                    align: 'top',
                    formatter: (value) => value > 0 ? '<?= $simboloMoneda ?> ' + value : '',
                    color: '#1e293b',
                    font: { weight: 'bold', size: 10 }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    // Habilitamos los números laterales
                    ticks: {
                        callback: function(value) { 
                            return '<?= $simboloMoneda ?> ' + value; 
                        },
                        stepSize: 500 // Ajusta según el volumen de ventas
                    },
                    grid: { color: '#f1f5f9' }
                },
                x: {
                    grid: { display: false } // Limpiamos el fondo horizontal
                }
            }
        },
        plugins: [ChartDataLabels]
    });
});
</script>
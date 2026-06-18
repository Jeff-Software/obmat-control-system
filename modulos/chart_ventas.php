<?php
// modulos/chart_ventas.php
require_once('../config/config_global.php');

function getVentasPorBloque($conexion, $fecha)
{
    $data = [0, 0, 0, 0, 0, 0, 0];

    $sql = "
        SELECT
            FLOOR(HOUR(fecha) / 4) AS bloque,
            SUM(total) AS monto_total
        FROM ventas
        WHERE DATE(fecha) = ?
        GROUP BY bloque
        ORDER BY bloque
    ";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $fecha);
    $stmt->execute();

    $resultado = $stmt->get_result();

    while ($row = $resultado->fetch_assoc()) {
        $indice = (int)$row['bloque'];

        if ($indice >= 0 && $indice <= 6) {
            $data[$indice] = (float)$row['monto_total'];
        }
    }

    $acumulado = [];
    $suma = 0;

    foreach ($data as $valor) {
        $suma += $valor;
        $acumulado[] = round($suma, 2);
    }

    return $acumulado;
}

$hoy_data = getVentasPorBloque(
    $conexion,
    date('Y-m-d')
);



$ayer_data = getVentasPorBloque(
    $conexion,
    date('Y-m-d', strtotime('-1 day'))
);

$total_hoy = end($hoy_data);

$mostrar_ayer = array_sum($ayer_data) > 0;
?>

<div class="dashboard-card chart-main-box">

    <div class="chart-header-custom">
        <div>
            <h3>Evolución de ventas del día</h3>
            <small style="color:#64748b;">
                Total hoy: <?= $simboloMoneda ?> <?php echo number_format($total_hoy, 2); ?>
            </small>
        </div>
    </div>

    <div class="chart-wrapper">
        <canvas id="salesEvolutionChart"></canvas>
    </div>

</div>

<script>
document.addEventListener("DOMContentLoaded", function() {

    const ctx = document
        .getElementById("salesEvolutionChart")
        .getContext("2d");

    const dataHoy = <?php echo json_encode($hoy_data); ?>;
    const dataAyer = <?php echo json_encode($ayer_data); ?>;

    const MONEDA = "<?= $simboloMoneda ?>";

    new Chart(ctx, {

        type: "line",

        data: {

            labels: [
                "00:00",
                "04:00",
                "08:00",
                "12:00",
                "16:00",
                "20:00",
                "24:00"
            ],

            datasets: [

                {
                    label: "Ventas de hoy",
                    data: dataHoy,
                    borderColor: "#0061f2",
                    backgroundColor: "rgba(0,97,242,0.10)",
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6
                },

                {
                    label: "Ventas de ayer",
                    data: dataAyer,
                    hidden: <?php echo $mostrar_ayer ? 'false' : 'true'; ?>,
                    borderColor: "#94a3b8",
                    borderWidth: 2,
                    borderDash: [6,6],
                    tension: 0.4,
                    pointRadius: 0,
                    fill: false
                }

            ]
        },

        options: {

            responsive: true,
            maintainAspectRatio: false,

            plugins: {

                legend: {
                    display: true,
                    position: "top"
                },

                tooltip: {
                    mode: "index",
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                        return context.dataset.label +
                            ": " + MONEDA + " " +
                            Number(context.raw).toFixed(2);
                        }
                    }
                }
            },

            interaction: {
                mode: "index",
                intersect: false
            },

            scales: {

                x: {
                    grid: {
                        display: false
                    }
                },

                y: {
                    beginAtZero: true,

                    ticks: {
                        callback: function(value) {
                            return MONEDA + " " + value;
                        }
                    },

                    grid: {
                        color: "#f1f5f9"
                    }
                }

            }

        }

    });

});
</script>
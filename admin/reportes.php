<?php
require_once('../config/auth.php');
require_once('../config/conexion.php');
require_once('../config/config_global.php');

$fechaInicio = $_GET['fechaInicio'] ?? '';
$fechaFin    = $_GET['fechaFin'] ?? '';

$whereFecha = '';

if(!empty($fechaInicio) && !empty($fechaFin)){

    $whereFecha =
        " WHERE DATE(fecha)
          BETWEEN '$fechaInicio'
          AND '$fechaFin' ";
}

// KPIs

$totalVentas = 0;
$totalTransacciones = 0;
$ticketPromedio = 0;
$totalProductos = 0;

$ventasMesActual = 0;
$ventasMesAnterior = 0;
$variacionVentas = null;

$sqlMesActual = "
SELECT SUM(total) total
FROM ventas
WHERE YEAR(fecha)=YEAR(CURDATE())
AND MONTH(fecha)=MONTH(CURDATE())
";

$resMesActual = $conexion->query($sqlMesActual);

if($resMesActual){
    $ventasMesActual =
        $resMesActual->fetch_assoc()['total'] ?? 0;
}

$sqlMesAnterior = "
SELECT SUM(total) total
FROM ventas
WHERE YEAR(fecha)=YEAR(
    DATE_SUB(CURDATE(), INTERVAL 1 MONTH)
)
AND MONTH(fecha)=MONTH(
    DATE_SUB(CURDATE(), INTERVAL 1 MONTH)
)
";

$resMesAnterior = $conexion->query($sqlMesAnterior);

if($resMesAnterior){
    $ventasMesAnterior =
        $resMesAnterior->fetch_assoc()['total'] ?? 0;
}

if($ventasMesAnterior > 0){

    $variacionVentas =
        (($ventasMesActual - $ventasMesAnterior)
        / $ventasMesAnterior) * 100;

}else{

    $variacionVentas = null;

}
// Ventas
$sql = "
    SELECT
        SUM(total) AS ventas,
        COUNT(*) AS transacciones,
        AVG(total) AS ticket
    FROM ventas
    $whereFecha
";

$resultado = $conexion->query($sql);

if($resultado && $resultado->num_rows > 0){
    $datos = $resultado->fetch_assoc();

    $totalVentas = $datos['ventas'] ?? 0;
    $totalTransacciones = $datos['transacciones'] ?? 0;
    $ticketPromedio = $datos['ticket'] ?? 0;
}

$filtroVentas = '';

if(!empty($fechaInicio) && !empty($fechaFin)){

    $filtroVentas = "
    WHERE DATE(v.fecha)
    BETWEEN '$fechaInicio'
    AND '$fechaFin'
    ";

}

// Productos vendidos
$sqlProductos = "
SELECT SUM(dv.cantidad) AS vendidos
FROM detalle_ventas dv
INNER JOIN ventas v
    ON v.id = dv.id_venta
$filtroVentas
";

$resProductos = $conexion->query($sqlProductos);

if($resProductos){
    $fila = $resProductos->fetch_assoc();
    $totalProductos = $fila['vendidos'] ?? 0;
}
/* ==========================
   RESUMEN EJECUTIVO
========================== */

// Producto líder

$sqlTopProducto = "
SELECT
    p.nombre,
    SUM(dv.cantidad) total
FROM detalle_ventas dv
INNER JOIN productos p
    ON p.id = dv.producto_id
INNER JOIN ventas v
    ON v.id = dv.id_venta
$filtroVentas
GROUP BY p.id
ORDER BY total DESC
LIMIT 1
";

$resTopProducto = $conexion->query($sqlTopProducto);
$topProducto = $resTopProducto->fetch_assoc();


// Método de pago favorito

$sqlMetodo = "
SELECT
    metodo_pago,
    COUNT(*) total
FROM ventas
$whereFecha
GROUP BY metodo_pago
ORDER BY total DESC
LIMIT 1
";

$resMetodo = $conexion->query($sqlMetodo);
$metodoFavorito = $resMetodo->fetch_assoc();


// Mejor cajero

$filtroCajero = "
WHERE u.rol='cajero'
";

if(!empty($fechaInicio) && !empty($fechaFin)){

    $filtroCajero .= "
    AND DATE(v.fecha)
    BETWEEN '$fechaInicio'
    AND '$fechaFin'
    ";
}

$sqlCajero = "
SELECT
    u.nombre,
    SUM(v.total) ventas
FROM ventas v
INNER JOIN usuarios u
    ON u.id = v.usuario_id
$filtroCajero
GROUP BY u.id
ORDER BY ventas DESC
LIMIT 1
";

$resCajero = $conexion->query($sqlCajero);
$mejorCajero = $resCajero->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Reportes</title>

<link rel="stylesheet" href="../assets/css/admin.css">
<link rel="stylesheet" href="../assets/css/reportes.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

<?php include('../modulos/sidebar.php'); ?>

<main class="main-content">

<div class="reportes-header">
    <div>
        <h2>Centro de Reportes</h2>
        <p>Análisis financiero e indicadores del negocio</p>
    </div>

    <div class="estado-reporte">
        <span class="badge-live">
            <i class="fas fa-circle"></i>
            Datos actualizados
        </span>
    </div>
</div>

<!-- FILTROS -->

<div class="reportes-filtros">

    <div class="reporte-titulo">
        <i class="fas fa-chart-line"></i>
        Ventas
    </div>

    <form method="GET" class="filtro-form">

        <input type="date" name="fechaInicio"
        value="<?php echo $_GET['fechaInicio'] ?? ''; ?>">

        <input type="date" name="fechaFin"
        value="<?php echo $_GET['fechaFin'] ?? ''; ?>">

        <button type="submit" class="btn-primary">
            <i class="fas fa-filter"></i>
            Filtrar
        </button>

        <a href="reportes.php" class="btn-secondary">
            <i class="fas fa-rotate-left"></i>
            Limpiar
        </a>

    </form>

    <button
        onclick="
            window.open(
                '../modulos/generar_pdf.php?fechaInicio=<?php echo $fechaInicio; ?>&fechaFin=<?php echo $fechaFin; ?>',
                '_blank'
            )
        "
        class="btn-primary">

        <i class="fas fa-file-pdf"></i>
        Generar PDF

    </button>

</div>

<!-- KPI -->

<div class="kpi-grid">

    <div class="kpi-card">
        <i class="fas fa-money-bill-wave"></i>
        <div>
            <h3>Ventas Totales</h3>
            <p><?= $simboloMoneda ?> <?php echo number_format($totalVentas,2); ?></p>

            <?php if($variacionVentas !== null): ?>

                <span class="
                <?php echo $variacionVentas >= 0
                    ? 'kpi-change positive'
                    : 'kpi-change negative'; ?>
                ">

                    <?php echo $variacionVentas >= 0 ? '↑' : '↓'; ?>

                    <?php echo number_format(abs($variacionVentas),1); ?>%

                    vs mes anterior

                </span>

            <?php else: ?>

                <span class="kpi-change">
                    Sin datos comparativos
                </span>

            <?php endif; ?>
        </div>
    </div>

    <div class="kpi-card">
        <i class="fas fa-shopping-cart"></i>
        <div>
            <h3>Transacciones</h3>
            <p><?php echo $totalTransacciones; ?></p>
        </div>
    </div>

    <div class="kpi-card">
        <i class="fas fa-receipt"></i>
        <div>
            <h3>Ticket Promedio</h3>
            <p><?= $simboloMoneda ?> <?php echo number_format($ticketPromedio,2); ?></p>
        </div>
    </div>

    <div class="kpi-card">
        <i class="fas fa-box"></i>
        <div>
            <h3>Productos Vendidos</h3>
            <p><?php echo $totalProductos; ?></p>
        </div>
    </div>

</div>
<div class="resumen-ejecutivo">

    <div class="resumen-item">
        🏆 Producto líder:
        <strong>
            <?php echo htmlspecialchars($topProducto['nombre'] ?? 'N/A'); ?>
        </strong>
    </div>

    <div class="resumen-item">
        💳 Método preferido:
        <strong>
            <?php echo ucfirst($metodoFavorito['metodo_pago'] ?? 'N/A'); ?>
        </strong>
    </div>

    <div class="resumen-item">
        👤 Mejor cajero:
        <strong>
            <?php echo htmlspecialchars($mejorCajero['nombre'] ?? 'N/A'); ?>
        </strong>
    </div>

</div>

<div class="reportes-graficos">

    <div class="grafico-card">
        <h3>Evolución de Ventas por Mes</h3>
        <canvas id="ventasMesChart"></canvas>
    </div>

    <div class="grafico-card">
        <h3>Ventas por Método de Pago</h3>
        <canvas id="metodoPagoChart"></canvas>
    </div>

</div>

<div class="reportes-tablas">

    <?php include('../modulos/reporte_ventas_cajero.php'); ?>

    <?php include('../modulos/reporte_top_productos.php'); ?>

</div>

<div class="info-footer">
    <i class="fas fa-info-circle"></i>
    Los reportes se generan en base a las ventas registradas en el sistema.
</div>

</main>

<script>
const simboloMoneda = "<?= $simboloMoneda ?>";
</script>

<script>

// Ventas por mes

fetch('../modulos/reporte_ventas_mes.php')
.then(res => res.json())
.then(data => {

    new Chart(
        document.getElementById('ventasMesChart'),
        {
            type:'line',
            data:{
                labels:data.meses,
                datasets:[{
                    label:'Ventas ' + simboloMoneda,
                    data:data.totales,
                    borderColor:'#2563eb',
                    backgroundColor:'rgba(37,99,235,.15)',
                    borderWidth:4,
                    tension:.4,
                    fill:true,
                    pointRadius:5,
                    pointHoverRadius:7,
                    pointBackgroundColor:'#2563eb'
                }]
            },
            options:{
                responsive:true,
                maintainAspectRatio:false,

                plugins:{
                    legend:{
                        display:false
                    },
                    tooltip:{
                        backgroundColor:'#1e293b',
                        titleColor:'#fff',
                        bodyColor:'#fff',
                        padding:12,
                        cornerRadius:10,
                        displayColors:false,
                        callbacks:{
                            label:function(context){
                                return simboloMoneda + ' ' + context.raw;
                            }
                        }
                    }
                },

                scales:{
                    y:{
                        beginAtZero:true,
                        ticks:{
                            color:'#64748b',
                            callback:function(value){
                                return simboloMoneda + ' ' + value;
                            }
                        },
                        grid:{
                            color:'#e2e8f0'
                        }
                    },
                    x:{
                        ticks:{
                            color:'#64748b'
                        },
                        grid:{
                            display:false
                        }
                    }
                },

                interaction:{
                    intersect:false,
                    mode:'index'
                }
            }
        }
    );

});

// Métodos de pago

fetch('../modulos/reporte_metodos_pago.php')
.then(res => res.json())
.then(data => {

    const totalGeneral =
        data.montos.reduce((a,b) => a + b, 0);

    new Chart(
        document.getElementById('metodoPagoChart'),
        {
            type:'doughnut',

            data:{
                labels:data.metodos,

                datasets:[{
                    data:data.montos,

                    backgroundColor:[
                        '#2563eb',
                        '#10b981',
                        '#f59e0b'
                    ],

                    borderWidth:0,
                    hoverOffset:15
                }]
            },

            options:{
                responsive:true,
                maintainAspectRatio:false,

                plugins:{

                    legend:{
                        position:'bottom',
                        labels:{
                            padding:20,
                            usePointStyle:true,
                            pointStyle:'circle'
                        }
                    },

                    tooltip:{
                        callbacks:{
                            label:function(context){

                                const valor = context.raw;

                                const porcentaje =
                                    ((valor / totalGeneral) * 100)
                                    .toFixed(1);

                                return context.label +
                                    ': ' + simboloMoneda + ' ' + valor.toFixed(2) +
                                    ' (' +
                                    porcentaje +
                                    '%)';
                            }
                        }
                    }

                },

                cutout:'65%'
            }
        }
    );

});

</script>
</body>
</html>
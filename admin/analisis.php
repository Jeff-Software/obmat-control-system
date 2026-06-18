<?php
require_once('../config/auth.php'); 
require_once('../config/conexion.php');
require_once('../config/config_global.php');

$nombre_usuario = $_SESSION['nombre'] ?? $_SESSION['usuario'] ?? 'Luis Ramos';

// Consulta para notificaciones (igual que en dashboard)
$stmt = $conexion->prepare("SELECT COUNT(*) as total FROM notificaciones WHERE leido = ?");
$leido = 0;
$stmt->bind_param("i", $leido);
$stmt->execute();
$num_notif = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    
    
    <meta charset="UTF-8">
    <title>Análisis de Ventas | InkaDigital</title>
    <link rel="stylesheet" href="../assets/css/admin.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    
</head>
<body>

    <?php include('../modulos/sidebar.php'); ?>
    
    <main class="main-content pagina-analisis">
        
        <header class="dashboard-header">
            <div class="dashboard-header-title-block">
                <h2>Análisis de Ventas</h2>
                <p>Visualiza el comportamiento y rendimiento de tu minimarket</p>
            </div>

            </header>

        <div class="analisis-nav-bar" style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
            <div class="tabs-analisis">
                <button class="tab-btn active">Resumen General</button>
            </div>
        </div>

<section class="kpi-container-grid">
    <div class="kpi-row">
        <div class="kpi-card card-ventas">
            <i class="fas fa-shopping-bag"></i>
            <div><h3>Ventas Totales</h3><p id="kpi-ventas">Cargando...</p></div>
        </div>
        <div class="kpi-card card-ganancia">
            <i class="fas fa-money-bill-wave"></i>
            <div><h3>Ganancia Neta</h3><p id="kpi-ganancia">Cargando...</p></div>
        </div>
        <div class="kpi-card card-margen">
            <i class="fas fa-chart-line"></i>
            <div><h3>Margen</h3><p id="kpi-margen">Cargando...</p></div>
        </div>
        <div class="kpi-card card-transacciones">
            <i class="fas fa-exchange-alt"></i>
            <div><h3>Transacciones</h3><p id="kpi-transacciones">Cargando...</p></div>
        </div>
    </div>
    
    <div class="kpi-row" style="margin-top: 20px;">
        <div class="kpi-card card-ticket">
            <i class="fas fa-receipt"></i>
            <div><h3>Ticket Promedio</h3><p id="kpi-ticket">Cargando...</p></div>
        </div>
        <div class="kpi-card card-clientes">
            <i class="fas fa-users"></i>
            <div><h3>Clientes Atendidos</h3><p id="kpi-clientes">Cargando...</p></div>
        </div>
        <div class="kpi-card card-productos">
            <i class="fas fa-box-open"></i>
            <div><h3>Productos Vendidos</h3><p id="kpi-productos">Cargando...</p></div>
        </div>
        <div class="kpi-card card-devoluciones">
            <i class="fas fa-undo"></i>
            <div><h3>Devoluciones</h3><p id="kpi-devoluciones">Cargando...</p></div>
        </div>
    </div>
</section>

<div class="analisis-graficos-row">

    <div class="chart-container chart-ventas">
        <h3>Evolución de Ventas (Últimos 7 días)</h3>
        <canvas id="ventasChart"></canvas>
    </div>

    <div class="chart-container chart-categorias">
        <?php include('../modulos/ventas_categoria.php'); ?>
    </div>

</div>
<!-- NUEVA FILA -->
<div class="analisis-grid-bottom">

    <?php include('../modulos/ventas_dia_semana.php'); ?>

    <div class="chart-container">
        <?php include('../modulos/ventas_hora.php'); ?>
    </div>

    <div class="chart-container">
        <?php include('../modulos/productos_rentables.php'); ?>
    </div>
</div>

<!-- Barra de estado de actualización -->
<div class="update-info-bar">
            <i class="fas fa-info-circle"></i>
            <span>Los datos se actualizan en tiempo real con la información registrada en el sistema.</span>
            <span class="update-timestamp" style="margin-left: auto;">
                Última actualización: <?php echo date('d/m/Y - h:i A'); ?>
            </span>
            <button onclick="window.location.reload()" style="background:none; border:none; cursor:pointer; margin-left: 15px; color: #64748b;">
                <i class="fas fa-sync-alt"></i>
            </button>
        </div>

    </main>

<script>
const simboloMoneda = "<?= $simboloMoneda ?>";
</script>   

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // 1. Cargar KPIs (se ejecuta al cargar)
        fetch('../modulos/obtener_kpis_analisis.php')
        .then(res => res.json())
        .then(data => {
            document.getElementById('kpi-ventas').innerText = simboloMoneda + ' ' + data.ventas;
            document.getElementById('kpi-ganancia').innerText = simboloMoneda + ' ' + data.ganancia;
            document.getElementById('kpi-margen').innerText = data.margen;
            document.getElementById('kpi-transacciones').innerText = data.transacciones;
            document.getElementById('kpi-ticket').innerText = simboloMoneda + ' ' + data.ticket_promedio;
            document.getElementById('kpi-clientes').innerText = data.clientes;
            document.getElementById('kpi-productos').innerText = data.productos_vendidos;
            document.getElementById('kpi-devoluciones').innerText = data.devoluciones;
        })
        .catch(err => console.error("Error al cargar KPIs:", err));

        // 2. Cargar Gráfico (usando la función reutilizable)
        cargarGrafico();
        
        // Opcional: Refrescar gráfico cada 30 segundos automáticamente
        // setInterval(cargarGrafico, 30000); 
    });

    function cargarGrafico() {
        fetch('../modulos/obtener_evolucion_ventas.php')
        .then(res => res.json())
        .then(data => {
            const ctx = document.getElementById('ventasChart').getContext('2d');

            if (window.miGrafico) window.miGrafico.destroy();

            window.miGrafico = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.fechas,
                    datasets: [{
                        label: 'Ventas ' + simboloMoneda,
                        data: data.totales,
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59,130,246,0.1)',
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true
                    }]
                }
            });
        })
        .catch(err => console.error("Error al cargar gráfico:", err));
    }
</script>
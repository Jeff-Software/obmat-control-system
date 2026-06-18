<?php
require_once('../config/config_global.php');
// 1. Aseguramos la definición de las variables de filtro
$tipo_reporte = $tipo_reporte ?? 'hoy';
$filtro = ($tipo_reporte === 'hoy') ? "WHERE DATE(fecha) = CURDATE()" : "";
$filtro_v = ($tipo_reporte === 'hoy') ? "WHERE DATE(v.fecha) = CURDATE()" : "";

// 2. Consultas SQL
$query_total = "SELECT COALESCE(SUM(total), 0) AS total FROM ventas $filtro";
$res_total = $conexion->query($query_total)->fetch_assoc();
$ventas = $res_total['total'];

$query_ticket = "SELECT COALESCE(SUM(total) / NULLIF(COUNT(id), 0), 0) AS promedio FROM ventas $filtro";
$res_ticket = $conexion->query($query_ticket)->fetch_assoc();
$ticket_promedio = $res_ticket['promedio'];

$query_trans = "SELECT COUNT(id) AS total FROM ventas $filtro";
$res_trans = $conexion->query($query_trans)->fetch_assoc();
$transacciones = $res_trans['total'];

$query_utilidad = "SELECT COALESCE(SUM((dv.precio_unitario - p.precio_compra) * dv.cantidad), 0) AS utilidad_total
    FROM detalle_ventas dv
    JOIN ventas v ON dv.id_venta = v.id
    JOIN productos p ON dv.producto_id = p.id
    $filtro_v";
$res_utilidad = $conexion->query($query_utilidad)->fetch_assoc();
$utilidad = $res_utilidad['utilidad_total'];
?> <section class="kpi-container">
    
    <div class="kpi-card">
        <div class="kpi-icon-box icon-bg-blue">
            <i class="fas fa-shopping-bag"></i>
        </div>
        <div class="kpi-content">
            <span class="kpi-title">VENTAS TOTALES</span>
            <h3 class="kpi-value"><?php echo ($ventas > 0) ? $simboloMoneda . ' ' . number_format($ventas, 2) : $simboloMoneda . ' 0.00'; ?></h3>
            <span class="kpi-subtext text-green">Total acumulado</span>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon-box icon-bg-green">
            <i class="fas fa-chart-line"></i>
        </div>
        <div class="kpi-content">
            <span class="kpi-title">TICKET PROMEDIO</span>
            <h3 class="kpi-value"><?= $simboloMoneda ?> <?php echo number_format($ticket_promedio, 2); ?></h3>
            <span class="kpi-subtext text-green">Por transacción</span>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon-box icon-bg-purple">
            <i class="fas fa-shopping-cart"></i>
        </div>
        <div class="kpi-content">
            <span class="kpi-title">TRANSACCIONES</span>
            <h3 class="kpi-value"><?php echo $transacciones; ?></h3>
            <span class="kpi-subtext text-green">Ventas realizadas</span>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon-box icon-bg-orange">
            <i class="fas fa-dollar-sign"></i>
        </div>
        <div class="kpi-content">
            <span class="kpi-title">UTILIDAD ESTIMADA</span>
            <h3 class="kpi-value"><?= $simboloMoneda ?> <?php echo number_format($utilidad, 2); ?></h3>
            <span class="kpi-subtext text-green">Ganancia bruta</span>
        </div>
    </div>

</section>
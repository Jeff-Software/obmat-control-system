<?php
session_start();
require_once('../config/conexion.php');
require_once('../config/config_global.php');
/** @var array<string,mixed> $configSistema */


$rol = $_SESSION['rol'] ?? '';
if ($rol !== 'cajero') {
    header("Location:../index.php");
    exit();
}

$query_estado = "SELECT caja_activa, caja_abierta FROM estado_caja WHERE id = 1";
$res_estado = $conexion->query($query_estado);
$estado = $res_estado->fetch_assoc();

//bloqueo de seguridad: Si la caja está cerrada, detenemos la carga del HTML
if (!$estado || $estado['caja_activa'] == 0) {
    die("<h1><?= __('caja_no_disponible') ?></h1>");
}

if ($estado['caja_abierta'] == 0) {
    die("
    <div style='text-align:center; margin-top:50px;'>
        <h1><?= __('caja_cerrada') ?></h1>
        <p><?= __('realizar_apertura_mensaje') ?></p>
        <form action='abrir_caja.php' method='POST'>
            <button type='submit' name='abrir_caja' style='padding:15px 30px; background:#28a745; color:white; border:none; border-radius:5px; cursor:pointer;'>
                <?= __('abrir_caja') ?>
            </button>
        </form>
    </div>");
}

$usuario_sesion = $_SESSION['usuario'] ?? '';
$ventas_dia = 0;
$total_ventas = 0;
$promedio_ventas = 0;

$query_ventas = "
    SELECT 
        COUNT(*) as total_ventas,
        SUM(v.total) as total_dia,
        AVG(v.total) as promedio
    FROM ventas v
    INNER JOIN usuarios u ON v.usuario_id = u.id
    WHERE DATE(v.fecha) = CURDATE() AND u.usuario = ?
";
$stmt = $conexion->prepare($query_ventas);
if ($stmt) {
    $stmt->bind_param("s", $usuario_sesion);
    $stmt->execute();
    $res_ventas = $stmt->get_result()->fetch_assoc();
    $total_ventas = $res_ventas['total_ventas'] ?? 0;
    $ventas_dia = $res_ventas['total_dia'] ?? 0;
    $promedio_ventas = $res_ventas['promedio'] ?? 0;
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>InkaDigital | Panel Cajero</title>
    <link rel="stylesheet" href="../assets/css/cajero.css">
    <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">   
</head>

<body>

    <?php
    $pagina_actual = basename($_SERVER['PHP_SELF']);
    include('../modulos/sidebar_cajero.php');
    ?>

    <main class="main-content">
        <header class="dashboard-header">
            <div class="welcome-box"
                style="background: #ffffff; padding: 30px; border-radius: 15px; border: 1px solid #e0e0e0; display: flex; align-items: center; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                <div class="kpi-icon-box icon-bg-blue welcome-icon">🖥️</div>
                <div style="flex-grow: 1; margin-left: 20px;">
                    <h2 style="margin: 0; color: #333;"><?= __('bienvenido_nuevo') ?>
                        <?php echo htmlspecialchars($_SESSION['nombre']); ?>!
                    </h2>
                    <p style="margin: 5px 0; color: #666; font-weight: 500;"><?= __('trabajando_en') ?>
                        <?php echo htmlspecialchars($_SESSION['caja']); ?>
                    </p>
                    <hr style="border: 0; border-top: 1px solid #eee; margin: 10px 0;">
                    <div style="color: #999; font-size: 0.9em;">
                        <span id="reloj"><?= __('cargando_fecha') ?></span>
                    </div>
                </div>

            </div>

            

<script>
function actualizarReloj() {

    const ahora = new Date();

    const fecha = ahora.toLocaleDateString(
        "<?= $configSistema['idioma'] ?? 'es' ?>-PE",
        {
            weekday:'long',
            year:'numeric',
            month:'long',
            day:'numeric'
        }
    );


    const hora = ahora.toLocaleTimeString(
        "<?= $configSistema['idioma'] ?? 'es' ?>-PE",
        {
            hour:'2-digit',
            minute:'2-digit'
        }
    );


    document.getElementById('reloj').textContent =
        '📅 ' + fecha + ' ' + hora;

}


actualizarReloj();

setInterval(actualizarReloj,1000);

</script>
        </header>

        <a href="nueva_venta.php" class="kpi-card nueva-venta-card">
            <div class="kpi-icon-box">🛒</div>
            <div class="kpi-content">
                <span class="nueva-venta-titulo"><?= __('nueva_venta') ?></span>
                <p class="nueva-venta-desc"><?= __('descripcion_nueva_venta') ?></p>
            </div>
            <span class="nueva-venta-flecha">→</span>
        </a>

        <a href="venta_espera.php" class="kpi-card venta-espera-card">
            <div class="kpi-icon-box-es">⏱️</div>
            <div class="kpi-content-es">
                <span class="nueva-venta-titulo"><?= __('ventas_espera') ?></span>
                <p class="nueva-venta-desc"><?= __('descripcion_venta_espera') ?></p>
            </div>
            <span class="nueva-venta-flecha">→</span>
        </a>

        <div class="kpi-container">
            <div class="kpi-resumen">
                <p class="kpi-resumen-titulo"> <?= __('resumen_dia') ?> </p>

                <div class="kpi-resumen-grid">

                    <div class="kpi-card">

                        <div class="kpi-icon-box icon-bg-blue">🧾</div>
                        <div class="kpi-content">
                            <span class="kpi-title"><?= __('ventas_realizadas') ?></span>
                            <span class="kpi-value"><?php echo $total_ventas; ?></span>
                        </div>

                    </div>

                    <div class="kpi-card">
                        <div class="kpi-icon-box icon-bg-green">💰</div>
                        <div class="kpi-content">
                            <span class="kpi-title"><?= __('total_vendido_hoy') ?></span>
                            <span class="kpi-value">
                            <?= $configSistema['simbolo'] ?> <?php echo number_format($ventas_dia, 2); ?>
                            </span>
                        </div>
                    </div>

                    <div class="kpi-card">
                        <div class="kpi-icon-box icon-bg-orange">📊</div>
                        <div class="kpi-content">
                            <span class="kpi-title"><?= __('promedio_por_venta') ?></span>
                            <span class="kpi-value">
                            <?= $configSistema['simbolo'] ?> <?php echo number_format($promedio_ventas, 2); ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </main>

<script>

function actualizarFechaSidebar(){

    const ahora = new Date();


    const idioma = "<?= $configSistema['idioma'] ?? 'es' ?>";


    const formato = idioma === "en"
        ? "en-US"
        : "es-PE";


    const fecha = ahora.toLocaleDateString(
        formato,
        {
            year:'numeric',
            month:'long',
            day:'numeric'
        }
    );


    const hora = ahora.toLocaleTimeString(
        formato,
        {
            hour:'2-digit',
            minute:'2-digit'
        }
    );


    document.getElementById('sidebar-fecha').textContent =
        fecha + ' ' + hora;


}


actualizarFechaSidebar();

setInterval(actualizarFechaSidebar,1000);


</script>

</body>

</html>
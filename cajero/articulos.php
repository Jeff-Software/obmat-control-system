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

// KPI datos
$total_activos = $conexion->query("SELECT COUNT(*) as total FROM productos WHERE estado = 1")->fetch_assoc()['total'];
$valor_inventario = $conexion->query("SELECT SUM(precio) as total FROM productos WHERE estado = 1")->fetch_assoc()['total'] ?? 0;

// Productos
$resultado = $conexion->query("SELECT id, nombre, categoria, precio_compra, precio, estado FROM productos ORDER BY nombre ASC");
$productos = $resultado->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= __('articulos') ?> | InkaDigital</title>
    <link rel="stylesheet" href="../assets/css/cajero.css">
    <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .estado-activo { color: #16a34a; font-weight: 600; }
        .estado-inactivo { color: #ef4444; font-weight: 600; }
    </style>
</head>
<body>

    <?php
    $pagina_actual = basename($_SERVER['PHP_SELF']);
    include('../modulos/sidebar_cajero.php');
    ?>

    <main class="main-content">

        <!-- KPI -->
        <div class="kpi-container" style="margin-bottom:20px;">
            <div class="kpi-card">
                <div class="kpi-icon-box icon-bg-blue">📦</div>
                <div class="kpi-content">
                    <span class="kpi-title"><?= __('total_articulos') ?></span>
                    <span class="kpi-value"><?php echo $total_activos; ?></span>
                    <span style="font-size:12px;color:#64748b;"><?= __('activos') ?></span>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon-box icon-bg-blue">💰</div>
                <div class="kpi-content">
                    <span class="kpi-title"><?= __('valor_inventario') ?></span>
                    <span class="kpi-value">
                    <?= $configSistema['simbolo'] ?> <?php echo number_format($valor_inventario, 2); ?>
                    </span>                    
                    <span style="font-size:12px;color:#64748b;"><?= __('valor_total') ?></span>
                </div>
            </div>
        </div>

        <!-- Tabla -->
        <div class="tabla-carrito">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th><?= __('nombre') ?></th>
                        <th><?= __('categoria') ?></th>
                        <th><?= __('precio_compra') ?></th>
                        <th><?= __('precio_venta') ?></th>
                        <th><?= __('estado') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $p): ?>
                    <tr>
                        <td><?php echo $p['id']; ?></td>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <span style="font-size:24px;">🛍️</span>
                                <?php echo htmlspecialchars($p['nombre']); ?>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($p['categoria']); ?></td>
                        <td><?= $configSistema['simbolo'] ?> <?php echo number_format($p['precio_compra'], 2); ?></td>
                        <td><?= $configSistema['simbolo'] ?> <?php echo number_format($p['precio'], 2); ?></td>
                        <td>
                            <?php if ($p['estado'] == 1): ?>
                                <span class="estado-activo">✅ <?= __('activo') ?></span>
                            <?php else: ?>
                                <span class="estado-inactivo">❌ <?= __('inactivo') ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
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


    const fechaSidebar = document.getElementById('sidebar-fecha');


    if(fechaSidebar){

        fechaSidebar.textContent =
        fecha + ' ' + hora;

    }

}


actualizarFechaSidebar();

setInterval(actualizarFechaSidebar,1000);


</script>


</body>
</html>
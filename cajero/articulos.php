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
    <title>Artículos | InkaDigital</title>
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
                    <span class="kpi-title">TOTAL ARTÍCULOS</span>
                    <span class="kpi-value"><?php echo $total_activos; ?></span>
                    <span style="font-size:12px;color:#64748b;">Activos</span>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon-box icon-bg-blue">💰</div>
                <div class="kpi-content">
                    <span class="kpi-title">VALOR INVENTARIO</span>
                    <span class="kpi-value">
                    <?= $configSistema['simbolo'] ?> <?php echo number_format($valor_inventario, 2); ?>
                    </span>                    
                    <span style="font-size:12px;color:#64748b;">Valor total</span>
                </div>
            </div>
        </div>

        <!-- Tabla -->
        <div class="tabla-carrito">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Precio Compra</th>
                        <th>Precio Venta</th>
                        <th>Estado</th>
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
                                <span class="estado-activo">✅ Activo</span>
                            <?php else: ?>
                                <span class="estado-inactivo">❌ Inactivo</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </main>

</body>
</html>
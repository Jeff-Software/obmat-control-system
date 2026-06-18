<?php
session_start();
require_once('../config/conexion.php');

$rol = $_SESSION['rol'] ?? '';
if ($rol !== 'cajero') {
    header("Location:../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Nueva Venta</title>
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
        <div class="venta-container">
            <!-- PANEL IZQUIERDO -->
            <div class="panel-izquierdo">
                <div class="tabla-carrito">
                    <table>
                        <thead>
                            <tr>
                                <th> VENTAS EN ESPERA</th>
                            </tr>
                        </thead>
                        <tbody id="carrito-body">
                            <tr id="fila-vacia">
                                <td colspan="5" class="carrito-vacio"> En espera.... </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="barra-inferior">
                    <span class="info-badge">📦 <span id="total-articulos">0</span> Artículos</span>
                    <span class="info-badge">🏪 <?php echo htmlspecialchars($_SESSION['caja']); ?></span>
                    <div class="barra-acciones">
                        <button onclick="window.location.href='nueva_venta.php'">← Volver a Nueva Venta</button>
                        <button class="btn-cancelar" onclick="cancelarVenta()">Cancelar</button>
                        <button class="btn-imprimir" onclick="window.open('generar_ticket.php','_blank')">
                            Imprimir
                        </button>
                    </div>
                </div>
            </div>
    </main>
<script>
function actualizarFechaSidebar() {
    const ahora = new Date();
    const fecha = ahora.toLocaleDateString('es-PE');
    const hora = ahora.toLocaleTimeString('es-PE', {
        hour: '2-digit',
        minute: '2-digit'
    });

    const fechaSidebar = document.getElementById('sidebar-fecha');

    if (fechaSidebar) {
        fechaSidebar.textContent = fecha + ' ' + hora;
    }
}

actualizarFechaSidebar();
setInterval(actualizarFechaSidebar, 1000);
</script>
    <script src="../assets/js/venta_espera.js"></script>
</body>

</html>
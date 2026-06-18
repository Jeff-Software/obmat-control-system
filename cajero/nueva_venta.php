<?php
session_start();
require_once('../config/conexion.php');
require_once('../config/config_global.php');
/** @var array<string,mixed> $configSistema */


/** @var array<string,mixed> $configSistema */

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
                <div class="buscador-box">
                    <input type="text" id="buscador" placeholder="🔍 Buscar producto por nombre..." autocomplete="off">
                </div>
                <div class="resultados-busqueda">
                    <div class="lista-resultados" id="lista-resultados"></div>
                </div>
                <div class="tabla-carrito">
                    <table>
                        <thead>
                            <tr>
                                <th>Descripción</th>
                                <th>Precio unitario</th>
                                <th>Cantidad</th>
                                <th>Importe</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="carrito-body">
                            <tr id="fila-vacia">
                                <td colspan="5" class="carrito-vacio">Busca un producto para agregarlo</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="barra-inferior">
                    <span class="info-badge">📦 <span id="total-articulos">0</span> Artículos</span>
                    <span class="info-badge">🏪 <?php echo htmlspecialchars($_SESSION['caja']); ?></span>
                    <div class="barra-acciones">
                        <button class="btn-lista" onclick="irAListaEspera()">Lista de espera</button>
                        <button class="btn-cancelar" onclick="cancelarVenta()">Cancelar</button>
                    </div>
                </div>
            </div>

            <!-- PANEL DERECHO -->
            <div class="panel-derecho">
                <h3 class="resumen-titulo">Resumen de venta</h3>
                <div class="resumen-fila">
                    <span>Subtotal (<span id="resumen-articulos">0</span> artículos)</span>
                    <span><?= $configSistema['simbolo'] ?> <span id="resumen-subtotal">0.00</span></span>
                </div>
                <div class="descuento-box">
                    <input type="number" id="descuento-input" min="0" max="100" value="0" placeholder="0">
                    <span>%</span>
                    <span class="descuento-valor">
                        -<?= $configSistema['simbolo'] ?>
                        <span id="resumen-descuento">0.00</span>
                    </span>
                </div>
                <div class="resumen-fila">
                    <span>Subtotal</span>
                    <span><?= $configSistema['simbolo'] ?> <span id="resumen-subtotal2">0.00</span></span>
                </div>
                <div class="resumen-fila total">
                    <span>TOTAL FINAL</span>
                    <span><?= $configSistema['simbolo'] ?> <span id="total-final">0.00</span></span>
                </div>
                <div class="ahorro-badge" id="ahorro-badge">
                    🏷️ Ahorro del cliente:
                    <?= $configSistema['simbolo'] ?>
                    <span id="ahorro-valor">0.00</span>
                </div>
                <button class="btn-cobrar" id="btn-cobrar" onclick="abrirModalPago()" disabled>
                    Cobrar (F9)
                </button>
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
    <!-- MODAL PAGO -->
    <div class="modal-overlay" id="modal-pago">
        <div class="modal">
            <h3>💳 Seleccionar método de pago</h3>
            <div class="modal-total"><?= $configSistema['simbolo'] ?> <span id="modal-total">0.00</span></div>
            <div class="metodos-pago">
                <button class="metodo-btn" onclick="seleccionarMetodo('efectivo', this)">💵 Efectivo</button>
                <button class="metodo-btn" onclick="seleccionarMetodo('tarjeta', this)">💳 Tarjeta</button>
                <button class="metodo-btn" onclick="seleccionarMetodo('yape', this)">📱 Yape</button>
            </div>
            <div class="modal-botones">
                <button class="btn-modal-cancelar" onclick="cerrarModalPago()">Cancelar</button>
                <button class="btn-modal-confirmar" id="btn-confirmar" onclick="confirmarVenta()" disabled>
                    Confirmar venta
                </button>
            </div>
        </div>
    </div>

    <audio id="audioVenta">
    <source src="../assets/audio/venta.mp3" type="audio/mpeg">
</audio>

<script>
const CONFIG_SISTEMA = {
    confirmarCancelarVenta:
        <?= (int)$configSistema['confirmar_cancelar_venta']; ?>,

    redondeoTotales:
        <?= (int)$configSistema['redondeo_totales']; ?>,

    simboloMoneda:
        "<?= $configSistema['simbolo'] ?>"
};
</script>

<script src="../assets/js/nueva_venta.js"></script>
<script src="../assets/js/sidebar.js"></script>

</body>
</html>
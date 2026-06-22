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
$metodosPago = $conexion->query("
    SELECT *
    FROM metodos_pago
    ORDER BY id
");
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title><?= __('nueva_venta') ?></title>
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
                    <input type="text" id="buscador" placeholder="<?= __('buscar_producto') ?>" autocomplete="off">
                </div>
                <div class="resultados-busqueda">
                    <div class="lista-resultados" id="lista-resultados"></div>
                </div>
                <div class="tabla-carrito">
                    <table>
                        <thead>
                            <tr>
                            <th><?= __('descripcion') ?></th>
                            <th><?= __('precio_unitario') ?></th>
                            <th><?= __('cantidad') ?></th>
                            <th><?= __('importe') ?></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="carrito-body">
                            <tr id="fila-vacia">
                                <td colspan="5" class="carrito-vacio">
                                    <?= __('carrito_vacio') ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="barra-inferior">
                    <span class="info-badge">📦 <span id="total-articulos">0</span> <?= __('articulos') ?></span>
                    <span class="info-badge">🏪 <?php echo htmlspecialchars($_SESSION['caja']); ?></span>
                    <div class="barra-acciones">
                        <button class="btn-lista" onclick="irAListaEspera()">
                            <i class="fas fa-clock"></i>
                            <?= __('lista_espera') ?>
                        </button>
                        <button class="btn-cancelar" onclick="cancelarVenta()">
                        <i class="fas fa-times"></i>
                        <?= __('cancelar') ?>
                    </button>
                    </div>
                </div>
            </div>

            <!-- PANEL DERECHO -->
            <div class="panel-derecho">
                <h3 class="resumen-titulo"><?= __('resumen_venta') ?></h3>
                <div class="resumen-fila">
                    <span>
                        <?= __('subtotal') ?> (
                        <span id="resumen-articulos">0</span>
                        <?= __('articulos') ?>
                        )
                    </span>
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
                    <span><?= __('subtotal') ?></span>
                    <span><?= $configSistema['simbolo'] ?> <span id="resumen-subtotal2">0.00</span></span>
                </div>
                <div class="resumen-fila total">
                    <span><?= __('total_final') ?></span>
                    <span><?= $configSistema['simbolo'] ?> <span id="total-final">0.00</span></span>
                </div>
            <div class="ahorro-badge" id="ahorro-badge">
                <i class="fas fa-tags"></i>
                <?= __('ahorro_cliente') ?>:
                <?= $configSistema['simbolo'] ?>
                <span id="ahorro-valor">0.00</span>
            </div>
            <button
                id="btn-cobrar"
                class="btn-cobrar"
                onclick="abrirModalPago()"
                disabled
            >
                <?= __('cobrar') ?> (F9)
            </button>
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


    const fechaSidebar = document.getElementById('sidebar-fecha');


    if(fechaSidebar){

        fechaSidebar.textContent =
        fecha + ' ' + hora;

    }

}


actualizarFechaSidebar();

setInterval(actualizarFechaSidebar,1000);


</script>

    <!-- MODAL PAGO -->
    <div class="modal-overlay" id="modal-pago">
        <div class="modal">
            <h3>💳 <?= __('seleccionar_pago') ?></h3>
            <div class="modal-total"><?= $configSistema['simbolo'] ?> <span id="modal-total">0.00</span></div>
            <div class="metodos-pago">

            <?php while($metodo = $metodosPago->fetch_assoc()) { ?>

                <button 
                    class="metodo-btn"
                    onclick="seleccionarMetodo(
                        '<?= htmlspecialchars($metodo['nombre']) ?>',
                        this
                    )"
                >

                <?php

                switch($metodo['nombre']) {

                    case 'efectivo':
                        echo "💵";
                        break;

                    case 'tarjeta':
                        echo "💳";
                        break;

                    case 'yape':
                        echo "📱";
                        break;

                    default:
                        echo "💰";
                }

                ?>

                <?= __(strtolower($metodo['nombre'])) ?>

                </button>

            <?php } ?>

            </div>
            <div class="modal-botones">
                <button class="btn-modal-cancelar" onclick="cerrarModalPago()"><?= __('cancelar') ?></button>
                <button class="btn-modal-confirmar" id="btn-confirmar" onclick="confirmarVenta()" disabled>
                    <?= __('confirmar_venta') ?>
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

<script>

const LANG = {

    noProductos:
        "<?= __('no_productos') ?>",

    buscarProducto:
        "<?= __('buscar_producto_carrito') ?>",

    confirmarCancelar:
        "<?= __('confirmar_cancelar_venta') ?>",

    seleccionarPago:
        "<?= __('seleccionar_metodo_pago') ?>",

    ventaCorrecta:
        "<?= __('venta_correcta') ?>",

    errorVenta:
        "<?= __('error_venta') ?>",

    ventaEspera:
        "<?= __('venta_espera') ?>",

    carritoVacio:
        "<?= __('carrito_vacio') ?>",

    confirmarVenta:
    "<?= __('confirmar_venta') ?>"    

};

</script>

<script src="../assets/js/nueva_venta.js"></script>
<script src="../assets/js/sidebar.js"></script>

</body>
</html>
<?php
session_start();
require_once('../config/conexion.php');
require_once('../config/config_global.php');

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
    <title><?= __('ventas_espera') ?></title>
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
                        <th><?= __('ventas_espera') ?></th>
                        <th>Acciones</th>
                    </tr>
                    </thead>
                        <tbody id="carrito-body">
                            <tr id="fila-vacia">
                                <td colspan="5" class="carrito-vacio"> <?= __('en_espera') ?> </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="barra-inferior">
                    <span class="info-badge">
                    📦 <span id="total-articulos">0</span> <?= __('articulos') ?>
                    </span>
                    <span class="info-badge">🏪 <?php echo htmlspecialchars($_SESSION['caja']); ?></span>
                    <div class="barra-acciones">
                    <button class="btn-volver" onclick="window.location.href='nueva_venta.php'">
                        ← <?= __('volver_nueva_venta') ?>
                    </button>
                    </div>
                </div>
            </div>
    </main>
<script>
const LANG = {
    enEspera: "<?= __('en_espera') ?>",
    productos: "<?= __('productos') ?>",
    recuperar: "<?= __('recuperar') ?>",
    eliminar: "<?= __('eliminar') ?>",
    imprimir: "<?= __('imprimir') ?>"
};
</script>

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
    <script src="../assets/js/venta_espera.js"></script>
</body>

</html>
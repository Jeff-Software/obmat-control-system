<?php
// 1. Incluimos el archivo de seguridad centralizado
require_once('../config/auth.php'); 

// 2. Validación adicional de rol
if ($_SESSION['rol'] !== 'admin') {
    header("Location: ../cajero/dashboard_cajero.php");
    exit();
}

require_once('../config/conexion.php');
require_once('../config/config_global.php');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="stylesheet" href="../assets/css/admin.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Historial de Ventas</title>
</head>
<body>

    <?php include('../modulos/sidebar.php'); ?>

    <main class="main-content compacto">
        <?php include('../modulos/ventas_historial.php'); ?>
    </main>

    <div id="modalDetalle" class="modal">
        <div class="modal-content">
            <span class="close" onclick="document.getElementById('modalDetalle').style.display='none'">&times;</span>
            <div id="contenidoDetalle">Cargando...</div>
        </div>
    </div>
<script>
const CONFIG_SISTEMA = {
    simboloMoneda: "<?= $simboloMoneda ?>"
};
</script>

<script src="../assets/js/ventas.js"></script>
</body>
</html>
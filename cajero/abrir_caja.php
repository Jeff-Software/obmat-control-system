<?php
require_once('../config/conexion.php');
if (isset($_POST['abrir_caja'])) {
    $conexion->query("UPDATE estado_caja SET caja_abierta = 1 WHERE id = 1");
    header("Location: dashboard_cajero.php");
    exit();
}
?>
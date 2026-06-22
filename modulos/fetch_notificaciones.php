<?php
require_once('../config/conexion.php');

$query = "SELECT mensaje FROM notificaciones WHERE leido = 0 ORDER BY fecha DESC LIMIT 5";
$result = $conexion->query($query);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo '<div class="notif-item">';
        echo '<i class="fas fa-exclamation-circle" style="margin-right: 8px; color: #f59e0b;"></i>';
        echo htmlspecialchars($row['mensaje']);
        echo '</div>';
    }
} else {
    echo '<p style="padding: 15px; font-size: 12px; color: #94a3b8; text-align: center;">'
    . __('sin_alertas_pendientes') .
    '</p>';
}
?>
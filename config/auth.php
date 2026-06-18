<?php
// auth.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit();
}

// Actualizar último acceso solo si la conexión ya está definida globalmente
if (isset($conexion) && isset($_SESSION['id'])) {
    $sql_update = "UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?";
    $stmt = $conexion->prepare($sql_update);
    $stmt->bind_param("i", $_SESSION['id']);
    $stmt->execute();
}
?>
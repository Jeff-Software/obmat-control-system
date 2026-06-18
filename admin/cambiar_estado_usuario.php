<?php

require_once('../config/conexion.php');
require_once('../config/auth.php');
require_once('../config/logs.php');

$id = (int)($_GET['id'] ?? 0);
$estado = $_GET['estado'] ?? '';

if ($_SESSION['id'] == $id && $estado == 'Inactivo') {

    header("Location: ../admin/usuarios.php?error=propia_cuenta");
    exit();

}

if ($id > 0 && ($estado == 'Activo' || $estado == 'Inactivo')) {

    $stmt = $conexion->prepare("
        UPDATE usuarios
        SET estado = ?
        WHERE id = ?
    ");

    $stmt->bind_param("si", $estado, $id);
    $stmt->execute();

    registrarLog(
        $conexion,
        $_SESSION['id'],
        "Cambió estado del usuario ID $id a $estado"
    );
}

header("Location: ../admin/usuarios.php");
exit();
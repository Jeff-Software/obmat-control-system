<?php
require_once('../config/conexion.php');
require_once('../config/logs.php');
session_start();

$usuario = $_POST['usuario'] ?? '';
$pass = $_POST['password'] ?? '';

if (empty($usuario) || empty($pass)) {
    header("Location: ../index.php?error=campos_vacios");
    exit();
}

$sql = "SELECT * FROM usuarios WHERE usuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {

    // Verificar si el usuario está activo
    if ($user['estado'] != 'Activo') {

        header("Location: ../index.php?error=inactivo");
        exit();

    }

    // Verificar contraseña
if (password_verify($pass, $user['password'])) {

        session_regenerate_id(true);

        $_SESSION['id'] = $user['id'];
        registrarLog(
            $conexion,
            $user['id'],
            'Inicio de sesión'
        );
        $_SESSION['usuario'] = $user['usuario'];
        $_SESSION['nombre'] = $user['nombre'];
        $_SESSION['rol'] = $user['rol'];
        $_SESSION['caja'] = $user['caja_asignada'];

        $_SESSION['ultimo_acceso'] = date('d/m/Y - H:i A');

        $sql_update = "UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?";
        $stmt_upd = $conexion->prepare($sql_update);
        $stmt_upd->bind_param("i", $user['id']);
        $stmt_upd->execute();

        if ($user['rol'] === 'admin') {

            header("Location: ../admin/dashboard_admin.php");

        } else {

            header("Location: ../cajero/dashboard_cajero.php");

        }

        exit();

    } else {

            header("Location: ../index.php?error=password");
            exit();

    }

} else {

            header("Location: ../index.php?error=usuario");
            exit();

}

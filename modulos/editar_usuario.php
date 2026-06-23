<?php
require_once('../config/conexion.php');
require_once('../config/auth.php');
require_once('../config/config_global.php');
require_once('../config/logs.php');

$id = $_GET['id'] ?? null;

// Procesar el formulario al guardar
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $nombre = trim($_POST['nombre']);
    $usuario_form = trim($_POST['usuario']);
    $rol = $_POST['rol'];
    $estado = $_POST['estado'];
    $caja_asignada = trim($_POST['caja_asignada']);
    $password = trim($_POST['password']);

    // Si es administrador no necesita caja
    if ($rol == 'admin') {
        $caja_asignada = null;
    }

    // Si escribió nueva contraseña
    if (!empty($password)) {

        $password_hash = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        $stmt = $conexion->prepare("
            UPDATE usuarios
            SET
                nombre = ?,
                usuario = ?,
                password = ?,
                rol = ?,
                estado = ?,
                caja_asignada = ?
            WHERE id = ?
        ");


        $stmt->bind_param(
            "ssssssi",
            $nombre,
            $usuario_form,
            $password_hash,
            $rol,
            $estado,
            $caja_asignada,
            $id
        );

    } else {

        // Mantener contraseña actual
        $stmt = $conexion->prepare("
            UPDATE usuarios
            SET
                nombre = ?,
                usuario = ?,
                rol = ?,
                estado = ?,
                caja_asignada = ?
            WHERE id = ?
        ");

        $stmt->bind_param(
            "sssssi",
            $nombre,
            $usuario_form,
            $rol,
            $estado,
            $caja_asignada,
            $id
        );
    }

    $stmt->execute();

    registrarLog(
        $conexion,
        $_SESSION['id'],
        "Editó usuario: $usuario_form"
    );

    header("Location: ../admin/usuarios.php?mensaje=actualizado");
    exit();
}
// Obtener datos actuales
$usuario = $conexion->query("SELECT * FROM usuarios WHERE id = $id")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= __('editar_usuario') ?></title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="../assets/css/editar_usuario.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<div class="main-content-form">

<div class="card-form">

        <div class="user-header">
            <div class="avatar-user">
                <i class="fas fa-user"></i>
            </div>

            <div class="user-info-header">
                <h3><?= htmlspecialchars($usuario['nombre']) ?></h3>
                <span><?= htmlspecialchars($usuario['usuario']) ?></span>
            </div>
        </div>

        <h2><?= __('editar_usuario') ?></h2>
        <p class="subtitulo">
            <?= __('modificar_usuario') ?>
        </p>

        <form method="POST">

            <div class="grupo">
                <label><?= __('nombre_completo') ?></label>
                <input type="text"
                       name="nombre"
                       value="<?= htmlspecialchars($usuario['nombre']) ?>"
                       required>
            </div>

            <div class="grupo">
                <label><?= __('usuario') ?></label>
                <input type="text"
                       name="usuario"
                       value="<?= htmlspecialchars($usuario['usuario']) ?>"
                       required>
            </div>

            <div class="grupo">
                <label><?= __('rol') ?></label>
                <select name="rol">

                    <option value="admin"
                    <?= $usuario['rol']=='admin'?'selected':'' ?>>
                    <?= __('administrador') ?>
                    </option>

                    <option value="cajero"
                    <?= $usuario['rol']=='cajero'?'selected':'' ?>>
                    <?= __('cajero') ?>
                    </option>

                </select>
            </div>

            <div class="grupo">
                <label><?= __('estado') ?></label>

                <select name="estado">

                    <option value="Activo"
                    <?= $usuario['estado']=='Activo'?'selected':'' ?>>
                    <?= __('activo') ?>
                    </option>

                    <option value="Inactivo"
                    <?= $usuario['estado']=='Inactivo'?'selected':'' ?>>
                    <?= __('inactivo') ?>
                    </option>

                </select>
            </div>

            <div class="grupo">
                <label><?= __('caja_asignada') ?></label>

                <input type="text"
                       name="caja_asignada"
                       value="<?= htmlspecialchars($usuario['caja_asignada']) ?>">
            </div>

            <div class="grupo">
                <label><?= __('nueva_contrasena') ?></label>

                <input type="password"
                       name="password">

                <small>
                    <?= __('dejar_vacio_password') ?>
                </small>
            </div>

            <div class="acciones">

                <button type="submit" class="btn-guardar">
                    <?= __('guardar_cambios') ?>
                </button>

                <a href="../admin/usuarios.php"
                   class="btn-cancelar">
                    <?= __('cancelar') ?>
                </a>

            </div>

        </form>

    </div>

</div>

</body>
</html>
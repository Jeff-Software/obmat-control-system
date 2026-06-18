<?php
require_once('../config/conexion.php');
require_once('../config/auth.php');
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
    <title>Editar Usuario</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
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

        <h2>Editar Usuario</h2>
        <p class="subtitulo">
            Modifica la información del usuario seleccionado.
        </p>

        <form method="POST">

            <div class="grupo">
                <label>Nombre Completo</label>
                <input type="text"
                       name="nombre"
                       value="<?= htmlspecialchars($usuario['nombre']) ?>"
                       required>
            </div>

            <div class="grupo">
                <label>Usuario</label>
                <input type="text"
                       name="usuario"
                       value="<?= htmlspecialchars($usuario['usuario']) ?>"
                       required>
            </div>

            <div class="grupo">
                <label>Rol</label>
                <select name="rol">

                    <option value="admin"
                    <?= $usuario['rol']=='admin'?'selected':'' ?>>
                    Administrador
                    </option>

                    <option value="cajero"
                    <?= $usuario['rol']=='cajero'?'selected':'' ?>>
                    Cajero
                    </option>

                </select>
            </div>

            <div class="grupo">
                <label>Estado</label>

                <select name="estado">

                    <option value="Activo"
                    <?= $usuario['estado']=='Activo'?'selected':'' ?>>
                    Activo
                    </option>

                    <option value="Inactivo"
                    <?= $usuario['estado']=='Inactivo'?'selected':'' ?>>
                    Inactivo
                    </option>

                </select>
            </div>

            <div class="grupo">
                <label>Caja Asignada</label>

                <input type="text"
                       name="caja_asignada"
                       value="<?= htmlspecialchars($usuario['caja_asignada']) ?>">
            </div>

            <div class="grupo">
                <label>Nueva Contraseña</label>

                <input type="password"
                       name="password">

                <small>
                    Dejar vacío para mantener la contraseña actual
                </small>
            </div>

            <div class="acciones">

                <button type="submit" class="btn-guardar">
                    Guardar Cambios
                </button>

                <a href="../admin/usuarios.php"
                   class="btn-cancelar">
                    Cancelar
                </a>

            </div>

        </form>

    </div>

</div>

</body>
</html>
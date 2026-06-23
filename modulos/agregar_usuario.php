<?php
require_once('../config/conexion.php');
require_once('../config/auth.php');
require_once('../config/config_global.php');
require_once('../config/logs.php');


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $nombre = trim($_POST['nombre']);
    $usuario = trim($_POST['usuario']);

    $passwordPlano = trim($_POST['password']);

    $password = password_hash(
        $passwordPlano,
        PASSWORD_DEFAULT
    );

    $rol = $_POST['rol'];
    $estado = $_POST['estado'];
    $caja_asignada = trim($_POST['caja_asignada']);

    // Verificar si existe el usuario
    $verificar = $conexion->prepare(
        "SELECT id FROM usuarios WHERE usuario=?"
    );

    $verificar->bind_param("s", $usuario);
    $verificar->execute();

    if($verificar->get_result()->num_rows > 0){

        $error = __('usuario_existe');

    }else{

        // Si es administrador no necesita caja
    if($rol == 'admin'){
        $caja_asignada = NULL;
    }

        $stmt = $conexion->prepare("
            INSERT INTO usuarios
            (
                nombre,
                usuario,
                password,
                rol,
                caja_asignada,
                estado
            )
            VALUES
            (?, ?, ?, ?, ?, ?)
        ");


        $stmt->bind_param(
            "ssssss",
            $nombre,
            $usuario,
            $password,
            $rol,
            $caja_asignada,
            $estado
        );

        $stmt->execute();

        registrarLog(
            $conexion,
            $_SESSION['id'],
            "Creó usuario: $usuario"
        );

        header("Location: ../admin/usuarios.php?mensaje=creado");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title><?= __('nuevo_usuario') ?></title>

    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="../assets/css/editar_usuario.css">
    <link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="pagina-usuarios">

<?php include('../modulos/sidebar.php'); ?>

<main class="main-content">

<div class="card-form">

<div class="user-header">
    <div class="avatar-user">
        <i class="fas fa-user-plus"></i>
    </div>

    <div>
    <h3><?= __('nuevo_usuario') ?></h3>

    <span>
        <?= __('crear_admin_cajero') ?>
    </span>
    </div>
</div>

<form method="POST">

    <div class="grupo">
        <label><?= __('nombre_completo') ?></label>
        <input type="text" name="nombre" required>
    </div>

    <div class="grupo">
        <label><?= __('usuario') ?></label>
        <input type="text" name="usuario" required>
    </div>

    <div class="grupo">
        <label><?= __('contrasena') ?></label>
        <input type="password" name="password" required>
    </div>

    <div class="grupo">
        <label><?= __('rol') ?></label>

        <select name="rol" id="rol">

        <option value="admin">
            <?= __('administrador') ?>
        </option>

        <option value="cajero">
            <?= __('cajero') ?>
        </option>

        </select>
    </div>

<div class="grupo grupo-full">
    <label><?= __('estado') ?></label>

    <select name="estado">
    <option value="Activo">
        <?= __('activo') ?>
    </option>

    <option value="Inactivo">
        <?= __('inactivo') ?>
    </option>
    </select>
</div>

<div class="grupo grupo-full" id="grupoCaja">
    <label><?= __('caja_asignada') ?></label>

    <input
        type="text"
        name="caja_asignada"
        placeholder="<?= __('ej_caja') ?>">
</div>

    <div class="acciones">

        <button type="submit" class="btn-guardar">
            <?= __('guardar_usuario') ?>
        </button>

        <a href="../admin/usuarios.php"
           class="btn-cancelar">
            <?= __('cancelar') ?>
        </a>

    </div>

</form>

</main>

<script>

const rol = document.getElementById('rol');
const grupoCaja = document.getElementById('grupoCaja');

function controlarCaja(){

    if(rol.value === 'admin'){
        grupoCaja.style.display = 'none';
    }else{
        grupoCaja.style.display = 'block';
    }

}

rol.addEventListener('change', controlarCaja);

controlarCaja();

</script>

</body>
</html>
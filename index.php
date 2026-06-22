<?php
require_once('config/config_global.php');
?>

<!DOCTYPE html>
<html lang="<?= $configSistema['idioma'] ?? 'es' ?>">
<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        OBMAT CONTROL - <?= __('titulo_login') ?>
    </title>

    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>
    <div class="login-container">
        <h2 class="titulo-sistema">OBMAT CONTROL</h2>
        <h3><?= __('login') ?></h3>
        <p><?= __('login_subtitulo') ?></p>
        <?php if(isset($_GET['error'])): ?>

    <div class="mensaje-error">

        <?php
        switch($_GET['error']){

            case 'inactivo':
                echo __('error_inactivo');
                break;

            case 'password':
                echo __('error_password');
                break;

            case 'usuario':
                echo __('error_usuario');
                break;

            case 'campos_vacios':
                echo __('error_campos_vacios');
                break;
        }
        ?>

    </div>

<?php endif; ?>
        <form action="modulos/validar_login.php" method="POST">
            <input 
            type="text" 
            name="usuario" 
            placeholder="<?= __('usuario') ?>" 
            required>
            <input 
            type="password" 
            name="password" 
            placeholder="<?= __('contrasena') ?>" 
            required>
            <button type="submit">
                <?= __('iniciar_sesion') ?>
            </button>
        </form>
    </div>
</body>
</html>